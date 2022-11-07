<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Laravel\Passport\HasApiTokens;

/**
 *
 * @Date 12/08/20
 */
class ClassGroupSyllabuses extends Model
{
    use HasApiTokens;

    protected $primaryKey = 'class_group_syllabus_id';

    public $incrementing = false;

    protected $fillable = [
        'class_group_syllabus_id',
        'class_group_id',
        'syllabus_id',
        'is_active',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];


    /**
     * @return BelongsToMany
     */
    public function syllabuses()
    {
        return $this->belongsToMany(Syllabuses::class, 'class_group_syllabuses', 'class_group_id', 'syllabus_id');
    }

    /**
     * @return BelongsTo
     */
    public function syllabus()
    {
        return $this->belongsTo(Syllabuses::class, "syllabus_id");
    }

    /**
     * @return BelongsToMany
     */
    public function subjects()
    {
        return $this->belongsToMany(Subjects::class, 'class_group_syllabus_subjects', 'class_group_syllabus_id', 'subject_id');
    }

    /**
     * @return BelongsTo
     */
    public function classGroups()
    {
        return $this->belongsTo(ClassGroups::class, 'class_group_id', 'class_group_id');
    }

    /**
     * @return BelongsToMany
     */
    public function subscriptionMonths()
    {
        return $this->belongsToMany(SubscriptionMonths::class, "syllabus_subscription_months", "class_group_syllabus_id", "subscription_month_id")
            ->orderBy("priority");
    }

    /**
     * @return HasOne
     */
    public function annualFee()
    {
        return $this->hasOne(CourseAnnualFees::class, "class_group_syllabus_id");
    }

    /**
     * @return HasMany
     */
    public function syllabusSubscriptionMonths()
    {
        return $this->hasMany(SyllabusSubscriptionMonths::class, "class_group_syllabus_id", "class_group_syllabus_id");
    }

}
