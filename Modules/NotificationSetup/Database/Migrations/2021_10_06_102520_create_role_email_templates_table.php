<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoleEmailTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_email_templates', function (Blueprint $table) {
            $table->id();
            $table->integer('role_id');
            $table->string('template_act');
            $table->integer('status')->default(1);
            $table->timestamps();
        });


                DB::statement("INSERT INTO `role_email_templates` (`id`, `role_id`, `template_act`, `created_at`, `updated_at`) VALUES
                        -- Admin
                        (1, 1,'OffLine_Payment', now(), now()),
                        (2, 1, 'Bank_Payment', now(), now()),
                        (3, 1, 'Course_Enroll_Payment', now(), now()),
                        (4, 1, 'Course_Publish_Successfully', now(), now()),
                        -- (5, 1, 'Course_Chapter_Added', now(), now()),
                        -- (6, 1, 'Course_Lesson_Added', now(), now()),
                        -- (7, 1, 'Course_Quiz_Added', now(), now()),
                        -- (8, 1, 'Course_ExerciseFile_Added', now(), now()),
                        (9, 1, 'Course_Unpublished', now(), now()),
                        (10, 1, 'Enroll_notify_Instructor', now(), now()),
                        (11, 1, 'Course_comment', now(), now()),
                        (12, 1, 'Course_comment_Reply', now(), now()),
                        (13, 1, 'Course_Review', now(), now()),
                        -- (14, 1, 'PASS_UPDATE', now(), now()),
                        -- (15, 1, 'Email_Verification', now(), now()),
                        -- (16, 1, 'Enroll_Rejected', now(), now()),
                        -- (17, 1, 'Enroll_Enabled', now(), now()),
                        (18, 1, 'Reset_Password', now(), now()),
                        -- (19, 1, 'Offline_Enrolled', now(), now()),
                        (20, 1, 'Course_Assignment_Added', now(), now()),
                        (21, 1, 'Student_Submit_Assignment', now(), now()),
                        -- (22, 1, 'Teacher_Marked_Assignment', now(), now()),
                        (23, 1, 'Student_Submit_Homework', now(), now()),
                        -- (24, 1, 'Teacher_Marked_Homework', now(), now()),
                        -- (25, 1, 'Student_Certificate_Generate', now(), now()),
                        -- (26, 1, 'Communicate_Email', now(), now()),

                        -- Instructor
                        (27, 2,'OffLine_Payment', now(), now()),
                        (28, 2, 'Bank_Payment', now(), now()),
                        (29, 2, 'Course_Enroll_Payment', now(), now()),
                        (30, 2, 'Course_Publish_Successfully', now(), now()),
                        -- (5, 2, 'Course_Chapter_Added', now(), now()),
                        -- (6, 2, 'Course_Lesson_Added', now(), now()),
                        -- (7, 2, 'Course_Quiz_Added', now(), now()),
                        -- (8, 2, 'Course_ExerciseFile_Added', now(), now()),
                        (35, 2, 'Course_Unpublished', now(), now()),
                        (36, 2, 'Enroll_notify_Instructor', now(), now()),
                        (37, 2, 'Course_comment', now(), now()),
                        (38, 2, 'Course_comment_Reply', now(), now()),
                        (39, 2, 'Course_Review', now(), now()),
                        -- (14, 2, 'PASS_UPDATE', now(), now()),
                        (41, 2, 'Email_Verification', now(), now()),
                        (42, 2, 'Enroll_Rejected', now(), now()),
                        (43, 2, 'Enroll_Enabled', now(), now()),
                        (44, 2, 'Reset_Password', now(), now()),
                        (45, 2, 'Offline_Enrolled', now(), now()),
                        (46, 2, 'Course_Assignment_Added', now(), now()),
                        (47, 2, 'Student_Submit_Assignment', now(), now()),
                        -- (22, 2, 'Teacher_Marked_Assignment', now(), now()),
                        (49, 2, 'Student_Submit_Homework', now(), now()),
                        -- (24, 2, 'Teacher_Marked_Homework', now(), now()),
                        -- (25, 2, 'Student_Certificate_Generate', now(), now()),
                        (52, 2, 'Communicate_Email', now(), now()),

                        -- Student
                        (53, 3,'OffLine_Payment', now(), now()),
                        (54, 3, 'Bank_Payment', now(), now()),
                        (55, 3, 'Course_Enroll_Payment', now(), now()),
                        (56, 3, 'Course_Publish_Successfully', now(), now()),
                        (57, 3, 'Course_Chapter_Added', now(), now()),
                        (58, 3, 'Course_Lesson_Added', now(), now()),
                        (59, 3, 'Course_Quiz_Added', now(), now()),
                        (60, 3, 'Course_ExerciseFile_Added', now(), now()),
                        -- (9, 3, 'Course_Unpublished', now(), now()),
                        -- (10, 3, 'Enroll_notify_Instructor', now(), now()),
                        -- (13, 3, 'Course_comment', now(), now()),
                        (64, 3, 'Course_comment_Reply', now(), now()),
                        -- (13, 3, 'Course_Review', now(), now()),
                        -- (14, 3, 'PASS_UPDATE', now(), now()),
                        (67, 3, 'Email_Verification', now(), now()),
                        (68, 3, 'Enroll_Rejected', now(), now()),
                        (69, 3, 'Enroll_Enabled', now(), now()),
                        (70, 3, 'Reset_Password', now(), now()),
                        (71, 3, 'Offline_Enrolled', now(), now()),
                        (72, 3, 'Course_Assignment_Added', now(), now()),
                        -- (23, 3, 'Student_Submit_Assignment', now(), now()),
                        (73, 3, 'Teacher_Marked_Assignment', now(), now()),
                        -- (23, 3, 'Student_Submit_Homework', now(), now()),
                        (75, 3, 'Teacher_Marked_Homework', now(), now()),
                        (76, 3, 'Student_Certificate_Generate', now(), now()),
                        (77, 3, 'Communicate_Email', now(), now()),

                        -- Staff
                        (78, 4,'OffLine_Payment', now(), now()),
                        (79, 4, 'Bank_Payment', now(), now()),
                        (80, 4, 'Course_Enroll_Payment', now(), now()),
                        (81, 4, 'Course_Publish_Successfully', now(), now()),
                        -- (5, 4, 'Course_Chapter_Added', now(), now()),
                        -- (6, 4, 'Course_Lesson_Added', now(), now()),
                        -- (7, 4, 'Course_Quiz_Added', now(), now()),
                        -- (8, 4, 'Course_ExerciseFile_Added', now(), now()),
                        (82, 4, 'Course_Unpublished', now(), now()),
                        (83, 4, 'Enroll_notify_Instructor', now(), now()),
                        (84, 4, 'Course_comment', now(), now()),
                        (85, 4, 'Course_comment_Reply', now(), now()),
                        (86, 4, 'Course_Review', now(), now()),
                        -- (14, 4, 'PASS_UPDATE', now(), now()),
                        -- (15, 4, 'Email_Verification', now(), now()),
                        -- (16, 4, 'Enroll_Rejected', now(), now()),
                        -- (17, 4, 'Enroll_Enabled', now(), now()),
                        (87, 4, 'Reset_Password', now(), now()),
                        -- (19, 4, 'Offline_Enrolled', now(), now()),
                        (88, 4, 'Course_Assignment_Added', now(), now()),
                        (89, 4, 'Student_Submit_Assignment', now(), now()),
                        -- (22, 4, 'Teacher_Marked_Assignment', now(), now()),
                        (90, 4, 'Student_Submit_Homework', now(), now()),
                        -- (24, 4, 'Teacher_Marked_Homework', now(), now()),
                        -- (25, 4, 'Student_Certificate_Generate', now(), now()),
                        (91, 4, 'Communicate_Email', now(), now()),
                        (92, 3, 'Course_Invitation', now(), now())
                        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_email_templates');
    }
}
