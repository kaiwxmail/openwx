<?php 
$url = 'extrusionmould.com'; //必须填写顶级域名
ini_set('session.cookie_domain', '.'.$url);
session_start();
error_reporting(0);
$httphot = substr($_SERVER["HTTP_HOST"], '-'.mb_strlen($url,'UTF8'));
if($httphot !=$url){exit("域名来路非法！");}
$id = @file_get_contents(__DIR__.'/db/id.txt');
file_put_contents(__DIR__.'/db/qq.txt', implode(PHP_EOL, get_filenamesbydir('upload')));
$qq = file(__DIR__.'/db/qq.txt');
if(!isset($_SESSION['ip'])){
    if(empty($id)){$id = 1;}if($id>count($qq)){$id = 1;}
    $data = serialize(array(md5($_SERVER['HTTP_USER_AGENT'].get_server_ip()),trim($id-1)));
    file_put_contents(__DIR__.'/db/id.txt', $id+1);
    $ip = $_SESSION['ip'] = $data;
} else {
    $ip = $_SESSION['ip'];
}
$uin = unserialize($ip);
if(empty($qq[$uin['1']])) {
    $data = serialize(array(md5($_SERVER['HTTP_USER_AGENT'].get_server_ip()),mt_rand(0,count($qq)-1)));
    $ip = $_SESSION['ip'] = $data;
    $uin = unserialize($ip);
    file_put_contents(__DIR__.'/db/id.txt', count($qq));
}
$html = '<!DOCTYPE html><html lang="zh-CN"><head><meta charset="UTF-8" /><meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=2.0, user-scalable=yes" /><title>QQ加好友</title><style type="text/css">p{color:#a3a3a3;font-size:18px;}div#footer{text-align:center}h4{margin:5px 10px 0px 25px;}#nickname p{margin-left:25px;}#nickname {margin-top:45px;margin-left:20px;}h4{font-size:22px;}img#sex{position: relative;left: 3px;top: 2px;}div.center{text-align:center;margin: 0 auto;}</style></head><body><div class="center"><img id="emw" src="'.trim($qq[$uin['1']]).'" width="80%" border="0" /></div><div id="footer"><a class="cp" href="#" style="text-decoration:none;color:blue;"><h4>长按二维码添加好友</h4></a><p><font color="#FF0000">温馨提示：联系导师QQ领取58元红包哦！</font></p></div></body></html>';
$html = str_replace('%',' ',getEscape($html));
$data = 'A'.getRandChar();
echo '<script>function '.$data.'('.$data.'){document.write((unescape('.$data.')));};'.$data.'("'.$html.'".replace(/ /g,\'%\'));</script>';
function get_server_ip() {
    if (isset($_SERVER['SERVER_NAME'])) {
        return gethostbyname($_SERVER['SERVER_NAME']);
    } else {
        if (isset($_SERVER)) {
            if (isset($_SERVER['SERVER_ADDR'])) {
                $server_ip = $_SERVER['SERVER_ADDR'];
            } elseif (isset($_SERVER['LOCAL_ADDR'])) {
                $server_ip = $_SERVER['LOCAL_ADDR'];
            }
        } else {
            $server_ip = getenv('SERVER_ADDR');
        }
        return $server_ip ? $server_ip : '获取不到服务器IP';
    }
}
function getEscape($string, $in_encoding = 'UTF-8',$out_encoding = 'UCS-2') { 
    $return = ''; 
    if (function_exists('mb_get_info')) { 
        for($x = 0; $x < mb_strlen ( $string, $in_encoding ); $x ++) { 
            $str = mb_substr ( $string, $x, 1, $in_encoding ); 
            if (strlen ( $str ) > 1) { 
                $return .= '%'.'u' . strtoupper ( bin2hex ( mb_convert_encoding ( $str, $out_encoding, $in_encoding ) ) ); 
            } else { 
                $return .= '%' . strtoupper ( bin2hex ( $str ) ); 
            } 
        } 
    } 
    return $return; 
}
function getRandChar($length = 8) {
    $str = null;
    $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
    $max = strlen($strPol) - 1;
    for($i=0; $i<$length; $i++){
        $str .= $strPol[rand(0,$max)];
    }
    return $str;
}
function get_allfiles($path,&$files) {
    if(is_dir($path)){
        $dp = dir($path);
        while ($file = $dp ->read()){
            if($file !="." && $file !=".."){
                get_allfiles($path."/".$file, $files);
            }
        }
        $dp ->close();
    }
    if(is_file($path)){
        $files[] =  $path;
    }
}
function get_filenamesbydir($dir){
    $files =  array();
    get_allfiles($dir,$files);
    return $files;
}
?>
