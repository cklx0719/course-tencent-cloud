<?php

namespace App\Services\Logic\Point\History;

use App\Models\PointHistory as PointHistoryModel;
use App\Models\PointRedeem as PointRedeemModel;
use App\Repos\PointHistory as PointHistoryRepo;
use App\Repos\User as UserRepo;
use App\Services\Logic\Point\PointHistory;

class PointRefund extends PointHistory
{

    public function handle(PointRedeemModel $redeem)
    {
        $eventId = $redeem->id;
        $eventType = PointHistoryModel::EVENT_POINT_REFUND;
        $eventPoint = $redeem->gift_point;

        $historyRepo = new PointHistoryRepo();

        $history = $historyRepo->findEventHistory($eventId, $eventType);

        if ($history) return;

        $userRepo = new UserRepo();

        $user = $userRepo->findById($redeem->user_id);

        $eventInfo = [
            'point_redeem' => [
                'id' => $redeem->id,
                'gift_id' => $redeem->gift_id,
                'gift_name' => $redeem->gift_name,
                'gift_type' => $redeem->gift_type,
                'gift_point' => $redeem->gift_point,
            ]
        ];

        $history = new PointHistoryModel();

        $history->user_id = $user->id;
        $history->user_name = $user->name;
        $history->event_id = $eventId;
        $history->event_type = $eventType;
        $history->event_point = $eventPoint;
        $history->event_info = $eventInfo;

        $this->handlePointHistory($history);
    }

}
