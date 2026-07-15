<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryTreeService
{
    private const CACHE_KEY = 'category_tree_active_v2';

    private const CACHE_META_KEY = 'category_catalog_meta_v2';

    private const CACHE_TTL = 3600;

    public const CATALOG_PER_PAGE = 12;

    /**
     * @return Collection<int, Category>
     */
    public static function activeCategories(): Collection
    {
        return Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * @return array<int, array<int>>
     */
    public static function childrenMap(Collection $categories): array
    {
        $map = [];

        foreach ($categories as $category) {
            $parentId = $category->parent_id ?? 0;
            $map[$parentId] ??= [];
            $map[$parentId][] = $category->id;
        }

        return $map;
    }

    /**
     * @return array<int, int>
     */
    public static function descendantIdsMap(Collection $categories): array
    {
        $childrenMap = self::childrenMap($categories);
        $descendants = [];

        $walk = function (int $categoryId) use (&$walk, $childrenMap, &$descendants): array {
            if (isset($descendants[$categoryId])) {
                return $descendants[$categoryId];
            }

            $ids = [];
            foreach ($childrenMap[$categoryId] ?? [] as $childId) {
                $ids[] = $childId;
                $ids = array_merge($ids, $walk($childId));
            }

            $descendants[$categoryId] = array_values(array_unique($ids));

            return $descendants[$categoryId];
        };

        foreach ($categories as $category) {
            $walk($category->id);
        }

        return $descendants;
    }

    /**
     * @return array<int, array<int>>
     */
    public static function bookIdsByCategory(): array
    {
        // Лише книги, які реально є в books (без «сиріт» у pivot)
        $rows = DB::table('book_category')
            ->join('books', 'books.id', '=', 'book_category.book_id')
            ->select('book_category.book_id', 'book_category.category_id')
            ->get();
        $map = [];

        foreach ($rows as $row) {
            $map[$row->category_id] ??= [];
            $map[$row->category_id][] = (int) $row->book_id;
        }

        return $map;
    }

    public static function booksCountForCategory(
        int $categoryId,
        array $descendantIdsMap,
        array $bookIdsByCategory
    ): int {
        $ids = array_merge([$categoryId], $descendantIdsMap[$categoryId] ?? []);
        $bookIds = [];

        foreach ($ids as $id) {
            foreach ($bookIdsByCategory[$id] ?? [] as $bookId) {
                $bookIds[$bookId] = true;
            }
        }

        return count($bookIds);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function toFrontendTree(?Collection $categories = null): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () use ($categories) {
            $categories ??= self::activeCategories();
            $byId = $categories->keyBy('id');
            $childrenMap = self::childrenMap($categories);
            $descendantIdsMap = self::descendantIdsMap($categories);
            $bookIdsByCategory = self::bookIdsByCategory();

            $build = function (?int $parentId = 0) use (&$build, $byId, $childrenMap, $descendantIdsMap, $bookIdsByCategory): array {
                $nodes = [];

                foreach ($childrenMap[$parentId] ?? [] as $categoryId) {
                    /** @var Category|null $category */
                    $category = $byId->get($categoryId);
                    if (!$category) {
                        continue;
                    }

                    $children = $build($categoryId);

                    $nodes[] = [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'color' => $category->color,
                        'parent_id' => $category->parent_id,
                        'books_count' => self::booksCountForCategory(
                            $category->id,
                            $descendantIdsMap,
                            $bookIdsByCategory
                        ),
                        'children' => $children,
                    ];
                }

                return $nodes;
            };

            return $build(0);
        });
    }

    /**
     * @param  array<int, string>  $slugs
     * @return array<int, string>
     */
    public static function expandSlugs(array $slugs): array
    {
        $slugs = array_values(array_filter(array_map('trim', $slugs)));
        if ($slugs === []) {
            return [];
        }

        $categories = self::activeCategories();
        $bySlug = $categories->keyBy('slug');
        $descendantIdsMap = self::descendantIdsMap($categories);
        $expanded = [];

        foreach ($slugs as $slug) {
            $category = $bySlug->get($slug);
            if (!$category) {
                $expanded[] = $slug;
                continue;
            }

            $expanded[] = $category->slug;
            foreach ($descendantIdsMap[$category->id] ?? [] as $childId) {
                $child = $categories->firstWhere('id', $childId);
                if ($child) {
                    $expanded[] = $child->slug;
                }
            }
        }

        return array_values(array_unique($expanded));
    }

    /**
     * @return array<int, int>
     */
    public static function categoryIdsIncludingDescendants(Category $category): array
    {
        $categories = self::activeCategories();
        $descendantIdsMap = self::descendantIdsMap($categories);

        return array_values(array_unique(array_merge(
            [$category->id],
            $descendantIdsMap[$category->id] ?? []
        )));
    }

    public static function booksQueryForCategory(Category $category, bool $includeDescendants = true): Builder
    {
        $categoryIds = $includeDescendants
            ? self::categoryIdsIncludingDescendants($category)
            : [$category->id];

        return Book::query()->whereHas('categories', function ($query) use ($categoryIds) {
            $query->whereIn('categories.id', $categoryIds);
        });
    }

    /**
     * @return array<int, string>
     */
    public static function breadcrumb(Category $category): array
    {
        $trail = [];
        $current = $category;

        while ($current) {
            array_unshift($trail, $current->name);
            $current = $current->parent_id ? Category::find($current->parent_id) : null;
        }

        return $trail;
    }

    public static function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        Cache::forget(self::CACHE_META_KEY);
    }

    /**
     * @return array{descendantIdsMap: array<int, array<int>>, bookIdsByCategory: array<int, array<int>>}
     */
    public static function catalogMeta(): array
    {
        return Cache::remember(self::CACHE_META_KEY, self::CACHE_TTL, function () {
            $categories = self::activeCategories();

            return [
                'descendantIdsMap' => self::descendantIdsMap($categories),
                'bookIdsByCategory' => self::bookIdsByCategory(),
            ];
        });
    }

    public static function paginateCatalogCards(
        int $page = 1,
        int $perPage = self::CATALOG_PER_PAGE,
        ?string $search = null
    ): LengthAwarePaginator {
        $meta = self::catalogMeta();

        $query = Category::query()
            ->where('is_active', true)
            ->withCount('children')
            ->with('parent:id,name,slug');

        if ($search !== null && $search !== '') {
            $like = '%' . $search . '%';
            $query->where(function ($q) use ($like) {
                $q->where('name', 'like', $like)
                    ->orWhere('description', 'like', $like);
            });
        }

        $paginator = $query
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate($perPage, ['*'], 'page', $page);

        $categoryIds = $paginator->getCollection()->pluck('id')->all();
        $sampleBooks = self::batchSampleBooks($categoryIds, $meta['descendantIdsMap']);

        $cards = $paginator->getCollection()->map(
            fn (Category $category) => self::formatCatalogCard(
                $category,
                $meta,
                $sampleBooks[$category->id] ?? []
            )
        );

        return $paginator->setCollection($cards);
    }

    /**
     * @param  array<int, array<int>>  $descendantIdsMap
     * @return array<int, Collection<int, Book>>
     */
    public static function batchSampleBooks(
        array $displayCategoryIds,
        array $descendantIdsMap,
        int $limitPerCategory = 4
    ): array {
        if ($displayCategoryIds === []) {
            return [];
        }

        $reverseMap = [];
        $allSearchIds = [];

        foreach ($displayCategoryIds as $displayId) {
            $ids = array_merge([$displayId], $descendantIdsMap[$displayId] ?? []);
            foreach ($ids as $id) {
                $allSearchIds[] = $id;
                $reverseMap[$id][] = $displayId;
            }
        }

        $allSearchIds = array_values(array_unique($allSearchIds));
        $result = array_fill_keys($displayCategoryIds, collect());
        $seenPerDisplay = array_fill_keys($displayCategoryIds, []);

        if ($allSearchIds === []) {
            return $result;
        }

        $rows = DB::table('book_category')
            ->join('books', 'books.id', '=', 'book_category.book_id')
            ->whereIn('book_category.category_id', $allSearchIds)
            ->select([
                'books.id',
                'book_category.category_id as pivot_category_id',
            ])
            ->orderByDesc('books.rating')
            ->orderByDesc('books.id')
            ->get();

        $orderedBookIds = [];

        foreach ($rows as $row) {
            foreach ($reverseMap[$row->pivot_category_id] ?? [] as $displayId) {
                if (count($seenPerDisplay[$displayId]) >= $limitPerCategory) {
                    continue;
                }
                if (isset($seenPerDisplay[$displayId][$row->id])) {
                    continue;
                }
                $seenPerDisplay[$displayId][$row->id] = true;
                $orderedBookIds[$row->id] = true;
                $result[$displayId]->push((int) $row->id);
            }
        }

        if ($orderedBookIds === []) {
            return $result;
        }

        $booksById = Book::query()
            ->whereIn('id', array_keys($orderedBookIds))
            ->get(['id', 'title', 'book_name_ua', 'slug', 'cover_image', 'rating'])
            ->keyBy('id');

        foreach ($result as $displayId => $ids) {
            $result[$displayId] = $ids
                ->map(fn (int $id) => $booksById->get($id))
                ->filter()
                ->values();
        }

        return $result;
    }

    /**
     * @param  array{descendantIdsMap: array<int, array<int>>, bookIdsByCategory: array<int, array<int>>}  $meta
     * @param  Collection<int, Book>  $books
     * @return array<string, mixed>
     */
    public static function formatCatalogCard(Category $category, array $meta, Collection $books): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'color' => $category->color ?: '#6366f1',
            'parent_name' => $category->parent?->name,
            'books_count' => self::booksCountForCategory(
                $category->id,
                $meta['descendantIdsMap'],
                $meta['bookIdsByCategory']
            ),
            'children_count' => (int) ($category->children_count ?? 0),
            'url' => route('categories.show', $category->slug),
            'books' => $books->map(fn (Book $book) => [
                'title' => $book->book_name_ua ?: $book->title,
                'cover' => $book->cover_image_display,
            ])->values()->all(),
        ];
    }

    /**
     * Resolve or create a category path like "Художня / Фантастика".
     */
    public static function resolvePath(string $path, bool $previewMode = false): array
    {
        $segments = preg_split('/\s*[>\/|]\s*/u', trim($path)) ?: [trim($path)];
        $segments = array_values(array_filter(array_map('trim', $segments), fn ($s) => $s !== ''));

        if ($segments === []) {
            return ['id' => null, 'name' => null, 'will_create' => false];
        }

        $parentId = null;
        $category = null;
        $willCreate = false;

        foreach ($segments as $segment) {
            $slug = Str::slug($segment);
            $query = Category::query()
                ->where(function ($q) use ($segment, $slug) {
                    $q->where('name', $segment);
                    if ($slug !== '') {
                        $q->orWhere('slug', $slug);
                    }
                });

            if ($parentId !== null) {
                $query->where('parent_id', $parentId);
            } else {
                $query->whereNull('parent_id');
            }

            $category = $query->first();

            if (!$category) {
                if ($previewMode) {
                    $willCreate = true;
                    $parentId = null;
                    continue;
                }

                $uniqueSlug = self::ensureUniqueSlug($slug !== '' ? $slug : Str::slug($segment));
                $category = Category::create([
                    'name' => $segment,
                    'slug' => $uniqueSlug,
                    'parent_id' => $parentId,
                    'description' => 'Автоматично створено під час імпорту',
                    'color' => '#3B82F6',
                    'icon' => 'heroicon-o-book-open',
                    'sort_order' => 0,
                    'is_active' => true,
                ]);
                $willCreate = true;
                self::forgetCache();
            }

            $parentId = $category->id;
        }

        return [
            'id' => $category?->id,
            'name' => $category?->name,
            'will_create' => $willCreate,
        ];
    }

    private static function ensureUniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug !== '' ? $baseSlug : 'category';
        $original = $slug;
        $counter = 1;

        while (Category::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
