<?php
if (!$_G['uid']) {
	showmessage('not_loggedin', NULL, array(), array('login' => 1));
}

$sql = "select * from `pre_yangcong` where `uid` = %f  limit 1";
$_G['yangcong'] = DB::fetch_first($sql, array($_G['uid']));
