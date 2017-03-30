# php-aliyun-open-api-search
开放搜索接口

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist aliyunapi/php-aliyun-open-api-search
```

or add

```
"aliyunapi/php-aliyun-open-api-search": "~1.0"
```

to the require section of your composer.json.

使用方式
------------
```
$client = new \aliyun\search\Client([
    'accessKeyId' => '123456',
    'accessSecret' => '123456'
    'appName' => 'search',
    'baseUri' => 'http://opensearch-cn-hangzhou.aliyuncs.com',
]);

//发送接口请求
$response = $client->getApps();

print_r($response);


exit;
```