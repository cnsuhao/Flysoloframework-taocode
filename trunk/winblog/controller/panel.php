<?php
import (APP_PATH.'/controller/general.php');

/**
 * 个人设置
 * @author jake
 * @version 1.0
 * @created 27-二月-2010 9:36:07
 */
class panel extends general
{

	/**
	 * 头像设置
	 */
	public function avatar()
	{
		$this->avatarhtml = spClass('spUcenter')->uc_avatar($_SESSION['winblognow']['uid']);
		$this->display("panel_avatar.html");
	}

	/**
	 * 修改密码
	 */
	public function chgpw()
	{
		if( $newpw = $this->spArgs('newpw') ){
			$newpw = $this->spArgs('newpw');
			$oldpw = $this->spArgs('oldpw');
			if( strlen($oldpw) < 30 && strlen($oldpw) >= 6 ){
				$oldpw = substr(md5($oldpw),0,20);
				$newpw = substr(md5($newpw),0,20);
				$ucresult = spClass('spUcenter')->uc_user_edit($_SESSION['winblognow']['username'], $oldpw, $newpw);
				if($ucresult == 1) {
					$this->success('修改成功，请重新登录！', spUrl('main','logout'));
				}
				$this->err = "旧密码错误！";
			}
		}
		$this->display("panel_chgpw.html");
	}

	/**
	 * 个人资料设置
	 */
	public function info()
	{
		if( 1 == $this->spArgs("ustatic") ){
			$updaterow = array(
				'city' => $this->spArgs('addprovince').' '.$this->spArgs('addcity'),
				'blog' => $this->spArgs('blog'),
				'intro' => $this->spArgs('intro'),
			);
			spClass('lib_user')->update(array('uid'=>$_SESSION['winblognow']['uid']), array_map('htmlspecialchars',$updaterow));
		}
		$city = spClass('lib_user')->find(array('uid'=>$_SESSION['winblognow']['uid']),null,'city');
		$this->thecity = explode(" ", $city['city']);
		$this->display("panel_info.html");
	}
	
	/**
	 * 博客挂件设置
	 */
	public function widget()
	{
		if( true == $GLOBALS['G_SP']['url']['url_path_info']){
			$this->widget_url = $_SERVER['HTTP_HOST'].spUrl('widget','iframe').'?';
		}else{
			$this->widget_url = $_SERVER['HTTP_HOST'].spUrl('widget','iframe').'&';
		}
		$this->display("panel_widget.html");
	}
	
	
	/**
	 * 共享书签设置
	 */
	public function bookmark()
	{
		if( true == $GLOBALS['G_SP']['url']['url_path_info']){
			$this->share_url = $_SERVER['HTTP_HOST'].spUrl('widget','share').'?';
		}else{
			$this->share_url = $_SERVER['HTTP_HOST'].spUrl('widget','share').'&';
		}
		$this->display("panel_bookmark.html");
	}

	/**
	 * 提醒设置
	 */
	public function reminder()
	{
		if( 1 == $this->spArgs('poststatic') ){
			$reminder = array(
				'atme' => (($this->spArgs('atme') == false) ? 0 : 1),
				'comment' => (($this->spArgs('comment') == false) ? 0 : 1),
				'repost' => (($this->spArgs('repost') == false) ? 0 : 1),
				'myfans' => (($this->spArgs('myfans') == false) ? 0 : 1)
			);
			$condition = array('username'=>$_SESSION['winblognow']['username']);
			spClass('lib_user')->update($condition, array('reminder'=>serialize($reminder)));
		}
		$userinfo = spClass('lib_user')->find($condition);
		$this->reminder = unserialize($userinfo['reminder']);
		$this->display("panel_reminder.html");
	}
	
	/**
	 * 模板选择
	 */
	public function template()
	{
		$this->display("panel_template.html");
	}

	/**
	 * 用户设置侧栏
	 */
	public function sidebar()
	{
		$this->userinfo = spClass('lib_user')->userintro($_SESSION['winblognow']['username']);
		$this->username = $username;
		return $this->display("panel_sidebar.html", FALSE);
	}


}
?>