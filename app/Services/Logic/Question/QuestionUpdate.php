<?php

namespace App\Services\Logic\Question;

use App\Models\Question as QuestionModel;
use App\Services\Logic\QuestionTrait;
use App\Services\Logic\Service as LogicService;
use App\Validators\Question as QuestionValidator;

class QuestionUpdate extends LogicService
{

    use QuestionTrait;
    use QuestionDataTrait;

    public function handle($id)
    {
        $post = $this->request->getPost();

        $question = $this->checkQuestion($id);

        $validator = new QuestionValidator();

        $user = $this->getLoginUser();

        $validator->checkOwner($user->id, $question->owner_id);

        $validator->checkIfAllowEdit($question);

        $data = $this->handlePostData($post);

        if ($question->published == QuestionModel::PUBLISH_REJECTED) {
            $data['published'] = QuestionModel::PUBLISH_PENDING;
        }

        /**
         * 当通过审核后，禁止修改部分属性
         */
        if ($question->published == QuestionModel::PUBLISH_APPROVED) {
            unset(
                $data['title'],
                $data['content'],
                $post['xm_tag_ids'],
            );
        }

        $question->update($data);

        if (isset($post['xm_tag_ids'])) {
            $this->saveTags($question, $post['xm_tag_ids']);
        }

        $this->eventsManager->fire('Question:afterUpdate', $this, $question);

        return $question;
    }

}
