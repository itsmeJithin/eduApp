<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 *
 * @Date 10/06/21
 */
class CourseAnnualFees extends Model
{
    protected $table = "course_annual_fees";

    protected $primaryKey = "course_annual_fee_id";

    protected $fillable = [
        "class_group_syllabus_id",
        "price",
        "discount",
        "created_by",
        "updated_by"
    ];

}
