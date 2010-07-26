<?php

/**
 * 用户数据模型类
 * @author jake
 * @version 1.0
 * @created 27-二月-2010 14:04:23
 */
class lib_user extends spModel
{

	/**
	 * 主键
	 */
	public $pk = 'uid';
	/**
	 * 表名
	 */
	public $table = 'user';
	
	/**
	 * 找回密码有效时间，秒
	 */
	public $pwhashtime = 86400; // 默认一天

	// 定义新的验证规则，以验证用户名重复和电邮重复
	var $addrules = array(
		// 自定义验证规则的函数名可以有两种形式
		// 第一种是 '规则名称' => '验证函数名'，这是当函数是一个单纯的函数时使用
		// 第二种是‘规则名称’=> array('类名', '方法函数名')，这是当函数是一个类的某个方法函数时候使用。
		// 'checkusername' => 'checkname', //  '规则名称' => '验证函数名'
		'rule_checkusername' => array('lib_user', 'checkusername'),  //‘规则名称’=> array('类名', '方法函数名')
		'rule_checknickname' => array('lib_user', 'checknickname'), //‘规则名称’=> array('类名', '方法函数名')
		'rule_checkemail' => array('lib_user', 'checkemail'), //‘规则名称’=> array('类名', '方法函数名')
		// 当然我们还可以定义更多的自定义规则
	);
	
	// 定义验证规则，从教程内直接复制的^_^
	
	// 这个是注册的验证规则
	var $verifier_register = array(
		"rules" => array( // 规则
			'username' => array(  // 这里是对username的验证规则
				'notnull' => TRUE, // username不能为空
				'minlength' => 3,  // username长度不能小于3
				'maxlength' => 15, // username长度不能大于15
				'rule_checkusername' => TRUE, // 用rule_checkusername规则验证用户名
			),
			'password' => array(  // 这里是对password的验证规则
				'notnull' => TRUE, // password不能为空
				'minlength' => 6,  // password长度不能小于6
				'maxlength' => 30, // password长度不能大于30
			),
			'email' => array(   // 这里是对email的验证规则
				'notnull' => TRUE, // email不能为空
				'email' => TRUE,   // 必须要是电子邮件格式
				'minlength' => 6,  // email长度不能小于6
				'maxlength' => 30, // email长度不能大于30
				'rule_checkemail' => TRUE, // 用rule_checkemail规则验证电子邮件
			),
		),
		"messages" => array( // 提示消息，从上面的rules复制下来，很快捷。
			'username' => array(  
				'notnull' => "用户名不能为空", 
				'minlength' => "用户名长度不能少于3个字符",  
				'maxlength' => "用户名长度不能大于15个字符",
				'rule_checkusername' => "用户名已存在", 
			),

			'password' => array(  
				'notnull' => "密码不能为空", 
				'minlength' => "密码长度不能少于6个字符",  
				'maxlength' => "密码长度不能大于30个字符", 
			),
			'email' => array(   
				'notnull' => "电子邮件不能为空",
				'email' => "电子邮件格式不正确",  
				'minlength' => "电子邮件长度不能少于6个字符",  
				'maxlength' => "电子邮件长度不能大于30个字符", 
				'rule_checkemail' => "该电子邮件已注册", 
			),
		),
	);
	
	var $linker = array( 
		array(
			'type' => 'hasmany',   // 一对多关联
			'map' => 'myfans_count',    // 关注我的人，叫myfans
			'mapkey' => 'uid',
			'fclass' => 'lib_fans',
			'fkey' => 'touid',
			'enabled' => true,
			'countonly' => true, // 仅计算
		),
		array(
			'type' => 'hasmany',   // 一对多关联
			'map' => 'asfans_count',    // 我关注的人，所以叫as fans
			'mapkey' => 'uid',
			'fclass' => 'lib_fans',
			'fkey' => 'fromuid',
			'enabled' => true,
			'countonly' => true, // 仅计算
		),
		array(
			'type' => 'hasmany',   // 多对多关联
			'map' => 'myfans_list', 
			'mapkey' => 'uid',
			'fclass' => 'lib_fans',
			'fkey' => 'touid',
			'enabled' => true,
			'limit' => '6', // 仅6条
		),
		array(
			'type' => 'hasmany',   // 一对多关联
			'map' => 'asfans_list',  
			'mapkey' => 'uid',
			'fclass' => 'lib_fans',
			'fkey' => 'fromuid',
			'enabled' => true,
			'limit' => '6', // 仅6条
		),
	);

	
	// 这是激活的验证规则
	var $verifier_active = array(
		"rules" => array( // 规则
			'nickname' => array(  
				'notnull' => TRUE, 
				'minlength' => 2,  
				'maxlength' => 15, 
				'rule_checknickname' => TRUE, 
			),
			'addcity' => array(  
				'notnull' => TRUE, 
			),
		),
		"messages" => array( // 提示消息，从上面的rules复制下来，很快捷。
			'nickname' => array(  
				'notnull' => "昵称不能为空", 
				'minlength' => "昵称长度不能少于2个字符",  
				'maxlength' => "昵称长度不能大于15个字符",
				'rule_checknickname' => "昵称已存在", 
			),
			'addcity' => array(  
				'notnull' => '请选择城市', 
			),
		),
	);
	

