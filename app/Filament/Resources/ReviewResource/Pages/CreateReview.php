<?php

namespace App\Filament\Resources\ReviewResource\Pages;

use App\Filament\Resources\ReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateReview extends CreateRecord
{
    protected static string $resource = ReviewResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Сохраняем хештеги отдельно
        $hashtagNames = $data['hashtags'] ?? [];
        unset($data['hashtags']);
        
        return $data;
    }

    protected function afterCreate(): void
    {
        // Оновлюємо модель після створення
        $this->record->refresh();
        
        // Витягуємо хештеги з контенту
        $hashtagsFromContent = \App\Models\Hashtag::extractFromText($this->record->content);
        
        // Отримуємо хештеги з поля форми (TagsInput)
        $hashtagsFromForm = $this->form->getState()['hashtags'] ?? [];
        
        // Об'єднуємо хештеги з контенту та з форми, видаляємо дублікати
        $allHashtags = array_unique(array_merge($hashtagsFromContent, $hashtagsFromForm));
        
        // Синхронізуємо всі хештеги
        if (!empty($allHashtags)) {
            $this->record->syncHashtags($allHashtags);
        }
    }
}
