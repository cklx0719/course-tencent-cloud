<?php

namespace App\Console\Tasks;

use App\Models\User as UserModel;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class RevokeVipTask extends Task
{

    public function mainAction()
    {
        $users = $this->findUsers();

        if ($users->count() == 0) {
            return;
        }

        foreach ($users as $user) {
            $user->vip = 0;
            $user->update();
        }
    }

    /**
     * 查找待撤销会员
     *
     * @param int $limit
     * @return ResultsetInterface|Resultset|UserModel[]
     */
    protected function findUsers($limit = 1000)
    {
        $time = time();

        return UserModel::query()
            ->where('vip = 1')
            ->andWhere('vip_expiry_time < :time:', ['time' => $time])
            ->limit($limit)
            ->execute();
    }

}
