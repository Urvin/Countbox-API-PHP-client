<?php

/**
 * cURL wrapper class
 *
 * @author Yuriy Gorbachev, urvindt@gmail.com
 * @version 0.1
 */
class Curl
{
	/**
	 * cURL instance
	 * @var resource
	 */
	protected $fCurl;

	public function __construct()
	{
		$this->fCurl = curl_init();
		curl_setopt($this->fCurl, CURLOPT_USERAGENT,      'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; .NET CLR 1.1.4322)');
		curl_setopt($this->fCurl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->fCurl, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($this->fCurl, CURLOPT_FAILONERROR,    true);
		curl_setopt($this->fCurl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($this->fCurl, CURLOPT_AUTOREFERER,    true);
		curl_setopt($this->fCurl, CURLOPT_SSL_VERIFYPEER, false);
	}

	public function __destruct()
	{
		if($this->fCurl)
			curl_close($this->fCurl);
	}

	//------------------------------------------------------------------------------------------------------------------//

	protected function exec()
	{
		$lResult = curl_exec($this->fCurl);
		if(curl_errno($this->fCurl))
			$lResult = '';
		return $lResult;
	}

	/**
	 * Perform GET method
	 * @param string $aUrl URL
	 * @return mixed|string
	 */
	public function get($aUrl)
	{
		curl_setopt($this->fCurl, CURLOPT_URL, $aUrl);
		curl_setopt($this->fCurl, CURLOPT_HTTPGET, true);
		curl_setopt($this->fCurl, CURLOPT_POST, false);

		return $this->exec();
	}

	/**
	 * PERFORM POST method
	 * @param $aUrl URL
	 * @param array $aPostParams POST method params
	 * @return mixed|string
	 */
	public function post($aUrl, $aPostParams)
	{
		curl_setopt($this->fCurl, CURLOPT_URL, $aUrl);
		curl_setopt($this->fCurl, CURLOPT_HTTPGET, false);
		curl_setopt($this->fCurl, CURLOPT_POST, true);
		curl_setopt($this->fCurl, CURLOPT_POSTFIELDS, http_build_query($aPostParams));

		return $this->exec();
	}
}