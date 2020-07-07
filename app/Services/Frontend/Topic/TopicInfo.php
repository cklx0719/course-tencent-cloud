<?php

namespace App\Services\Frontend\Topic;

use App\Models\Topic as TopicModel;
use App\Services\Frontend\Service as FrontendService;
use App\Services\Frontend\TopicTrait;

class TopicInfo extends FrontendService
{

    use TopicTrait;

    public function handle($id)
    {
        $topic = $this->checkTopicCache($id);

        return $this->handleTopic($topic);
    }

    protected function handleTopic(TopicModel $topic)
    {
        return [
            'id' => $topic->id,
            'title' => $topic->title,
            'summary' => $topic->summary,
        ];
    }

}
