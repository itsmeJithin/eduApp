<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Laravel\Passport\HasApiTokens;

/**
 *
 * @Date 11/08/20
 */
class ClassGroupSyllabusSubjects extends Model
{
    use HasApiTokens;

    /**
     * @var string
     */
    protected $table = "class_group_syllabus_subjects";

    /**
     * @var string
     */
    protected $primaryKey = 'class_group_syllabus_subject_id';

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = [
        'class_group_syllabus_id',
        'subject_id',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    /**
     * @return HasMany
     */
    public function subjects()
    {
        return $this->hasMany(Subjects::class, 'subject_id', 'subject_id');
    }

    /**
     * @return HasMany
     */
    public function classGroups()
    {
        return $this->hasMany(ClassGroups::class);
    }

    /**
     * @return HasOneThrough
     */
    public function classGroup()
    {
        return $this->hasOneThrough(ClassGroups::class,
            ClassGroupSyllabuses::class,
            "class_group_syllabus_id",
            "class_group_id",
            "class_group_syllabus_id",
            "class_group_id");
    }

    public function chapters()
    {
        return $this->hasMany(Chapters::class, "class_group_syllabus_subject_id")
            ->orderBy("created_at", "DESC");
    }

    public function subject()
    {
        return $this->hasOne(Subjects::class, "subject_id", "subject_id");
    }
}
