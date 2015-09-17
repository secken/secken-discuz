<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if (!$_G['uid']) {
	showmessage('not_loggedin', NULL, array(), array('login' => 1));
}

require_once 'language.'.currentlang().'.php';

require_once DISCUZ_ROOT . './source/plugin/yangcong/secken.class.php';

$app_id  = $_G['cache']['plugin']['yangcong']['appid'];
$app_key = $_G['cache']['plugin']['yangcong']['appkey'];
$auth_id = $_G['cache']['plugin']['yangcong']['auth_id'];

$yangcong = new secken($app_id, $app_key, $auth_id);

$authhash = 'L' . random(4);

if (submitcheck('confirmsubmit')) {

	$info = $yangcong->getResult($_POST['event_id']);
	if (!empty($info['uid'])) {

		C::t('#yangcong#yangcong')->deleteBindInfo($_G['uid'], $info['uid']);
		showmessage($lang['bind_cancel_success'], 'home.php?mod=spacecp&ac=plugin&id=yangcong:binding', null, array('alert' => 'info', 'msgtype' => 3, 'showmsg' => 1, 'handle' => 0, 'showdialog' => 1, 'locationtime' => 1));
	} else {
		if ($yangcong->getCode() == 602) {
			showmessage($lang['please_confirm'], null, null, array('alert' => 'error', 'msgtype' => 3, 'showmsg' => 1, 'handle' => 0));
		} else {
			$code = $yangcong->getCode();
			$error_message = !isset($lang['error_code'][$code]) ? $lang['error_code']['unknow_error'] : $lang['error_code'][$code];

			showmessage($error_message, 'home.php?mod=spacecp&ac=plugin&id=yangcong:binding', null, array('alert' => 'error', 'msgtype' => 3, 'showmsg' => 1, 'handle' => 0, 'showdialog' => 1));
		}
	}
}

$_G['yangcong'] = C::t('#yangcong#yangcong')->getYangCongUid($_G['uid']);

require_once template('yangcong:cancle');
