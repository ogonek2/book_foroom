<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Форма поиска -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Поиск и модерация контента
            </h2>
            
            <form wire:submit="save">
                {{ $this->form }}
            </form>
        </div>

        <!-- Результаты поиска -->
        @if(count($searchResults) > 0)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Результаты поиска ({{ count($searchResults) }})
                    </h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Тип</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Содержимое</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Автор</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Связанный элемент</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Статус</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Дата</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Действия</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($searchResults as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $item['id'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($item['type'] === 'post') bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400
                                            @elseif($item['type'] === 'review') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400
                                            @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400 @endif">
                                            {{ $item['type_label'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100 max-w-xs">
                                        <div class="truncate" title="{{ $item['content'] }}">
                                            {{ Str::limit($item['content'], 100) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $item['author'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $item['related_item'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($item['status'] === 'active') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400
                                            @elseif($item['status'] === 'blocked') bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400
                                            @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400 @endif">
                                            @if($item['status'] === 'active') Активно
                                            @elseif($item['status'] === 'blocked') Заблокировано
                                            @else Ожидает модерации @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $item['created_at']->format('d.m.Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            @if($item['status'] !== 'active')
                                                <x-filament::button
                                                    wire:click="approveContent({{ $item['id'] }}, '{{ $item['type'] }}')"
                                                    wire:confirm="Вы уверены, что хотите одобрить этот контент?"
                                                    color="success"
                                                    size="sm">
                                                    Одобрить
                                                </x-filament::button>
                                            @endif
                                            
                                            @if($item['status'] !== 'blocked')
                                                <x-filament::button
                                                    onclick="blockItem({{ $item['id'] }}, '{{ $item['type'] }}')"
                                                    color="danger"
                                                    size="sm">
                                                    Заблокировать
                                                </x-filament::button>
                                            @endif
                                            
                                            @if($item['status'] === 'blocked')
                                                <x-filament::button
                                                    wire:click="unblockContent({{ $item['id'] }}, '{{ $item['type'] }}')"
                                                    wire:confirm="Вы уверены, что хотите разблокировать этот контент?"
                                                    color="info"
                                                    size="sm">
                                                    Разблокировать
                                                </x-filament::button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @elseif($searchQuery || $searchType !== 'all')
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Ничего не найдено</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        По вашему запросу ничего не найдено. Попробуйте изменить параметры поиска.
                    </p>
                </div>
            </div>
        @else
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Начните поиск</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Введите ID или ключевые слова для поиска контента.
                    </p>
                </div>
            </div>
        @endif

    </div>

    <script>
        function blockItem(id, type) {
            const reason = prompt('Причина блокировки:');
            if (reason && reason.trim() !== '') {
                // Вызываем Livewire метод
                @this.call('blockContent', id, type, reason);
            }
        }
    </script>
</x-filament-panels::page>