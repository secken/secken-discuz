<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class table_yangcong extends discuz_table{

    function __construct(){
        $this->_table = 'yangcong';
        parent::__construct();
    }

    function getBindInfo($discuz_uid, $yangcong_uid){
        $sql = "select `uid` from %t where `uid` = %d AND `yangcong` = %s LIMIT 1";
		return DB::fetch_first($sql, array($this->_table, $discuz_uid, $yangcong_uid));
    }

    function updateBindInfo($discuz_uid, $yangcong_uid){
        return DB::update($this->_table, array('yangcong' => $yangcong_uid), DB::field('uid', $discuz_uid));
    }

    function insertBindInfo($data){
        return DB::insert($this->_table, $data, false, true);
    }

    function getYangCongUid($discuz_uid){
        $sql = "select * from %t where `uid` = %d  limit 1";
        return DB::fetch_first($sql, array($this->_table, $discuz_uid));
    }

    function deleteBindInfo($discuz_uid, $yangcong_uid){
        return DB::delete($this->_table, array('uid'=>$discuz_uid, 'yangcong'=>$yangcong_uid));
    }

    function getUid($yangcong_uid){
        $sql = "select `uid` from %t where `yangcong` =  %s LIMIT 1";
        return DB::fetch_first($sql, array($this->_table, $yangcong_uid));
    }
}
