<?php

namespace App\Caches;

use App\Repos\Stat as StatRepo;

class SiteTodayStat extends Cache
{

    protected $lifetime = 15 * 60;

    public function getLifetime()
    {
        return $this->lifetime;
    }

    public function getKey($id = null)
    {
        return 'site_today_stat';
    }

    public function getContent($id = null)
    {
        $statRepo = new StatRepo();

        $date = date('Y-m-d');

        $saleCount = $statRepo->countDailySales($date);
        $refundCount = $statRepo->countDailyRefunds($date);
        $saleAmount = $statRepo->sumDailySales($date);
        $refundAmount = $statRepo->sumDailyRefunds($date);
        $registerCount = $statRepo->countDailyRegisteredUsers($date);
        $pointRedeemCount = $statRepo->countDailyPointRedeems($date);

        return [
            'sale_count' => $saleCount,
            'refund_count' => $refundCount,
            'sale_amount' => $saleAmount,
            'refund_amount' => $refundAmount,
            'register_count' => $registerCount,
            'point_redeem_count' => $pointRedeemCount,
        ];
    }

}
