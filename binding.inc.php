<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
require_once 'lang.'.currentlang().'.php';

if (!$_G['uid']) {
	showmessage('not_loggedin', NULL, array(), array('login' => 1));
}

$_G['yangcong'] = C::t('#yangcong#yangcong')->getYangCongUid($_G['uid']);
