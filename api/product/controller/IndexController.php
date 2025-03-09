<?php
declare (strict_types = 1);

namespace api\product\controller;

use cmf\controller\RestBaseController;

/**
 * @OA\Tag(
 *     name="product",
 *     description=""
 * )
 */
class IndexController extends RestBaseController
{
    /**
     * index
     * @OA\Get(
     *     tags={"product"},
     *     path="/product/index",
     *     @OA\Response(response=200,ref="#/components/responses/200")
     * )
     */
    public function index()
    {
        $this->success('请求成功!', "product api");
    }

    /**
     * version
     * @OA\Get(
     *     tags={"product"},
     *     path="/product/version",
     *     @OA\Response(response=200,ref="#/components/responses/200")
     * )
     */
    public function version()
    {
        $content = file_get_contents(APP_PATH.'product'.'/version');
        $this->success('请求成功!', $content);
    }

}
