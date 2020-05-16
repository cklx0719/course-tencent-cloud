<?php

namespace App\Services\Frontend\Comment;

use App\Models\Chapter as ChapterModel;
use App\Models\Course as CourseModel;
use App\Services\Frontend\ChapterTrait;
use App\Services\Frontend\CommentTrait;
use App\Services\Frontend\CourseTrait;
use App\Services\Frontend\Service as FrontendService;
use App\Validators\Comment as CommentValidator;

class CommentDelete extends FrontendService
{

    use CommentTrait, ChapterTrait, CourseTrait;

    public function handle($id)
    {
        $comment = $this->checkComment($id);

        $chapter = $this->checkChapter($comment->chapter_id);

        $course = $this->checkCourse($comment->course_id);

        $user = $this->getLoginUser();

        $validator = new CommentValidator();

        $validator->checkOwner($user->id, $comment->user_id);

        $comment->delete();

        $this->decrChapterCommentCount($chapter);

        $this->decrCourseCommentCount($course);
    }

    protected function decrChapterCommentCount(ChapterModel $chapter)
    {
        $this->eventsManager->fire('chapterCounter:decrCommentCount', $this, $chapter);
    }

    protected function decrCourseCommentCount(CourseModel $course)
    {
        $this->eventsManager->fire('courseCounter:decrCommentCount', $this, $course);
    }

}
