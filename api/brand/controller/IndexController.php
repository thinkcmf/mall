<?php
declare (strict_types = 1);

namespace api\brand\controller;

use app\brand\service\BrandService;
use cmf\controller\RestBaseController;

/**
 * @OA\Tag(
 *     name="brand",
 *     description="品牌应用"
 * )
 */
class IndexController extends RestBaseController
{
    /**
     * 品牌列表
     * @OA\Get(
     *     tags={"brand"},
     *     path="/brand/index",
     *     description="品牌列表",
     *     @OA\Response(
     *          response="200",
     *          description="success",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="code", type="integer", example=1),
     *              @OA\Property(property="msg", type="string", example="success"),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(property="total", type="integer", example=1),
     *                  @OA\Property(property="per_page", type="integer", example=15),
     *                  @OA\Property(property="current_page", type="integer", example=1),
     *                  @OA\Property(property="last_page", type="integer", example=1),
     *                  @OA\Property(
     *                      property="data",
     *                      type="array",
     *                      @OA\Items(
     *                          type="object",
     *                          @OA\Property(property="id", type="integer", example=11),
     *                          @OA\Property(property="create_time", type="string", format="date-time", example="2025-02-28 22:55:45"),
     *                          @OA\Property(property="update_time", type="string", format="date-time", example="2025-02-28 23:22:29"),
     *                          @OA\Property(property="delete_time", type="integer", example=0),
     *                          @OA\Property(property="status", type="integer", example=1),
     *                          @OA\Property(property="list_order", type="integer", example=10000),
     *                          @OA\Property(property="name", type="string", example="百事"),
     *                          @OA\Property(property="alias", type="string", example="百事"),
     *                          @OA\Property(property="logo", type="string", example="http://test3.to/upload/brand/20250228/087ebb7ea8a56b6620dfca6ee830e18c.png"),
     *                          @OA\Property(property="url", type="string", example="www.baishi.com"),
     *                          @OA\Property(property="keywords", type="string", example="百事"),
     *                          @OA\Property(property="description", type="string", example="百事百事百事百事")
     *                      )
     *                  ),
     *                  @OA\Property(property="has_more", type="boolean", example=false)
     *              )
     *          )
     *     )
     * )
     */
    public function index(): void
    {
        $sort = $this->request->param('sort');

        $data = (new BrandService())->page([
            'where'=>[['status','=',1]]
        ]);
        $this->success('请求成功!', $data);
    }
}
