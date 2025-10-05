@extends('layouts.app')

@section('title', 'Редактировать библиотеку')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Редактировать библиотеку</h1>
                <a href="{{ route('libraries.show', $library) }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-times text-xl"></i>
                </a>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('libraries.update', $library) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-6">
                    <label for="name" class="block text-gray-700 text-sm font-medium mb-2">Название библиотеки</label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $library->name) }}" 
                           required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="description" class="block text-gray-700 text-sm font-medium mb-2">Описание (необязательно)</label>
                    <textarea id="description" 
                              name="description" 
                              rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500 @error('description') border-red-500 @enderror">{{ old('description', $library->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="is_private" 
                               value="1" 
                               {{ old('is_private', $library->is_private) ? 'checked' : '' }}
                               class="mr-2">
                        <span class="text-gray-700">Приватная библиотека</span>
                    </label>
                    <p class="text-gray-500 text-sm mt-1">Приватные библиотеки видны только вам</p>
                </div>

                <div class="flex space-x-4">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-lg font-medium transition duration-200">
                        <i class="fas fa-save mr-2"></i>Сохранить изменения
                    </button>
                    <a href="{{ route('libraries.show', $library) }}" class="bg-gray-600 hover:bg-gray-700 text-white py-2 px-6 rounded-lg font-medium transition duration-200">
                        <i class="fas fa-times mr-2"></i>Отмена
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
