<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('register', "API\UserController@register");
Route::post('login', "API\UserController@login");
Route::post('send-resend-otp', "API\UserController@sendOrResendOtp");
Route::get('credentials', "API\CredentialsController@getClientDetails");
Route::get('parent-credentials', "API\CredentialsController@getParentCredentials");
Route::group(['prefix' => 'auth'], function () {
    Route::get('signup/activate/{token}', 'API\UserController@signupActivate');
    Route::post('create', 'API\PasswordResetController@create');
    Route::get('find/{token}', 'API\PasswordResetController@find');
    Route::post('reset', 'API\PasswordResetController@reset');
});

Route::middleware('auth:api')->group(function () {
    Route::get('classes', 'API\ClassController@getAllClasses');
    Route::post('class-groups', 'API\ClassGroupController@getAllClassGroups');
    Route::post('syllabuses', 'API\SyllabusController@getAllClassGroupSyllabuses');
    Route::post('subjects', 'API\SubjectController@getAllSubjects');
    Route::post('chapters', 'API\ChapterController@getAllChapters');
    Route::get('subscription-months', 'API\SubscriptionMonthController@getAllSubscriptionMonths');
    Route::post('subscription-months', 'API\SubscriptionMonthController@getAllSubscribedTopics');

    Route::post('topics', 'API\TopicController@getAllTopics');
    Route::post('study-materials', 'API\TopicController@getAllStudyMaterialsByTopicId');
    Route::post('ask-doubts', 'API\TopicController@askDoubts');
    Route::post('get-all-topic-doubts', 'API\TopicController@getAllTopicDoubts');
    Route::get('get-all-open-doubts', 'API\DoubtController@getAllOpenDoubts');
    Route::get('get-all-closed-doubts', 'API\DoubtController@getAllClosedDoubts');
    Route::post('mark-topic-as-favourite', 'API\TopicController@markTopicAsFavourite');
    Route::post('unmark-topic-from-favourite', 'API\TopicController@unmarkTopicAFromFavourite');
    Route::get('all-favourite-topics', 'API\TopicController@getAllFavouriteTopics');

    Route::post('check-user-subscribed', "API\UserSubscriptionController@checkUserIsSubscribed");
    Route::post('live-classes', "API\LiveClassController@getAllLiveClasses");

    Route::post('add-watch-list', "API\TopicController@addOrUpdateTopicWatchList");
    Route::get('get-all-watch-list', "API\TopicController@getAllWatchList");

    Route::get('get-addons-study-materials', "API\AddonsController@getAllAddonsStudyMaterials");
    Route::get('get-addons-video-materials', "API\AddonsController@getAllAddonsVideoMaterials");


    Route::post('logout', 'API\UserController@logout');
    Route::get('resend-otp', 'API\UserController@resendOtp');
    Route::post('verify-otp', 'API\UserController@verifyOTP');
    Route::post('exam-modes', 'API\ExamController@getAllExamsModes');

    Route::get("get-latest-news", 'API\LatestNewsController@getAllLatestNews');

    Route::post("is-user-completed-topics", 'API\ExamController@isUserCompletedGivenChapter');
    Route::post("get-chapter-exam-modes", 'API\ExamController@getChapterExamModes');
    Route::post("get-chapter-exams", 'API\ExamController@getChapterExamByExamMode');
    Route::post("register-for-exam", 'API\ExamController@examRegistration');
    Route::post("get-next-exam-question", 'API\ExamController@getExamQuestion');
    Route::post("mark-student-exam-answer", 'API\ExamController@markStudentExamAnswer');
    Route::post("store-fcm-token", 'API\UserController@storeFcmToken');

    Route::get("get-all-live-exams", 'API\ExamController@getAllLiveExams');
    Route::post("register-for-live-exam", 'API\ExamController@registerForLiveExams');
    Route::get("get-subject-demo-topic", 'API\TopicController@getSubjectDemoTopic');

    Route::get("get-user-details", "API\UserController@getUserDetails");
    Route::post("update-user-details", "API\UserController@updateUserDetails");
    Route::post("update-profile-picture", "API\UserController@updateProfilePicture");

    Route::post("checkout", "API\UserSubscriptionController@checkout");
    Route::post("verify-payment", "API\UserSubscriptionController@completeOrder");
    Route::get("get-course-annual-fee", "API\UserSubscriptionController@getCourseAnnualFee");

    Route::get('get-subscription-month-price', 'API\SubscriptionMonthController@getSubscriptionMonthPrice');

    Route::get("get-student-registered-exams", 'API\ExamController@getStudentRegisteredExams');
});

Route::middleware("auth:parent")->group(function () {
    Route::get("/parent", 'API\UserParentController@index');
    Route::prefix("/parent")->group(function () {
        Route::get("get-student-registered-exams", 'API\ExamController@getStudentRegisteredExams');
        Route::get("get-user-details", "API\UserController@getUserDetails");
    });
});
