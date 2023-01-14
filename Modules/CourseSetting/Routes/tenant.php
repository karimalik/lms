<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/course', 'middleware' => ['auth']], function () {
    Route::get('/allCategory', 'CourseSettingController@allCategory');
    Route::get('/getSubcat/{id}', 'CourseSettingController@getSubcat');
});

//Route For Admin

Route::group(['prefix' => 'admin/course', 'middleware' => ['auth', 'admin']], function () {


    Route::any('/change-chapter-position', 'CourseSettingController@changeChapterPosition')->name('changeChapterPosition');
    Route::any('/change-lesson-position', 'CourseSettingController@changeLessonPosition')->name('changeLessonPosition');
    Route::any('/change-lesson-chapter', 'CourseSettingController@changeLessonChapter')->name('changeLessonChapter');

    //Get Course Subcategory
    Route::get('/ajaxGetCourseSubCategory', 'CourseSettingController@ajaxGetCourseSubCategory');

    //Manage Category
    Route::get('/messages', 'CourseSettingController@toastrMessages')->name('toastrMessages');

    Route::get('/searchCategory', 'CourseSettingController@searchCategory')->name('searchCategory');
    Route::get('/searchCourse', 'CourseSettingController@searchCourse')->name('searchCourse');
    Route::post('/saveCategory', 'CourseSettingController@saveCategory')->name('saveCategory');
    Route::get('/categoryEdit/{id}', 'CourseSettingController@categoryEdit')->name('categoryEdit');

    Route::post('/updateCategory', 'CourseSettingController@updateCategory')->name('updateCategory');
    Route::get('/categoryStatus/{id}', 'CourseSettingController@categoryStatus')->name('categoryStatus');

    //Manage Subcategory

    Route::get('/editSubCategory/{id}', 'CourseSettingController@editSubCategory')->name('editSubCategory');
    Route::post('/updateSubCategory', 'CourseSettingController@updateSubCategory')->name('updateSubCategory');
    Route::post('/disableSubCategory', 'CourseSettingController@disableSubCategory')->name('disableSubCategory');

    //Course Invitation
    Route::get('/course-invitation/{id}', 'CourseInvitationController@courseInvitation')->name('course.courseInvitation')->middleware('RoutePermissionCheck:course.courseInvitation');
    Route::get('/course-statistics', 'CourseInvitationController@courseStatistics')->name('course.courseStatistics')->middleware('RoutePermissionCheck:course.courseStatistics');
    Route::get('/course-students/{course_id}', 'CourseInvitationController@enrolled_students')->name('course.enrolled_students');
    Route::get('/course-students-list/{course_id}', 'CourseInvitationController@getAllStudentData')->name('course.getAllStudentData');
    Route::get('/course-student-notify/{course_id}/{student_id}', 'CourseInvitationController@courseStudentNotify')->name('course.courseStudentNotify')->middleware('RoutePermissionCheck:course.courseStudentNotify');

    Route::get('/course-details/{id}', 'CourseSettingController@courseDetails')->name('courseDetails')->middleware('RoutePermissionCheck:course.details');
    Route::get('/course-feature/{id}/{type}', 'CourseSettingController@courseMakeAsFeature')->name('courseMakeAsFeature');
    Route::get('/course-lesson-show/{course_id}/{chapter_id}/{lesson_id}', 'CourseSettingController@CourseLessonShow')->name('CourseQuetionShow');
    Route::get('/course-question-show/{question_id}/{course_id}/{chapter_id}/{lesson_id}', 'CourseSettingController@CourseQuetionShow')->name('CourseQuetionShow');
    Route::get('/course-chapter-show/{course_id}/{chapter_id}', 'CourseSettingController@CourseChapterShow')->name('CourseChapterShow');

    Route::get('/course-question-delete/{quiz_id}/{question_id}', 'CourseSettingController@CourseQuestionDelete')->name('CourseQuestionDelete');


    Route::post('/setCourseDripContent', 'CourseSettingController@setCourseDripContent')->name('setCourseDripContent');
    // Route::get('/course-test/{id}', 'CourseSettingController@courseDetails2')->name('courseDetails2');


    //Manage course
    Route::get('/all/courses', 'CourseSettingController@getAllCourse')->name('getAllCourse')->middleware('RoutePermissionCheck:getAllCourse');

    Route::get('/new/course', 'CourseSettingController@addNewCourse')->name('addNewCourse')->middleware('RoutePermissionCheck:course.store');
//    Route::get('/edit/course/{id}', 'CourseSettingController@editCourse')->name('addNewCourse')->middleware('RoutePermissionCheck:addNewCourse');

    Route::get('/active/courses', 'CourseSettingController@getAllCourse')->name('getActiveCourse')->middleware('RoutePermissionCheck:getActiveCourse');
    Route::get('/pending/courses', 'CourseSettingController@getAllCourse')->name('getPendingCourse')->middleware('RoutePermissionCheck:getPendingCourse');

    Route::post('/saveCourse', 'CourseSettingController@saveCourse')->name('AdminSaveCourse')->middleware('RoutePermissionCheck:course.store');
    Route::get('/editCourse/{id}', 'CourseSettingController@editCourse')->name('editCourse')->middleware('RoutePermissionCheck:course.edit');
    Route::post('/updateCourse', 'CourseSettingController@AdminUpdateCourse')->name('AdminUpdateCourse')->middleware('RoutePermissionCheck:course.edit');
    Route::post('/updatecourse-certificate', 'CourseSettingController@AdminUpdateCourseCertificate')->name('AdminUpdateCourseCertificate')->middleware('RoutePermissionCheck:course.edit');
    Route::post('/unpublishCourse', 'CourseSettingController@unpublishCourse')->name('AdminUnpublishCourse');
    Route::get('/publishCourse/{id}', 'CourseSettingController@publishCourse')->name('publishCourse');
    Route::post('/courseStatus', 'CourseSettingController@courseStatus')->name('AdminCourseStatus')->middleware('RoutePermissionCheck:course.status_update');


    Route::get('/getEnroll/{id}', 'CourseSettingController@getEnroll')->name('getEnroll');
    Route::post('/rejectEnroll', 'CourseSettingController@rejectEnroll')->name('rejectEnroll');
    Route::post('/enableEnroll', 'CourseSettingController@enableEnroll')->name('enableEnroll');
    Route::post('/submitEnroll/{id}', 'CourseSettingController@submitEnroll')->name('submitEnroll');
    Route::post('/course-sort-by', 'CourseSettingController@courseSortBy')->name('courseSortBy');
    Route::get('/course-sort-by', 'CourseSettingController@getAllCourse')->name('courseSortByGet');
    Route::get('/courseSortByCat/{id}', 'CourseSettingController@courseSortByCat')->name('courseSortByCat');
    Route::get('/courseSort/{value}', 'CourseSettingController@courseSort')->name('courseSort');
    Route::get('/courseSortByInstructor/{value}', 'CourseSettingController@courseSortByInstructor')->name('courseSortByInstructor');
    Route::get('/course-delete/{id}', 'CourseSettingController@courseDelete')->name('course.delete');


    Route::get('chapter', 'ChapterController@index')->name('chapterPage');
    Route::POST('chapter', 'ChapterController@store')->name('saveChapterPage');
    Route::POST('chapter-search', 'ChapterController@chapterSearchByCourse')->name('chapterSearchByCourse');
    Route::get('chapter/{id}', 'ChapterController@chapterEdit')->name('chapterEdit');
    Route::PUT('chapter-update', 'ChapterController@chapterUpdate')->name('chapterUpdate');

    Route::get('lesson/{id}', 'LessonController@index')->name('lessonPage');
    Route::post('/addLesson', 'LessonController@addLesson')->name('addLesson');
    Route::get('/edit-lesson/{id}', 'LessonController@editLesson')->name('editLesson');
    Route::put('/updateLesson', 'LessonController@updateLesson')->name('updateLesson');
    Route::post('/deleteLesson', 'LessonController@deleteLesson')->name('deleteLesson');
    Route::post('/deleteLessonAssignment', 'LessonController@deleteLessonAssignment')->name('deleteLessonAssignment');

    Route::post('/addAssignment', 'CourseAssignmentController@AssignmentStore')->name('course_assignment_store');
    Route::get('/course-assignment-show/{course_id}/{chapter_id}/{lesson_id}', 'CourseAssignmentController@CourseAssignmentShow')->name('course_assignment_show');
    Route::post('/updateAssignment', 'CourseAssignmentController@AssignmentUpdate')->name('course_assignment_update');


    Route::post('/add-chapter', 'InstructorCourseSettingController@saveChapter')->name('saveChapter');
    Route::post('/saveFile', 'InstructorCourseSettingController@saveFile')->name('saveFile');
    Route::get('/download-file/{id}', 'InstructorCourseSettingController@download_course_file')->name('download_course_file');
    Route::get('/edit-chapter/{id}/{course}', 'InstructorCourseSettingController@editChapter')->name('editChapter');
    Route::get('/delete-chapter/{id}/{course}', 'InstructorCourseSettingController@deleteChapter')->name('deleteChapter');
    Route::put('/update-chapter', 'InstructorCourseSettingController@updateChapter')->name('updateChapter');
    Route::POST('/updateFile', 'InstructorCourseSettingController@updateFile')->name('updateFile');
    Route::get('/course_chapters/{id}', 'InstructorCourseSettingController@course_chapters')->name('course_chapters');
    Route::post('/deleteFile2', 'InstructorCourseSettingController@deleteFile')->name('deleteFile');


    Route::resource('course-level', 'CourseLevelController')->middleware('RoutePermissionCheck:course-level.index')->except('destroy');
    Route::get('course-level-delete/{id}', 'CourseLevelController@delete')->middleware('RoutePermissionCheck:course-level.destroy')->name('course-level.destroy');


    Route::get('/all/courses-data', 'CourseSettingController@getAllCourseData')->name('getAllCourseData')->middleware('RoutePermissionCheck:getAllCourse');

    Route::get('/vdocipher/video-list', 'CourseSettingController@getAllVdocipherData')->name('getAllVdocipherData');
    Route::get('/vdocipher/video/{id}', 'CourseSettingController@getSingleVdocipherData')->name('getSingleVdocipherData');
});



