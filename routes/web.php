<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Auth::routes();

Route::get('/register', 'Web\StaffUserController@index')->name("register");
Route::post('/save-user-details', 'Web\StaffUserController@staffUserRegistration')->name("saveUserDetails");
Route::get('/login', 'Web\StaffUserController@userLoginIndex')->name("login");
Route::post('/check-credentials', 'Web\StaffUserController@checkCredentials')->name("checkCredentials");
Route::get('/logout', 'Web\HomeController@logout')->name("logout");


Route::middleware('auth:web')->group(function () {
    Route::get('/', 'Web\HomeController@dashboard')->name('home');
    Route::get('/courses', 'Web\CourseController@index')
        ->name('course');
    Route::get('/courses/get-all-courses', 'Web\CourseController@getAllCourses')
        ->name('getAllCourses');
    Route::post('/courses/create-or-update-course', 'Web\CourseController@createOrUpdate')
        ->name("createOrUpdateCourse");
    Route::delete("/courses/delete-course", 'Web\CourseController@deleteCourse')
        ->name("deleteCourse");

    Route::prefix("/classes")->group(function () {
        Route::get('/', 'Web\ClassController@index')
            ->name('classes');
        Route::get("/get-all-classes", 'Web\ClassController@getAllClasses')->name("getAllClasses");
        Route::post('/create-or-update-class', 'Web\ClassController@createOrUpdate')
            ->name("createOrUpdateClass");
        Route::delete("/delete-class", 'Web\ClassController@deleteClass')
            ->name("deleteClass");
        Route::get("/get-class-by-course", "Web\ClassController@getClassesByCourse")
            ->name("getClassByCourse");
    });

    Route::prefix("/class-groups")->group(function () {
        Route::get('/', 'Web\ClassGroupController@index')
            ->name('classGroups');
        Route::get("/get-all-class-groups", 'Web\ClassGroupController@getAllClassGroups')->name("getAllClassGroupClasses");
        Route::post('/create-or-update-class-group', 'Web\ClassGroupController@createOrUpdate')
            ->name("createOrUpdateClassGroup");
        Route::delete("/delete-class-group", 'Web\ClassGroupController@deleteClassGroup')
            ->name("deleteClassGroup");
        Route::get("/get-all-class-groups-by-class", "Web\ClassGroupController@getAllClassGroupsByClass")
            ->name("getAllClassGroupsByClass");
    });

    Route::prefix("/syllabuses")->group(function () {
        Route::get('/', 'Web\SyllabusController@index')
            ->name('syllabuses');
        Route::get("/get-all-syllabuses", 'Web\SyllabusController@getAllSyllabuses')->name("getAllSyllabuses");
        Route::post('/create-or-update-syllabus', 'Web\SyllabusController@createOrUpdate')
            ->name("createOrUpdateSyllabuses");
        Route::delete("/delete-syllabus", 'Web\SyllabusController@deleteSyllabus')
            ->name("deleteSyllabus");
    });
    Route::prefix("/design-class-subjects")->group(function () {
        Route::get('/{classGroupSyllabusId?}/{subjectId?}/{chapterId?}', 'Web\SubjectController@index')
            ->name('designClassSubjects');
    });

    Route::prefix("/subjects")->group(function () {

        Route::get("/get-all-subjects-by-class-group-syllabus-id", 'Web\SubjectController@getAllSubjectsByClassGroupSyllabusId')->name("getAllSubjects");
        Route::get("/filter-subjects", 'Web\SubjectController@filterSubjects')->name("filterSubjects");
        Route::post('/create-or-update-subject', 'Web\SubjectController@createOrUpdate')
            ->name("createOrUpdateSubjects");
        Route::delete("/delete-subject", 'Web\SubjectController@deleteSubject')
            ->name("deleteSubject");
        Route::get("/get-all-form-listing-items", 'Web\SubjectController@getAllFormListingItems')
            ->name("getAllFormListingItems");
        Route::get("/get-all-class-groups-for-subject", 'Web\ClassGroupSyllabusController@getAllClassGroups')
            ->name("getAllClassGroups");
        Route::post("/assign-class-group-syllabus", 'Web\ClassGroupSyllabusController@assignClassGroupSyllabus')
            ->name("assignClassGroup");
        Route::delete("/delete-class-group-syllabus", 'Web\ClassGroupSyllabusController@deleteClassGroupSyllabus')
            ->name("deleteClassGroupSyllabus");
    });
    Route::prefix("/subscription-months")->group(function () {
        Route::get('/manage-subscription-month/{classGroupSyllabusId}', 'Web\SubscriptionMonthsController@index')
            ->name('manageSubscriptionMonth');
        Route::get("/get-class-subscription-months", "Web\SubscriptionMonthsController@getClassSubscriptionMonths")
            ->name("getClassSubscriptionMonths");
        Route::post("/assign-cg-ss-months", "Web\SubscriptionMonthsController@assignClassGroupSyllabusSubscriptionMonths")
            ->name("assignClassGroupSyllabusSubscriptionMonths");
        Route::get("/get-all-assigned-subscription-months", 'Web\SubscriptionMonthsController@getAssignedClassSubscriptionMonths')
            ->name("getAssignedClassSubscriptionMonths");
    });

    /**
     * Chapter routes
     */
    Route::prefix("/chapters")->group(function () {
        Route::get("/get-all-chapters", 'Web\ChapterController@getAllChapters')->name("getAllChapters");
        Route::post("/create-or-update-chapter", 'Web\ChapterController@createOrUpdate')
            ->name("createOrUpdateChapter");
        Route::delete("/delete-chapter", 'Web\ChapterController@deleteChapter')
            ->name("deleteChapter");
        Route::get("/get-all-subscription-month-chapters", 'Web\ChapterController@getAllSubscriptionMonthChapters')
            ->name("getAllSubscriptionMonthChapters");
    });

    Route::prefix("/manage-demo-topics")->group(function () {
        Route::get("/", 'Web\DemoTopicController@index')->name("manageDemoTopics");
        Route::get("/get-subject-demo-topic", 'Web\DemoTopicController@getSubjectDemoTopic')->name("getSubjectDemoTopic");
        Route::post("/create-or-update-demo-topics", 'Web\DemoTopicController@createOrUpdateDemoTopic')->name("createOrUpdateDemoTopic");
    });

    /**
     * Topic Routes
     */
    Route::prefix("/topics")->group(function () {
        Route::get("/get-all-topics", 'Web\TopicController@getAllTopics')
            ->name("getAllTopics");
        Route::post("/update-topic-order", 'Web\TopicController@updateTopicOrder')
            ->name("updateTopicOrder");
        Route::post("/create-or-update-topics", 'Web\TopicController@createOrUpdate')
            ->name("createOrUpdateTopic");
        Route::delete("/delete-topic", 'Web\TopicController@deleteTopic')
            ->name("deleteTopic");
        Route::delete("/delete-study-material", 'Web\TopicController@deleteStudyMaterial')
            ->name("deleteStudyMaterial");
        Route::get("/get-study-materials", 'Web\TopicController@getAllTopicStudyMaterials')
            ->name("getAllTopicStudyMaterials");
    });

    Route::prefix("/file-upload")->group(function () {
        Route::get("/get-conf", 'Web\HelperController@getS3Conf')
            ->name("getConf");
        Route::post("/endpoint", 'Web\S3FineUploaderController@endpoint')
            ->name("signRequest");
        Route::delete("/delete-endpoint/{fileId}", 'Web\S3FineUploaderController@deleteObject')
            ->name("deleteObject");
    });

    Route::prefix("/course-fee")->group(function () {
        Route::get("/assign-fee/{classGroupSyllabusId}", 'Web\CourseFeeManagementController@index')
            ->name("assignFee");
        Route::get('/get-assigned-month-fee', 'Web\CourseFeeManagementController@getAssignedMonthFee')
            ->name("getAssignedMonthFee");
        Route::post("/save-monthly-fee", 'Web\CourseFeeManagementController@saveMonthFee')
            ->name("saveMonthlyFee");
        Route::get("/get-annual-fee", 'Web\CourseFeeManagementController@getAnnualFee')
            ->name("getAnnualFee");
        Route::post("/save-annual-fee", 'Web\CourseFeeManagementController@saveAnnualFee')
            ->name("saveAnnualFee");
    });

    Route::prefix("/exams")->group(function () {
        Route::get("/manage-exams/{classGroupSyllabusId?}/{classGroupSyllabusSubjectId?}/{examId?}", 'Web\ExamController@index')
            ->name("exams");
        Route::get("/manage-exam-modes", 'Web\ExamController@manageExamModes')
            ->name("manageExamModes");
        Route::get("/get-all-exam-modes", 'Web\ExamController@getAllExamModes')
            ->name("getAllExamModes");
        Route::post("/create-or-update-exam-modes", 'Web\ExamController@createOrUpdateExamModes')
            ->name("createOrUpdateExamModes");
        Route::get("/manage-subject-questions/{classGroupSyllabusSubjectId?}", 'Web\QuestionPoolController@index')
            ->name("questionPool");
        Route::post("/create-or-update-exam", 'Web\ExamController@createOrUpdateExam')
            ->name("createOrUpdateExam");
        Route::get("/get-all-exams", 'Web\ExamController@getExams')
            ->name("getExams");
        Route::get("/get-all-assigned-available-questions", 'Web\ExamController@getAssignedAndAvailableExamQuestions')
            ->name("getAssignedAndAvailableExamQuestions");
        Route::post("/manage-exam-questions", 'Web\ExamController@manageExamQuestions')
            ->name("manageExamQuestions");
        Route::delete("/delete-subject-exam", 'Web\ExamController@deleteExam')
            ->name("deleteExam");
        Route::post("/toggle-exam-status", 'Web\ExamController@toggleExamStatus')
            ->name("toggleExamStatus");
        Route::get("get-all-exam-results", 'Web\ExamController@getAllExamResults')
            ->name("getExamResults");
    });
    Route::prefix("/questions")->group(function () {
        Route::get("/get-all-subject-questions", 'Web\QuestionPoolController@getAllSubjectQuestionPaper')
            ->name("getAllSubjectQuestionPaper");
        Route::post("/save-question-details", 'Web\QuestionPoolController@saveQuestionDetails')
            ->name("saveQuestionDetails");
        Route::delete("/delete-question", 'Web\QuestionPoolController@deleteQuestion')
            ->name("deleteQuestion");
    });

    Route::prefix("/users")->group(function () {
        Route::get("/", "Web\UsersController@index")->name("users");
        Route::get("/get-all-users", "Web\UsersController@getAllUsers")->name("getAllUsers");
        Route::get("/get-user-subscribed-months", "Web\UsersController@getUserSubscribedMonths")->name("getUserSubscribedMonths");
        Route::post("/add-new-user", "Web\UsersController@addNewUser")->name("addNewUser");
        Route::post("/assign-subscription-month", "Web\UsersController@addSubscriptions")->name("addSubscriptions");
        Route::get("/get-user-not-subscribed-months", "Web\UsersController@getUserNotSubscribedMonths")->name("getUserNotSubscribedMonths");
    });
    Route::prefix("/doubts")->group(function () {
        Route::get("/{classGroupSyllabusSubjectId?}", "Web\DoubtsController@viewDoubts")->name("doubts");
        Route::post("/get-all-subject-doubts", "Web\DoubtsController@getAllSubjectDoubts")->name("getAllSubjectDoubts");
        Route::post("/answer-doubts", "Web\DoubtsController@answerDoubts")->name("answerDoubts");
    });

});
