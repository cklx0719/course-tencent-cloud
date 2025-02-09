<?php

namespace App\Services\Logic\Comment;

use App\Services\Logic\CommentTrait;
use App\Services\Logic\Service as LogicService;
use App\Validators\Comment as CommentValidator;

class CommentDelete extends LogicService
{

    use CommentTrait;
    use CountTrait;

    public function handle($id)
    {
        $comment = $this->checkComment($id);

        $user = $this->getLoginUser();

        $validator = new CommentValidator();

        $validator->checkOwner($user->id, $comment->owner_id);

        $comment->deleted = 1;

        $comment->update();

        if ($comment->parent_id > 0) {

            $parent = $this->checkComment($comment->parent_id);

            $this->decrCommentReplyCount($parent);
        }

        $this->decrItemCommentCount($comment);

        $this->eventsManager->fire('Comment:afterDelete', $this, $comment);
    }

}
