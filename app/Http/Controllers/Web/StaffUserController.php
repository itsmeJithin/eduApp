<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\StaffUsers;
use Avatar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;
use Storage;

class StaffUserController extends Controller
{
    public function index()
    {
        return view('auth.register');
    }

    public function userLoginIndex()
    {
        return view('auth.login');
    }

    public function staffUserRegistration(Request $request)
    {

        // validate form fields
        $request->validate([
            'staff_name' => 'required',
            'staff_phone_number' => 'required|max:10|min:10',
            'staff_email' => 'required|email',
            'staff_password' => 'required|min:6',
            'confirm_password' => 'required|same:staff_password'
        ]);

        $input = $request->all();
        $input['staff_code'] = "APTU" . generateUniqueStaffCode(6);
        $input['staff_user_id'] = Uuid::uuid4();
        $input['activation_token'] = str_shuffle(substr(str_repeat(md5(mt_rand()), 2 + 60 / 32), 0, 60));
        $input['staff_password'] = bcrypt($request->staff_password);

        // register user
        $user = StaffUsers::create($input);
        // if registration success then return with success message
        if (!is_null($user)) {
            $avatar = Avatar::create($user->name)->getImageObject()->encode('png');
            Storage::put('avatars/' . $user->staff_user_id . '/avatar.png', (string)$avatar);
            return back()->with('success', 'You have registered successfully.');
        } // else return with error message
        else {
            return back()->with('error', 'Whoops! some error encountered. Please try again.');
        }
    }

    public function checkCredentials(Request $request)
    {

        $request->validate([
            "staff_email" => "required|email",
            "password" => "required|min:6"
        ]);

        $userCredentials = $request->only('staff_email', 'password');

        // check user using auth function
        if (Auth::guard("web")->attempt($userCredentials)) {
            return redirect()->route("home");
//            return redirect()->intended('dashboard');
        } else {
            return back()->with('error', 'Whoops! invalid username or password.');
        }
    }
}
