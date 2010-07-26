<?php

/**
 * 微博内容数据处理类
 * @author jake
 * @version 1.0
 * @created 03-三月-2010 13:46:38
 */
class lib_win extends spModel
{

	/**
	 * 主键
	 */
	public $pk = 'wid';
	/**
	 * 表名
	 */
	public $table = 'win';

	/**
	 * 在数据表中新增一行数据，同时要处理对应的话题，回复，评论等内容
	 * 
	 * @param row    数组形式，数组的键是数据表中的字段名，键对应的值是需要新增的数据。
	 */
	public function create($row)
	{
		// 平板化及截取字符串
		$contents = cutwin($this->ripwin($row['contents']));

		$newrow = array(
			'username' => $row['username'],
			'uid' => $row['uid'],
			'nickname' => $row['nickname'],
			'contents' => $contents,
			'atuser' => $row['atuser'],
			'refrom' => $row['refrom'],
			'repostid' => $row['repostid'],
			'commentto' => $row['commentto'],
			'ctime' => time(),
		);
		$newid = parent::create($newrow);if(false == $newid)return false;
		
		// 获取话题，然后写入对应表
		if( $topics = $this->gettopic($contents) ){spClass('lib_topic')->createAll($topics, $newid);}
		
		// 处理repostid，commentto的对应微博的repostsum和commentsum
		if( !empty($row['repostid']) ){
			if( $repost = $this->find(array('wid'=>$row['repostid'])) ){
				$this->update(array('wid'=>$row['repostid']), array('repostsum'=>$repost['repostsum']+1));
			}
		}
		if( !empty($row['commentto']) ){
			if( $compost = $this->find(array('wid'=>$row['commentto'])) ){
				$this->update(array('wid'=>$row['commentto']), array('commentsum'=>$compost['commentsum']+1));
			}
		}
		
		// 用户的微博加一
		$userinfo = spClass('lib_user')->find(array('uid'=>$row['uid']));
		spClass('lib_user')->update(array('uid'=>$row['uid']), array('winsum'=>$userinfo['winsum']+1));
		
		return $newid;
	}
	
	/**
	 * 从字符串内获取话题#标签
	 * 
	 * @param str    
	 */
	public function gettopic($str)
	{
		$arr = explode('#', $str);
		$topics = false;
		for($i = 0; $i < count($arr); $i++){
			if( ( $i % 2 == 1 ) 
				&& isset($arr[$i]) 
					&& isset($arr[$i+1]) )$topics[] = $arr[$i];
		}
		return $topics;
	}

	/**
	 * 平板化字符串
	 * 
	 * @param str    
	 */
	public function ripwin($str)
	{
		$str = trim($str); 
		$str = strip_tags($str,""); 
		$str = ereg_replace("\t","",$str); 
		$str = ereg_replace("\r\n","",$str); 
		$str = ereg_replace("\r","",$str); 
		$str = ereg_replace("\n","",$str); 
		$str = ereg_replace(" "," ",$str); 
		return trim($str);
	}


}
?>