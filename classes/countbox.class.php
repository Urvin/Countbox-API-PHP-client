<?php

/**
 * Countbox API PHP client class
 *
 * @author Yuriy Gorbachev, urvindt@gmail.com
 * @version 0.1
 */
class Countbox
{
	/**
	 * Main API gate
	 */
	const cApiUrl = 'http://deluxe.count-box.com/api/json/';

	/**
	 * Countbox API user login
	 * @var string
	 */
	protected $fLogin;

	/**
	 * Countbox API user password
	 * @var string
	 */
	protected $fPassword;

	/**
	 * Receieved token
	 * @var string
	 */
	protected $fToken;

	/**
	 * cURL wrapper Instance
	 * @var Curl
	 */
	protected $fCurl;

	//------------------------------------------------------------------------------------------------------------------//

	/**
	 * @param string $aLogin Countbox API user login
	 * @param string $aPassword Countbox API user password
	 */
	public function __construct($aLogin = '', $aPassword = '')
	{
		$this->fLogin = empty($aLogin) ? C_COUNTBOX_LOGIN : $aLogin;
		$this->fPassword = empty($aPassword) ? C_COUNTBOX_PASSWORD : $aPassword;

		$this->fCurl = new Curl();
	}

	//------------------------------------------------------------------------------------------------------------------//

	/**
	 * Common request handler
	 * @param $aUrlCommand API url command
	 * @param bool $aPostMethod Use POST method (either GET)
	 * @param null $aPostParams POST method params
	 * @return array Data structure
	 * @throws CountboxException
	 */
	protected function &request($aUrlCommand, $aPostMethod = false, $aPostParams = null)
	{
		$lResponse = $aPostMethod ? $this->fCurl->post(self::cApiUrl . $aUrlCommand, $aPostParams) : $this->fCurl->get(self::cApiUrl . $aUrlCommand);

		if(!empty($lResponse))
		{
					$lResponseArr = @json_decode($lResponse, true);
			if(!empty($lResponseArr) && is_array($lResponseArr))
			{
				if(!empty($lResponseArr['error']))
					throw new CountboxException($lResponseArr['Message'], intval($lResponseArr['ErrorCode']));

				return $lResponseArr;
			}
		}
		return array();
	}

	protected function createDateTimeString($aDatetime)
	{
		return date('Y-m-d\TH:i:s', is_int($aDatetime) ? $aDatetime : strtotime($aDatetime));
	}
	//------------------------------------------------------------------------------------------------------------------//

	/**
	 * Request a temporary user token
	 * @return string
	 * @throws CountboxException
	 */
	protected function getToken()
	{
		$lResponse = $this->request('token/get/' . base64_encode($this->fLogin . ':' . $this->fPassword));
		if(!isset($lResponse['key']) || empty($lResponse['key']))
			throw new CountboxException('Empty token');
		return $lResponse['key'];
	}

	/**
	 * Request and remember user token
	 */
	protected function obtainToken()
	{
		if(empty($this->fToken))
			$this->fToken = $this->getToken();
	}

	//------------------------------------------------------------------------------------------------------------------//

	/**
	 * Get points list of account
	 * @return array of hash ('id', 'name', 'address')
	 */
	public function getPoints()
	{
		$this->obtainToken();
		return $this->request('point/all/', true, array('access_token' => $this->fToken));
	}

	/**
	 * Get attendance for given period
	 * @param $aPointId Point ID (see getPoints())
	 * @param $aDateBegin Period begin datetime
	 * @param $aDateEnd Perod end datetime
	 * @param $aGroupType Attendance group by 'quarter' - 15 min, 'hour' - hourly, 'day' - daily
	 * @param $aIgnoreWorkTime Ignore point work time
	 * @return array of hash ('id', 'in', 'out', 'datebegin', 'dateend')
	 */
	public function getAttendance($aPointId, $aDateBegin, $aDateEnd, $aGroupType, $aIgnoreWorkTime = false)
	{
		$this->obtainToken();

		if($aGroupType == 'quarter')
			$lGroupType = 1;
		elseif($aGroupType == 'hour')
			$lGroupType = 2;
		else
			$lGroupType = 3;

		return $this->request('point/getattendance/', true, array(
			'access_token' => $this->fToken,
			'id' => $aPointId,
			'datebegin' => $this->createDateTimeString($aDateBegin),
			'dateend' => $this->createDateTimeString($aDateEnd),
			'groupType' => $lGroupType,
			'isAllTime' => $aIgnoreWorkTime ? 'true' : false
		));
	}

	/**
	 * Get visitors count on a requested datetime
	 * @param $aPointId Point ID
	 * @param $aDatetime Requested datetime
	 * @return array hash('id', 'visitor', 'datetime')
	 */
	public function getVisitors($aPointId, $aDatetime)
	{
		$this->obtainToken();

		return $this->request('point/getvisitors/', true, array(
			'access_token' => $this->fToken,
			'id' => $aPointId,
			'datetime' => $this->createDateTimeString($aDatetime)
		));
	}
}