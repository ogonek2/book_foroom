<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use Symfony\Component\Mailer\Exception\TransportException;

class PasswordResetLinkController extends Controller
{
    /**
     * Show the password reset link request page.
     */
    public function create(Request $request): View
    {
        return view('auth.forgot-password', [
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            $status = Password::sendResetLink(
                $request->only('email')
            );
        } catch (TransportException $e) {
            Log::error('Password reset mail failed', [
                'email' => $request->input('email'),
                'message' => $e->getMessage(),
            ]);

            return back()->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Наразі не вдалося надіслати лист. Спробуйте пізніше або зверніться до адміністратора.',
                ]);
        }

        if ($status == Password::RESET_LINK_SENT) {
            return back()->with('status', 'Якщо вказана електронна адреса зареєстрована в системі, ми надіслали вам посилання для відновлення пароля.');
        }

        return back()->withInput($request->only('email'))
            ->withErrors(['email' => 'Якщо вказана електронна адреса зареєстрована в системі, ми надіслали вам посилання для відновлення пароля.']);
    }
}
