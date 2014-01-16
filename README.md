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

$curl->request_headers = $headers;

$curl->send_request();

$response = $curl->response();

// get the response headers/body
$response_headers = $response['headers'];
$response_body = $response['body'];

// or if you just need the body...
echo $curl;
```
