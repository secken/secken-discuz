<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
require_once DISCUZ_ROOT . './source/plugin/yangcong/yangcong.class.php';
$yangcong = new \plugin_yangcong_base();
if (!empty($_GET['auth_page'])) {
	exit(header('Location:' . $yangcong->authPage($_G['siteurl'] . 'plugin.php?id=yangcong:callback')));
} elseif (!empty($_GET['cechk']) && !empty($_POST['event_id'])) {
	$info = $yangcong->getResult($_POST['event_id']);
	if (!empty($info['uid'])) {
		var_dump($info);
		$sql = "select * from `pre_yangcong` where `yangcong` = '%f'  limit 1";
		$var = DB::fetch_first($sql, array($info['uid']));
		var_dump($var);
		exit();
		if (!empty($var['uid'])) {
			$member = getuserbyuid($var['uid'], 1);
			dsetcookie('auth', authcode("{$member['password']}\t{$member['uid']}", 'ENCODE'), 31536000);
			showmessage('登录成功', null, null, array('alert' => 'info', 'msgtype' => 3, 'showmsg' => 1, 'handle' => 0, 'showdialog' => 1, 'locationtime' => 1));
		} else {
			$auth = authcode($info['uid'], 'ENCODE', $_G['config']['security']['authkey']);
			dsetcookie('yangconguid', $auth);
			showmessage('授权失败，可能没有绑定洋葱授权', 'member.php?mod=register', null, array('alert' => 'error', 'msgtype' => 3, 'showmsg' => 1, 'handle' => 0, 'showdialog' => 1, 'locationtime' => 1));
		}
	} else {
		if ($yangcong->get_code() === 602) {
            showmessage('请扫二维码进行授权绑定', NULL, null, array('alert' => 'error', 'msgtype' => 3, 'showmsg' => 1, 'handle' => 0));
			// showmessage($yangcong->get_message(), null, null, array('alert' => 'error', 'msgtype' => 3, 'showmsg' => 1, 'handle' => 0));
		} else {
			showmessage($yangcong->get_message(), null, null, array('alert' => 'error', 'msgtype' => 3, 'showmsg' => 1, 'handle' => 0));
		}
	}
}
$loginCode = $yangcong->getLoginCode();
$loginhash = 'L' . random(4);
include template('common/header');
if (empty($_POST['handlekey'])) {
	?>
<div id="layer_lostpw_<?php echo $loginhash?>" style="margin:5px;text-align: center;">
    <h3 class="flb">
        <em >洋葱扫一扫登录</em>
        <span><a href="javascript:;" class="flbc" onclick="hideWindow('yangconglogin')" title="关闭">关闭</a></span>
    </h3>
    <form method="post" autocomplete="off" class="cl" id="yangcongform_<?php echo $loginhash?>" action="plugin.php?id=yangcong&cechk=true" fwin="yangconglogin">
        <div class="c cl">
            <input type="hidden" name="event_id" value="<?php echo $loginCode['event_id']?>" />
            <input type='hidden' name='handlekey' value="yangcong_message<?php echo $loginhash?>" />
            <?php if (is_array($loginCode)) {?>
                <div class="rfm yangcong-content">
                    <img width="260px" id="yangcongqrcode"  src="<?php echo $loginCode['qrcode_url'];?>">
                </div>
            <?php } else {?>
            <div class="alert">
                <strong>获取登陆二维码错误</strong>
            </div>
            <?php }?>
            <div id="return_yangcong_message<?php echo $loginhash?>" class="yangcong-message-box">
                <?php echo $yangcong->get_message();?>
            </div>
        </div>
    </form>
    <a class="yangcong-offline-button" href="plugin.php?id=yangcong&auth_page=true">洋葱离线授权</a>
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
