<x-filament-panels::page>
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('import-authors-form');
            const progressBar = document.getElementById('import-progress-bar');
            const importButton = document.getElementById('import-button');
            
            if (form && progressBar) {
                form.addEventListener('submit', function(e) {
                    // Показываем прогресс-бар сразу при нажатии
                    progressBar.style.display = 'block';
                    if (importButton) {
                        importButton.disabled = true;
                    }
                });
            }
            
            // Также слушаем события Livewire
            window.addEventListener('livewire:init', () => {
                Livewire.hook('morph.updated', ({ component, el }) => {
                    // Если импорт завершен, скрываем прогресс-бар
                    if (progressBar && !component.get('isImporting')) {
                        progressBar.style.display = 'none';
                    }
                });
            });
        });
    </script>
    @endpush
    
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Импорт авторов из Excel файла
            </h2>
            
            <form wire:submit="import" id="import-authors-form">
                {{ $this->form }}
                
                <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <x-filament::button
                        type="button"
                        color="gray"
                        icon="heroicon-o-eye"
                        wire:click="preview"
                        wire:loading.attr="disabled"
                        wire:target="preview"
                    >
                        Предпросмотр
                    </x-filament::button>

                    <x-filament::button
                        type="submit"
                        color="primary"
                        icon="heroicon-o-arrow-up-tray"
                        wire:loading.attr="disabled"
                        wire:target="import"
                        id="import-button"
                    >
                        <span wire:loading.remove wire:target="import">Импортировать</span>
                        <span wire:loading wire:target="import">Импорт...</span>
                    </x-filament::button>
                </div>
            </form>

            <div wire:loading wire:target="import" id="import-progress-bar" class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-6" style="display: none;">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 dark:border-blue-400"></div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-1">
                            Идет обработка...
                        </h3>
                        <p class="text-sm text-blue-700 dark:text-blue-300">
                            Пожалуйста, подождите...
                        </p>
                        <div class="mt-3 w-full bg-blue-200 dark:bg-blue-800 rounded-full h-2">
                            <div class="bg-blue-600 dark:bg-blue-400 h-2 rounded-full animate-pulse" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($isImporting)
                <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-6">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 dark:border-blue-400"></div>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-1">
                                Идет обработка...
                            </h3>
                            <p class="text-sm text-blue-700 dark:text-blue-300">
                                @if($importProgress)
                                    {{ $importProgress }}
                                @else
                                    Пожалуйста, подождите...
                                @endif
                            </p>
                            <div class="mt-3 w-full bg-blue-200 dark:bg-blue-800 rounded-full h-2">
                                <div class="bg-blue-600 dark:bg-blue-400 h-2 rounded-full animate-pulse" style="width: 100%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200 mb-2">
                Инструкции по импорту:
            </h3>
            <ul class="text-sm text-blue-700 dark:text-blue-300 space-y-1">
                <li>• Скачайте шаблон Excel файла для правильного формата</li>
                <li>• Заполните обязательные поля (как минимум `first_name_ua` / `first_name` или `last_name_ua` / `last_name`)</li>
                <li>• Изображения будут автоматически загружены на CDN из URL или base64</li>
                <li>• Синонимы можно указать через запятую, точку с запятой или вертикальную черту</li>
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
                                <th class="px-4 py-3">Имя</th>
                                <th class="px-4 py-3">Фамилия</th>
                                <th class="px-4 py-3">Псевдоним</th>
                                <th class="px-4 py-3">Статус</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            @forelse($previewRows as $row)
                                @php
                                    $meta = $row['meta'] ?? [];
                                    $attributes = $row['attributes'] ?? [];
                                @endphp
                                <tr class="align-top">
                                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                                        {{ $row['row'] }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-gray-900 dark:text-white">
                                            {{ $attributes['first_name'] ?? '—' }}
                                        </div>
                                        @if(!empty($attributes['first_name_ua']) && ($attributes['first_name_ua'] !== ($attributes['first_name'] ?? '')))
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                UA: {{ $attributes['first_name_ua'] }}
                                            </div>
                                        @endif
                                        @if(!empty($attributes['first_name_eng']) && ($attributes['first_name_eng'] !== ($attributes['first_name'] ?? '')))
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                EN: {{ $attributes['first_name_eng'] }}
                                            </div>
                                        @endif
                                        @if(!empty($meta['slug']))
                                            <div class="text-xs text-gray-400 mt-1">
                                                slug: {{ $meta['slug'] }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="text-gray-900 dark:text-gray-100">
                                            {{ $attributes['last_name'] ?? '—' }}
                                        </div>
                                        @if(!empty($attributes['last_name_ua']) && ($attributes['last_name_ua'] !== ($attributes['last_name'] ?? '')))
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                UA: {{ $attributes['last_name_ua'] }}
                                            </div>
                                        @endif
                                        @if(!empty($attributes['last_name_eng']) && ($attributes['last_name_eng'] !== ($attributes['last_name'] ?? '')))
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                EN: {{ $attributes['last_name_eng'] }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if(!empty($attributes['pseudonym']))
                                            <div class="text-gray-900 dark:text-gray-100">
                                                {{ $attributes['pseudonym'] }}
                                            </div>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-500">—</span>
                                        @endif
                                        @if(!empty($attributes['synonyms']))
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                Синонимы: {{ implode(', ', $attributes['synonyms']) }}
                                            </div>
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
                                        @if($meta['action'] === 'update')
                                            <div class="text-xs text-blue-600 dark:text-blue-300 mt-1">
                                                Будет обновлен существующий автор
                                            </div>
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
