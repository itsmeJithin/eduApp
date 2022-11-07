<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Passport\HasApiTokens;

/**
 *
 * @Date 07/08/20
 */
class Subjects extends Model
{
    use HasApiTokens;

    /**
     * @var string
     */
    protected $primaryKey = 'subject_id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var string
     */
    protected $table = 'subjects';

    /**
     * @var array
     */
    protected $fillable = [
        'subject_id',
        'subject_name',
        'subject_code',
        'class_group_id',
        'subject_description',
        'is_active',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    /**
     * @return BelongsToMany
     */
    public function classGroups()
    {
        return $this->belongsToMany(ClassGroups::class, 'class_group_subjects', 'class_group_id', 'subject_id');
    }

}
