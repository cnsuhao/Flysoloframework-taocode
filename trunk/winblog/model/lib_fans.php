<?php

/**
 * 关注以及被关注的数据模型类
 * @author jake
 * @version 1.0
 * @updated 03-三月-2010 15:37:33
 */
class lib_fans extends spModel
{

	/**
	 * 主键
	 */
	public $pk = 'fansid';
	/**
	 * 表名
	 */
	public $table = 'fans';
	
	/**
	 * 关联，查会员
	 */
	var $linker = array();

	
	/**
	 * 成为你的粉丝，也就是关注对方
	 * 
	 * @param meuid    我的UID
	 * @param youruid    对方的UID
	 */
	public function asyourfans($meuid, $youruid)
	{
		$newrow = array(
			'fromuid' => $meuid,
			'touid' => $youruid
		);
		if( $result = $this->find($newrow) ){
			return $result['fansid'];
		}else{
			return $this->create($newrow);
		}
	}
	
	/**
	 * 不要做你的粉丝，也就是解除关注
	 * 
	 * @param meuid    我的UID
	 * @param youruid    对方的UID
	 */
	public function notyourfans($meuid, $youruid)
	{
		$newrow = array(
			'fromuid' => $meuid,
			'touid' => $youruid
		);
		$this->delete($newrow);
	}

	/**
	 * 是否已经是对方的粉丝呢？是否已经关注对方呢？
	 * touid是对方
	 * fromuid是自己
	 * 同时也包括判断对方是否自己
	 * 
	 * @param myuid    我的UID
	 * @param youruid    对方的uid
	 */
	public function isyourfans($myuid = 0, $youruid = 0)
	{
		if( 0 == $myuid || 0 == $youruid )return false;
		if( $myuid == $youruid )return false;
		$condition = array('fromuid'=>$myuid, 'touid'=>$youruid);
		return false != $this->find($condition);
	}

		
	/** 
	 * 获取最多人关注的前9位用户
	 * @param top    前多少位
	 */
	public function topfans($top = 9){
		$prefix = $GLOBALS['G_SP']['db']['prefix'];
		$sql = "select {$prefix}user.uid, {$prefix}user.username,{$prefix}user.nickname, {$prefix}fans.touid, count({$prefix}fans.touid) as counter from {$prefix}fans,{$prefix}user where {$prefix}user.uid = {$prefix}fans.touid group by {$prefix}fans.touid order by counter DESC limit {$top}";
		return $this->findSql($sql);
	}
}
?>