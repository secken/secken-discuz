<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
require_once DISCUZ_ROOT . './source/plugin/yangcong/secken.class.php';

$app_id  = $_G['cache']['plugin']['yangcong']['appid'];
$app_key = $_G['cache']['plugin']['yangcong']['appkey'];
$auth_id = $_G['cache']['plugin']['yangcong']['auth_id'];

$yangcong = new secken($app_id, $app_key, $auth_id);

$bindhash = isset($_POST['handlekey']) ? trim($_POST['handlekey']) : 'L' . random(4);

//进行绑定请求
if (!empty($_GET['cechk']) && !empty($_POST['event_id'])) {
	//查询详细事件信息
	$info = $yangcong->getResult($_POST['event_id']);

	if (!empty($info['uid'])) {
		$sql = "select `uid` from `pre_yangcong` where `uid` = %d AND `yangcong` = %s";
		$var = DB::fetch_first($sql, array($_G['uid'], $info['uid']));

		//如果已经绑定，跳转到解绑页面
		if (!empty($var['uid'])) {
			showmessage('账号已经绑定', 'home.php?mod=spacecp&ac=plugin&id=yangcong:binding', null, array('alert' => 'info', 'msgtype' => 3, 'showmsg' => 1, 'handle' => 0));
		}

		//进行绑定业务
		$sql = "select `uid` from `pre_yangcong` where `uid` = %d";
		$var = DB::fetch_first($sql, array($_G['uid']));

		//更新绑定纪录
		if (!empty($var['uid'])) {
			DB::update('yangcong', array('yangcong' => $info['uid']), DB::field('uid', $_G['uid']));
		} else {
			$data = array();
			$data = array(
				'uid' => $_G['uid'],
				'yangcong' => $info['uid'],
			);
			DB::insert('yangcong', $data, false, true);
		}
		showmessage('绑定成功', 'home.php?mod=spacecp&ac=plugin&id=yangcong:binding', null, array('alert' => 'info', 'msgtype' => 3, 'showmsg' => 1, 'handle' => 0, 'showdialog' => 1, 'locationtime' => 1));

	} else {
		if ($yangcong->getCode() === 602) {
			showmessage('请扫二维码进行授权绑定', NULL, null, array('alert' => 'error', 'msgtype' => 3, 'showmsg' => 1, 'handle' => 0));
		} else {
			showmessage($yangcong->getMessage(), 'home.php?mod=spacecp&ac=plugin&id=yangcong:binding', null, array('alert' => 'error', 'msgtype' => 3, 'showmsg' => 1, 'handle' => 0, 'showdialog' => 1, 'locationtime' => 1));
		}
	}
}

require_once template('yangcong:bind');