	/**
	 * 屏蔽用户
	 * 
	 * @param uid    用户ID
	 */
	public function block($uid)
	{
	}
	
	/**
	 * 通过UCENTER验证用户
	 * 
	 * @param username    用户名
	 * @param password    MD5后的密码
	 */
	public function login($username, $password)
	{
		$password = substr($password,0,20);
		list($uid, $username, $password, $email) = spClass("spUcenter")->uc_user_login($username, $password);
		if($uid > 0){
			return array(
				'uid' => $uid,
				'username' => $username,
				'email' => $email
			);
		}
		return false;
	}


	/**
	 * 注册用户
	 * 
	 * @param values    注册输入的数据
	 */
	public function register($values)
	{
		$this->verifier = $this->verifier_register; // 使用注册的验证规则
		
		$verifier_result = $this->spVerifier($values);
		if( false == $verifier_result ){
			// false是通过验证，然后开始写入数据
			// 本系统登录时需要用到spAcl加密密码框，所以密码还要MD5一下。
			// 由于UCenter目前不能支持32位的密码，所以这里只能截取20位来作为密码。
			$values["password"] = substr(md5($values["password"]),0,20);
			// 使用spUcenter的uc_user_register注册
			$uid = spClass("spUcenter")->uc_user_register($values["username"], $values["password"], $values["email"]);
			if($uid <= 0) { // 没有返回用户ID，注册不成功，直接返回原因
				// 这里是直接复制UCenter文档的。
				if($uid == -1) {
					return array('username'=>array('用户名不合法'));
				} elseif($uid == -2) {
					return array('username'=>array('包含要允许注册的词语'));
				} elseif($uid == -3) {
					return array('username'=>array('用户名已经存在'));
				} elseif($uid == -4) {
					return array('email'=>array('电子邮件格式有误'));
				} elseif($uid == -5) {
					return array('email'=>array('电子邮件不允许注册'));
				} elseif($uid == -6) {
					return array('email'=>array('该电子邮件已经被注册'));
				} else {
					return array('username'=>array('未定义错误'));
				}
			}else{
				return true;
			}
		}else{
			// true不能通过验证，这里直接返回给控制器处理
			return $verifier_result;
		}
	}

	/** 
	 * 增加的验证器，检查用户名是否存在
	 * @param val    待验证字符串
	 * @param right    正确值
	 */
	public function checkusername($val, $right) // 注意，这里的$right就是TRUE
	{
		if( spClass("spUcenter")->uc_user_checkname($val) == $right ){ // 当$val（输入值）等于$right（正确值）的时候，返回TRUE；
			return TRUE; // 返回TRUE则通过验证
		}else{
			return FALSE; // 返回FALSE则无法通过验证
		}
	}
	
	/** 
	 * 检查昵称是否存在
	 * @param nickname    
	 */
	public function checknickname($nickname)
	{
		return false == $this->find(array("nickname"=>$nickname));
	}
	
	/** 
	 * 增加的验证器，检查电邮是否存在
	 * @param val    待验证字符串
	 * @param right    正确值
	 */
	public function checkemail($val, $right) // 注意，这里的$right就是TRUE
	{
		if( spClass("spUcenter")->uc_user_checkemail($val) == $right ){ 
			return TRUE; // 返回TRUE则通过验证
		}else{
			return FALSE; // 返回FALSE则无法通过验证
		}
	}


