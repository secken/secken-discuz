<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$sql = <<<EOF
CREATE TABLE IF NOT EXISTS pre_yangcong (
	`uid` int(11) NOT NULL,
	`yangcong` varchar(50) DEFAULT NULL,
	`pass` varchar(32) DEFAULT NULL,
	INDEX  (`yangcong`) comment ''
) ENGINE=`MyISAM` COMMENT='洋葱授权';
EOF;

runquery($sql);

$finish = TRUE;