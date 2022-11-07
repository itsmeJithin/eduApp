<?php


namespace App\Http\Controllers\Web;

use App\Exceptions\CoreException;
use App\Models\SyllabusSubscribedMonthDemoTopics;
use App\Rules\YoutubeURL;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 *
 * @Date 01/07/21
 */
class DemoTopicController extends StaffBaseController
{

    public function index()
    {
        return view("pages.demoTopics.demo_topics");
    }

    public function getSubjectDemoTopic(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'class_group_syllabus_subject_id' => 'required|int',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Invalid request sent', $errors, CoreException::VALIDATION_ERRORS);
        }
        try {
            $classGroupSyllabusSubjectId = $input['class_group_syllabus_subject_id'];
            $topic = SyllabusSubscribedMonthDemoTopics::where("class_group_syllabus_subject_id", "=", $classGroupSyllabusSubjectId)
                ->where("is_active", "=", 1)
                ->first();
            return $this->sendResponse($topic, "Demo topic fetched successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function createOrUpdateDemoTopic(Request $request)
    {
        $input = $request->all();
        $topic = null;
        try {
            $userId = $request->user()->staff_user_id;
            if (isset($input['ss_month_demo_topic_id']) && !empty($input['ss_month_demo_topic_id'])) {
                $validator = Validator::make($input, [
                    "ss_month_demo_topic_id" => 'required|int',
                    "topic_name" => 'required|min:3',
                    'video_url' => ['required', new YoutubeURL()]
                ]);
                if ($validator->fails()) {
                    $errors = $validator->errors();
                    return $this->sendJsonError("Invalid data submitted", $errors);
                }
                $topic = SyllabusSubscribedMonthDemoTopics::find($input['ss_month_demo_topic_id']);
                if (empty($topic)) {
                    return $this->sendJsonError("Demo topic not available now. Refresh your window to get all updates");
                }
                $topic->demo_topic_name = $input['topic_name'];
                $topic->description = $input['topic_description'];
                $topic->demo_video_url = $input['video_url'];
                $topic->save();
            } else {
                $validator = Validator::make($input, [
                    "class_group_syllabus_subject_id" => 'required|int',
                    "topic_name" => 'required|min:3',
                    'video_url' => ['required', new YoutubeURL()]
                ]);
                if ($validator->fails()) {
                    $errors = $validator->errors();
                    return $this->sendJsonError("Invalid data submitted", $errors);
                }
                $topic = SyllabusSubscribedMonthDemoTopics::create([
                    "demo_topic_name" => $input['topic_name'],
                    "description" => $input['topic_description'],
                    "is_active" => 1,
                    "class_group_syllabus_subject_id" => $input['class_group_syllabus_subject_id'],
                    "demo_video_url" => $input['video_url'],
                    "created_by" => $userId,
                    "updated_by" => $userId
                ]);
            }

            return $this->sendResponse($topic, "Demo topic saved successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }
}
