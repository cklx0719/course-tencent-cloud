<?php

namespace App\Console\Tasks;

use App\Repos\Question as QuestionRepo;
use App\Services\Sync\QuestionScore as QuestionScoreSync;
use App\Services\Utils\QuestionScore as QuestionScoreService;

class SyncQuestionScoreTask extends Task
{

    public function mainAction()
    {
        $redis = $this->getRedis();

        $key = $this->getSyncKey();

        $questionIds = $redis->sRandMember($key, 1000);

        if (!$questionIds) return;

        $questionRepo = new QuestionRepo();

        $questions = $questionRepo->findByIds($questionIds);

        if ($questions->count() == 0) return;

        $service = new QuestionScoreService();

        foreach ($questions as $question) {
            $service->handle($question);
        }

        $redis->sRem($key, ...$questionIds);
    }

    protected function getSyncKey()
    {
        $sync = new QuestionScoreSync();

        return $sync->getSyncKey();
    }

}
