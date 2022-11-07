<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;

/**
 *
 * @Date 26/06/21
 */
class StudentExamRegistrations extends Model
{
    use SoftDeletes, HasApiTokens;

    /**
     * @var string
     */
    protected $table = "student_exam_registrations";

    /**
     * @var string
     */
    protected $primaryKey = "student_exam_registration_id";

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var string[]
     */
    protected $fillable = [
        "student_exam_registration_id",
        "user_id",
        "cgs_subject_exam_id",
        "status",
        "is_completed",
        "created_at",
        "updated_at",
        "exam_completed_at",
        "exam_started_at"
    ];

    /**
     * @return HasOne
     */
    public function user()
    {
        return $this->hasOne(Users::class, "user_id", "user_id");
    }

    /**
     * returns exam answers
     * @return HasOne
     */
    public function questionAnswers()
    {
        return $this->hasOne(StudentERAnswers::class, "student_exam_registration_id", 'student_exam_registration_id');
    }

    /**
     * @return HasOne
     */
    public function exam()
    {
        return $this->hasOne(CgsSubjectExams::class, "cgs_subject_exam_id", "cgs_subject_exam_id");
    }

    public function examMark()
    {
        return $this->hasOne(StudentERAnswers::class,
            "student_exam_registration_id",
            'student_exam_registration_id')
            ->select(["student_exam_registration_id", "marks_obtained"]);
    }

}
