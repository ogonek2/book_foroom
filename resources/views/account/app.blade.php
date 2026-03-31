@extends('layouts.app')

@section('title', 'Кабінет — Профіль')

@php
    $mixManifestPath = public_path('mix-manifest.json');
    $mixManifest = file_exists($mixManifestPath)
        ? json_decode(file_get_contents($mixManifestPath), true)
        : [];

    $accountCssPath = $mixManifest['/css/account.css'] ?? null;
    $accountJsPath = $mixManifest['/js/account.js'] ?? null;
@endphp

@push('styles')
    @if ($accountCssPath)
        <link rel="stylesheet" href="{{ asset(ltrim($accountCssPath, '/')) }}">
    @endif
@endpush

@section('main')
    <div class="acc-shell w-screen relative left-1/2 right-1/2 -ml-[50vw] -mr-[50vw]">
        <div id="account-app" v-cloak class="min-h-[calc(100vh-6.5rem)]"></div>
    </div>

    @php
        $accountBootstrap = [
            'viewer' => auth()->check()
                ? [
                    'id' => auth()->id(),
                    'username' => auth()->user()->username,
                    'name' => auth()->user()->name,
                    'avatar' => auth()->user()->avatar_display ?? null,
                ]
                : null,
            'profileUsername' => $username ?? null,
        ];
    @endphp

    <script>
        window.__ACCOUNT_BOOTSTRAP__ = @json($accountBootstrap);
    </script>
@endsection

@push('scripts')
    @if ($accountJsPath)
        <script src="{{ asset(ltrim($accountJsPath, '/')) }}" defer></script>
    @endif
@endpush

