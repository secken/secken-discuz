<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once DISCUZ_ROOT . './source/plugin/yangcong/yangcong.class.php';
$yangcong = new \plugin_yangcong_base();
if (!empty($_GET['auth_page'])) {
	exit(header('Location:' . $yangcong->authPage($_G['siteurl'] . 'plugin.php?id=yangcong:callback')));
} elseif (!empty($_GET['cechk']) && !empty($_POST['uuid'])) {
	$info = $yangcong->getResult($_POST['uuid']);
	if (!empty($info['userid'])) {
		$sql = "select * from `pre_yangcong` where `uid` != %f AND `yangcong` = %f  limit 1";
		$var = DB::fetch_first($sql, array($_G['uid'], $info['userid']));
		if (!empty($var['uid'])) {
			showmessage('已经被其他账号绑定了', 'home.php?mod=spacecp&ac=plugin&id=yangcong:binding', null, array('alert' => 'info', 'msgtype' => 3, 'showmsg' => 1, 'handle' => 0));
		}
		$sql = "select * from `pre_yangcong` where `uid` = %f  limit 1";
		$var = DB::fetch_first($sql, array($_G['uid']));
		if (!empty($var['uid'])) {
			DB::update('yangcong', array('yangcong' => $info['userid']), DB::field('uid', $_G['uid']));
		} else {
			$data = array(
				'uid' => $_G['uid'],
				'yangcong' => $info['userid'],
			);
			DB::insert('yangcong', $data, false, true);
		}
		showmessage('绑定成功', 'home.php?mod=spacecp&ac=plugin&id=yangcong:binding', null, array('alert' => 'info', 'msgtype' => 3, 'showmsg' => 1, 'handle' => 0, 'showdialog' => 1, 'locationtime' => 1));
	} else {
		if ($yangcong->get_code() === 300056) {
			showmessage('请扫二维码进行授权绑定', NULL, null, array('alert' => 'error', 'msgtype' => 3, 'showmsg' => 1, 'handle' => 0));
		} else {
			showmessage($yangcong->get_message(), 'home.php?mod=spacecp&ac=plugin&id=yangcong:binding', null, array('alert' => 'error', 'msgtype' => 3, 'showmsg' => 1, 'handle' => 0, 'showdialog' => 1, 'locationtime' => 1));
			//showmessage($yangcong->get_message(), 'home.php?mod=spacecp&ac=plugin&id=yangcong:binding', null, array('alert' => 'error', 'msgtype' => 3, 'showmsg' => 1, 'handle' => 0));
		}
	}
}

$loginCode = $yangcong->getBindingCode();
$loginhash = 'L' . random(4);

include template('common/header');
if (empty($_POST['handlekey'])) {
	?>
<div id="layer_lostpw_<?php echo $loginhash?>" style="margin:5px;text-align: center;">
    <h3 class="flb">
        <em >绑定洋葱授权</em>
        <span><a href="javascript:;" class="flbc" onclick="hideWindow('yangcongbinding')" title="关闭">关闭</a></span>
    </h3>
    <form method="post" autocomplete="off" class="cl" id="yangcongform_<?php echo $loginhash?>" action="plugin.php?id=yangcong:bind&cechk=true">
        <div class="c cl">
            <input type="hidden" name="uuid" value="<?php echo $loginCode['uuid']?>" />
            <input type='hidden' name='handlekey' value="yangcong_message<?php echo $loginhash?>" />
            <?php if (is_array($loginCode)) {?>
                <div class="rfm">
                    <img width="75%" id="yangcongqrcode"  src="<?php echo $loginCode['url'];?>">
                </div>
            <?php } else {?>
            <div class="alert">
                <strong>获取绑定二维码错误</strong>
            </div>
            <?php }?>
            <div id="return_yangcong_message<?php echo $loginhash?>">
                <?php echo $yangcong->get_message();?>
            </div>
        </div>
    </form>
    <hr class="l">
    <a href="plugin.php?id=yangcong&auth_page=true">洋葱离线授权</a>
</div>
<script src="./source/plugin/yangcong/template/js/yangcong.js" type="text/javascript"></script>
<script type="text/javascript">
            setInterval("yangcong_GetResult('<?php echo $loginhash?>')", 3000);
</script>
<?php } else {?>
        <div class="alert">
            <strong><?php echo $yangcong->get_message();?></strong>
        </div>
<?php }?>
<?php include template('common/footer');?>
