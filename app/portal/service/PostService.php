<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2019 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\portal\service;

use app\portal\model\PortalPostModel;
use think\db\Query;

class PostService
{
    /**
     * 文章查询
     * @param $filter
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function adminArticleList($filter)
    {
        return $this->adminPostList($filter);
    }

    /**
     * 页面文章列表
     * @param $filter
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function adminPageList($filter)
    {
        return $this->adminPostList($filter, true);
    }

    /**
     * 文章查询
     * @param      $filter
     * @param bool $isPage
     * @return \think\Paginator
     * @throws \think\exception\DbException
     */
    public function adminPostList($filter, $isPage = false)
    {

        $field = 'a.*,u.user_login,u.user_nickname,u.user_email';

        $portalPostModel = new PortalPostModel();
        $articlesQuery   = $portalPostModel->alias('a');
        $articlesQuery->join('user u', 'a.user_id = u.id');

        $category = empty($filter['category']) ? 0 : intval($filter['category']);
        if (!empty($category)) {
            $articlesQuery->join('portal_category_post b', 'a.id = b.post_id');
            $field = 'a.*,b.id AS post_category_id,b.list_order,b.category_id,u.user_login,u.user_nickname,u.user_email';
        }

        $articles = $articlesQuery->field($field)
            ->where('a.create_time', '>=', 0)
            ->where('a.delete_time', 0)
            ->where(function (Query $query) use ($filter, $isPage) {

                $category = empty($filter['category']) ? 0 : intval($filter['category']);
                if (!empty($category)) {
                    $query->where('b.category_id', $category);
                }

                $startTime = empty($filter['start_time']) ? 0 : strtotime($filter['start_time']);
                $endTime   = empty($filter['end_time']) ? 0 : strtotime($filter['end_time']);
                if (!empty($startTime)) {
                    $query->where('a.published_time', '>=', $startTime);
                }
                if (!empty($endTime)) {
                    $query->where('a.published_time', '<=', $endTime);
                }

                $keyword = empty($filter['keyword']) ? '' : $filter['keyword'];
                if (!empty($keyword)) {
                    $query->where('a.post_title', 'like', "%$keyword%");
                }

                if ($isPage) {
                    $query->where('a.post_type', 2);
                } else {
                    $query->where('a.post_type', 1);
                }
            })
            ->order('update_time', 'DESC')
            ->paginate(10);

        return $articles;

    }

    /**
     * 已发布文章查询
     * @param int $postId     文章id
     * @param int $categoryId 分类id
     * @return array|string|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function publishedArticle($postId, $categoryId = 0)
    {
        $portalPostModel = new PortalPostModel();

        $wherePublishedTime = function (Query $query) {
            $query->where('post.published_time', '>', 0)
                ->where('post.published_time', '<', time());
        };

        if (empty($categoryId)) {

            $where = [
                'post.post_type'   => 1,
                'post.post_status' => 1,
                'post.delete_time' => 0,
                'post.id'          => $postId
            ];

            $article = $portalPostModel->alias('post')->field('post.*')
                ->where($where)
                ->where($wherePublishedTime)
                ->find();
        } else {
            $where = [
                'post.post_type'       => 1,
                'post.post_status'     => 1,
                'post.delete_time'     => 0,
                'relation.category_id' => $categoryId,
                'relation.post_id'     => $postId
            ];

            $article = $portalPostModel->alias('post')->field('post.*')
                ->join('portal_category_post relation', 'post.id = relation.post_id')
                ->where($where)
                ->where($wherePublishedTime)
                ->find();
        }


        return $article;
    }

    /**
     * 上一篇文章
     * @param int $postId     文章id
     * @param int $categoryId 分类id
     * @return array|string|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function publishedPrevArticle($postId, $categoryId = 0)
    {
        $portalPostModel = new PortalPostModel();

        $wherePublishedTime = function (Query $query) {
            $query->where('post.published_time', '>', 0)
                ->where('post.published_time', '<', time());
        };

        if (empty($categoryId)) {

            $where = [
                'post.post_type'   => 1,
                'post.post_status' => 1,
                'post.delete_time' => 0,
            ];

            $article = $portalPostModel
                ->alias('post')
                ->field('post.*')
                ->where($where)
                ->where('post.id', '<', $postId)
                ->where($wherePublishedTime)
                ->order('id', 'DESC')
                ->find();

        } else {
            $where = [
                'post.post_type'       => 1,
                'post.post_status'     => 1,
                'post.delete_time'     => 0,
                'relation.category_id' => $categoryId,
            ];

            $article = $portalPostModel
                ->alias('post')
                ->field('post.*')
                ->join('portal_category_post relation', 'post.id = relation.post_id')
                ->where($where)
                ->where('relation.post_id', '<', $postId)
                ->where($wherePublishedTime)
                ->order('id', 'DESC')
                ->find();
        }


        return $article;
    }

    /**
     * 下一篇文章
     * @param int $postId     文章id
     * @param int $categoryId 分类id
     * @return array|string|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function publishedNextArticle($postId, $categoryId = 0)
    {
        $portalPostModel = new PortalPostModel();

        $wherePublishedTime = function (Query $query) {
            $query->where('post.published_time', '>', 0)
                ->where('post.published_time', '<', time());
        };

        if (empty($categoryId)) {

            $where = [
                'post.post_type'   => 1,
                'post.post_status' => 1,
                'post.delete_time' => 0,
            ];

            $article = $portalPostModel->alias('post')->field('post.*')
                ->where($where)
                ->where('post.id', '>', $postId)
                ->where($wherePublishedTime)
                ->order('id', 'ASC')
                ->find();
        } else {



            $where = [
                'post.post_type'       => 1,
                'post.post_status'     => 1,
                'post.delete_time'     => 0,
                'relation.category_id' => $categoryId,

            ];

            $article = $portalPostModel->alias('post')->field('post.*')
                ->join('portal_category_post relation', 'post.id = relation.post_id')
                ->where($where)
                ->where('relation.post_id', '>', $postId)
                ->where($wherePublishedTime)
                ->order('id', 'ASC')
                ->find();
        }


        return $article;
    }

    /**
     * 页面管理查询
     * @param int $pageId 文章id
     * @return array|string|\think\Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function publishedPage($pageId)
    {

        $where = [
            'post_type'   => 2,
            'post_status' => 1,
            'delete_time' => 0,
            'id'          => $pageId
        ];

        $wherePublishedTime = function (Query $query) {
            $query->where('published_time', '>', 0)
                ->where('published_time', '<', time());
        };

        $portalPostModel = new PortalPostModel();
        $page            = $portalPostModel
            ->where($where)
            ->where($wherePublishedTime)
            ->find();

        return $page;
    }

}
