<?php

/**
 * 话题相关数据模型类
 * @author jake
 * @version 1.0
 * @created 27-二月-2010 14:04:52
 */
class lib_topic extends spModel
{

	/**
	 * 主键
	 */
	public $pk = 'tid';
	/**
	 * 表名
	 */
	public $table = 'topic';

	
	/**
	 * 在数据表中新增一系列话题
	 * 
	 * @param topics    数组形式的多条记录
	 * @param newid    新微博的ID
	 */
	public function createAll($topics, $newid)
	{
		foreach($topics as $topic){
			$tid = null;
			if( $result = $this->find(array('topic'=>$topic)) ){
				// 话题存在
				$this->update(array('topic'=>$topic), array('clicks'=>$result['clicks']+1));
				$tid = $result['tid'];
			}else{
				// 话题不存在，则新增
				$tid = $this->create(array('topic' => $topic));
			}
			$maprow = array('tid' => $tid,'wid' => $newid);
			spClass('lib_win2topic')->create($maprow);
		}
	}
}
?>