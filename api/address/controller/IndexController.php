<?php
declare (strict_types = 1);

namespace api\address\controller;

use cmf\controller\RestBaseController;

/**
 * @OA\Tag(
 *     name="address",
 *     description=""
 * )
 */
class IndexController extends RestBaseController
{
    /**
     * index
     * @OA\Get(
     *     tags={"address"},
     *     path="/address/index",
     *     @OA\Response(response=200,ref="#/components/responses/200")
     * )
     */
    public function index()
    {
        $this->success('请求成功!', "address api");
    }

    /**
     * version
     * @OA\Get(
     *     tags={"address"},
     *     path="/address/version",
     *     @OA\Response(response=200,ref="#/components/responses/200")
     * )
     */
    public function version()
    {
        $content = file_get_contents(APP_PATH.'address'.'/version');
        $this->success('请求成功!', $content);
    }

}
