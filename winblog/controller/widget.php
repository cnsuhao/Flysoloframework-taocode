<?php
import (APP_PATH.'/controller/general.php');

/**
 * 挂件控制器
 * @author jake
 * @version 1.0
 * @updated 06-三月-2010 13:57:16
 */
class widget extends general
{

	/**
	 * iframe类型的博客挂件
	 */
	public function iframe()
	{
		if( $uid = $this->spArgs('uid') ){
			$this->widget_user = spClass('lib_user')->find(array('uid'=>$uid));
			$this->widget_height = $this->spArgs('height') - 60 - 35;
			$this->winlist = spClass('lib_win')->findAll(array('uid'=>$uid),'wid DESC','contents,wid,ctime','1,10');
			$this->display("widget_iframe.html");
		}
	}


	/**
	 * 分享微博的接口页面
	 */
	public function share()
	{
		if( $title = $this->spArgs('title') ){
			$this->share_title = $title;
			$this->share_url = $this->spArgs('url');
		}
		$this->display("widget_share.html");
	}

}
?>