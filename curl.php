<?php

/**
 * Class Curl
 *
 * A simple wrapper for cURL
 */
class Curl
{

    protected $url;
    protected $mode;
    protected $requestHeaders = array();
    protected $requestBody;
    protected $responseHeaders;
    protected $responseBody;

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function setRequestMode($mode = 'post')
    {
        $this->mode = $mode;
    }

    public function setRequestHeaders($headersArray)
    {
        $this->requestHeaders[] = $headersArray;
    }

    public function setRequestBody($body)
    {
        $this->requestBody = $body;
    }

    public function sendRequest()
    {
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, false);
        curl_setopt($ch, CURLOPT_VERBOSE, true);

        if ($this->mode === 'post') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $this->requestBody);
        } else {
            curl_setopt($ch, CURLOPT_POST, false);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->requestHeaders);
        $response = curl_exec($ch);

        $pattern = '#HTTP/\d\.\d.*?$.*?\r\n\r\n#ims';
        preg_match_all($pattern, $response, $matches);
        $headers_string = array_pop($matches[0]);
        $headers = explode("\r\n", str_replace("\r\n\r\n", '', $headers_string));
        $this->responseBody = str_replace($headers_string, '', $response);

        $version_and_status = array_shift($headers);
        preg_match('#HTTP/(\d\.\d)\s(\d\d\d)\s(.*)#', $version_and_status, $matches);
        $this->responseHeaders['http_version'] = $matches[1];
        $this->responseHeaders['status_code'] = $matches[2];

        // get the rest of the headers
        foreach ($headers as $header) {
            list($key, $value) = array_map('trim', explode(':', $header));
            if (!strstr($key, 'X-')) {
                $key = strtolower(str_replace('-', '_', $key));
            }

            $this->responseHeaders[$key] = $value;
        }
    }

    public function getFullResponse()
    {
        return array(
            'headers' => $this->responseHeaders,
            'body' => $this->responseBody
        );
    }

    public function getResponseBody($format = '')
    {
        switch ($format) {
            case 'json':
                return json_decode($this->responseBody);
                break;
            case '':
            default:
                return $this->responseBody;
                break;
        }
    }

    public function getResponseHeaders()
    {
        return $this->responseHeaders;
    }

    public function __toString()
    {
        return $this->responseBody;
    }
}

