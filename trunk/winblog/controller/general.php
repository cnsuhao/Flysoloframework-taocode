<?php

/**
 * 全部控制器页面的父类
 * 
 * 实现一些全局的页面显示
 * @author jake
 * @version 1.0
 * @created 27-二月-2010 9:31:54
 */
class general extends spController
{
	/**
	 * 默认风格
	 */
	var $themes = "default";
	
	/**
	 * 站点配置
	 */
	var $defined = "";
	
	/**
	 * 当前页面title
	 */
	var $title = "";
	
	/**
	 * 侧栏显示着谁的资料呢？
	 */
	var $sidebar_username = "";
	
	/**
	 * 覆盖控制器构造函数，进行相关的赋值操作
	 */
	public function __construct()
	{
		parent::__construct();
		
		// 用户未登录和登录后的分别，对$sidebar_uid赋值
		if(isset($_SESSION['winblognow']) && $_SESSION['winblognow']['uid'] > 0){
			$this->site_user = $_SESSION['winblognow'];
		}
		$this->defined = $GLOBALS['G_SP']['winblog_defined'];
		// 将站点配置输入模板中
		$this->winblog = $this->defined;
	}
	
	/**
	 * 返回表前缀
	 */
	public function prefix()
	{
		return $GLOBALS['G_SP']['db']['prefix'];
	}
	
	/**
	 * 显示模板的同时将输出侧栏
	 */
	public function display($tplname, $output = TRUE)
	{
		if( FALSE == $output)return parent::display($this->defined["template"]."/".$tplname, $output);
			
		// 模板内引用文件的路径
		$site_uri = trim(dirname($GLOBALS['G_SP']['url']["url_path_base"]),"\/\\");
		if( '' == $site_uri ){
			$site_uri = 'http://'.$_SERVER["HTTP_HOST"];
		}else{
			$site_uri = 'http://'.$_SERVER["HTTP_HOST"].'/'.$site_uri;
		}
		
		$this->site_uri = $site_uri;
		
		// 风格路径
		$this->themes_path = $site_uri."/themes/".$this->themes."/";
		
		// UCenter头像地址
		$this->themes_ucenter_path = $GLOBALS['G_SP']['ext']['spUcenter']['UC_API'];
		
		// 侧栏
		$this->template_sidebar = $this->sidebar();
		// 页面title
		$this->template_title = $this->title. ( ($this->title != "") ? " - " : "" );

		parent::display($this->defined["template"]."/".$tplname, $output);
	}

	/**
	 * 错误提示程序  应用程序的控制器类可以覆盖该函数以使用自定义的错误提示
	 * @param $msg   错误提示需要的相关信息
	 * @param $url   跳转地址
	 * 
	 * @param msg
	 * @param url
	 */
	public function error($msg, $url)
	{
		$this->msg = $msg;
		$this->url = $url;
		$this->display("error.html");
		exit();
	}

	/**
	 * 供继承的侧栏
	 */
	public function sidebar(){return;}

	/**
	 * 成功提示程序  应用程序的控制器类可以覆盖该函数以使用自定义的成功提示
	 * @param $msg   成功提示需要的相关信息
	 * @param $url   跳转地址
	 * 
	 * @param msg
	 * @param url
	 */
	public function success($msg, $url)
	{
		$this->msg = $msg;
		$this->url = $url;
		$this->display("success.html");
		exit();
	}

}
?>