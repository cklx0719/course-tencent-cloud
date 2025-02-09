<?php

namespace App\Services\Logic\Question;

use App\Models\Question as QuestionModel;
use App\Models\QuestionTag as QuestionTagModel;
use App\Repos\QuestionTag as QuestionTagRepo;
use App\Repos\Tag as TagRepo;
use App\Traits\Client as ClientTrait;
use App\Validators\Question as QuestionValidator;

trait QuestionDataTrait
{

    use ClientTrait;

    protected function handlePostData($post)
    {
        $data = [];

        $data['client_type'] = $this->getClientType();
        $data['client_ip'] = $this->getClientIp();

        $validator = new QuestionValidator();

        $data['title'] = $validator->checkTitle($post['title']);
        $data['content'] = $validator->checkContent($post['content']);

        if (isset($post['closed'])) {
            $data['closed'] = $validator->checkCloseStatus($post['closed']);
        }

        if (isset($post['anonymous'])) {
            $data['anonymous'] = $validator->checkAnonymousStatus($post['anonymous']);
        }

        return $data;
    }
    
    protected function saveTags(QuestionModel $question, $tagIds)
    {
        $originTagIds = [];

        /**
         * 修改数据后，afterFetch设置的属性会失效，重新执行
         */
        $question->afterFetch();

        if ($question->tags) {
            $originTagIds = kg_array_column($question->tags, 'id');
        }

        $newTagIds = $tagIds ? explode(',', $tagIds) : [];
        $addedTagIds = array_diff($newTagIds, $originTagIds);

        if ($addedTagIds) {
            foreach ($addedTagIds as $tagId) {
                $questionTag = new QuestionTagModel();
                $questionTag->question_id = $question->id;
                $questionTag->tag_id = $tagId;
                $questionTag->create();
            }
        }

        $deletedTagIds = array_diff($originTagIds, $newTagIds);

        if ($deletedTagIds) {
            $questionTagRepo = new QuestionTagRepo();
            foreach ($deletedTagIds as $tagId) {
                $questionTag = $questionTagRepo->findQuestionTag($question->id, $tagId);
                if ($questionTag) {
                    $questionTag->delete();
                }
            }
        }

        $questionTags = [];

        if ($newTagIds) {
            $tagRepo = new TagRepo();
            $tags = $tagRepo->findByIds($newTagIds);
            if ($tags->count() > 0) {
                $questionTags = [];
                foreach ($tags as $tag) {
                    $questionTags[] = ['id' => $tag->id, 'name' => $tag->name];
                }
            }
        }

        $question->tags = $questionTags;

        $question->update();

        /**
         * 重新执行afterFetch
         */
        $question->afterFetch();
    }

}
