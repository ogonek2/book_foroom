<?php

namespace App\Filament\Resources\ReviewResource\Pages;

use App\Filament\Resources\ReviewResource;
use App\Models\Hashtag;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReview extends EditRecord
{
    protected static string $resource = ReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Загружаем хештеги для формы
        $review = $this->record;
        
        // Отримуємо хештеги з бази даних
        $hashtagsFromDb = $review->hashtags->pluck('name')->toArray();
        
        // Витягуємо хештеги з контенту (на випадок, якщо вони є в тексті, але не в базі)
        $hashtagsFromContent = Hashtag::extractFromText($review->content);
        
        // Об'єднуємо та видаляємо дублікати
        $allHashtags = array_unique(array_merge($hashtagsFromDb, $hashtagsFromContent));
        
        $data['hashtags'] = $allHashtags;
        
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Сохраняем хештеги отдельно
        $hashtagNames = $data['hashtags'] ?? [];
        unset($data['hashtags']);
        
        return $data;
    }

    protected function afterSave(): void
    {
        // Оновлюємо модель після збереження
        $this->record->refresh();
        
        // Витягуємо хештеги з контенту
        $hashtagsFromContent = Hashtag::extractFromText($this->record->content);
        
        // Отримуємо хештеги з поля форми (TagsInput)
        $hashtagsFromForm = $this->form->getState()['hashtags'] ?? [];
        
        // Об'єднуємо хештеги з контенту та з форми, видаляємо дублікати
        $allHashtags = array_unique(array_merge($hashtagsFromContent, $hashtagsFromForm));
        
        // Синхронізуємо всі хештеги
        if (!empty($allHashtags)) {
            $this->record->syncHashtags($allHashtags);
        } else {
            // Якщо хештегів немає, очищаємо їх
            foreach ($this->record->hashtags as $hashtag) {
                if (!$this->record->is_draft && $this->record->status === 'active') {
                    $hashtag->decrementUsage();
                }
            }
            $this->record->hashtags()->detach();
        }
    }
}
