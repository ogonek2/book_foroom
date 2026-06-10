<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CategoryTreeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $paginator = CategoryTreeService::paginateCatalogCards(1);

        return view('categories.index', [
            'initialCards' => $paginator->items(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'has_more' => $paginator->hasMorePages(),
            ],
        ]);
    }

    public function cards(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->integer('page', 1));
        $search = $request->filled('search') ? trim($request->string('search')->toString()) : null;

        $paginator = CategoryTreeService::paginateCatalogCards($page, CategoryTreeService::CATALOG_PER_PAGE, $search);

        return response()->json([
            'data' => $paginator->items(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'total' => $paginator->total(),
            'has_more' => $paginator->hasMorePages(),
        ]);
    }

    public function show(Category $category): View
    {
        $category->load([
            'parent',
            'children' => fn ($query) => $query->where('is_active', true)->orderBy('sort_order')->orderBy('name'),
        ]);

        $meta = CategoryTreeService::catalogMeta();
        $allCategories = CategoryTreeService::activeCategories()->keyBy('id');

        $books = CategoryTreeService::booksQueryForCategory($category)
            ->with('categories')
            ->orderByDesc('rating')
            ->paginate(12);

        $booksCount = CategoryTreeService::booksCountForCategory(
            $category->id,
            $meta['descendantIdsMap'],
            $meta['bookIdsByCategory']
        );

        $breadcrumbs = [];
        $current = $category;
        while ($current) {
            array_unshift($breadcrumbs, $current);
            $current = $current->parent_id ? $allCategories->get($current->parent_id) : null;
        }

        return view('categories.show', compact('category', 'books', 'booksCount', 'breadcrumbs'));
    }
}
