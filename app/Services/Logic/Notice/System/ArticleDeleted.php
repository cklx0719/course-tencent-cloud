<?php

namespace App\Services\Logic\Notice\System;

use App\Models\Article as ArticleModel;
use App\Models\Notification as NotificationModel;
use App\Models\User as UserModel;
use App\Services\Logic\Service as LogicService;

class ArticleDeleted extends LogicService
{

    public function handle(ArticleModel $article, UserModel $sender, $reason = null)
    {
        $notification = new NotificationModel();

        $notification->sender_id = $sender->id;
        $notification->receiver_id = $article->owner_id;
        $notification->event_id = $article->id;
        $notification->event_type = NotificationModel::TYPE_ARTICLE_DELETED;
        $notification->event_info = [
            'article' => ['id' => $article->id, 'title' => $article->title],
            'reason' => $reason ?: '',
        ];

        $notification->create();
    }

}
