<?php
import (APP_PATH.'/controller/general.php');

/**
 * 微博
 * @author jake
 * @version 1.0
 * @updated 03-三月-2010 15:34:41
 */
class wblog extends general
{
	/**
	 * @我的
	 */
	public function atme()
	{
		$condition = array(
			'atuser' => $_SESSION['winblognow']['username'],
		);
		$winlist = spClass('lib_win')->spPager($this->spArgs('page', 1), 10)->findAll($condition,'wid DESC');
		$this->winlist = $winlist;
		$this->pager = spClass('lib_win')->spPager()->getPager();
		$this->display("wblog_atme.html");
	}

	/**
	 * 评论
	 */
	public function comment()
	{
		$uid = $_SESSION['winblognow']['uid'];
		$prefix = $this->prefix();
		$sql = "select tb1.* from {$prefix}win as tb1, (select * from {$prefix}win where {$prefix}win.uid = {$uid}) as tb2 where tb1.commentto = tb2.wid group by tb1.wid order by tb1.wid DESC";
		$winlist = spClass('lib_win')->spPager($this->spArgs('page', 1), 10)->findSql($sql);
		$this->winlist = $winlist;
		$this->pager = spClass('lib_win')->spPager()->getPager();
		$this->display("wblog_comment.html");
	}
	
	/**
	 * 查看某用户的微博页，包括自己的
	 */
	public function view()
	{
		if( $username = $this->spArgs("u") ){ // 当输入的是用户名的时候
			$userinfo = spClass('lib_user')->find(array('username'=>$username));
		}elseif( $uid = $this->spArgs("uid") ){
			$userinfo = spClass('lib_user')->find(array('uid'=>$uid));
		}elseif( isset($_SESSION['winblognow']['username']) ){
			$userinfo = spClass('lib_user')->find(array('username'=>$_SESSION['winblognow']['username']));
		}
		if(!isset($userinfo) || false == $userinfo)$this->jump(spUrl()); // 返回首页

		$this->info = $userinfo;
		$winlist = spClass('lib_win')->spPager($this->spArgs('page', 1), 10)->findAll(array('uid'=>$userinfo['uid']),'wid DESC');
		$this->winlist = $winlist;
		$this->pager = spClass('lib_win')->spPager()->getPager();
		
		$this->isfans = spClass('lib_fans')->isyourfans($_SESSION['winblognow']['uid'], $userinfo['uid']);
		
		// 对$this->sidebar_username设置则可以改变侧栏显示的资料
		$this->sidebar_username = $userinfo['username'];
		$this->display("wblog_view.html");
	}

	/**
	 * 我关注的人和关注我的人
	 */
	public function fans()
	{
		$username = $this->spArgs('username', $_SESSION['winblognow']['username']);
		$type = $this->spArgs('type', 'asfans');
		
		$userinfo = spClass('lib_user')->find(array('username'=>$username));
		$fansObj = spClass('lib_fans');
		
		if( 'asfans' == $type ){
			// 我关注的人
			$condition = array(
				'fromuid' => $userinfo['uid']
			);
			$fansObj->linker = array(array( // 重新设置关联
				'type' => 'hasone',
				'map' => 'info',
				'mapkey' => 'touid',
				'fclass' => 'lib_user',
				'fkey' => 'uid',
				'enabled' => true,
			));
		}else{
			// 关注我的人
			$condition = array(
				'touid' => $userinfo['uid']
			);
			$fansObj->linker = array(array( // 重新设置关联
				'type' => 'hasone',
				'map' => 'info',
				'mapkey' => 'fromuid',
				'fclass' => 'lib_user',
				'fkey' => 'uid',
				'enabled' => true,
			));
		}
		
		$result = $fansObj->spPager($this->spArgs('page', 1), 10)->findAll($condition,'fansid DESC');
		$this->pager = $fansObj->spPager()->getPager();
		$this->fanslist = $fansObj->spLinker()->run($result);

		$this->isme = ($username == $_SESSION['winblognow']['username']) ? '我' : 'TA';
		$this->type = $type;
		$this->username = $username;
		$this->sidebar_username = $username;
		$this->display("wblog_fans.html");
	}

