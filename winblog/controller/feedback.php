<?php
import (APP_PATH.'/controller/general.php');

/**
 * 反馈
 * @author jake
 * @version 1.0
 * @created 27-二月-2010 9:36:01
 */
class feedback extends general
{

	/**
	 * 建议
	 */
	public function idea()
	{
		if( $msg = $this->spArgs('msg') ){
			$newrow = array(
				'ctype' => 'idea',
				'fromuid' => $_SESSION['winblognow']['uid'],
				'msg' => $msg,
				'ctime' => time(),
			);
			spClass('lib_feedback')->create($newrow);
			$this->success("成功发送信息，感谢您的支持！", spUrl('wblog','index')); 
		}
		$this->display("feedback_idea.html");
	}

	/**
	 * 举报用户
	 */
	public function jubao()
	{
		if( ! $uid = $this->spArgs('uid') )$this->jump(spUrl());
		if( $msg = $this->spArgs('msg') ){
			$newrow = array(
				'ctype' => 'jubao',
				'fromuid' => $_SESSION['winblognow']['uid'],
				'msg' => $msg,
				'touid' => $uid,
				'ctime' => time(),
			);
			spClass('lib_feedback')->create($newrow);
			$this->success("成功发送信息，感谢您的支持！", spUrl('wblog','index')); 
		}
		$this->userinfo = spClass('lib_user')->find(array('uid'=>$uid));
		$this->display("feedback_jubao.html");
	}

	
	/**
	 * 反馈侧栏
	 */
	public function sidebar()
	{
		// 重用了main的侧栏
		$this->toplist = spClass('lib_fans')->topfans(9); // 取得前9位最多人关注的用户
		$this->newlist = spClass('lib_user')->findAll(null, 'uid DESC', 'uid,username,nickname','9' );
		return $this->display("main_sidebar.html", FALSE);
	}
}
?>