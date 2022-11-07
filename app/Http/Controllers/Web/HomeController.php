<?php


namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

/**
 *
 * @Date 02/06/21
 */
class HomeController extends Controller
{
    public function dashboard()
    {
        if (Auth::check()) {
            return view('home');
        }
        return redirect::to("login")->withSuccess('Oops! You do not have access');

    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        Auth::logout();
        return Redirect('login');
    }

}
