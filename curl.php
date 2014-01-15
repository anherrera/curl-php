<?php
/**
 * Class Curl
 *
 * A simple wrapper for cURL
 */
class Curl {

	protected $url;
	protected $request_headers = array();
	protected $request_body;
	protected $response_headers;
	protected $response_body;

	public function __set($key, $value) {
		$this->$key = $value;
	}

	public function __get($key) {
		return $this->$key;
	}

	public function send_request() {
		$ch = curl_init($this->url);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_NOBODY, false);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $this->request_headers);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->request_body);
		$response = curl_exec($ch);

		$pattern = '#HTTP/\d\.\d.*?$.*?\r\n\r\n#ims';
		preg_match_all($pattern, $response, $matches);
		$headers_string = array_pop($matches[0]);
		$headers = explode("\r\n", str_replace("\r\n\r\n", '', $headers_string));
		$this->response_body = str_replace($headers_string, '', $response);

		$version_and_status = array_shift($headers);
		preg_match('#HTTP/(\d\.\d)\s(\d\d\d)\s(.*)#', $version_and_status, $matches);
		$this->response_headers['http_version'] = $matches[1];
		$this->response_headers['status_code'] = $matches[2];

		// get the rest of the headers
		foreach ($headers as $header) {
			list($key, $value) = array_map('trim', explode(':', $header));
			if (!strstr($key, 'X-')) {
				$key = strtolower(str_replace('-', '_', $key));
			}

			$this->response_headers[$key] = $value;
		}
	}

	public function response() {
		return array(
			'headers' => $this->response_headers,
			'body' => $this->response_body
		);
	}

	public function __toString() {
		return $this->response_body;
	}
}

