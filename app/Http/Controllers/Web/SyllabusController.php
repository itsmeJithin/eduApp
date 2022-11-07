<?php


namespace App\Http\Controllers\Web;

use App\Exceptions\CoreException;
use App\Models\Courses;
use App\Models\Syllabuses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

/**
 *
 * @Date 04/06/21
 */
class SyllabusController extends StaffBaseController
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function index()
    {
        try {
            return view("pages.syllabuses.syllabuses");
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @return \Illuminate\Http\Response|mixed
     */
    public function getAllSyllabuses()
    {
        try {
            $syllabuses = Syllabuses::where("is_active", "=", "1")->get();
            return $this->sendResponse($syllabuses, "Syllabuses fetched successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    public function createOrUpdate(Request $request)
    {
        try {
            $userId = $request->user()->staff_user_id;
            $input = $request->all();
            $syllabus = null;
            if (isset($input['syllabus_id']) && !empty($input['syllabus_id'])) {
                $validator = Validator::make($request->all(), [
                    'syllabus_name' => 'required',
                    'start_year' => 'required|int',
                    'end_year' => 'required|int',
                    'syllabus_id' => new \App\Rules\UUID()
                ]);
                if ($validator->fails()) {
                    $errors = $validator->errors();
                    return $this->sendJsonError('Invalid details submitted', $errors);
                }
                $syllabus = Syllabuses::find($input['syllabus_id']);
                if (!$syllabus) {
                    return $this->sendJsonError("Syllabus not found", [], CoreException::SYLLABUS_NOT_FOUND);
                }
                $syllabus->syllabus_name = $input['syllabus_name'];
                $syllabus->start_year = $input['start_year'];
                $syllabus->end_year = $input['end_year'];
                $syllabus->updated_by = $userId;
                $syllabus->save();
            } else {
                $validator = Validator::make($request->all(), [
                    'syllabus_name' => 'required',
                    'start_year' => 'required|int',
                    'end_year' => 'required|int'
                ]);
                $input['syllabus_id'] = Uuid::uuid4();
                $input['created_by'] = $userId;
                if ($validator->fails()) {
                    $errors = $validator->errors();
                    return $this->sendJsonError('Validation Error.', $errors);
                }
                $syllabus = Syllabuses::create($input);
            }

            return $this->sendResponse($syllabus, "Syllabus created successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }

    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function deleteSyllabus(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'syllabus_id' => new \App\Rules\UUID()
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return $this->sendJsonError('Invalid request sent.', $errors);
            }
            $syllabus = Syllabuses::find($input['syllabus_id']);
            $syllabus->delete();
            return $this->sendResponse(null, "Syllabus deleted successfully");
        } catch (\Exception $e) {
            if ($e->getCode() === CoreException::INTEGRITY_CONSTRAINT_ERROR) {
                return $this->sendJsonError("You can't delete this syllabus because this syllabus contains class groups. Remove all those class groups before trying again", [], CoreException::INTEGRITY_CONSTRAINT_ERROR);
            }
            return $this->sendJsonError($e->getMessage());
        }
    }

}
