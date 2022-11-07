<?php


namespace App\Models;

use App\Casts\DateUTCCast;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;

/**
 *
 * @Date 20/05/21
 */
class CgsSubjectExams extends Model
{
    use SoftDeletes, HasApiTokens;

    /**
     * @var string
     */
    protected $table = "cgs_subject_exams";

    /**
     * @var string
     */
    protected $primaryKey = "cgs_subject_exam_id";

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = [
        "cgs_subject_exam_id",
        "cgs_subject_id",
        "exam_id",
        "is_chapter_wise",
        "chapter_id",
        "is_topic_wise",
        "is_live_exam",
        "topic_id",
        "is_weekly",
        "subject_exam_name",
        "subject_exam_description",
        "maximum_marks",
        "maximum_time",
        "is_monthly",
        "is_weekly",
        "subscription_month_id",
        "exam_mode_id",
        "is_mock_test",
        "start_date",
        "end_date",
        "is_published",
        "created_at",
        "updated_at"
    ];

    protected $casts = [
        "start_date" => DateUTCCast::class,
        "end_date" => DateUTCCast::class
    ];

    /**
     * @return HasMany
     */
    public function examModes()
    {
        return $this->hasMany(ExamModes::class, "exam_mode_id", "exam_mode_id");
    }

    public function examQuestions()
    {
        return $this->hasMany(CgsSubjectExamQuestions::class, "cgs_subject_exam_id", "cgs_subject_exam_id");
    }

    /**
     * @return BelongsToMany
     */
    public function questions()
    {
        return $this->belongsToMany(QuestionPool::class, "cgs_subject_exam_questions", "cgs_subject_exam_id", "question_id")
            ->withPivot("priority")
            ->orderBy("priority", "ASC");
    }

    public function chapter()
    {
        return $this->hasOne(Chapters::class, "chapter_id", "chapter_id");
    }

    public function subscriptionMonth()
    {
        return $this->hasOne(SubscriptionMonths::class, "subscription_month_id", "subscription_month_id");
    }

    public function examMode()
    {
        return $this->hasOne(ExamModes::class, "exam_mode_id", "exam_mode_id");
    }

    public function numberOfQuestions()
    {
        return $this->belongsToMany(QuestionPool::class, "cgs_subject_exam_questions", "cgs_subject_exam_id", "question_id");
    }

    public function subject()
    {
        return $this->hasOneThrough(Subjects::class, ClassGroupSyllabusSubjects::class, 'class_group_syllabus_subject_id', 'subject_id', 'cgs_subject_id', "subject_id");
    }

    /**
     * @return mixed
     */
    public function isPublished()
    {
        return $this->is_published;
    }
}
