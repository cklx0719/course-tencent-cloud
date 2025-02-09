<?php

namespace App\Services\Logic\Teacher\Console;

use App\Library\Paginator\Query as PagerQuery;
use App\Repos\TeacherLive as TeacherLiveRepo;
use App\Services\Logic\Service as LogicService;

class LiveList extends LogicService
{

    public function handle()
    {
        $user = $this->getLoginUser();

        $teacherLiveRepo = new TeacherLiveRepo();

        $pagerQuery = new PagerQuery();

        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $pager = $teacherLiveRepo->paginate($user->id, $page, $limit);

        if ($pager->total_items == 0) {
            return $pager;
        }

        $items = [];

        foreach ($pager->items as $item) {
            $items[] = [
                'course' => [
                    'id' => $item->course_id,
                    'title' => $item->course_title,
                ],
                'chapter' => [
                    'id' => $item->chapter_id,
                    'title' => $item->chapter_title,
                ],
                'start_time' => $item->live_start_time,
                'end_time' => $item->live_end_time,
            ];
        }

        $pager->items = $items;

        return $pager;
    }

}
