<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Passport\HasApiTokens;

/**
 *
 * @Date 07/08/20
 */
class ClassGroups extends Model
{
    use HasApiTokens;

    protected $primaryKey = 'class_group_id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    protected $fillable = [
        'class_group_id',
        'class_group_name',
        'class_group_code',
        'class_group_description',
        'class_id',
        'is_active',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    /**
     * @return HasOne
     */
    public function userClasses()
    {
        return $this->hasOne(Classes::class, "class_id", 'class_id');
    }


    public function classGroupCourse()
    {
        return $this->hasOneThrough(Courses::class, Classes::class, "class_id", 'class_id', 'course_id');
    }

    /**
     * @return BelongsToMany
     */
    public function subjects()
    {
        return $this->belongsToMany(Subjects::class, 'class_group_subjects', 'class_group_id', 'subject_id');
    }

    /**
     * @return BelongsToMany
     */
    public function syllabuses()
    {
        return $this->belongsToMany(Syllabuses::class, 'class_group_syllabuses', 'class_group_id', 'syllabus_id');
    }

    public function classGroupSyllabuses()
    {
        return $this->hasMany(ClassGroupSyllabuses::class, 'class_group_id');
    }


}
