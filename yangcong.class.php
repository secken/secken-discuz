<?php

if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

/**
 * 洋葱网授权类 v2.0
 * @author me@sanliang.org
 */
class plugin_yangcong_base {

	/**
	 * 应用id
	 * @var string
	 */
	public $APP_ID = '';

	/**
	 * 应用Key
	 * @var string
	 */
	public $APP_KEY = '';

	/**
	 * web授权code
	 * @var string
	 */
	public $WEBAUTHCODE = '';

	/**
	 * 获取绑定二维码
	 * @var string
	 */
	public $_GetBindingCode = 'https://api.yangcong.com/v2/qrcode_for_binding'; //GET

	/**
	 * 获取登录二维码
	 * @var string
	 */
	public $_GetLoginCode = 'https://api.yangcong.com/v2/qrcode_for_auth';//GET

	/**
	 * 查询UUID事件结果
	 * @var string
	 */
	public $_GetResult = 'https://api.yangcong.com/v2/event_result'; //GET

	/**
	 * 一键认证
	 * @var string
	 */
	public $_VerifyOneClick = 'https://api.yangcong.com/v2/realtime_authorization';

	/**
	 * 动态码验证
	 * @var string
	 */
	public $_VerifyOTP = 'https://api.yangcong.com/v2/offline_authorization';

	/**
	 * 洋葱网授权页
	 * @var string
	 */
	public $_AuthPage = 'https://auth.yangcong.com/v2/auth_page'; //GET

	/**
	 * 错误码
	 * @var array
	 */
	public $_error_code = array(
		200 => '请求成功',
		400=>'请求参数格式错误',
		401=>'动态码过期',
		402=>'app_id错误',
		403=>'请求签名错误',
		404=>'请你API不存在',
		405=>'请求方法错误',
		406=>'不在应用白名单里',
		407=>'30s离线验证太多次，请重新打开离线验证页面',
		500=>'洋葱系统服务错误',
		501=>'生成二维码图片失败',
		600=>'动态验证码错误',
		601=>'用户拒绝授权',
		602=>'等待用户响应超时，可重试',
		603=>'等待用户响应超时，不可重试',
		604=>'用户不存在'

	);
	private $_message, $_code;

