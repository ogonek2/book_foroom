<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Импорт книг из Excel файла
            </h2>
            
            <form wire:submit="import">
                {{ $this->form }}
                
                <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <x-filament::button
                        type="button"
                        color="gray"
                        icon="heroicon-o-eye"
                        wire:click="preview"
                    >
                        Предпросмотр
                    </x-filament::button>

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
                <li>• Заполните обязательные поля (как минимум `nazvanie` / `book_name_ua`)</li>
                <li>• Если автор или жанр не найдены, система предложит создать их автоматически (см. предпросмотр)</li>
                <li>• Для авторов используйте формат «Имя Фамилия»</li>
                <li>• Для рекомендуемых книг используйте «Да» или «Нет»</li>
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

        @if($hasPreview)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 space-y-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Предпросмотр импорта
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Проверьте данные, предупреждения и возможные ошибки перед окончательным импортом.
                    </p>
                </div>

                @if(!empty($previewErrors))
                    <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-red-800 dark:text-red-200 mb-2">
                            Ошибки (импорт прервет строки):
                        </h4>
                        <ul class="text-sm text-red-700 dark:text-red-100 space-y-1 list-disc list-inside">
                            @foreach($previewErrors as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(!empty($previewWarnings))
                    <div class="bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-700 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-amber-800 dark:text-amber-100 mb-2">
                            Предупреждения:
                        </h4>
                        <ul class="text-sm text-amber-700 dark:text-amber-100 space-y-1 list-disc list-inside">
                            @foreach($previewWarnings as $warning)
                                <li>{{ $warning }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-600 dark:text-gray-300">
                                <th class="px-4 py-3">#</th>
                                <th class="px-4 py-3">Название</th>
                                <th class="px-4 py-3">Автор</th>
                                <th class="px-4 py-3">Жанры</th>
                                <th class="px-4 py-3">Статус</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            @forelse($previewRows as $row)
                                <tr class="align-top">
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                                        {{ $row['row'] }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-gray-900 dark:text-white">
                                            {{ $row['title'] ?: '—' }}
                                        </div>
                                        @if(!empty($row['book_name_ua']) && $row['book_name_ua'] !== $row['title'])
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                UA: {{ $row['book_name_ua'] }}
                                            </div>
                                        @endif
                                        @if(!empty($row['slug']))
                                            <div class="text-xs text-gray-400 mt-1">
                                                slug: {{ $row['slug'] }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($row['author'])
                                            <div class="text-gray-900 dark:text-gray-100">
                                                {{ $row['author'] }}
                                            </div>
                                            @if($row['will_create_author'])
                                                <div class="text-xs text-amber-600 dark:text-amber-300 mt-1">
                                                    Буде створено нового автора
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if(!empty($row['categories']))
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($row['categories'] as $category)
                                                    @php
                                                        $isNew = in_array($category, $row['will_create_categories'] ?? [], true);
                                                    @endphp
                                                    <span class="px-2 py-1 rounded-full text-xs {{ $isNew ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-200' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-200' }}">
                                                        {{ $category }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if(!empty($row['errors']))
                                            <span class="inline-flex items-center rounded-full bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-200 px-2 py-1 text-xs font-medium">
                                                Ошибка
                                            </span>
                                        @elseif(!empty($row['warnings']))
                                            <div class="space-y-1">
                                                <span class="inline-flex items-center rounded-full bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-200 px-2 py-1 text-xs font-medium">
                                                    Предупреждение
                                                </span>
                                                <ul class="mt-2 text-xs text-amber-700 dark:text-amber-200 list-disc list-inside space-y-1">
                                                    @foreach($row['warnings'] as $warning)
                                                        <li>{{ $warning }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-200 px-2 py-1 text-xs font-medium">
                                                Готово к импорту
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                        Нет данных для предпросмотра
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>