<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

/**
 *
 * @Date 11/08/20
 */
class Syllabuses extends Model
{
    use HasApiTokens;

    protected $primaryKey = 'syllabus_id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    protected $fillable = [
        'syllabus_id',
        'syllabus_name',
        'start_year',
        'end_year',
        'is_active',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    public function classGroupSyllabus()
    {
        return $this->hasMany(ClassGroupSyllabuses::class, "syllabus_id");
    }

}
