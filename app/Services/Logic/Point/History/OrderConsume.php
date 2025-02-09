<?php

namespace App\Services\Logic\Point\History;

use App\Models\Order as OrderModel;
use App\Models\PointHistory as PointHistoryModel;
use App\Repos\PointHistory as PointHistoryRepo;
use App\Repos\User as UserRepo;
use App\Services\Logic\Point\PointHistory;

class OrderConsume extends PointHistory
{

    public function handle(OrderModel $order)
    {
        $setting = $this->getSettings('point');

        $pointEnabled = $setting['enabled'] ?? 0;

        if ($pointEnabled == 0) return;

        $ruleEnabled = $setting['consume_rule']['enabled'] ?? 0;

        if ($ruleEnabled == 0) return;

        $ruleRate = $setting['consume_rule']['rate'] ?? 0;

        if ($ruleRate <= 0) return;

        $eventId = $order->id;
        $eventType = PointHistoryModel::EVENT_ORDER_CONSUME;
        $eventPoint = $ruleRate * $order->amount;

        $historyRepo = new PointHistoryRepo();

        $history = $historyRepo->findEventHistory($eventId, $eventType);

        if ($history) return;

        $userRepo = new UserRepo();

        $user = $userRepo->findById($order->owner_id);

        $eventInfo = [
            'order' => [
                'sn' => $order->sn,
                'subject' => $order->subject,
                'amount' => $order->amount,
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
