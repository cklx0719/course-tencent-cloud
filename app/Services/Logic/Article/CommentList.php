<?php

namespace App\Services\Logic\Article;

use App\Library\Paginator\Query as PagerQuery;
use App\Models\Comment as CommentModel;
use App\Repos\Comment as CommentRepo;
use App\Services\Logic\ArticleTrait;
use App\Services\Logic\Comment\ListTrait;
use App\Services\Logic\Service as LogicService;

class CommentList extends LogicService
{

    use ArticleTrait;
    use ListTrait;

    public function handle($id)
    {
        $article = $this->checkArticle($id);

        $pagerQuery = new PagerQuery();

        $params = $pagerQuery->getParams();

        $params['item_id'] = $article->id;
        $params['item_type'] = CommentModel::ITEM_ARTICLE;
        $params['published'] = CommentModel::PUBLISH_APPROVED;

        $sort = $pagerQuery->getSort();
        $page = $pagerQuery->getPage();
        $limit = $pagerQuery->getLimit();

        $commentRepo = new CommentRepo();

        $pager = $commentRepo->paginate($params, $sort, $page, $limit);

        return $this->handleComments($pager);
    }

}
