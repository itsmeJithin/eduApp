<?php
/**
 * User: jithinvijayan
 * Date: 27/02/19
 * Time: 3:02 PM
 */

namespace App\Http\Controllers\API;


use App\Constants\Actions;
use App\Exceptions\CoreException;
use App\Jobs\OTPSend;
use App\Jobs\S3Upload;
use App\Models\Users;
use App\Models\UsersFCMTokens;
use File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Avatar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use Storage;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class UserController extends BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $users = Users::all();
        return $this->sendResponse($users->toArray(), 'Users retrieved successfully.');
    }


    /**
     * @param Request $request
     * @return JsonResponse|Response
     * @throws \Twilio\Exceptions\ConfigurationException
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'mobile_number' => 'required|unique:users,mobile_number',
            'parent_number' => 'required|unique:users,parent_number',
            'country' => 'nullable',
            'nationality' => 'nullable',
            'state' => 'nullable',
            'place' => 'nullable',
            'school' => 'nullable',
            'school_place' => 'nullable',
            'city' => 'nullable',
            'pin_code' => 'nullable',
            'email' => 'email|unique:users',
            'otp' => 'required|int|min:6',
//            'c_password' => 'required|same:password'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Validation Error.', $errors);
        }
        $input = $request->all();
        $input['user_id'] = Uuid::uuid4();
        $input['activation_token'] = str_shuffle(substr(str_repeat(md5(mt_rand()), 2 + 60 / 32), 0, 60));
        $twilio = new Client(config('app.twilio.SID'), config('app.twilio.TOKEN'));
        try {
            $verification = $twilio->verify->v2->services(config('app.twilio.TWILIO_VERIFICATION_SID'))
                ->verificationChecks
                ->create($request->get('otp'), array('to' => '+91' . $input['mobile_number']));
            if ($verification->valid) {
//            $input['password'] = bcrypt($input['password']);
                $user = Users::create($input);
                $avatar = Avatar::create($user->name)->getImageObject()->encode('png');
                Storage::put('avatars/' . $user->user_id . '/avatar.png', (string)$avatar);
                $success['token'] = $user->createToken('MyApp')->accessToken;
                $success['name'] = $user->first_name;
                return $this->sendResponse($success, 'User register successfully.');
            } else {
                return $this->sendJsonError("Invalid or expired login OTP given");
            }
        } catch (\Exception $e) {
            if ($e instanceof TwilioException)
                return $this->sendJsonError("Invalid OTP given");
            return $this->sendJsonError($e->getMessage());
        }

    }

    public function signupActivate($token)
    {
        $user = Users::where('activation_token', $token)->first();
        if (!$user) {
            return response()->json(['message' => "This activation token is invalid"], 404);
        }
        $user->is_active = true;
        $user->activation_token = '';
        $user->email_verified_at = now()->timestamp;
        $user->save();
        return $user;
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function sendOrResendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required|int|min:6'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Invalid mobile number.', $errors);
        }
        $input = $request->all();
        try {
            $status = $this->sendOtp($input['mobile_number']);
            return $this->sendResponse($status, "OTP sent successfully");
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * @param $phoneNumber
     * @return Response
     * @throws \Exception
     */
    private function sendOtp($phoneNumber)
    {
        try {
            return $this->dispatch(new OTPSend($phoneNumber));
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function resendOtp(Request $request)
    {
        $user = $request->user();
        try {
            $status = $this->sendOtp($user->getPhoneNumber());
            return $this->sendResponse($status, "OTP sent successfully");
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function verifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Validation Error.', $errors);
        }
        $user = $request->user();
        try {
            $twilio = new Client(config('app.twilio.SID'), config('app.twilio.TOKEN'));
            $verification = $twilio->verify->v2->services(config('app.twilio.TWILIO_VERIFICATION_SID'))
                ->verificationChecks
                ->create($request->get('otp'), array('to' => '+91' . $user->getPhoneNumber()));
            if ($verification->valid) {
                $user->is_active = true;
                $user->otp_verified_at = now()->toDateTimeString();
                $user->save();
                return $this->sendResponse([], "Phone number verified successfully");
            } else {
                return $this->sendJsonError("Invalid OTP");
            }
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), [], 500);

        }
    }

    public function logout(Request $request)
    {
        $accessToken = Auth::user()->token();
        $token = $request->user()->tokens->find($accessToken);
        $token->revoke();;
        return $this->sendResponse([], "You are logged out");
    }

    /**
     * @param Request $request
     * @return Response|mixed
     */
    public function storeFcmToken(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'token' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Validation Error.', $errors);
        }
        try {
            UsersFCMTokens::create([
                'user_id' => $request->user()->user_id,
                "token" => $input['token']
            ]);
            return $this->sendResponse(null, "Tokens saved successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function getUserDetails(Request $request)
    {
        $user = $request->user();
        try {
            if ($user->avatar === "avatar.png") {
                $user->avatar = asset("storage/avatars/" . $user->user_id . "/avatar.png");
            }
            return $this->sendResponse($user, "User details fetched successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * updating user details
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function updateUserDetails(Request $request)
    {
        $user = $request->user();
        $input = $request->all();
        try {
            $validator = Validator::make($input, [
                'name' => 'required',
                'parent_number' => 'nullable|unique:users,parent_number',
                'country' => 'nullable',
                'nationality' => 'required',
                'state' => 'required',
                'place' => 'required',
                'school' => 'nullable',
                'school_place' => 'nullable',
                'city' => 'nullable',
                'pin_code' => 'required|int',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return $this->sendJsonError('Validation Error.', $errors, CoreException::VALIDATION_ERRORS);
            }
            $availableUser = Users::where('email', "=", $input['email'])->where("user_id", "!=", $user->user_id)->first();
            if ($availableUser) {
                return $this->sendJsonError("Validation Error", [
                    "email" => "This email has already been taken"
                ], CoreException::VALIDATION_ERRORS);
            }
            $user->name = $input['name'];
            if (isset($input['parent_number']) && !empty($input['parent_number']))
                $user->parent_number = $input['parent_number'];
            $user->nationality = $input['nationality'];
            $user->state = $input['state'];
            $user->place = $input['place'];
            $user->school = $input['school'];
            $user->school_place = $input['school_place'];
            $user->city = $input['city'];
            $user->pin_code = $input['pin_code'];
            $user->email = $input['email'];
            $user->save();
            if (isset($input['profile_picture']) && !empty($input['profile_picture'])) {
                $this->dispatch(new S3Upload($input['profile_picture'], $user->user_id));
            }
            return $this->sendResponse($user, "User details updated successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * updating profile picture
     *
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function updateProfilePicture(Request $request)
    {
        $user = $request->user();
        $input = $request->all();
        $validator = Validator::make($input, [
            'profile_picture' => 'required',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Validation Error.', $errors, CoreException::VALIDATION_ERRORS);
        }
        try {
            $this->dispatch(new S3Upload($input['profile_picture'], $user->user_id));
            return $this->sendResponse(null, "Your profile picture will update soon");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }
}
