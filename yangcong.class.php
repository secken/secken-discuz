<?php

if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_yangcong_base {

	function init() {
		global $_G;

		require_once template('yangcong:module');

		$_G['yangcong']['referer'] = !$_G['inajax'] && CURSCRIPT != 'plugin' ? $_G['basefilename'] . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '') : dreferer();
	}
}

class plugin_yangcong extends plugin_yangcong_base {

	function __construct(){
		$this->init();
	}

	function global_login_extra() {
		global $lang;
		require_once 'language.'.currentlang().'.php';

		return tpl_global_login_extra();
	}

	function global_usernav_extra1() {
		global $_G;

		if (!$_G['uid']) {
			return;
		}

		$var = C::t('#yangcong#yangcong')->getYangCongUid($_G['uid']);
		if (!empty($var['yangcong'])) {
			return;
		}

		return tpl_global_usernav_extra1();
	}

}

class plugin_yangcong_home extends plugin_yangcong_base {

}

class plugin_yangcong_member extends plugin_yangcong_base {

	function __construct(){
		$this->init();
	}

	function logging_method() {
		return tpl_logging_method();
	}

	function register_logging_method() {
		return tpl_logging_method();
	}
}
