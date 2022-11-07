<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

/**
 *
 * @Date 13/08/20
 */
class SyllabusSubscribedMonthDemoTopics extends Model
{
    use HasApiTokens;

    /**
     * @var string
     */
    protected $table = 'ss_month_demo_topics';
    /**
     * @var string
     */
    protected $primaryKey = 'ss_month_demo_topic_id';
    /**
     * @var array
     */
    protected $fillable = [
        'demo_topic_name',
        'demo_video_url',
        'class_group_syllabus_subject_id',
        'description',
        'is_active',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

}