	/** 
	 * 激活用户资料
	 * @param username    用户名
	 * @param values    用户设置值
	 */
	public function active($username, $values)
	{
		if( false == $this->find(array('username'=>$username)) ){ // 检查用户是否已激活
			$this->verifier = $this->verifier_active; // 使用注册的验证规则
			$verifier_result = $this->spVerifier($values);
			if( false == $verifier_result ){
				$userinfo = spClass("spUcenter")->uc_get_user($username);
				$newrow = array(
					'uid' => $userinfo[0],
					'username' => $userinfo[1],
					'nickname' => $values['nickname'],
					'sex' => $values['sex'],
					'city' => $values['addprovince'].' '.$values['addcity'],
					'blog' => $values['blog'],
					'email' => $userinfo[2],
					'intro' => $values['intro'],
					'ctime' => time(),
					'acl_name' => 'USERS',
				);
				$this->create($newrow);
				return true;
			}else{
				// true不能通过验证，这里直接返回给控制器处理
				return $verifier_result;
			}
		}
		return true;
	}
	
	/** 
	 * 侧栏获取用户全部资料，包括用户的粉丝
	 * @param username    用户名
	 */
	public function userintro($username)
	{
		$result = $this->spLinker()->find(array('username'=>$username));
		if(!empty($result['myfans_list'])){
			foreach( $result['myfans_list'] as $key => $item ){
				$result['myfans_list'][$key]['info'] = $this->find(array('uid'=>$item['fromuid']),null,'uid,username,nickname');
			}
		}
		if(!empty($result['asfans_list'])){
			foreach( $result['asfans_list'] as $key => $item ){
				$result['asfans_list'][$key]['info'] = $this->find(array('uid'=>$item['touid']),null,'uid,username,nickname');
			}
		}
		return $result;
	}
	/** 
	 * 检测是否需要提醒用户
	 * @param uid    用户ID
	 * @param type    提醒类型
	 */
	public function remind($uid, $type){
		$userinfo = $this->find(array('uid'=>$uid));
		$reminder = unserialize($userinfo['reminder']);
		if( 1 == $reminder[$type] ){ // 需要提醒
			$from = 'webmaster@'.$_SERVER['SERVER_NAME'];
			$to = $userinfo['email'];
			if( 'atme' == $type ){
				$subject = '有人回复您！';
			}elseif( 'comment' == $type ){
				$subject = '有人对您的微博进行评论了！';
			}elseif( 'myfans' == $type ){
				$subject = '有人关注您！';
			}else{
				$subject = '有人转发了您的微博！';
			}
			$message = '本邮件由系统自动发送，请进入您的微博查看：' . spClass('lib_domain')->useruri(array('u'=>$userinfo['username']));
			$headers = "From: {$from}\r\nReply-To: {$from}\r\nX-Mailer: PHP/" . phpversion();
			@mail($to, $subject, $message, $headers);
		}
	}
	/** 
	 * 发送找回密码的邮件
	 * @param username    用户username
	 * @param email    电邮
	 * @param url    检测的地址
	 */
	public function pwhash_send($username,$email,$url){
		$hash = md5( $username.md5(time().mt_rand(999,10000))).time();
		$this->update(array('username'=>$username), array('pwhash'=>$hash));
		$url .= "hash=".$hash;
		$from = 'webmaster@'.$_SERVER['SERVER_NAME'];
		$subject = '邮件找回密码';
		$message = '尊敬的用户，\r\n请点击以下链接找回您的密码：\r\n\r\n<a href="'.$url.'">'.$url.'</a>\r\n\r\n';
		$headers = "From: {$from}\r\nReply-To: {$from}\r\nX-Mailer: PHP/" . phpversion();
		@mail($email, $subject, $message, $headers);
		//echo $url;exit;
	}
	
	/** 
	 * 检查找回密码的hash值
	 * @param username    填写的用户名
	 * @param hash    待检查的hash值
	 */
	public function pwhash_check($username,$hash){
		$lastmd5 = substr($hash, -11);
		if( time() < $lastmd5 + $this->pwhashtime ){
			$userinfo = $this->find(array('username'=>$username));
			if( false != $userinfo && $userinfo['pwhash'] == $hash ){
				$this->update(array('username'=>$username), array('pwhash'=>''));
				return true;
			}
		}
		return false;
	}
}
?>