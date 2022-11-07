<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 * @Date 26/06/21
 */
class StudentERAnswers extends Model
{
    /**
     * @var string
     */
    protected $table = "student_er_answers";

    /**
     * @var string
     */
    protected $primaryKey = "student_er_answers_id";

    /**
     * @var string[]
     */
    protected $fillable = [
        "student_exam_registration_id",
        "question_answers",
        "marks_obtained",
        "created_at",
        "updated_at"
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        "question_answers" => "array"
    ];

    /**
     * @return BelongsTo
     */
    public function examRegistration()
    {
        return $this->belongsTo(StudentExamRegistrations::class, "student_exam_registration_id");
    }

}
