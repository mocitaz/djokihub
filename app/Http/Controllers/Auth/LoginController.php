<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request; // Ditambahkan untuk digunakan di method authenticated
use Illuminate\Support\Facades\Log; // Ditambahkan untuk logging

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard'; // Pastikan ini mengarah ke rute dashboard Anda

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * The user has been authenticated.
     *
     * This method is called after a user is successfully authenticated.
     * We override it to add a session flash message for the welcome popup.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        Log::info('User authenticated successfully.', ['user_id' => $user->id, 'name' => $user->name]);
        
        // Set session flash data untuk menampilkan pop-up selamat datang di dashboard
        return redirect()->intended($this->redirectPath())
                         ->with('show_welcome_popup', true);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $userName = $request->user() ? $request->user()->name : 'User'; // Get user name before logout
        Log::info('User logging out.', ['user_id' => $request->user()->id ?? null, 'name' => $userName]);

        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        // Redirect ke halaman login setelah logout dengan pesan
        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/login')->with('status', 'Anda telah berhasil logout.');
    }
}