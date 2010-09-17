<?php

/**
 * A PHP5 class for invalidating Amazon CloudFront objects via its API.
 */

require_once 'HTTP/Request.php';  // grab with "pear install --onlyreqdeps HTTP_Request"


class CloudFront {
	
	var $serviceUrl;
	var $accessKeyId;
	var $responseBody;
	var $responseCode;
	var $parsedXml;
	var $distributionId;
	
	
	/**
	 * Constructs a CloudFront object
	 * @param $accessKeyId
	 * @param $secretKey
	 * @param $serviceUrl
	 * @param $distributionId
	 */
	function __construct($accessKeyId, $secretKey, $serviceUrl="https://cloudfront.amazonaws.com/", $distributionId){
		$this->serviceUrl     = $serviceUrl;
		$this->accessKeyId    = $accessKeyId;
		$this->secretKey      = $secretKey;
		$this->distributionId = $distributionId;
	}
	
	
	/**
	 * invalidateObject() Invalidates object with passed key on CloudFront
	 * @param $key 	{String} Key of file to be deleted
	 */   
	function invalidateObject($key){
		$key        = "/".$key;
		$httpDate   = gmdate("D, d M Y G:i:s T");		
		$requestUrl = $this->serviceUrl."2010-08-01/distribution/" . $this->distributionId . "/invalidation";
		$body       = "<InvalidationBatch><Path>".$key."</Path><CallerReference>".time()."</CallerReference></InvalidationBatch>";
		$req        = & new HTTP_Request($requestUrl);
		$req->setMethod("POST");
		$req->addHeader("Date", $httpDate);
		$req->addHeader("Authorization", $this->makeKey($httpDate));
		$req->addHeader("Content-Type", "text/xml");
		$req->setBody($body);
		$response           = $req->sendRequest();
		$this->responseBody = $req->getResponseBody();		
		// for debugging...		
		// $this->responseCode = $req->getResponseCode();
		// $this->parsedXml    = simplexml_load_string($this->responseBody);
		// $er = array();
		// array_push($er, "CloudFront: Invalidating Object: $key");
		// array_push($er, $requestUrl);
		// array_push($er, "body: $body");
		// array_push($er, "response: $response");
		// array_push($er, "response string: " . $this->responseBody);
		// array_push($er, "");
		// array_push($er, "response code: " . $this->responseCode);
		// array_push($er, "");
		// return implode("\n",$er);
		return ($this->responseCode === 201){
	}
	
	
	/**
	 * makeKey() Returns header string containing encoded authentication key
	 * @param 	$date 
	 * @return 	{String}
	 */
	function makeKey($date){
		return "AWS " . $this->accessKeyId . ":" . base64_encode($this->hmacSha1($this->secretKey, $date));
	}
	
	
	/**
	 * hmacSha1() Returns HMAC string
	 * @param 	$key
	 * @param 	$data	 
	 * @return 	{String}
	 */	
	function hmacSha1($key, $date){
		$blocksize = 64;
		$hashfunc  = 'sha1';
		if (strlen($key)>$blocksize){
			$key = pack('H*', $hashfunc($key));
		}
		$key  = str_pad($key,$blocksize,chr(0x00));
		$ipad = str_repeat(chr(0x36),$blocksize);
		$opad = str_repeat(chr(0x5c),$blocksize);
		$hmac = pack('H*', $hashfunc( ($key^$opad).pack('H*',$hashfunc(($key^$ipad).$date)) ));
		return $hmac;
	}
}
?>	
