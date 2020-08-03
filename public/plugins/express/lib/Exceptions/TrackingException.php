<?php

namespace plugins\express\lib\Exceptions;

/**
 * 当接口响应返回失败时抛出。
 */
class TrackingException extends \Exception
{
    /**
     * 响应数据
     *
     * @var object|string|null
     */
    protected $response;

    /**
     * 创建异常
     *
     * @param string $message
     * @param object|string|null $response
     */
    public function __construct($message, $response = null)
    {
        parent::__construct($message);
        $this->response = $response;
    }

    /**
     * 获取响应数据
     *
     * @return object|string|null
     */
    public function getResponse()
    {
        return $this->response;
    }
}
