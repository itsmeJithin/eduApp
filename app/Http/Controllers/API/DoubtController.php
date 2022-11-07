<?php


namespace App\Http\Controllers\API;

use App\Models\TopicDoubts;
use Illuminate\Http\Request;

/**
 *
 * @Date 01/07/21
 */
class DoubtController extends BaseController
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function getAllOpenDoubts(Request $request)
    {
        try {
            $userId = $request->user()->user_id;
            $doubts = \DB::table("topic_doubts as td")
                ->select("td.*", "t.topic_id", "t.topic_name", "c.chapter_name", "c.chapter_id",
                    "s.subject_id", "s.subject_name", "cgss.class_group_syllabus_subject_id",
                    "smt.syllabus_subscription_month_id", "cgss.class_group_syllabus_id")
                ->join("topics as t", "t.topic_id", "=", "td.topic_id")
                ->join("ss_month_topics as smt", "smt.topic_id", "=", "t.topic_id")
                ->join("chapters as c", "c.chapter_id", "=", "t.chapter_id")
                ->join("class_group_syllabus_subjects as cgss", "cgss.class_group_syllabus_subject_id", "=", "c.class_group_syllabus_subject_id")
                ->join("subjects as s", "s.subject_id", "=", "cgss.subject_id")
                ->leftJoin("topic_doubt_answers as tda", "td.topic_doubt_id", "=", "tda.topic_doubt_id")
                ->where("td.created_by", "=", $userId)
                ->whereNull("tda.topic_doubt_answer_id")
                ->get();

            return $this->sendResponse($doubts, "doubts fetched successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

    public function getAllClosedDoubts(Request $request)
    {
        try {
            $userId = $request->user()->user_id;
            $doubts = TopicDoubts::with("doubtAnswers")->whereHas("doubtAnswers")
                ->where("topic_doubts.created_by", "=", $userId)
                ->get();
            foreach ($doubts as $doubt) {
                $topic = \DB::table("topics as t")
                    ->select("t.topic_id", "t.topic_name", "c.chapter_name", "c.chapter_id",
                        "s.subject_id", "s.subject_name", "cgss.class_group_syllabus_subject_id",
                        "smt.syllabus_subscription_month_id", "cgss.class_group_syllabus_id")
                    ->join("ss_month_topics as smt", "smt.topic_id", "=", "t.topic_id")
                    ->join("chapters as c", "c.chapter_id", "=", "t.chapter_id")
                    ->join("class_group_syllabus_subjects as cgss", "cgss.class_group_syllabus_subject_id", "=", "c.class_group_syllabus_subject_id")
                    ->join("subjects as s", "s.subject_id", "=", "cgss.subject_id")
                    ->where("t.topic_id", "=", $doubt->topic_id)
                    ->first();
                if ($topic) {
                    foreach (get_object_vars($topic) as $key => $value) {
                        $doubt->$key = $value;
                    }
                }
            }

            return $this->sendResponse($doubts, "doubts fetched successfully");
        } catch (\Exception $e) {
            return $this->sendJsonError($e->getMessage());
        }
    }

}
