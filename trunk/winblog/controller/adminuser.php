<?php
import (APP_PATH.'/controller/general.php');

/**
 * 后台用户管理
 * @author jake
 * @version 1.0
 * @created 27-二月-2010 9:35:53
 */
class adminuser extends general
{


	/**
	 * 删除用户
	 */
	public function del()
	{
		spClass('lib_user')->deleteByPk($this->spArgs('uid'));
		$this->success("成功删除！",spUrl('adminuser'));
	}


	/**
	 * 后台用户管理
	 */
	public function index()
	{
		$userlist = spClass('lib_user')->spPager($this->spArgs('page', 1), 10)->findAll(null,'uid DESC');
		$this->userlist = $userlist;
		$this->pager = spClass('lib_user')->spPager()->getPager();
		$this->display("adminuser_index.html");
	}
	

	/**
	 * 后台侧栏
	 */
	public function sidebar()
	{
		// 重用了admin的侧栏
		return $this->display("admin_sidebar.html", FALSE);
	}

}
?>