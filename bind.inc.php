<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once 'language.'.currentlang().'.php';
require_once DISCUZ_ROOT . './source/plugin/yangcong/secken.class.php';

$app_id  = $_G['cache']['plugin']['yangcong']['appid'];
$app_key = $_G['cache']['plugin']['yangcong']['appkey'];
$auth_id = $_G['cache']['plugin']['yangcong']['auth_id'];

$yangcong = new secken($app_id, $app_key, $auth_id);

$bindhash = isset($_POST['handlekey']) ? trim($_POST['handlekey']) : 'L' . random(4);

//进行绑定请求
if (submitcheck('confirmsubmit')) {
	//查询详细事件信息
	$info = $yangcong->getResult($_POST['event_id']);

	if (!empty($info['uid'])) {
		$bind_info = C::t('#yangcong#yangcong')->getBindInfo($_G['uid'], $info['uid']);

		//如果已经绑定，跳转到解绑页面
		if (!empty($bind_info['uid'])) {
			showmessage($lang['has_bind'], 'home.php?mod=spacecp&ac=plugin&id=yangcong:binding', null, array('alert' => 'info', 'msgtype' => 3, 'showmsg' => 1, 'handle' => 0));
		}

		//更新绑定纪录
		if (!empty($bind_info['uid'])) {
			C::t('#yangcong#yangcong')->updateBindInfo($_G['uid'], $info['uid']);
		} else {
			$data = array();
			$data = array(
				'uid' => $_G['uid'],
				'yangcong' => $info['uid'],
			);

			C::t('#yangcong#yangcong')->insertBindInfo($data);
		}
		showmessage($lang['bind_success'], 'home.php?mod=spacecp&ac=plugin&id=yangcong:binding', null, array('alert' => 'info', 'msgtype' => 3, 'showmsg' => 1, 'handle' => 0, 'showdialog' => 1, 'locationtime' => 1));

	} else {
		if ($yangcong->getCode() === 602) {
			showmessage($lang['scan_to_bind'], NULL, null, array('alert' => 'error', 'msgtype' => 3, 'showmsg' => 1, 'handle' => 0));
		} else {
			$code = $yangcong->getCode();
			$error_message = !isset($lang['error_code'][$code]) ? $lang['error_code']['unknow_error'] : $lang['error_code'][$code];

			showmessage($error_message, 'home.php?mod=spacecp&ac=plugin&id=yangcong:binding', null, array('alert' => 'error', 'msgtype' => 3, 'showmsg' => 1, 'handle' => 0, 'showdialog' => 1, 'locationtime' => 1));
		}
	}
}

require_once template('yangcong:bind');
