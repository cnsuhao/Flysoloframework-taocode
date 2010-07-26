<?php
import (APP_PATH.'/controller/general.php');

/**
 * 管理员系统管理控制器
 * @author jake
 * @version 1.0
 * @created 27-二月-2010 9:32:31
 */
class admin extends general
{

	/**
	 * 屏蔽词设置
	 */
	public function badword()
	{
		$this->display("admin_badword.html");
	}

	/**
	 * 查看反馈信息，包括举报以及建议两种
	 */
	public function feedback()
	{
		$ctype = $this->spArgs('ctype','idea');
		if( 'del' == $ctype ){
			$fid = $this->spArgs('fid');
			spClass('lib_feedback')->deleteByPk($fid);
			$this->jump(spUrl('admin', 'feedback', array('ctype'=>$this->spArgs('oldtype'))));
		}elseif( 'jubao' == $ctype ){
			$condition = array('ctype'=>'jubao');
			$this->msglist = spClass('lib_feedback')->spLinker()->findAll($condition,'fid DESC');
		}else{
			$condition = array('ctype'=>'idea');
			$this->msglist = spClass('lib_feedback')->spLinker()->findAll($condition,'fid DESC');
		}
		$this->display("admin_feedback_{$ctype}.html");
	}

	/**
	 * 后台管理首页
	 */
	public function index()
	{
		$this->checkurl = "http://speedphp.com/app/winblog/?v=" . WINBLOG_VERSION;
		$this->display("admin_index.html");
	}

	/**
	 * 参数设置
	 */
	public function setting()
	{
		if( 1 == $this->spArgs('cstatic') ){
			$settings = $this->spArgs('settings');
			file_put_contents(APP_PATH."/config.php", stripslashes($settings) );
			$this->msg = "站点配置修改成功！";
		}
		$this->settings = file_get_contents(APP_PATH."/config.php");;
		$this->display("admin_setting.html");
	}

	/**
	 * 管理员侧栏
	 */
	public function sidebar()
	{
		return $this->display("admin_sidebar.html", FALSE);
	}

}
?>