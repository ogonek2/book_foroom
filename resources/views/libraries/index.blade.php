@extends('layouts.app')

@section('title', 'Добірки - Книжковий форум')

@section('main')
    <div id="libraries-app">
        <libraries-explorer
            :initial-libraries='@json($initialData["data"])'
            :initial-meta='@json($initialData["meta"])'
            :initial-links='@json($initialData["links"])'
            :initial-filters='@json($initialFilters)'
            :is-authenticated="{{ $isAuthenticated ? 'true' : 'false' }}"
            create-url="{{ $createUrl }}"
        ></libraries-explorer>
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
