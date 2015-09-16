<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once 'lang.'.currentlang().'.php';

require_once DISCUZ_ROOT . './source/plugin/yangcong/secken.class.php';

$app_id  = $_G['cache']['plugin']['yangcong']['appid'];
$app_key = $_G['cache']['plugin']['yangcong']['appkey'];
$auth_id = $_G['cache']['plugin']['yangcong']['auth_id'];

$yangcong = new secken($app_id, $app_key, $auth_id);

$loginhash = isset($_POST['handlekey']) ? trim($_POST['handlekey']) : 'L' . random(4);

//登陆验证
if (submitcheck('confirmsubmit')) {

    $info = $yangcong->getResult($_POST['event_id']);
	$code = $yangcong->getCode();

	if ($code == 200) {

		$var = C::t('#yangcong#yangcong')->getUid($info['uid']);

		if (!empty($var)) {

            $uid = $var['uid'];

            if ($uid) {
                $member = getuserbyuid($uid, 1);
                dsetcookie('auth', authcode("{$member['password']}\t{$member['uid']}", 'ENCODE'), 31536000);
                showmessage($lang['login_success'], null, null, array('alert' => 'info', 'msgtype' => 3, 'showmsg' => 1, 'handle' => 0, 'showdialog' => 1, 'locationtime' => 1));
			} else {
                showmessage($lang['login_failed'], null, null, array('alert' => 'error', 'msgtype' => 3, 'showmsg' => 1, 'handle' => 0));
			}
		} else {
			// $auth = authcode($info['uid'], 'ENCODE', $_G['config']['security']['authkey']);
			// dsetcookie('yangconguid', $auth);
			showmessage($lang['unbind'], 'member.php?mod=register', null, array('alert' => 'error', 'msgtype' => 3, 'showmsg' => 1, 'handle' => 0, 'showdialog' => 1, 'locationtime' => 1));
		}
	} else {
			$code = $yangcong->getCode();
			$error_message = !isset($lang['error_code'][$code]) ? $lang['error_code']['unknow_error'] : $lang['error_code'][$code];
			showmessage($error_message, null, null, array('alert' => 'error', 'msgtype' => 3, 'showmsg' => 1, 'handle' => 0));
	}
}

require_once template('yangcong:login');
