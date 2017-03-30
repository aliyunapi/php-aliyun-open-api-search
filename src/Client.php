<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace aliyun\search;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client as HttpClient;
use aliyun\guzzle\subscriber\Rpc;

/**
 * Class Client
 * @package aliyun\search
 */
class Client
{
    /**
     * @var string
     */
    public $accessKeyId;

    /**
     * @var string
     */
    public $accessSecret;

    /**
     * @var string 应用名称
     */
    public $appName;

    /**
     * @var string API版本
     */
    public $version = 'v2';

    /**
     * @var string 网关地址
     */
    public $baseUri;

    /**
     * @var HttpClient
     */
    private $_httpClient;

    /**
     * Client constructor.
     * @param array $config
     * @throws \Exception
     */
    public function __construct($config = [])
    {
        foreach ($config as $name => $value) {
            $this->{$name} = $value;
        }
        if (empty ($this->accessKeyId)) {
            throw new \Exception ('The "accessKeyId" property must be set.');
        }
        if (empty ($this->accessSecret)) {
            throw new \Exception ('The "accessSecret" property must be set.');
        }
        if (empty ($this->baseUri)) {
            throw new \Exception ('The "baseUri" property must be set.');
        }
        if (empty ($this->appName)) {
            throw new \Exception ('The "appName" property must be set.');
        }
    }

    /**
     * 获取Http Client
     * @return HttpClient
     */
    public function getHttpClient()
    {
        if (!is_object($this->_httpClient)) {
            $stack = HandlerStack::create();
            $middleware = new Rpc([
                'accessKeyId' => $this->accessKeyId,
                'accessSecret' => $this->accessSecret,
                'Version' => $this->version
            ]);
            $stack->push($middleware);

            $this->_httpClient = new HttpClient([
                'base_uri' => $this->baseUri,
                'handler' => $stack,
                'verify' => false,
                'http_errors' => false,
                'connect_timeout' => 3,
                'read_timeout' => 10,
                'debug' => false,
            ]);
        }
        return $this->_httpClient;
    }

    /**
     * 获取应用列表
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getApps()
    {
        return $this->getHttpClient()->get('/index');
    }

    /**
     * 查看应用信息
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function appStatus()
    {
        return $this->getHttpClient()->get('/' . $this->appName);
    }

    /**
     * 搜索
     * @param array $params
     * @return \Psr\Http\Message\ResponseInterface
     * @see https://help.aliyun.com/document_detail/29150.html
     */
    public function search(array $params)
    {
        return $this->getHttpClient()->get('/search', ['query' => $params]);
    }

    /**
     * 下拉提示
     * @param array $params
     * @return \Psr\Http\Message\ResponseInterface
     * @see https://help.aliyun.com/document_detail/29151.html
     */
    public function suggest(array $params)
    {
        return $this->getHttpClient()->get('/suggest', ['query' => $params]);
    }

    /**
     * 重建索引
     * @param array $params
     * @return \Psr\Http\Message\ResponseInterface
     * @see https://help.aliyun.com/document_detail/29152.html
     */
    public function indexRebuild(array $params)
    {
        return $this->getHttpClient()->get('/index/' . $this->appName, ['query' => array_merge(['action' => 'createtask'], $params)]);
    }

    /**
     * 获取错误日志
     * @param int $page
     * @param int $pageSize
     * @param string $sortMode
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function errorLog($page, $pageSize, $sortMode)
    {
        return $this->getHttpClient()->get('/index/error/' . $this->appName, ['query' => ['page' => $page, 'page_size' => $pageSize, 'sort_mode' => $sortMode]]);
    }

    /**
     * 推送数据
     * @param string $tableName 要上传数据的表名
     * @param array $items 规定JSON格式，如下所示
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function Push($tableName, array $items)
    {
        return $this->getHttpClient()->post('/index/doc/' . $this->appName, [
            'query' => [
                'action' => 'push',
                'table_name' => $tableName,
            ],
            'form_params' => [
                'items' => \GuzzleHttp\json_encode($items)
            ]
        ]);
    }
}