<?php

namespace api\swagger\request;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     required={}
 * )
 */
class ListOrdersRequest
{

    /**
     * @OA\Property(
     *     type="object",
     *     example={"1":"10000","2":"1"},
     *     description="排序数据"
     * )
     */
    public $list_orders;
}
