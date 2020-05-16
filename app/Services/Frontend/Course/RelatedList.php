<?php

namespace App\Services\Frontend\Course;

use App\Caches\CourseRelatedList as CourseRelatedListCache;
use App\Services\Frontend\CourseTrait;
use App\Services\Frontend\Service as FrontendService;

class RelatedList extends FrontendService
{

    use CourseTrait;

    public function handle($id)
    {
        $course = $this->checkCourse($id);

        $cache = new CourseRelatedListCache();

        $result = $cache->get($course->id);

        return $result ?: [];
    }

}
