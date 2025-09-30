<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;

class ProfileController extends Controller
{
    public function show($username = null)
    {
        $user = $username ? User::where('username', $username)->firstOrFail() : Auth::user();
        return view('profile.pages.overview', compact('user'));
    }

    public function library($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        return view('profile.pages.library', compact('user'));
    }

    public function reviews($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        return view('profile.pages.reviews', compact('user'));
    }

    public function discussions($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        return view('profile.pages.discussions', compact('user'));
    }

    public function quotes($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        return view('profile.pages.quotes', compact('user'));
    }

    public function collections($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        return view('profile.pages.collections', compact('user'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'bio' => 'nullable|string|max:1000',
            'avatar' => [
                'nullable',
                File::image()
                    ->max(2048) // 2MB
                    ->types(['jpeg', 'png', 'gif', 'webp'])
            ],
        ]);

        $data = $request->only(['name', 'username', 'email', 'bio']);

        // Обработка загрузки аватарки
        if ($request->hasFile('avatar')) {
            // Удаляем старую аватарку, если она есть
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Сохраняем новую аватарку
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $avatarPath;
        }

        $user->update($data);

        return redirect()->route('profile.show')
            ->with('success', 'Профиль успешно обновлен!');
    }

    public function destroyAvatar()
    {
        $user = Auth::user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
        }

        return redirect()->route('profile.edit')
            ->with('success', 'Аватарка удалена!');
    }
}
