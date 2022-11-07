<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;

/**
 *
 * @Date 08/05/21
 */
class ExamModes extends Model
{
    use HasApiTokens, SoftDeletes;

    /**
     * @var string
     */
    protected $primaryKey = "exam_mode_id";

    /**
     * @var string
     */
    protected $table = "exam_modes";

    /**
     * @var array
     */
    protected $fillable = [
        "exam_mode_name",
        "exam_mode_code",
        "exam_mode_description",
        "is_active",
        "created_by",
        "updated_by"
    ];

}
