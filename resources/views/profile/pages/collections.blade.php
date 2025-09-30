@extends('profile.layout')

@section('profile-content')
<div>
    <h2 class="text-2xl font-bold text-white mb-6">Мої добірки</h2>
    
    <div class="text-center py-12 bg-gray-800 rounded-lg">
        <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
        </svg>
        <h3 class="text-xl font-semibold text-gray-300 mb-2">Ще немає добірок</h3>
        <p class="text-gray-500 mb-6">Створюйте добірки книг за темами</p>
        <button class="px-6 py-3 bg-gradient-to-r from-orange-500 to-pink-500 text-white rounded-lg font-medium hover:from-orange-600 hover:to-pink-600 transition-all">
            Створити добірку
        </button>
    </div>
</div>
@endsection
