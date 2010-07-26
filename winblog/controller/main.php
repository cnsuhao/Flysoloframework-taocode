<?php
import (APP_PATH.'/controller/general.php');

/**
 * 微博网站页面
 * @author jake
 * @version 1.0
 * @created 27-二月-2010 9:33:29
 */
class main extends general
{

	/**
	 * 忘记密码的邮件地址验证 Step 1
	 */
	public function forgetpw()
	{
		if( $username = $this->spArgs('username') ){
			if( $userinfo = spClass('spUcenter')->uc_get_user($username) ){
				if( $userinfo[2] == $this->spArgs('email') ){
					if( true == $GLOBALS['G_SP']['url']['url_path_info']){
						$url = 'http://'.$_SERVER['HTTP_HOST'].spUrl('main','forgethash').'?';
					}else{
						$url = 'http://'.$_SERVER['HTTP_HOST'].spUrl('main','forgethash').'&';
					}
					spClass('lib_user')->pwhash_send($username,$userinfo[2],$url);
					$this->success("成功发送找回密码邮件到您的邮箱，请查收！", spUrl()); 
				}
			}
			$this->errmsg = "用户名/邮件地址错误或不匹配！";
		}
		$this->display("main_forgetpw.html");
	}
	
	/**
	 * 找回密码的hash认证以及用户名验证 Step 2
	 */
	public function forgethash()
	{
		$this->pwhash = $this->spArgs('hash');
		if( $username = $this->spArgs('username') ){
			if( true == spClass('lib_user')->pwhash_check($username,$this->spArgs('pwhash')) ){
				$newpw = $this->spArgs('password');
				if( strlen($newpw) < 30 && strlen($newpw) >= 6 ){
					$newpw = substr(md5($newpw),0,20);
					$ucresult = spClass('spUcenter')->uc_user_edit($username, "", $newpw, "", 1);
					if($ucresult == 1) {
						$this->success('修改成功，请登录！', spUrl('wblog'));
					}
				}
			}
			$this->errmsg = "找回密码邮件已过期，请<a href='".spUrl('main', 'forgetpw')."'>重新申请</a>！";
		}
		$this->display("main_forgetcheck.html");
	}

	/**
	 * 网站首页
	 */
	public function index()
	{
		$winlist = spClass('lib_win')->spPager($this->spArgs('page', 1), 15)->findAll(null,'wid DESC');
		$this->winlist = $winlist;
		$this->pager = spClass('lib_win')->spPager()->getPager();
		$this->display("main_index.html");
	}

	/**
	 * 登录，使用加密的密码输入框
	 */
	public function login()
	{
		import("spAcl.php"); // 载入spAcl.php文件
		if( $this->spArgs("username") ){
			$password = spClass("spAcl")->pwvalue();
			// 通过UCenter检查用户登录
			$userinfo = spClass('lib_user')->login($this->spArgs("username"),$password);
			if( false != $userinfo ){
				// 搜索本地用户库，看用户是否已经激活（激活即有记录），未激活将显示用户设置表单
				if($result = spClass('lib_user')->find(array('uid'=>$userinfo['uid']))){
					// 激活，设置SESSION登录
					$_SESSION['winblognow'] = $result;
					spClass('spAcl')->set($result['acl_name']);
					if( strlen($this->spArgs("ref")) > 2 )
						$this->jump(urldecode($this->spArgs("ref"))); // 跳回来源页
					$this->jump(spUrl('wblog','index')); // 跳回首页
				}else{
					// 未激活，记录SESSION并跳转显示表单
					$_SESSION['winblogunactive'] = $userinfo;
					$this->jump(spUrl('main','active')); // 跳转到激活页面
				}
			}else{
				$this->errmsg = "用户名/密码错误！";
			}
		}
		$this->ref = urlencode($this->spArgs("ref",$_SERVER['HTTP_REFERER']));
		$this->display("main_login.html");
	}

	/**
	 * 登出
	 */
	public function logout()
	{
		$_SESSION['winblognow'] = null;
		unset($_SESSION['winblognow']);
		$this->jump(spUrl());
	}

	/**
	 * 注册
	 */
	public function register()
	{
		if( $this->spArgs("username") ){
			$result = spClass("lib_user")->register($this->spArgs());
			if(true === $result){ // 注意是===
				// 成功向UCenter注册，接下来开始本地的激活流程
				$_SESSION['winblogunactive'] = $this->spArgs();
				$this->jump(spUrl('main','active')); // 跳转到激活页面
			}
			$this->errmsg_arr = $result;
			$this->remaininput = $this->spArgs();
		}
		
		// 未输入用户名则显示表单
		$this->display("main_register.html");
	}
	
	/**
	 * 激活操作
	 */
	public function active()
	{
		if( 1 == $this->spArgs("ustatic") ){
			$username = $_SESSION['winblogunactive']['username'];
			$result = spClass("lib_user")->active($username, array_map('htmlspecialchars',$this->spArgs()));
			if(true === $result){ // 注意是===
				// 成功激活，设置SESSION登录，跳转到用户微博
				$_SESSION['winblogunactive'] = null;unset($_SESSION['winblogunactive']);
				$_SESSION['winblognow'] = spClass("lib_user")->find(array('username'=>$username));
				$this->success("成功激活微博！", spUrl('wblog','index')); 
			}
			$this->errmsg_arr = $result;
			$this->remaininput = $this->spArgs();
		}
		// 显示表单
		$this->userinfo = $_SESSION['winblogunactive'];
		$this->display("main_active.html");
	}

	/**
	 * 网站侧栏
	 */
	public function sidebar()
	{
		$this->toplist = spClass('lib_fans')->topfans(9); // 取得前9位最多人关注的用户
		$this->newlist = spClass('lib_user')->findAll(null, 'uid DESC', 'uid,username,nickname','9' );
		return $this->display("main_sidebar.html", FALSE);
	}
	


}
?>