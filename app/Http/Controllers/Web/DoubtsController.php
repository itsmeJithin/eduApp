<?php


namespace App\Http\Controllers\Web;

use App\Models\TopicDoubtAnswers;
use App\Rules\UUID;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 *
 * @Date 02/07/21
 */
class DoubtsController extends StaffBaseController
{
    public function viewDoubts()
    {
        return view("pages.doubts.doubts");
    }

    /**
     * returns all subject doubts
     *
     * @param Request $request
     * @return \Illuminate\Http\Response|mixed
     */
    public function getAllSubjectDoubts(Request $request)
    {
        try {
            $input = $request->all();
            $validator = Validator::make($input, [
                'class_group_syllabus_subject_id' => 'required|int',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return $this->sendJsonError('Invalid request sent', $errors);
            }
            $classGroupSyllabusSubjectId = $input['class_group_syllabus_subject_id'];
            $doubts = \DB::table("topic_doubts as td")
                ->select("td.*", "t.topic_id", "t.topic_name", "c.chapter_name", "c.chapter_id",
                    "s.subject_id", "s.subject_name", "cgss.class_group_syllabus_subject_id",
                    "smt.syllabus_subscription_month_id", "cgss.class_group_syllabus_id",
                    "u.name as user_name", "u.avatar", "u.user_id", "tda.answer", "su.staff_name")
                ->join("topics as t", "t.topic_id", "=", "td.topic_id")
                ->join("ss_month_topics as smt", "smt.topic_id", "=", "t.topic_id")
                ->join("chapters as c", "c.chapter_id", "=", "t.chapter_id")
                ->join("class_group_syllabus_subjects as cgss", "cgss.class_group_syllabus_subject_id", "=", "c.class_group_syllabus_subject_id")
                ->join("subjects as s", "s.subject_id", "=", "cgss.subject_id")
                ->join("users as u", "u.user_id", "=", "td.created_by")
                ->leftJoin("topic_doubt_answers as tda", "td.topic_doubt_id", "=", "tda.topic_doubt_id")
                ->leftJoin("staff_users as su", "su.staff_user_id", "=", "tda.answered_by")
                ->where("c.class_group_syllabus_subject_id", "=", $classGroupSyllabusSubjectId)
                ->orderBy("td.created_at", "DESC")
                ->orderBy("tda.topic_doubt_answer_id")
                ->get(50);

            return $this->sendResponse($doubts, "Subject doubts fetched successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response|mixed
     */
    public function answerDoubts(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'topic_doubt_id' => [new UUID(), "required"],
            'answer' => 'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors();
            return $this->sendJsonError('Invalid request sent', $errors);
        }
        try {
            $userId = $request->user()->staff_user_id;
            $topicDoubtAnswer = TopicDoubtAnswers::create([
                'topic_doubt_id' => $input['topic_doubt_id'],
                'answer' => $input['answer'],
                'answered_by' => $userId,
                'updated_by' => $userId,
                'created_by' => $userId
            ]);
            return $this->sendResponse($topicDoubtAnswer, "Answer addedd successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

}
