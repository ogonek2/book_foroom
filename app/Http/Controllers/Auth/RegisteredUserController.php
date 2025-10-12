<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Helpers\CDNUploader;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class RegisteredUserController extends Controller
{
    // SMTP Configuration Variables (loaded from config/.env at runtime)
    private $smtpConfig = [];

    // OAuth Configuration Variables (to be moved to .env later)
    private $oauthConfig = [
        'google' => [
            'client_id' => '28002860055-l3skskj8cp2u2r4flp4cqmh03bvqvic1.apps.googleusercontent.com',
            'client_secret' => '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',
            'redirect_uri' => 'http://localhost:2020/auth/google/callback'
        ],
        'facebook' => [
            'client_id' => 'your-facebook-app-id',
            'client_secret' => 'your-facebook-app-secret',
            'redirect_uri' => 'http://localhost:8000/auth/facebook/callback'
        ]
    ];

    public function __construct()
    {
        // Prefer config() so values are cached correctly in production
        $this->smtpConfig = [
            'host' => config('mail.mailers.smtp.host', env('MAIL_HOST', 'mail.adm.tools')),
            'port' => (int) config('mail.mailers.smtp.port', env('MAIL_PORT', 587)),
            'username' => config('mail.mailers.smtp.username', env('MAIL_USERNAME')),
            'password' => config('mail.mailers.smtp.password', env('MAIL_PASSWORD')),
            'encryption' => config('mail.mailers.smtp.encryption', env('MAIL_ENCRYPTION', 'tls')),
            'from_address' => config('mail.from.address', env('MAIL_FROM_ADDRESS')),
            'from_name' => config('mail.from.name', env('MAIL_FROM_NAME', 'FOXY')),
        ];
    }

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
            'birth_date' => 'nullable|date|before:today',
            'city' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'favorite_genres' => 'nullable|array',
            'favorite_genres.*' => 'string|in:fiction,non-fiction,mystery,romance,fantasy,sci-fi',
            'newsletter' => 'nullable|boolean',
            'terms' => 'required|accepted'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'username' => Str::slug($request->name) . '_' . Str::random(10),
            'avatar' => $this->createAvatar(),
            'birth_date' => $request->birth_date,
            'city' => $request->city,
            'bio' => $request->bio,
            'favorite_genres' => $request->favorite_genres ? json_encode($request->favorite_genres) : null,
            'newsletter_subscribed' => $request->boolean('newsletter'),
            'email_verified_at' => null, // Will be verified via email
        ]);

        // Send email verification
        $this->sendEmailVerification($user);

        event(new Registered($user));

        // Don't auto-login, require email verification first
        return redirect()->route('verification.notice')->with('status', 'Реєстрація успішна! Перевірте свою електронну пошту для підтвердження акаунту.');
    }

    /**
     * Send email verification to user
     */
    protected function sendEmailVerification(User $user): void
    {
        try {
            // Generate verification token
            $token = Str::random(64);
            $user->update(['email_verification_token' => $token]);

            // Send verification email
            Mail::send('emails.verify-email', [
                'user' => $user,
                'token' => $token,
                'verificationUrl' => route('email.verify', ['token' => $token])
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                        ->subject('Підтвердження електронної пошти - Книжковий форум');
            });

            Log::info('Email verification sent to user: ' . $user->email);
        } catch (\Exception $e) {
            Log::error('Failed to send email verification: ' . $e->getMessage());
        }
    }

    /**
     * Verify email with token
     */
    public function verifyEmail(Request $request, $token): RedirectResponse
    {
        $user = User::where('email_verification_token', $token)->first();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Невірний токен підтвердження.');
        }

        $user->update([
            'email_verified_at' => now(),
            'email_verification_token' => null
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')->with('status', 'Електронна пошта успішно підтверджена!');
    }

    /**
     * Resend email verification
     */
    public function resendVerification(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $user = User::where('email', $request->email)->first();
        
        if ($user && !$user->hasVerifiedEmail()) {
            $this->sendEmailVerification($user);
            return back()->with('status', 'Лист підтвердження надіслано повторно.');
        }

        return back()->with('error', 'Користувач не знайдений або email вже підтверджений.');
    }

    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle(Request $request): RedirectResponse
    {
        $googleClientId = $this->oauthConfig['google']['client_id'];
        // Build the exact callback URL from route to avoid mismatches
        $redirectUri = route('auth.google.callback');

        $state = Str::random(40);
        $request->session()->put('google_oauth_state', $state);

        $params = http_build_query([
            'client_id' => $googleClientId,
            'redirect_uri' => $redirectUri,
            'scope' => 'openid email profile',
            'response_type' => 'code',
            'access_type' => 'offline',
            'include_granted_scopes' => 'true',
            'state' => $state,
            'prompt' => 'consent'
        ]);

        return redirect("https://accounts.google.com/o/oauth2/v2/auth?{$params}");
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        Log::info('Google OAuth callback started', ['has_error' => $request->has('error')]);
        
        if ($request->has('error')) {
            Log::warning('Google OAuth error received', ['error' => $request->get('error')]);
            return redirect()->route('login')->with('error', 'Google OAuth помилка: ' . $request->get('error'));
        }

        $state = $request->get('state');
        $expectedState = $request->session()->pull('google_oauth_state');
        
        Log::info('Google OAuth state check', [
            'state_present' => !empty($state),
            'expected_state_present' => !empty($expectedState),
            'states_match' => $state && $expectedState && hash_equals($expectedState, $state)
        ]);
        
        if (!$state || !$expectedState || !hash_equals($expectedState, $state)) {
            Log::warning('Google OAuth state validation failed');
            return redirect()->route('login')->with('error', 'Невірний стан OAuth. Спробуйте ще раз.');
        }

        $code = $request->get('code');
        if (!$code) {
            Log::warning('Google OAuth code missing');
            return redirect()->route('login')->with('error', 'Відсутній код авторизації Google.');
        }
        
        Log::info('Google OAuth code received, proceeding to token exchange');

        // Configure Guzzle client with SSL verification disabled for local development
        $clientConfig = ['timeout' => 10];
        
        // Disable SSL verification in local/development environment
        if (config('app.env') !== 'production') {
            $clientConfig['verify'] = false;
        }
        
        $client = new Client($clientConfig);

        try {
            // 1) Exchange code for access token
            $tokenResponse = $client->post('https://oauth2.googleapis.com/token', [
                'form_params' => [
                    'code' => $code,
                    'client_id' => $this->oauthConfig['google']['client_id'],
                    'client_secret' => $this->oauthConfig['google']['client_secret'],
                    'redirect_uri' => route('auth.google.callback'),
                    'grant_type' => 'authorization_code',
                ],
            ]);

            $tokenData = json_decode((string) $tokenResponse->getBody(), true);
            $accessToken = $tokenData['access_token'] ?? null;
            if (!$accessToken) {
                return redirect()->route('login')->with('error', 'Не вдалося отримати токен доступу Google.');
            }

            // 2) Fetch user info
            $userInfoResponse = $client->get('https://openidconnect.googleapis.com/v1/userinfo', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                ],
            ]);

            $googleUser = json_decode((string) $userInfoResponse->getBody(), true);
            $googleEmail = $googleUser['email'] ?? null;
            $googleName = $googleUser['name'] ?? null;
            $googlePicture = $googleUser['picture'] ?? null;

            if (!$googleEmail) {
                return redirect()->route('login')->with('error', 'Google не повернув email.');
            }

            // 3) Find or create user
            $user = User::where('email', $googleEmail)->first();
            if (!$user) {
                // Create new user
                Log::info('Google OAuth: Creating new user', ['email' => $googleEmail]);
                $user = User::create([
                    'name' => $googleName ?: explode('@', $googleEmail)[0],
                    'email' => $googleEmail,
                    'password' => Hash::make(Str::random(32)),
                    'username' => Str::slug($googleName ?: explode('@', $googleEmail)[0]) . '_' . Str::random(10),
                    'email_verified_at' => now(),
                    'avatar' => $googlePicture, // Set Google avatar for new users
                ]);
            } else {
                // Update existing user: verify email and optionally update avatar
                Log::info('Google OAuth: Found existing user', [
                    'email' => $googleEmail, 
                    'user_id' => $user->id,
                    'email_verified_before' => $user->email_verified_at ? 'yes' : 'no'
                ]);
                
                if (!$user->email_verified_at) {
                    $user->email_verified_at = now();
                    Log::info('Google OAuth: Email verified for existing user', ['user_id' => $user->id]);
                }
                // Update avatar only if user doesn't have one
                if (!$user->avatar && $googlePicture) {
                    $user->avatar = $googlePicture;
                    Log::info('Google OAuth: Avatar set from Google for existing user', ['user_id' => $user->id]);
                }
                $user->save();
            }

            Auth::login($user, true);
            
            // Regenerate session to prevent fixation attacks
            $request->session()->regenerate();
            
            Log::info('Google OAuth: User logged in successfully', [
                'user_id' => $user->id, 
                'email' => $user->email,
                'email_verified' => $user->email_verified_at ? 'yes' : 'no',
                'auth_check' => Auth::check() ? 'yes' : 'no',
                'auth_id' => Auth::id()
            ]);
            
            // Direct redirect to home instead of intended
            return redirect()->route('home')->with('status', 'Ви успішно увійшли через Google!');
        } catch (\Throwable $e) {
            Log::error('Google OAuth callback failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('login')->with('error', 'Не вдалося увійти через Google. Спробуйте пізніше.');
        }
    }

    /**
     * Redirect to Facebook OAuth
     */
    public function redirectToFacebook(): RedirectResponse
    {
        $facebookAppId = $this->oauthConfig['facebook']['client_id'];
        $redirectUri = $this->oauthConfig['facebook']['redirect_uri'];
        
        $params = http_build_query([
            'client_id' => $facebookAppId,
            'redirect_uri' => $redirectUri,
            'scope' => 'email',
            'response_type' => 'code',
            'state' => Str::random(40)
        ]);

        return redirect("https://www.facebook.com/v18.0/dialog/oauth?{$params}");
    }

    /**
     * Handle Facebook OAuth callback
     */
    public function handleFacebookCallback(Request $request): RedirectResponse
    {
        // This will be implemented when Facebook OAuth is fully configured
        return redirect()->route('login')->with('error', 'Facebook OAuth ще не налаштований.');
    }

    /**
     * Get SMTP configuration for reference
     */
    public function getSmtpConfig(): array
    {
        return $this->smtpConfig;
    }

    /**
     * Get OAuth configuration for reference
     */
    public function getOauthConfig(): array
    {
        return $this->oauthConfig;
    }
}
