<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$sql = " DROP TABLE ". DB::table('yangcong') .";";
runquery($sql);

$finish = TRUE;
