@extends('layouts.app')

@section('title', 'Добірки - Книжковий форум')

@section('main')
    <div class="content-with-skeleton relative min-h-[480px]">
    <div id="libraries-app" v-cloak>
        <libraries-explorer
            :initial-libraries='@json($initialData["data"])'
            :initial-meta='@json($initialData["meta"])'
            :initial-links='@json($initialData["links"])'
            :initial-filters='@json($initialFilters)'
            :is-authenticated="{{ $isAuthenticated ? 'true' : 'false' }}"
            create-url="{{ $createUrl }}"
        ></libraries-explorer>
    </div>

    <!-- Skeleton: сітка добірок -->
    <div class="skeleton-placeholder pointer-events-none" aria-hidden="true">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @for($i = 0; $i < 8; $i++)
            <div class="rounded-2xl border border-gray-200/30 dark:border-gray-700/30 overflow-hidden bg-white/60 dark:bg-gray-800/60">
                <div class="skeleton aspect-video w-full"></div>
                <div class="p-4 space-y-2">
                    <div class="skeleton h-5 w-2/3 rounded"></div>
                    <div class="skeleton h-3 w-full rounded"></div>
                    <div class="skeleton h-3 w-1/2 rounded"></div>
                </div>
            </div>
            @endfor
        </div>
    </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (window.Vue) {
                    new Vue({ el: '#libraries-app' });
                }
            });
        </script>
    @endpush
@endsection
