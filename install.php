<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$sql = "
CREATE TABLE IF NOT EXISTS ".DB::table('yangcong')."(
	`uid` int(11) NOT NULL,
	`yangcong` varchar(50) DEFAULT NULL,
	`pass` varchar(32) DEFAULT NULL,
	INDEX  (`yangcong`) comment ''
) ENGINE=`MyISAM`;
";

runquery($sql);

$finish = TRUE;
