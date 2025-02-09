<?php

namespace App\Models;

use App\Caches\MaxAnswerId as MaxAnswerIdCache;
use Phalcon\Mvc\Model\Behavior\SoftDelete;

class Answer extends Model
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
     * 用户编号
     *
     * @var integer
     */
    public $owner_id = 0;

    /**
     * 问题编号
     *
     * @var integer
     */
    public $question_id = 0;

    /**
     * 封面
     *
     * @var string
     */
    public $cover = '';

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
     * 匿名标识
     *
     * @var integer
     */
    public $anonymous = 0;

    /**
     * 采纳标识
     *
     * @var integer
     */
    public $accepted = 0;

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
     * 评论数
     *
     * @var integer
     */
    public $comment_count = 0;

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
        return 'kg_answer';
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
        if (empty($this->cover)) {
            $this->cover = kg_parse_first_content_image($this->content);
        }

        if (empty($this->summary)) {
            $this->summary = kg_parse_summary($this->content);
        }

        $this->create_time = time();
    }

    public function beforeUpdate()
    {
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
        $cache = new MaxAnswerIdCache();

        $cache->rebuild();
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
            'latest' => '最新',
            'popular' => '最热',
        ];
    }

}