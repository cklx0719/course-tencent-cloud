<?php

namespace App\Services\Logic\Comment;

use App\Models\Comment as CommentModel;
use App\Repos\User as UserRepo;
use App\Services\Logic\CommentTrait;
use App\Services\Logic\Service as LogicService;

class CommentInfo extends LogicService
{

    use CommentTrait;

    public function handle($id)
    {
        $comment = $this->checkComment($id);

        return $this->handleComment($comment);
    }

    protected function handleComment(CommentModel $comment)
    {
        $owner = $comment->owner_id > 0 ? $this->handleOwnerInfo($comment) : new \stdClass();
        $toUser = $comment->to_user_id > 0 ? $this->handleToUserInfo($comment) : new \stdClass();

        return [
            'id' => $comment->id,
            'content' => $comment->content,
            'parent_id' => $comment->parent_id,
            'like_count' => $comment->like_count,
            'reply_count' => $comment->reply_count,
            'create_time' => $comment->create_time,
            'owner' => $owner,
            'to_user' => $toUser,
        ];
    }

    protected function handleOwnerInfo(CommentModel $comment)
    {
        $userRepo = new UserRepo();

        $user = $userRepo->findById($comment->owner_id);

        return [
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->avatar,
        ];
    }

    protected function handleToUserInfo(CommentModel $comment)
    {
        $userRepo = new UserRepo();

        $user = $userRepo->findById($comment->to_user_id);

        return [
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->avatar,
        ];
    }

}
