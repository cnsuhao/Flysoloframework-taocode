<?php
/**
 * 文件名：Function.php
 * 版权：Copyright 2010 WuTao. Co. Ltd. All Rights Reserved.
 * 描述：全局函数库
 * 创建人：吴涛
 * 创建时间：2010-01-25 13:01
 * 版本: 1.0V
 */
// 浏览器友好的变量输出
function dump($var, $echo=true,$label=null, $strict=true)
{
    $label = ($label===null) ? '' : rtrim($label) . ' ';
    if(!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = "<pre style='background:#FFFFCC; border:1px solid #FF6633; text-align:left'>".$label.htmlspecialchars($output,ENT_QUOTES)."</pre>";
        } else {
            $output = $label . " : " . print_r($var, true);
        }
    }else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if(!extension_loaded('xdebug')) {
            $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
            $output = '<pre style=\'background:#FFFFCC; border:1px solid #FF6633; text-align:left\'>'. $label. htmlspecialchars($output, ENT_QUOTES). '</pre>';
        }
    }
    if ($echo) {
        echo($output);
        return null;
    }else
        return $output;
}
/*
 * 获取文件夹下文件
 * @var $path 路径
 * @var $ext 文件后缀 默认 NULL全部文件
 * @reutn array 文件类表数组
 */
function getDirFile($path,$ext = NULL) {
	//如果目录不存在返 False
	if(!file_exists($path)) return FALSE;
	$handler = opendir($path);
	$files = array();
	while ($filename = readdir($handler)) {
		$filePath = $path.DS.$filename;
		if(is_file($filePath)) {
			$fileInfo = explode('.', $filename);
			$fileExt = array_pop($fileInfo);	//获取后缀
			$fileName = implode('_', $fileInfo);
			//判断缀
			if($ext) {
				if($fileExt != $ext) continue;
			}
			$files[] = array(
						'name' => strtolower($fileName),
						'path' => $filePath
					);
		}
	}
	return count($files)?$files:FALSE;
}

//将数组转化为PHP文本
function arrayTostr($arr) {
 $tmp = "array(";
 foreach($arr as $key => $v){
  if(is_array($v)) {
   $tmp .= "'$key'=>";
   $tmp .= arrayTostr($v).",";
  } else
   $tmp .= "'$key' => '$v',";
 }
 $tmp = substr($tmp,0,-1);
 $tmp .= '),';
 $tmp = substr($tmp,0,-1);
 return $tmp;
}