	public function __construct() {
		global $_G;
		$this->APP_ID = $_G['cache']['plugin']['yangcong']['appid'];
		$this->APP_KEY = $_G['cache']['plugin']['yangcong']['appkey'];
		$this->WEBAUTHCODE = $_G['cache']['plugin']['yangcong']['code'];
		$_G['yangcong']['referer'] = !$_G['inajax'] && CURSCRIPT != 'plugin' ? $_G['basefilename'] . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '') : dreferer();

//        if (!empty($_G['uid'])) {
		//            $sql = "select * from `pre_yangcong` where `uid` = %f  limit 1";
		//            $_G['yangcong'] = DB::fetch_first($sql, array($_G['uid']));
		//            if (authcode(getcookie('yangcong'), 'DECODE') !== $_G['yangcong']['yangcong']) {
		//                echo '等待授权';
		//                exit;
		//            }
		//        }
	}

	/**
	 * 获取绑定二维码
	 * @return array
	 * code      成功、错误码
	 * message   错误信息
	 * url       二维码地址
	 * uuid      事件id
	 */
	public function getBindingCode() {
		// $result = $this->_post($this->_GetBindingCode, array('app_id' => $this->APP_ID, 'signature' => md5('app_id=' . $this->APP_ID . $this->APP_KEY)));
		$arr= array('app_id' => $this->APP_ID, 'signature' => md5('app_id=' . $this->APP_ID . $this->APP_KEY));
		$url=$this->__GetBindingCode."?app_id=".$arr['app_id']."&signature=".$arr['signature'];
	 	$result=$this->_get($url);
		return $result;

	}

	/**
	 * 获取登录二维码
	 * @return array
	 * code      成功、错误码
	 * message   错误信息
	 * url       二维码地址
	 * uuid      事件id
	 */
	public function getLoginCode() {
	 	// $result = $this->_post($this->_GetLoginCode, array('app_id' => $this->APP_ID, 'signature' => md5('app_id=' . $this->APP_ID . $this->APP_KEY)));
	 	$arr=array('app_id' => $this->APP_ID, 'signature' => md5('app_id=' . $this->APP_ID . $this->APP_KEY));
	 	$url=$this->_GetLoginCode."?app_id=".$arr['app_id']."&signature=".$arr['signature'];
	 	$result=$this->_get($url);
	   return $result;
	}

	/**
	 * 查询UUID事件结果
	 * @param string $uuid 事件id
	 * @return array
	 * code    成功、错误码
	 * message 错误信息
	 * userid  用户ID
	 */
	public function getResult($uuid) {
		// $result = $this->_post($this->_GetResult, array('app_id' => $this->APP_ID, 'event_id' => $uuid, 'signature' => md5('app_id=' . $this->APP_ID . 'event_id=' . $uuid . $this->APP_KEY)));
		$arr= array('app_id' => $this->APP_ID, 'event_id' => $uuid, 'signature' => md5('app_id=' . $this->APP_ID . 'event_id=' . $uuid . $this->APP_KEY));
		$url=$this->__GetResult."?app_id=".$arr['app_id']."&signature=".$arr['signature']."&event_id=".$arr['event_id'];
	 	$result=$this->_get($url);
		return $result;
	}

	/**
	 * 一键认证
	 * @param string $userid 用户ID
	 * @return array
	 * code    成功、错误码
	 * message 错误信息
	 * uuid    事件id
	 */
	public function verifyOneClick($userid, $action = 'login') {
		$result = $this->_post($this->_VerifyOneClick, array('app_id' => $this->APP_ID, 'uid' => $userid, 'action_typ' => $action, 'signature' => md5('action_typ=' . $action . 'app_id=' . $this->APP_ID . 'uid=' . $userid . $this->APP_KEY)));
		return $result;
	}

	/**
	 * 动态码验证
	 * @param string $userid 用户ID
	 * @param string $dnum 6位数字
	 * @return array
	 * code    成功、错误码
	 * message 错误信息
	 */
	public function verifyOTP($userid, $dnum) {
		$result = $this->_post($this->_VerifyOTP, array('app_id' => $this->APP_ID, 'uid' => $userid, 'dynamic_code' => $dnum, 'signature' => md5('app_id=' . $this->APP_ID . 'dynamic_code=' . $dnum . 'uid=' . $userid . $this->APP_KEY)));
		return $result;
	}

	public function authPage($callback) {
		$time = time();
		$d['signature'] = md5('auth_id=' . $this->WEBAUTHCODE . 'timestamp=' . $time . 'callback=' . $callback . $this->APP_KEY);
		$d['auth_id'] = $this->WEBAUTHCODE;
		$d['timestamp'] = $time;
		$d['callback'] = $callback;

		return $this->_AuthPage . '?' . http_build_query($d);
	}

	/**
	 * 返回消息
	 * @return string
	 */
	public function get_message() {
		return $this->_message;
	}

	public function get_code() {
		return $this->_code;
	}

	public function check_error($result) {
		$this->_code = $result['status'];
		$this->_message = (isset($this->_error_code[$result['status']]) ? $this->_error_code[$result['status']] : $result['description']);
		return $result['status'] === 200 ? TRUE : FALSE;
	}

	function _post($url, $post = array()) {
		$curl = curl_init(); //初始化一个cURL会话
		curl_setopt($curl, CURLOPT_URL, $url); //设置一个cURL传输选项
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); //禁用后cURL将终止从服务端进行验证
		//curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, TRUE);
		curl_setopt($curl, CURLOPT_USERAGENT, !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : FALSE); //在HTTP请求中包含一个"User-Agent: "头的字符串。 
		curl_setopt($curl, CURLOPT_HEADER, FALSE);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl, CURLOPT_POST, TRUE); //启用时会发送一个常规的POST请求
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($curl); // 执行一个cURL会话
		if (curl_errno($curl)) { //返回最后一次的错误号
			//exit('Errno' . curl_error($curl));
			return NULL;
		}
		curl_close($curl);//关闭一个cURL会话
		$result = (array) json_decode($result);
		return $result;
		// return $this->check_error($result) === TRUE ? $result : NULL;
	}
	//get方式发送
	function _get($url) {
		$curl = curl_init(); //初始化一个cURL会话
		curl_setopt($curl, CURLOPT_URL, $url); //设置一个cURL传输选项
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); //禁用后cURL将终止从服务端进行验证
		//curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, TRUE);
		curl_setopt($curl, CURLOPT_USERAGENT, !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : FALSE); //在HTTP请求中包含一个"User-Agent: "头的字符串。 
		curl_setopt($curl, CURLOPT_HEADER, FALSE);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($curl); // 执行一个cURL会话
		if (curl_errno($curl)) { //返回最后一次的错误号
			//exit('Errno' . curl_error($curl));
			return NULL;
		}
		curl_close($curl);//关闭一个cURL会话
		$result = (array) json_decode($result);
		return $this->check_error($result) === TRUE ? $result : NULL;
	}

}

class plugin_yangcong extends plugin_yangcong_base {

	function global_login_extra() {
		global $_G;
		include template('yangcong:login');
		return yangcong_login_extra();
	}

	function global_usernav_extra1() {
		global $_G;

		if (!$_G['uid']) {
			return;
		}
		$sql = "select * from `pre_yangcong` where `uid` = %f  limit 1";
		$var = DB::fetch_first($sql, array($_G['uid']));
		if (!empty($var['yangcong'])) {
			return;
		}

		include template('yangcong:bind');
		return yangcong_bind();
	}

}

class plugin_yangcong_home extends plugin_yangcong_base {

}

class plugin_yangcong_member extends plugin_yangcong_base {

	function logging_method() {
		include template('yangcong:login_bar');
		return yangcong_login_bar();
	}

	function register_logging_method() {
		include template('yangcong:login_bar');
		return yangcong_login_bar();
	}

	function register_output() {
		global $_G;
		if ($_G['uid'] != 0 && CURSCRIPT == 'member' && $_GET['inajax'] == 1) {
			$userid = authcode(getcookie('yangconguid'), 'DECODE', $_G['config']['security']['authkey']);
			if (!empty($userid)) {
				$sql = "select * from `pre_yangcong` where `uid` = %f  limit 1";
				$var = DB::fetch_first($sql, array($_G['uid']));
				if (!empty($var['uid'])) {
					DB::update('yangcong', array('yangcong' => $userid), DB::field('uid', $_G['uid']));
				} else {
					$data = array(
						'uid' => $_G['uid'],
						'yangcong' => $userid,
					);
					DB::insert('yangcong', $data, false, true);
				}
			}
		}
	}

}
