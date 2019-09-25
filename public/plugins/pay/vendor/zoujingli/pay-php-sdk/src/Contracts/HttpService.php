<?php

// +----------------------------------------------------------------------
// | pay-php-sdk
// +----------------------------------------------------------------------
// | 版权所有 2014~2017 广州楚才信息科技有限公司 [ http://www.cuci.cc ]
// +----------------------------------------------------------------------
// | 开源协议 ( https://mit-license.org )
// +----------------------------------------------------------------------
// | github开源项目：https://github.com/zoujingli/pay-php-sdk
// +----------------------------------------------------------------------
// | 项目设计及部分源码参考于 yansongda/pay，在此特别感谢！
// +----------------------------------------------------------------------

namespace Pay\Contracts;

use Pay\Exceptions\Exception;
use Pay\Exceptions\InvalidArgumentException;

/**
 * 网络访问工具
 * Class HttpService
 * @package Pay\Contracts
 */
class HttpService
{

    /**
     * 缓存路径
     * @var null
     */
    public static $cachePath = null;

    /**
     * 以get访问模拟访问
     * @param string $url 访问URL
     * @param array $query GET数
     * @param array $options
     * @return bool|string
     */
    public static function get($url, $query = [], $options = [])
    {
        $options['query'] = $query;
        return self::request('get', $url, $options);
    }

    /**
     * 以post访问模拟访问
     * @param string $url 访问URL
     * @param array $data POST数据
     * @param array $options
     * @return bool|string
     */
    public static function post($url, $data = [], $options = [])
    {
        $options['data'] = $data;
        return self::request('post', $url, $options);
    }


    /**
     * CURL模拟网络请求
     * @param string $method 请求方法
     * @param string $url 请求方法
     * @param array $options 请求参数[headers,data,ssl_cer,ssl_key]
     * @return bool|string
     */
    protected static function request($method, $url, $options = [])
    {
        $curl = curl_init();
        // GET参数设置
        if (!empty($options['query'])) {
            $url .= stripos($url, '?') !== false ? '&' : '?' . http_build_query($options['query']);
        }
        // POST数据设置
        if (strtolower($method) === 'post') {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, self::build($options['data']));
        }
        // CURL头信息设置
        if (!empty($options['headers'])) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $options['headers']);
        }
        // 证书文件设置
        if (!empty($options['ssl_cer'])) {
            if (file_exists($options['ssl_cer'])) {
                curl_setopt($curl, CURLOPT_SSLCERTTYPE, 'PEM');
                curl_setopt($curl, CURLOPT_SSLCERT, $options['ssl_cer']);
            } else {
                throw new InvalidArgumentException("Certificate files that do not exist. --- [{$options['ssl_cer']}]");
            }
        }
        if (!empty($options['ssl_key'])) {
            if (file_exists($options['ssl_key'])) {
                curl_setopt($curl, CURLOPT_SSLKEYTYPE, 'PEM');
                curl_setopt($curl, CURLOPT_SSLKEY, $options['ssl_key']);
            } else {
                throw new InvalidArgumentException("Certificate files that do not exist. --- [{$options['ssl_key']}]");
            }
        }
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        list($content, $status) = [curl_exec($curl), curl_getinfo($curl), curl_close($curl)];
        return (intval($status["http_code"]) === 200) ? $content : false;
    }

    /**
     * POST数据过滤处理
     * @param array $data
     * @return array
     */
    private static function build($data)
    {
        if (!is_array($data)) {
            return $data;
        }
        foreach ($data as $key => $value) {
            if (is_string($value) && class_exists('CURLFile', false) && stripos($value, '@') === 0) {
                $filename = realpath(trim($value, '@'));
                if ($filename && file_exists($filename)) {
                    $data[$key] = new \CURLFile($filename);
                }
            }
        }
        return $data;
    }

    /**
     * 缓存配置与存储
     * @param string $name 缓存名称
     * @param string $value 缓存内容
     * @param int $expired 缓存时间(0表示永久缓存)
     * @throws Exception
     */
    public static function setCache($name, $value = '', $expired = 3600)
    {
        $cache_file = self::getCacheName($name);
        $content = serialize(['name' => $name, 'value' => $value, 'expired' => time() + intval($expired)]);
        if (!file_put_contents($cache_file, $content)) {
            throw new Exception('local cache error.', 500);
        }
    }

    /**
     * 获取缓存内容
     * @param string $name 缓存名称
     * @return null|mixed
     */
    public static function getCache($name)
    {
        $cache_file = self::getCacheName($name);
        if (file_exists($cache_file) && ($content = file_get_contents($cache_file))) {
            $data = unserialize($content);
            if (isset($data['expired']) && (intval($data['expired']) === 0 || intval($data['expired']) >= time())) {
                return $data['value'];
            }
            self::delCache($name);
        }
        return null;
    }

    /**
     * 移除缓存文件
     * @param string $name 缓存名称
     * @return bool
     */
    public static function delCache($name)
    {
        $cache_file = self::getCacheName($name);
        return file_exists($cache_file) ? unlink($cache_file) : true;
    }

    /**
     * 应用缓存目录
     * @param string $name
     * @return string
     */
    private static function getCacheName($name)
    {
        if (empty(self::$cachePath)) {
            self::$cachePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Cache' . DIRECTORY_SEPARATOR;
        }
        self::$cachePath = rtrim(self::$cachePath, '/\\') . DIRECTORY_SEPARATOR;
        file_exists(self::$cachePath) || mkdir(self::$cachePath, 0755, true);
        return self::$cachePath . $name;
    }
}