<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <form wire:submit="save">
                {{ $this->form }}

                <div class="mt-6 flex items-center justify-end gap-3">
                    <x-filament::button type="submit" color="primary" icon="heroicon-o-check">
                        Сохранить
                    </x-filament::button>
                </div>
            </form>
        </div>

        <div class="bg-gray-50 dark:bg-gray-900/40 rounded-lg p-4 text-sm text-gray-700 dark:text-gray-300">
            <div class="font-semibold mb-2">Подсказка</div>
            <ul class="list-disc list-inside space-y-1">
                <li>Если в книге/авторе нет картинки или URL битый — будет использована заглушка.</li>
                <li>Значения сохраняются в БД и кешируются; после сохранения кеш очищается автоматически.</li>
            </ul>
        </div>
    </div>
</x-filament-panels::page>

