<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if (!$_G['uid']) {
	showmessage('not_loggedin', NULL, array(), array('login' => 1));
}

$sql = "select * from %t where `uid` = %d  limit 1";
$_G['yangcong'] = DB::fetch_first($sql, array('yangcong', $_G['uid']));
