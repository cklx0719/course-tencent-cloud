<?php

namespace App\Services\Logic\Notice\System;

use App\Models\Notification as NotificationModel;
use App\Models\Question as QuestionModel;
use App\Models\User as UserModel;
use App\Services\Logic\Service as LogicService;

class QuestionFavorited extends LogicService
{

    public function handle(QuestionModel $question, UserModel $sender)
    {
        $notification = new NotificationModel();

        $notification->sender_id = $sender->id;
        $notification->receiver_id = $question->owner_id;
        $notification->event_id = $question->id;
        $notification->event_type = NotificationModel::TYPE_QUESTION_FAVORITED;
        $notification->event_info = [
            'question' => ['id' => $question->id, 'title' => $question->title],
        ];

        $notification->create();
    }

}
