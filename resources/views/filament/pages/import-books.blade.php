<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Импорт книг из Excel файла
            </h2>
            
            <form wire:submit="import">
                {{ $this->form }}
                
                <div class="mt-6 flex justify-end">
                    <x-filament::button
                        type="submit"
                        color="primary"
                        icon="heroicon-o-arrow-up-tray"
                    >
                        Импортировать
                    </x-filament::button>
                </div>
            </form>
        </div>
        
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">
                Инструкции по импорту:
            </h3>
            <ul class="text-sm text-blue-700 dark:text-blue-300 space-y-1">
                <li>• Скачайте шаблон Excel файла для правильного формата</li>
                <li>• Убедитесь, что все категории и авторы уже существуют в системе</li>
                <li>• Заполните обязательные поля (название книги)</li>
                <li>• Для авторов используйте формат "Имя Фамилия"</li>
                <li>• Для рекомендуемых книг используйте "Да" или "Нет"</li>
                <li>• Сохраните файл в формате .xlsx или .xls</li>
            </ul>
        </div>
        
        <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">
            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200 mb-2">
                Важно:
            </h3>
            <p class="text-sm text-yellow-700 dark:text-yellow-300">
                Импорт может занять некоторое время в зависимости от размера файла. 
                Не закрывайте страницу до завершения процесса.
            </p>
        </div>
    </div>
</x-filament-panels::page>