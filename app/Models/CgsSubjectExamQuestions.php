<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;

/**
 *
 * @Date 20/05/21
 */
class CgsSubjectExamQuestions extends Model
{
    use  HasApiTokens;

    /**
     * @var string
     */
    protected $table = "cgs_subject_exam_questions";

    /**
     * @var string
     */
    protected $primaryKey = "cgss_exam_question_id";

    /**
     * @var array
     */
    protected $fillable = [
        "question_id",
        "cgs_subject_exam_id",
        "priority",
        "created_by",
        "created_by"
    ];


    /**
     * @return HasOne
     */
    public function question()
    {
        return $this->hasOne(QuestionPool::class, "question_id", "question_id");
    }


}
