<?php
declare (strict_types = 1);

namespace api\invoice\controller;

use cmf\controller\RestBaseController;

/**
 * @OA\Tag(
 *     name="invoice",
 *     description=""
 * )
 */
class IndexController extends RestBaseController
{
    /**
     * index
     * @OA\Get(
     *     tags={"invoice"},
     *     path="/invoice/index",
     *     @OA\Response(response=200,ref="#/components/responses/200")
     * )
     */
    public function index()
    {
        $this->success('请求成功!', "invoice api");
    }

    /**
     * version
     * @OA\Get(
     *     tags={"invoice"},
     *     path="/invoice/version",
     *     @OA\Response(response=200,ref="#/components/responses/200")
     * )
     */
    public function version()
    {
        $content = file_get_contents(APP_PATH.'invoice'.'/version');
        $this->success('请求成功!', $content);
    }

}
