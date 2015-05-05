<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$appIdentifier = 'smilies';
$pluginid = intval($_GET['pluginid']);

require_once libfile('class/cloudregister');

if($operation == 'enable') {

	new Cloud_Register($appIdentifier, $pluginid, 'appOpenFormView');

} elseif($operation == 'disable') {

	new Cloud_Register($appIdentifier, $pluginid, 'appCloseReasonsView');

}