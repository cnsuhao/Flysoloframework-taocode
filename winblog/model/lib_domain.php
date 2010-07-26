<?php


/**
 * 二级域名实现的用户级扩展
 * @author jake
 * @version 1.0
 * @created 01-三月-2010 14:19:18
 */
class lib_domain
{

	/**
	 * 实现二级域名功能，这里解析当前访问的用户
	 */
	public function enter()
	{
		GLOBAL $__controller, $__action;
		if( $__controller == "main" && $__action == "index" ){
			$uri = basename(trim($_SERVER["REQUEST_URI"],"\/\\"));
			if( $uri != "main" && $uri != "index" 
								&& ( false != spClass("lib_user")->find(array("username"=>$uri))) ){
				$__controller = "wblog";
				$__action = "view";
				spClass("spArgs")->set('u',$uri);
			}
		}
		spAddViewFunction('uri', array( $this, 'useruri'));
		spAddViewFunction('cutwin', array( $this, 'cutwin'));
		spAddViewFunction('pager', array( $this, 'pager'));
	}
	
	/**
	 * 实现显示该用户的二级域名的功能，主要给smarty模板使用
	 *
	 * @params  参数
	 */
	public function useruri($params)
	{
		$username = $params['u'];
		if( TRUE != $GLOBALS['G_SP']['winblog_defined']["subdomain"] ){
			return 'http://'.$_SERVER["HTTP_HOST"].spUrl('wblog','view',array('u'=>$username));
		}else{
			$uri = trim(dirname($GLOBALS['G_SP']['url']["url_path_base"]),"\/\\");
			if( '' == $uri ){
				$uri = $_SERVER["HTTP_HOST"];
			}else{
				$uri = $_SERVER["HTTP_HOST"].'/'.$uri;
			}
			return 'http://'.$uri.'/'.$username;
		}
	}
	
	/**
	 * 实现页码
	 *
	 * @params  参数
	 */
	public function pager($params)
	{
		$pager = $params['pager'];
		if( empty($pager) )return ;
		$data = '<div class="manu">'; $addonce = 1;
		if( $pager['current_page'] != $pager['first_page'] ){ // 不是第一页
			$data .= $this->formurl4pager($params, $pager['prev_page'], ' <  上一页');
		}else{
			$data .= '<span class="disabled"> <  上一页</span>';
		}
		foreach( $pager['all_pages'] as $p ){ // 循环页码
			if( $p == $pager['current_page'] ){
				$data .= '<span class="current">'.$p.'</span>';
			}elseif( $p == $pager['first_page'] ){
				$data .= $this->formurl4pager($params, $p, $p);
			}elseif( $p == $pager['last_page'] && ($p - 2) > $pager['first_page'] ){
				$data .= $this->formurl4pager($params, $p, $p);
			}elseif( $p < $pager['current_page'] + 5 &&  $p > $pager['current_page'] - 5 ){
				$data .= $this->formurl4pager($params, $p, $p);
			}elseif( 1 == $addonce){
				$data .= '...';$addonce = 0;
			}
		}
		if( $pager['current_page'] != $pager['last_page'] ){ // 不是最后一页
			$data .= $this->formurl4pager($params, $pager['next_page'], '下一页  > ');
		}else{
			$data .= '<span class="disabled">下一页  > </span>';
		}
		return $data.'</div>';
	}
	
	private function formurl4pager($params, $num, $name){
		$noargs = array( 'c', 'a', 'pagename', 'pager' );$args = array();
		foreach($params as $k => $v){if( !in_array($k, $noargs) )$args[$k] = $v;}
		$pagename = empty($params['pagename']) ? 'page' : $params['pagename'];
		$args += array($pagename=>$num);
		return '<a href="'.spUrl($params['c'], $params['a'], $args).'">'.$name.'</a>';
	}
	
	/**
	 * 截取字符串，主要给smarty模板使用
	 *
	 * @params  参数
	 */
	public function cutwin($params)
	{
		extract($params);
		$prefix = isset($prefix) ? $prefix : "...";
		return cutwin($str, $len, $prefix);
	}

	/**
	 * 无权限提示及跳转
	 */
	public function acljump(){ 
		// 这里直接“借用”了spController.php的代码来进行无权限提示
		$url = spUrl("main","login");
		echo "<html><head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"><script>function sptips(){alert(\"您没有权限进行此操作，请登录！\");location.href=\"{$url}\";}</script></head><body onload=\"sptips()\"></body></html>";
		exit;
	}
}
?>