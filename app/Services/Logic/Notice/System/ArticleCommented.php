<?php

namespace App\Services\Logic\Notice\System;

use App\Models\Article as ArticleModel;
use App\Models\Comment as CommentModel;
use App\Models\Notification as NotificationModel;
use App\Services\Logic\Service as LogicService;

class ArticleCommented extends LogicService
{

    public function handle(ArticleModel $article, CommentModel $comment)
    {
        $commentContent = kg_substr($comment->content, 0, 32);

        $notification = new NotificationModel();

        $notification->sender_id = $comment->owner_id;
        $notification->receiver_id = $article->owner_id;
        $notification->event_id = $comment->id;
        $notification->event_type = NotificationModel::TYPE_ARTICLE_COMMENTED;
        $notification->event_info = [
            'article' => ['id' => $article->id, 'title' => $article->title],
            'comment' => ['id' => $comment->id, 'content' => $commentContent],
        ];

        $notification->create();
    }

}
