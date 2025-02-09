<?php

namespace App\Services\Logic\Answer;

use App\Services\Logic\AnswerTrait;
use App\Services\Logic\QuestionTrait;
use App\Services\Logic\Service as LogicService;
use App\Traits\Client as ClientTrait;
use App\Validators\Answer as AnswerValidator;

class AnswerUpdate extends LogicService
{

    use ClientTrait;
    use QuestionTrait;
    use AnswerTrait;

    public function handle($id)
    {
        $post = $this->request->getPost();

        $answer = $this->checkAnswer($id);

        $user = $this->getLoginUser();

        $validator = new AnswerValidator();

        $validator->checkOwner($user->id, $answer->owner_id);

        $validator->checkIfAllowEdit($answer);

        $answer->content = $validator->checkContent($post['content']);
        $answer->client_type = $this->getClientType();
        $answer->client_ip = $this->getClientIp();

        $answer->update();

        $this->eventsManager->fire('Answer:afterUpdate', $this, $answer);

        return $answer;
    }

}
