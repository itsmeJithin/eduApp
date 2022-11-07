<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;

/**
 *
 * @Date 09/05/21
 */
class QuestionPool extends Model
{
    use HasApiTokens;

    /**
     * @var string
     */
    protected $table = "question_pool";

    /**
     * @var string
     */
    protected $primaryKey = "question_id";

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var string[]
     */
    protected $fillable = [
        "question_id",
        "question_type_id",
        "class_group_syllabus_subject_id",
        "question",
        "options",
        "mark",
        "question_time",
        "created_by",
        "updated_by"
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        "options" => "array"
    ];

}
