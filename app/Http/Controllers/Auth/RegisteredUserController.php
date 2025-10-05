<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Helpers\CDNUploader;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    protected function createAvatar()
    {
        $size = 8;
        $scale = 30;
        $imgSize = $size * $scale;

        $image = imagecreatetruecolor($imgSize, $imgSize);

        // Белый фон
        $bg = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $bg);

        // Случайный основной цвет
        $baseColor = imagecolorallocate(
            $image,
            rand(50, 200),
            rand(50, 200),
            rand(50, 200)
        );

        // Генерация паттерна
        for ($x = 0; $x < $size; $x++) {
            for ($y = 0; $y < $size; $y++) {
                if (rand(0, 1)) {
                    imagefilledrectangle(
                        $image,
                        $x * $scale,
                        $y * $scale,
                        ($x + 1) * $scale,
                        ($y + 1) * $scale,
                        $baseColor
                    );
                }
            }
        }

        // Временный путь
        $tmpPath = sys_get_temp_dir() . '/' . Str::uuid() . '.png';
        imagepng($image, $tmpPath);
        imagedestroy($image);

        // Создаём UploadedFile для CDNUploader
        $uploadedFile = new UploadedFile(
            $tmpPath,
            basename($tmpPath),
            'image/png',
            null,
            true // $test=true — чтобы не ругался, что файл не из HTTP-запроса
        );

        // Отправляем на CDN
        return CDNUploader::uploadFile($uploadedFile, 'avatars');
    }
    protected function gdToPng($image)
    {
        ob_start();
        imagepng($image);
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'username' => Str::slug($request->name) . '_' . Str::random(15),
            'avatar' => $this->createAvatar(),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return to_route('dashboard');
    }
}
