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