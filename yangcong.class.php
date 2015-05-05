<?php

if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

/**
 * 洋葱网授权类 v1.0
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
	public $_GetBindingCode = 'https://api.yangcong.com/v1/GetBindingCode';

	/**
	 * 获取登录二维码
	 * @var string
	 */
	public $_GetLoginCode = 'https://api.yangcong.com/v1/GetLoginCode';

	/**
	 * 查询UUID事件结果
	 * @var string
	 */
	public $_GetResult = 'https://api.yangcong.com/v1/GetResult';

	/**
	 * 一键认证
	 * @var string
	 */
	public $_VerifyOneClick = 'https://api.yangcong.com/v1/VerifyOneClick';

	/**
	 * 动态码验证
	 * @var string
	 */
	public $_VerifyOTP = 'https://api.yangcong.com/v1/VerifyOTP';

	/**
	 * 洋葱网授权页
	 * @var string
	 */
	public $_AuthPage = 'https://api.yangcong.com/v1/AuthPage';

	/**
	 * 错误码
	 * @var array
	 */
	public $_error_code = array(
		0 => '请求成功',
		6 => '系统错误',
		7 => '用户不存在',
		8 => 'app不存在',
		15 => '签名错误',
		17 => 'appkey匹配失败',
		19 => 'appid或appkey错误',
		300008 => '参数格式错误',
		300018 => '获取二维码图片失败',
		300039 => '调用接口过于频繁',
		300022 => '错误请求过于频繁',
		300056 => '请扫描二维码',
		300058 => '用户未操作，已超时',
		300040 => '推送消息失败',
		300055 => '用户拒绝授权验证',
		300008 => '参数格式错误',
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
		$result = $this->_post($this->_GetBindingCode, array('appid' => $this->APP_ID, 'signature' => md5('appid=' . $this->APP_ID . $this->APP_KEY)));
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
		$result = $this->_post($this->_GetLoginCode, array('appid' => $this->APP_ID, 'signature' => md5('appid=' . $this->APP_ID . $this->APP_KEY)));
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
		$result = $this->_post($this->_GetResult, array('appid' => $this->APP_ID, 'uuid' => $uuid, 'signature' => md5('appid=' . $this->APP_ID . 'uuid=' . $uuid . $this->APP_KEY)));
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
		$result = $this->_post($this->_VerifyOneClick, array('appid' => $this->APP_ID, 'userid' => $userid, 'action' => $action, 'signature' => md5('action=' . $action . 'appid=' . $this->APP_ID . 'userid=' . $userid . $this->APP_KEY)));
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
		$result = $this->_post($this->_VerifyOTP, array('appid' => $this->APP_ID, 'userid' => $userid, 'dnum' => $dnum, 'signature' => md5('appid=' . $this->APP_ID . 'dnum=' . $dnum . 'userid=' . $userid . $this->APP_KEY)));
		return $result;
	}

	public function authPage($callback) {
		$time = time();
		$d['signature'] = md5('authid=' . $this->WEBAUTHCODE . 'time=' . $time . 'callback=' . $callback . $this->APP_KEY);
		$d['authid'] = $this->WEBAUTHCODE;
		$d['time'] = $time;
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
		$this->_code = $result['code'];
		$this->_message = (isset($this->_error_code[$result['code']]) ? $this->_error_code[$result['code']] : $result['message']);
		return $result['code'] === 0 ? TRUE : FALSE;
	}

	function _post($url, $post = array()) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		//curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, TRUE);
		curl_setopt($curl, CURLOPT_USERAGENT, !empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : FALSE);
		curl_setopt($curl, CURLOPT_HEADER, FALSE);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl, CURLOPT_POST, TRUE);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($curl);
		if (curl_errno($curl)) {
			//exit('Errno' . curl_error($curl));
			return NULL;
		}
		curl_close($curl);
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
