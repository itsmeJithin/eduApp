<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

/**
 *
 * @Date 07/08/20
 */
class Chapters extends Model
{
    use HasApiTokens;

    protected $primaryKey = 'chapter_id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    protected $fillable = [
        'chapter_id',
        'chapter_name',
        'chapter_code',
        'class_group_syllabus_subject_id',
        'chapter_description',
        'is_active',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];
}
