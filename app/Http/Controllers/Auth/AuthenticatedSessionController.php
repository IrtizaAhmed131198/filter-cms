<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\imagetable;
use Session;

class AuthenticatedSessionController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
          $logo = imagetable::
               select('img_path')
               ->where('table_name','=','logo')
               ->first();

          $favicon = imagetable::
                           select('img_path')
                           ->where('table_name','=','favicon')
                           ->first();

        View()->share('logo',$logo);
        View()->share('favicon',$favicon);
    }

    /** =============================
     *  USER LOGIN
     *  ============================= */
    public function create(): View
    {
        return view('auth.login'); // normal user login page
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        // user must NOT be admin
        if ($user->role == 1) {
            Auth::logout();
            return redirect()->route('admin.login')->withErrors(['email' => 'Admins must log in from admin panel.']);
        }

        Session::flash('message', 'You have logged In  Successfully');
        Session::flash('alert-class', 'alert-success');

        return redirect()->intended(route('dashboard'));
    }

    /** =============================
     *  ADMIN LOGIN
     *  ============================= */
    public function createAdmin(): View
    {
        return view('auth.admin-login'); // a separate Blade for admin
    }

    public function storeAdmin(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

        // only admins allowed
        if ($user->role != 1) {
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'Access denied.']);
        }

        Session::flash('message', 'You have logged In  Successfully');
        Session::flash('alert-class', 'alert-success');

        return redirect()->intended(route('admin.dashboard'));
    }

    /** =============================
     *  LOGOUT (shared)
     *  ============================= */
    public function logout(Request $request): RedirectResponse
    {
        $user = Auth::user();
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($user && $user->role == 1) {
            return redirect()->route('admin.login');
        }

        return redirect()->route('login');
    }
}
