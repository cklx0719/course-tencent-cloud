<?php

namespace App\Models;

use App\Caches\MaxQuestionId as MaxQuestionIdCache;
use App\Services\Sync\QuestionIndex as QuestionIndexSync;
use App\Services\Sync\QuestionScore as QuestionScoreSync;
use Phalcon\Mvc\Model\Behavior\SoftDelete;

class Question extends Model
{

    /**
     * 发布状态
     */
    const PUBLISH_PENDING = 1; // 审核中
    const PUBLISH_APPROVED = 2; // 已发布
    const PUBLISH_REJECTED = 3; // 未通过

    /**
     * 自增编号
     *
     * @var integer
     */
    public $id = 0;

    /**
     * 分类编号
     *
     * @var integer
     */
    public $category_id = 0;

    /**
     * 提问者
     *
     * @var integer
     */
    public $owner_id = 0;

    /**
     * 最后回应用户
     *
     * @var integer
     */
    public $last_replier_id = 0;

    /**
     * 最后回答编号
     *
     * @var integer
     */
    public $last_answer_id = 0;

    /**
     * 采纳答案编号
     *
     * @var integer
     */
    public $accept_answer_id = 0;

    /**
     * 标题
     *
     * @var string
     */
    public $title = '';

    /**
     * 封面
     *
     * @var string
     */
    public $cover = '';

    /**
     * 标签
     *
     * @var array|string
     */
    public $tags = [];

    /**
     * 概要
     *
     * @var string
     */
    public $summary = '';

    /**
     * 内容
     *
     * @var string
     */
    public $content = '';

    /**
     * 综合得分
     *
     * @var float
     */
    public $score = 0.00;

    /**
     * 悬赏积分
     *
     * @var integer
     */
    public $bounty = 0;

    /**
     * 匿名标识
     *
     * @var integer
     */
    public $anonymous = 0;

    /**
     * 解决标识
     *
     * @var integer
     */
    public $solved = 0;

    /**
     * 关闭标识
     *
     * @var integer
     */
    public $closed = 0;

    /**
     * 状态标识
     *
     * @var integer
     */
    public $published = self::PUBLISH_PENDING;

    /**
     * 删除标识
     *
     * @var integer
     */
    public $deleted = 0;

    /**
     * 终端类型
     *
     * @var integer
     */
    public $client_type = 0;

    /**
     * 终端IP
     *
     * @var integer
     */
    public $client_ip = '';

    /**
     * 浏览数
     *
     * @var integer
     */
    public $view_count = 0;

    /**
     * 答案数
     *
     * @var integer
     */
    public $answer_count = 0;

    /**
     * 评论数
     *
     * @var integer
     */
    public $comment_count = 0;

    /**
     * 收藏数
     *
     * @var integer
     */
    public $favorite_count = 0;

    /**
     * 点赞数
     *
     * @var integer
     */
    public $like_count = 0;

    /**
     * 举报数
     *
     * @var integer
     */
    public $report_count = 0;

    /**
     * 回应时间
     *
     * @var integer
     */
    public $last_reply_time = 0;

    /**
     * 创建时间
     *
     * @var integer
     */
    public $create_time = 0;

    /**
     * 更新时间
     *
     * @var integer
     */
    public $update_time = 0;

    public function getSource(): string
    {
        return 'kg_question';
    }

    public function initialize()
    {
        parent::initialize();

        $this->addBehavior(
            new SoftDelete([
                'field' => 'deleted',
                'value' => 1,
            ])
        );
    }

    public function beforeCreate()
    {
        if (is_array($this->tags) || is_object($this->tags)) {
            $this->tags = kg_json_encode($this->tags);
        }

        if (empty($this->cover)) {
            $this->cover = kg_parse_first_content_image($this->content);
        }

        $this->create_time = time();
    }

    public function beforeUpdate()
    {
        if (time() - $this->update_time > 3 * 3600) {
            $sync = new QuestionIndexSync();
            $sync->addItem($this->id);

            $sync = new QuestionScoreSync();
            $sync->addItem($this->id);
        }

        if (is_array($this->tags) || is_object($this->tags)) {
            $this->tags = kg_json_encode($this->tags);
        }

        if (empty($this->cover)) {
            $this->cover = kg_parse_first_content_image($this->content);
        }

        if (empty($this->summary)) {
            $this->summary = kg_parse_summary($this->content);
        }

        $this->update_time = time();
    }

    public function afterCreate()
    {
        $cache = new MaxQuestionIdCache();

        $cache->rebuild();
    }

    public function afterFetch()
    {
        if (is_string($this->tags)) {
            $this->tags = json_decode($this->tags, true);
        }
    }

    public static function publishTypes()
    {
        return [
            self::PUBLISH_PENDING => '审核中',
            self::PUBLISH_APPROVED => '已发布',
            self::PUBLISH_REJECTED => '未通过',
        ];
    }

    public static function sortTypes()
    {
        return [
            'latest' => '最新提问',
            'active' => '最新回答',
            'unanswered' => '尚未回答',
        ];
    }

}