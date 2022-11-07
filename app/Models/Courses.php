<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Passport\HasApiTokens;

/**
 *
 * @Date 07/08/20
 */
class Courses extends Model
{
    use HasApiTokens;

    protected $primaryKey = 'course_id';

    protected $fillable = [
        'course_name',
        'course_code',
        'course_description',
        'is_active',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    /**
     * @return HasMany
     */
    public function classes()
    {
        return $this->hasMany(Classes::class, "course_id");
    }
}
