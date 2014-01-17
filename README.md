curl-php
========

PHP class that makes cURL super easy. Supports request and response headers.

```php
require 'curl.php'

$curl = new Curl();
$curl->setUrl('http://www.foo.bar');

$headers = array(
    'User-Agent: php-curl by ansjolander',
    'Pragma: no-cache'
);

$curl->setRequestHeaders($headers);

$curl->sendRequest();

$response = $curl->getFullResponse();

// get the response headers/body
$responseHeaders = $response['headers'];
$responseBody = $response['body'];

// get the body json_decoded
$jsonDecodedBody = $curl->getResponseBody('json');

// or if you just need the body raw...
echo $curl;
```
