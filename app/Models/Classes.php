<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Passport\HasApiTokens;

/**
 *
 * @Date 07/08/20
 */
class Classes extends Model
{
    use HasApiTokens;

    protected $table = "classes";

    protected $primaryKey = 'class_id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    protected $fillable = [
        'class_id',
        'class_name',
        'class_code',
        'class_description',
        'course_id',
        'is_active',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    /**
     * @return BelongsTo
     */
    public function course()
    {
        return $this->belongsTo(Courses::class, 'course_id', 'course_id');
    }

    /**
     * @return HasMany
     */
    public function classGroups()
    {
        return $this->hasMany(ClassGroups::class, "class_id");
    }
}
