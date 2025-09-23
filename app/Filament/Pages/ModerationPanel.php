<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Filament\Actions\Action as PageAction;
use Filament\Notifications\Notification;
use App\Models\Post;
use App\Models\Review;
use App\Models\Quote;
use Illuminate\Support\Facades\Auth;

class ModerationPanel extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-check-circle';
    protected static ?string $navigationLabel = 'Модерация';
    protected static ?string $title = 'Панель модерации';
    protected static string $view = 'filament.pages.moderation-panel';

    public ?array $data = [];
    public $searchQuery = '';
    public $searchType = 'all';
    public $searchResults = [];
    public $moderationReason = '';
    public $showBlockForm = false;
    public $blockItemId = null;
    public $blockItemType = null;

    public function mount(): void
    {
        $this->form->fill();
        $this->performSearch();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Поиск контента')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('searchQuery')
                                    ->label('Поиск по ID или содержимому')
                                    ->placeholder('Введите ID или ключевые слова')
                                    ->live()
                                    ->afterStateUpdated(function ($state) {
                                        $this->searchQuery = $state;
                                        $this->performSearch();
                                    }),
                                Select::make('searchType')
                                    ->label('Тип контента')
                                    ->options([
                                        'all' => 'Все типы',
                                        'post' => 'Посты',
                                        'review' => 'Рецензии',
                                        'quote' => 'Цитаты',
                                    ])
                                    ->live()
                                    ->afterStateUpdated(function ($state) {
                                        $this->searchType = $state;
                                        $this->performSearch();
                                    }),
                                Select::make('statusFilter')
                                    ->label('Статус')
                                    ->options([
                                        'all' => 'Все статусы',
                                        'active' => 'Активно',
                                        'blocked' => 'Заблокировано',
                                        'pending' => 'Ожидает модерации',
                                    ])
                                    ->live()
                                    ->afterStateUpdated(function ($state) {
                                        $this->performSearch();
                                    }),
                            ]),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            PageAction::make('refreshContent')
                ->label('Обновить')
                ->icon('heroicon-o-arrow-path')
                ->action(function () {
                    $this->performSearch();
                }),
        ];
    }

    public function performSearch(): void
    {
        $query = $this->searchQuery;
        $type = $this->searchType;
        $statusFilter = $this->data['statusFilter'] ?? 'all';

        $this->searchResults = [];

        // Поиск в постах
        if ($type === 'all' || $type === 'post') {
            $postsQuery = Post::with(['user', 'topic', 'moderator']);
            
            if (!empty($query)) {
                $postsQuery->where(function ($q) use ($query) {
                    $q->where('id', 'like', "%{$query}%")
                      ->orWhere('content', 'like', "%{$query}%");
                });
            }
            
            if ($statusFilter !== 'all') {
                $postsQuery->where('status', $statusFilter);
            }
            
            $posts = $postsQuery->orderBy('created_at', 'desc')->limit(50)->get();
            
            foreach ($posts as $post) {
                $this->searchResults[] = [
                    'id' => $post->id,
                    'type' => 'post',
                    'type_label' => 'Пост',
                    'content' => $post->content,
                    'author' => $post->user->name ?? 'Гость',
                    'related_item' => $post->topic->title ?? 'Без темы',
                    'status' => $post->status,
                    'created_at' => $post->created_at,
                    'moderated_at' => $post->moderated_at,
                    'moderation_reason' => $post->moderation_reason,
                ];
            }
        }

        // Поиск в рецензиях
        if ($type === 'all' || $type === 'review') {
            $reviewsQuery = Review::with(['user', 'book', 'moderator']);
            
            if (!empty($query)) {
                $reviewsQuery->where(function ($q) use ($query) {
                    $q->where('id', 'like', "%{$query}%")
                      ->orWhere('content', 'like', "%{$query}%");
                });
            }
            
            if ($statusFilter !== 'all') {
                $reviewsQuery->where('status', $statusFilter);
            }
            
            $reviews = $reviewsQuery->orderBy('created_at', 'desc')->limit(50)->get();
            
            foreach ($reviews as $review) {
                $this->searchResults[] = [
                    'id' => $review->id,
                    'type' => 'review',
                    'type_label' => 'Рецензия',
                    'content' => $review->content,
                    'author' => $review->user->name ?? $review->guest_name ?? 'Гость',
                    'related_item' => $review->book->title ?? 'Без книги',
                    'status' => $review->status,
                    'created_at' => $review->created_at,
                    'moderated_at' => $review->moderated_at,
                    'moderation_reason' => $review->moderation_reason,
                ];
            }
        }

        // Поиск в цитатах
        if ($type === 'all' || $type === 'quote') {
            $quotesQuery = Quote::with(['user', 'book', 'moderator']);
            
            if (!empty($query)) {
                $quotesQuery->where(function ($q) use ($query) {
                    $q->where('id', 'like', "%{$query}%")
                      ->orWhere('content', 'like', "%{$query}%");
                });
            }
            
            if ($statusFilter !== 'all') {
                $quotesQuery->where('status', $statusFilter);
            }
            
            $quotes = $quotesQuery->orderBy('created_at', 'desc')->limit(50)->get();
            
            foreach ($quotes as $quote) {
                $this->searchResults[] = [
                    'id' => $quote->id,
                    'type' => 'quote',
                    'type_label' => 'Цитата',
                    'content' => $quote->content,
                    'author' => $quote->user->name ?? 'Гость',
                    'related_item' => $quote->book->title ?? 'Без книги',
                    'status' => $quote->status,
                    'created_at' => $quote->created_at,
                    'moderated_at' => $quote->moderated_at,
                    'moderation_reason' => $quote->moderation_reason,
                ];
            }
        }

        // Сортируем по дате создания
        usort($this->searchResults, function ($a, $b) {
            return $b['created_at']->timestamp - $a['created_at']->timestamp;
        });
    }

    public function approveContent($id, $type)
    {
        try {
            $moderatorId = Auth::id();
            
            switch ($type) {
                case 'post':
                    $item = Post::findOrFail($id);
                    $item->approve($moderatorId, 'Одобрено через панель модерации');
                    break;
                case 'review':
                    $item = Review::findOrFail($id);
                    $item->approve($moderatorId, 'Одобрено через панель модерации');
                    break;
                case 'quote':
                    $item = Quote::findOrFail($id);
                    $item->approve($moderatorId, 'Одобрено через панель модерации');
                    break;
            }

            $this->performSearch();

            Notification::make()
                ->title('Контент одобрен')
                ->body('Элемент успешно одобрен')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Ошибка одобрения')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function blockContent($id, $type, $reason)
    {
        try {
            $moderatorId = Auth::id();
            
            switch ($type) {
                case 'post':
                    $item = Post::findOrFail($id);
                    $item->block($moderatorId, $reason);
                    break;
                case 'review':
                    $item = Review::findOrFail($id);
                    $item->block($moderatorId, $reason);
                    break;
                case 'quote':
                    $item = Quote::findOrFail($id);
                    $item->block($moderatorId, $reason);
                    break;
            }

            $this->performSearch();

            Notification::make()
                ->title('Контент заблокирован')
                ->body('Элемент успешно заблокирован')
                ->warning()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Ошибка блокировки')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function unblockContent($id, $type)
    {
        try {
            $moderatorId = Auth::id();
            
            switch ($type) {
                case 'post':
                    $item = Post::findOrFail($id);
                    $item->update([
                        'status' => 'active',
                        'moderated_at' => now(),
                        'moderated_by' => $moderatorId,
                        'moderation_reason' => 'Разблокировано через панель модерации',
                    ]);
                    break;
                case 'review':
                    $item = Review::findOrFail($id);
                    $item->update([
                        'status' => 'active',
                        'moderated_at' => now(),
                        'moderated_by' => $moderatorId,
                        'moderation_reason' => 'Разблокировано через панель модерации',
                    ]);
                    break;
                case 'quote':
                    $item = Quote::findOrFail($id);
                    $item->update([
                        'status' => 'active',
                        'moderated_at' => now(),
                        'moderated_by' => $moderatorId,
                        'moderation_reason' => 'Разблокировано через панель модерации',
                    ]);
                    break;
            }

            $this->performSearch();

            Notification::make()
                ->title('Контент разблокирован')
                ->body('Элемент успешно разблокирован')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Ошибка разблокировки')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function openBlockModal($id, $type)
    {
        $this->blockItemId = $id;
        $this->blockItemType = $type;
        $this->moderationReason = '';
        $this->showBlockForm = true;
    }

    public function closeBlockForm()
    {
        $this->showBlockForm = false;
        $this->blockItemId = null;
        $this->blockItemType = null;
        $this->moderationReason = '';
    }

    public function confirmBlock()
    {
        if (empty($this->moderationReason)) {
            Notification::make()
                ->title('Ошибка')
                ->body('Необходимо указать причину блокировки')
                ->danger()
                ->send();
            return;
        }

        $this->blockContent($this->blockItemId, $this->blockItemType, $this->moderationReason);
        $this->closeBlockForm();
    }
}