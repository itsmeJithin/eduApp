<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

/**
 *
 * @Date 30/05/21
 */
class LiveClasses extends Model
{
    use HasApiTokens;

    protected $table ="live_classes";

    protected $primaryKey = "live_class_id";

    public $incrementing =false;
    /**
     * @var array
     */
    protected $fillable = [
        "live_class_id",
        "class_group_syllabus_subject_id",
        'subscription_month_id',
        "live_class_description",
        "live_class_url",
        "live_class_start_on",
        "live_class_end_on",
        "created_by",
        "updated_by"
    ];

}
