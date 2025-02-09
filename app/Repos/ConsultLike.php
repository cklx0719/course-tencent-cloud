<?php

namespace App\Repos;

use App\Models\ConsultLike as ConsultLikeModel;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Resultset;
use Phalcon\Mvc\Model\ResultsetInterface;

class ConsultLike extends Repository
{

    /**
     * @param int $consultId
     * @param int $userId
     * @return ConsultLikeModel|Model|bool
     */
    public function findConsultLike($consultId, $userId)
    {
        return ConsultLikeModel::findFirst([
            'conditions' => 'consult_id = :consult_id: AND user_id = :user_id:',
            'bind' => ['consult_id' => $consultId, 'user_id' => $userId],
        ]);
    }

    /**
     * @param int $userId
     * @return ResultsetInterface|Resultset|ConsultLikeModel[]
     */
    public function findByUserId($userId)
    {
        return ConsultLikeModel::query()
            ->where('user_id = :user_id:', ['user_id' => $userId])
            ->execute();
    }

}
