<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

if (!$_G['uid']) {
	showmessage('not_loggedin', NULL, array(), array('login' => 1));
}
require_once DISCUZ_ROOT . './source/plugin/yangcong/yangcong.class.php';
$yangcong = new \plugin_yangcong_base();

if (!empty($_GET['cechk']) && !empty($_POST['uuid'])) {
	$info = $yangcong->getResult($_POST['uuid']);
	if (!empty($info['userid'])) {
		$sql = "delete from `pre_yangcong` where `uid`=%f and `yangcong`=%f  LIMIT 1";
		DB::fetch_first($sql, array($_G['uid'], $info['userid']));

		showmessage('绑定取消成功', 'home.php?mod=spacecp&ac=plugin&id=yangcong:binding', null, array('alert' => 'info', 'msgtype' => 3, 'showmsg' => 1, 'handle' => 0, 'showdialog' => 1, 'locationtime' => 1));
	} else {
		if ($yangcong->get_code() === 300056) {
			showmessage($yangcong->get_message(), null, null, array('alert' => 'error', 'msgtype' => 3, 'showmsg' => 1, 'handle' => 0));
		} else {
			showmessage($yangcong->get_message(), 'home.php?mod=spacecp&ac=plugin&id=yangcong:binding', null, array('alert' => 'error', 'msgtype' => 3, 'showmsg' => 1, 'handle' => 0, 'showdialog' => 1, 'locationtime' => 1));
			//showmessage($yangcong->get_message(), 'home.php?mod=spacecp&ac=plugin&id=yangcong:binding', null, array('alert' => 'error', 'msgtype' => 3, 'showmsg' => 1, 'handle' => 0));
		}
	}
}
$sql = "select * from `pre_yangcong` where `uid` = %f  limit 1";
$_G['yangcong'] = DB::fetch_first($sql, array($_G['uid']));
$loginCode = $yangcong->verifyOneClick($_G['yangcong']['yangcong'], "Discuz论坛解除洋葱绑定");

$loginhash = 'L' . random(4);
include template('common/header');
?>
<div id="layer_lostpw_<?php echo $loginhash?>" style="margin-bottom:30px;text-align: center;width:300px;height:50px;">
    <h3 class="flb">
        <em >取消绑定</em>
        <span><a href="javascript:;" class="flbc" onclick="hideWindow('yangcongcancel')" title="关闭">关闭</a></span>
    </h3>
    <form method="post" autocomplete="off" class="cl" id="yangcongform_<?php echo $loginhash?>" action="plugin.php?id=yangcong:cancel&cechk=true">
        <div class="c cl">
            <input type="hidden" name="uuid" value="<?php echo $loginCode['uuid']?>" />
            <input type='hidden' name='handlekey' value="yangcong_message<?php echo $loginhash?>" />
            <div id="return_yangcong_message<?php echo $loginhash?>">
                等待一键授权
            </div>
        </div>
    </form>

</div>
<script src="./source/plugin/yangcong/template/js/yangcong.js" type="text/javascript"></script>
<script type="text/javascript">
            setInterval("yangcong_GetResult('<?php echo $loginhash?>')", 3000);
</script>
<?php include template('common/footer');?>