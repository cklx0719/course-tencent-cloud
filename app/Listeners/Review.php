<?php

namespace App\Listeners;

use App\Models\Review as ReviewModel;
use Phalcon\Events\Event as PhEvent;

class Review extends Listener
{

    public function afterCreate(PhEvent $event, $source, ReviewModel $review)
    {

    }

    public function afterUpdate(PhEvent $event, $source, ReviewModel $review)
    {

    }

    public function afterDelete(PhEvent $event, $source, ReviewModel $review)
    {

    }

    public function afterRestore(PhEvent $event, $source, ReviewModel $review)
    {

    }

    public function afterApprove(PhEvent $event, $source, ReviewModel $review)
    {

    }

    public function afterReject(PhEvent $event, $source, ReviewModel $review)
    {

    }

    public function afterLike(PhEvent $event, $source, ReviewModel $review)
    {

    }

    public function afterUndoLike(PhEvent $event, $source, ReviewModel $review)
    {

    }

}