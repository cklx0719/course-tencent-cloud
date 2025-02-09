<?php

namespace App\Caches;

use App\Models\Article as ArticleModel;
use App\Repos\Article as ArticleRepo;

class TaggedArticleList extends Cache
{

    protected $limit = 5;

    protected $lifetime = 1 * 86400;

    public function getLifetime()
    {
        return $this->lifetime;
    }

    public function getKey($id = null)
    {
        return "tagged_article_list:{$id}";
    }

    public function getContent($id = null)
    {
        $articleRepo = new ArticleRepo();

        $where = [
            'tag_id' => $id,
            'published' => ArticleModel::PUBLISH_APPROVED,
        ];

        $pager = $articleRepo->paginate($where);

        if ($pager->total_items == 0) return [];

        return $this->handleContent($pager->items);
    }

    /**
     * @param ArticleModel[] $articles
     * @return array
     */
    public function handleContent($articles)
    {
        $result = [];

        $count = 0;

        foreach ($articles as $article) {
            if ($count < $this->limit) {
                $result[] = [
                    'id' => $article->id,
                    'title' => $article->title,
                    'cover' => $article->cover,
                    'view_count' => $article->view_count,
                    'like_count' => $article->like_count,
                    'comment_count' => $article->comment_count,
                    'favorite_count' => $article->favorite_count,
                ];
                $count++;
            }
        }

        return $result;
    }

}
