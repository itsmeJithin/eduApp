<?php


namespace App\Http\Controllers\Web;

use App\Constants\UserType;
use App\Models\ClassGroupSyllabuses;
use App\Models\Users;
use App\Models\UserSubscribedSyllabusMonths;
use App\Rules\UUID;
use Avatar;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 *
 * @Date 30/06/21
 */
class UsersController extends StaffBaseController
{

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request)
    {
        return view("pages.users.users");
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function getAllUsers(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'start_date' => 'nullable|date|date_format:d-m-Y',
                'end_date' => ['nullable', 'date', 'after_or_equal:start_date', 'date_format:d-m-Y'],
                'mobile_number' => 'nullable|int',
                'parent_mobile_number' => 'nullable|int',
                'name' => 'nullable|string'
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return $this->sendJsonError('Validation Error.', $errors);
            }
            $users = Users::whereNotNull("name");
            if (isset($input['name'])) {
                $name = $input['name'];
                $users->where("name", "LIKE", "%$name%");
            }
            if (isset($input['mobile_number'])) {
                $number = $input['mobile_number'];
                $users->where("mobile_number", "LIKE", "%$number%");
            }
            if (isset($input['parent_mobile_number'])) {
                $number = $input['parent_mobile_number'];
                $users->where("parent_number", "LIKE", "%$number%");
            }
            if (isset($input['start_date']) && !empty($input['start_date']) && isset($input['end_date']) && !empty($input['end_date'])) {
                $startDate = $input['start_date'] . " 00:00:00";
                $startDate = Carbon::createFromFormat('d-m-Y H:i:s', $startDate)->format("Y-m-d H:i:s");
                $endDate = $input['end_date'] . " 23:59:59";
                $endDate = Carbon::createFromFormat('d-m-Y H:i:s', $endDate)->format("Y-m-d H:i:s");
                $users->where("created_at", ">=", $startDate);
                $users->where("created_at", "<=", $endDate);
            } elseif (isset($input['start_date']) && !empty($input['start_date']) && (!isset($input['end_date']) || empty($input['end_date']))) {
                $startDate = $input['start_date'] . " 00:00:00";
                $date = Carbon::createFromFormat('d-m-Y H:i:s', $startDate)->format("Y-m-d H:i:s");
                $users->where("created_at", ">=", $date);
            } elseif (isset($input['end_date']) && !empty($input['end_date']) && (!isset($input['start_date']) || empty($input['start_date']))) {
                $endDate = $input['end_date'] . " 23:59:59";
                $date = Carbon::createFromFormat('d-m-Y H:i:s', $endDate)->format("Y-m-d H:i:s");
                $users->where("created_at", "<=", $date);
            }
            $usersList = $users->orderBy("created_at", "DESC")->paginate(50);
            return $this->sendResponse($usersList, "Users list fetched successfully");
        } catch (\Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return Response|mixed
     */
    public function getUserSubscribedMonths(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'user_id' => ['required', new UUID()]
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Validation Error.', $errors);
        }
        try {
            $subscribedMonths = \DB::table("subscription_months as sm")
                ->select("sm.*", "ussm.created_at as subscribed_date")
                ->join("syllabus_subscription_months as ssm", "ssm.subscription_month_id", "=", "sm.subscription_month_id")
                ->join("user_subscribed_syllabus_months as ussm", "ussm.syllabus_subscription_month_id", "=", "ssm.syllabus_subscription_month_id")
                ->where("ussm.user_id", "=", $input['user_id'])
                ->orderBy("ssm.priority", "ASC")
                ->get();
            return $this->sendResponse($subscribedMonths, "Subscribed month fetched successfully");
        } catch (\Exception $e) {
            return $this->sendResponse($e->getMessage());
        }
    }

    public function addNewUser(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                "name" => "required|min:3",
                "mobile_number" => "required|min:10|unique:users,mobile_number",
                "parent_number" => "required|min:10|unique:users,parent_number",
                "nationality" => "required|min:3",
                "state" => "required|min:3",
                "city" => "required|min:3",
                "school" => "required|min:3",
                "place" => "required|min:3",
                "school_place" => "required|min:3",
                "pin_code" => "required|min:3",
                "email" => "email|unique:users"
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return $this->sendJsonError('Validation Error.', $errors);
            }
            $input['user_id'] = \Ramsey\Uuid\Uuid::uuid4();
            $input['is_active'] = false;
            $input['activation_token'] = str_shuffle(substr(str_repeat(md5(mt_rand()), 2 + 60 / 32), 0, 60));
            $user = Users::create($input);
            $avatar = Avatar::create($user->name)->getImageObject()->encode('png');
            return $this->sendResponse($user, "User details added successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    public function getUserNotSubscribedMonths(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'user_id' => ['required', new UUID()],
                "class_group_id" => ['required', new UUID()],
                "syllabus_id" => ['required', new UUID()]
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return $this->sendJsonError('Validation Error.', $errors);
            }
            $userId = $input['user_id'];
            $classGroupSyllabusId = $this->getClassGroupSyllabusId($input['class_group_id'], $input['syllabus_id']);
            if (!$classGroupSyllabusId) {
                return $this->sendJsonError("This class group syllabus mapping not found. You can create this class group mapping in `Design Class Group Subject` menu");
            }
            $subscribedMonths = \DB::table("subscription_months as sm")
                ->select("sm.*", "ssm.price", "ussm.user_id as is_assigned", "ssm.class_group_syllabus_id", "ssm.syllabus_subscription_month_id")
                ->join("syllabus_subscription_months as ssm", function ($join) use ($classGroupSyllabusId) {
                    $join->on("ssm.subscription_month_id", "=", "sm.subscription_month_id")
                        ->where("ssm.class_group_syllabus_id", "=", $classGroupSyllabusId);
                })
                ->leftJoin("user_subscribed_syllabus_months as ussm", function ($join) use ($userId) {
                    $join->on("ussm.syllabus_subscription_month_id", "=", "ssm.syllabus_subscription_month_id")
                        ->where("ussm.user_id", "=", $userId);
                })
                ->whereNull("ussm.us_syllabus_month_id")
                ->orderBy("ssm.priority", "ASC")
                ->get();

            return $this->sendResponse($subscribedMonths, "subscription months fetched successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param $classGroupId
     * @param $syllabusId
     * @throws \Exception
     */
    protected function getClassGroupSyllabusId($classGroupId, $syllabusId)
    {
        try {
            return ClassGroupSyllabuses::where("class_group_id", "=", $classGroupId)
                ->where("syllabus_id", "=", $syllabusId)
                ->where("is_active", "=", 1)
                ->value("class_group_syllabus_id");
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function addSubscriptions(Request $request)
    {
        $paidUserId = $request->user()->staff_user_id;
        $input = $request->all();
        $validator = Validator::make($input, [
            'user_id' => ['required', new UUID()],
            "assigned_months" => 'required|array|min:1',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Validation Error.', $errors);
        }
        $userId = $input['user_id'];
        $assignedMonths = $input['assigned_months'];
        try {
            foreach ($assignedMonths as $month) {
                UserSubscribedSyllabusMonths::create(
                    [
                        "user_id" => $userId,
                        "syllabus_subscription_month_id" => $month['syllabus_subscription_month_id'],
                        "class_group_syllabus_id" => $month['class_group_syllabus_id'],
                        "is_active" => 1,
                        "paid_amount" => $month['paid_amount'],
                        "paid_through" => "CASH",
                        "paid_on" => Carbon::now(),
                        "paid_by" => $paidUserId,
                        "paid_by_user_type" => UserType::ADMIN_USER,
                        "created_by" => $paidUserId,
                        "update_by" => $paidUserId
                    ]
                );
            }
            return $this->sendResponse(null, "User subscription months added successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }


}