	/**
	 * 我的微博首页，有我的和关注的人的微博
	 */
	public function index()
	{
		$uid = $_SESSION['winblognow']['uid'];
		$prefix = $this->prefix();
		$sql = "select tb1.* from (select {$prefix}win.* from {$prefix}win where {$prefix}win.uid = {$uid} union select {$prefix}win.* from {$prefix}fans, {$prefix}win where {$prefix}win.uid = {$uid} or {$prefix}win.uid = {$prefix}fans.touid) as tb1 order by tb1.wid DESC";
		$winlist = spClass('lib_win')->spPager($this->spArgs('page', 1), 10)->findSql($sql);
		$this->winlist = $winlist;
		$this->pager = spClass('lib_win')->spPager()->getPager();
		$this->display("wblog_index.html");
	}
	
	/**
	 * 删除微博
	 */
	public function del()
	{
		$ref = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : spUrl();
		// 首先验证wid的微博是否当前session用户所有
		$condition = array(
			'wid' => $this->spArgs('wid'),
			'uid' => $_SESSION['winblognow']['uid']
		);
		if( false != spClass('lib_win')->find($condition) )spClass('lib_win')->delete($condition);
		$this->jump($ref);
	}

	/**
	 * 发布微博，包括回复、评论、@谁等操作
	 */
	public function post()
	{
		$winObj = spClass('lib_win');
		$ref = urldecode($this->spArgs('ref',$_SERVER['HTTP_REFERER']));
		// 检查输入
		$contents = $winObj->ripwin($this->spArgs('contents'));
		if( strlen($contents) < 1 )$this->error('请输入微博文字', $ref);
		$row = array('contents'=>$contents);
		$fromwin = $winObj->find(array('wid'=>$this->spArgs('wid')));
		if( 'repost' == $this->spArgs('formtype') ){ // 转发
			if( false == $fromwin )$this->error('参数错误', $ref);
			$row['repostid'] = $fromwin['wid']; $reminder_type = 'repost'; 
		}elseif( 'at' == $this->spArgs('formtype') ){ // @回复
			if( false == $fromwin )$this->error('参数错误', $ref);
			$row['atuser'] = $fromwin['username'];$reminder_type = 'atme';
		}elseif( 'comment' == $this->spArgs('formtype') ){ // 评论
			if( false == $fromwin )$this->error('参数错误', $ref);
			$row['commentto'] = $fromwin['wid']; $reminder_type = 'comment';
		}
		$addrow = array(
			'uid'=>$_SESSION['winblognow']['uid'],
			'username'=>$_SESSION['winblognow']['username'],
			'nickname'=>$_SESSION['winblognow']['nickname'],
			'refrom'=>'网站'
		);
		spClass('lib_win')->create(array_merge($row,$addrow));
		
		spClass('lib_user')->remind($fromwin['uid'], $reminder_type); // 发送提醒
		// 成功返回
		$this->success('微博发布成功', $ref);
	}

	/**
	 * 微博侧栏，将根据不同的目标用户有不同的显示
	 */
	public function sidebar()
	{
		// $this->sidebar_username是当前侧栏需要显示的用户名
		$username = ( "" == $this->sidebar_username ) ? $_SESSION['winblognow']['username'] : $this->sidebar_username;
		$this->userinfo = spClass('lib_user')->userintro($username);
		
		// 这里要判断一下是不是当前登录的用户
		$this->isme = ($username == $_SESSION['winblognow']['username']) ? '我' : 'TA';
		$this->username = $username;
		return $this->display("wblog_sidebar.html", FALSE);
	}

	/**
	 * 查看一条微博的详细情况
	 */
	public function win()
	{
		$win = spClass('lib_win')->find(array('wid'=>$this->spArgs('wid', false)));
		if( false == $win )$this->jump(spUrl());
		$condition = array('commentto' => $win['wid'] );// 这里只要显示评论
		$win['block'] = spClass('lib_win')->spPager($this->spArgs('page', 1), 10)->findAll($condition);
		$this->win = array($win);
		$this->pager = spClass('lib_win')->spPager()->getPager();
		$this->display("wblog_win.html");
	}

	/**
	 * 关注某人
	 */
	public function asfans()
	{
		$ref = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : spUrl();
		if( $touid = $this->spArgs('touid') ){
			if( 'befans' == $this->spArgs('do', 'befans') ){
				// 加关注
				spClass('lib_fans')->asyourfans($_SESSION['winblognow']['uid'], $touid);
			
				spClass('lib_user')->remind($touid, 'myfans'); // 发送提醒
			}else{
				// 解除关注
				spClass('lib_fans')->notyourfans($_SESSION['winblognow']['uid'], $touid);
			}
		}
		$this->jump($ref);
	}

}
?>