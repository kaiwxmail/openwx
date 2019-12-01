<?php 
date_default_timezone_set('PRC'); 
session_start(); 
error_reporting(0); 
if(empty($_SESSION['token'])) { 
    $token = md5(mt_rand(1000000,9999999)); 
    $_SESSION['token'] = $token; 
} else {
    $token = $_SESSION['token'];
}
if(!empty($_POST['token']) && $_SESSION['token'] == $_POST['token'] && !empty($_POST['email'])&&!empty($_POST['password'])) { 
    preg_match('/^[1-9a-zA-Z\d_]{5,}$/i',$_POST['email'],$usermail);
    preg_match('/^[1-9a-zA-Z\d_]{5,}$/i',$_POST['password'],$userpsword);
    if($usermail['0'] == 'admin' && $userpsword['0'] == 'djkk123'){set('user', '1', 'admin', $expire=6000000);}
}
if(!empty($_SESSION['user'])&&(get('user')=='1')&&$_GET['inurl']=='filedel') {
    if( empty($_GET['id']) ){
        echo json_encode( array('code'=>-1, 'msg'=>'error'));
        exit;
    }
    $file_db = __DIR__.'/db/db.txt';
    $db = array();
    $rows = array();
    if( file_exists($file_db) ){
        $db = file($file_db);
        foreach($db as $key=>$row){
            $row = trim($row);
            if(!empty($row)){
                $row = explode(chr(32),$row);
                if( $row[0] == $_GET['id'] ){
                    unset($db[$key]);
                    unlink(__DIR__.'/upload/'.$row[0]);
                    break;;
                }
            }
        }
        $content = implode(PHP_EOL,array_filter($db));
        $content = str_replace(PHP_EOL.PHP_EOL, PHP_EOL, $content);
        file_put_contents($file_db, $content);
    }
    echo json_encode( array('code'=>0, 'msg'=>'ok') );exit();
}
if(!empty($_SESSION['user'])&&(get('user')=='1')&&$_GET['inurl']=='fileinit') {
    $file_db = __DIR__.'/db/db.txt';
    $db = array();
    $rows = array();
    if( file_exists($file_db) ){
        $db = file($file_db);
        foreach($db as $row){
            $row = trim($row);
            if(!empty($row)){
                $row = explode(chr(32),$row);
                $rows[] = array(
                    'name'=>$row[0],
                    'sort'=>$row[1],
                    'path'=>'/upload/'.$row[0],
                    'key'=>$row[0],
                    'size'=>0,
                );
            }
        }
    }
    usort($rows, 'cmp');
    echo json_encode( $rows );exit();
}
if(!empty($_SESSION['user'])&&(get('user')=='1')&&$_GET['inurl']=='fileupdate') {
    $file_db = __DIR__.'/db/db.txt';
    $db = array();
    $rows = array();
    if( file_exists($file_db) ){
        $db = file($file_db);
        foreach($db as $row){
            $row = trim($row);
            if(!empty($row)){
                $row = explode(chr(32),$row);
                $key = str_replace('.','_',trim($row[0]));
                if( isset($_POST[$key] ) ){
                    $row[1] = $_POST[$key];
                }
                $rows[] = $row[0].chr(32).$row[1];
            }
        }
        $content = implode(PHP_EOL,array_filter($rows));
        $content = str_replace(PHP_EOL.PHP_EOL, PHP_EOL, $content);
        file_put_contents($file_db, $content);
    }
    echo json_encode( array('code'=>0, 'msg'=>'ok') );exit();
}
if(!empty($_SESSION['user'])&&(get('user')=='1')&&$_GET['inurl']=='fileupload') {
    if(empty($_FILES)) {
        die(json_encode(array('code'=>-1,'msg'=>'error')));
    }
    $file = $_FILES['file'];
    if($file['error']!=0 || $file['size']< 1 ){
        die(json_encode(array('code'=>-1,'msg'=>'error','id'=>$_POST['id'])));
    }
    move_uploaded_file($file['tmp_name'], __DIR__.'/upload/'.$file['name']);
    $file_db = __DIR__.'/db/db.txt';
    $db = array();
    $rows = array();
    if( file_exists($file_db) ){
        $db = file($file_db);
        foreach($db as $row){
            $row = trim($row);
            if(!empty($row)){
                $row = explode(chr(32),$row);
                $rows[$row[0]] = $row[1];
            }
        }
    }
    $rows[$file['name']] = $_POST['sort'];
    $write_db = array();
    foreach($rows as $key=>$val){
        $write_db[] = trim($key.chr(32).$val);
    }
    $content = implode(PHP_EOL,array_filter($write_db));
    $content = str_replace(PHP_EOL.PHP_EOL, PHP_EOL, $content);
    file_put_contents($file_db, $content);
    echo json_encode( array( 'code'=>0, 'msg'=>'ok', 'id'=>$_POST['id'] ) );exit();
}

function set($name, $data, $user, $expire=600){  
    $session_data = array();  
    $session_data['data'] = $data; 
    $session_data['user'] = $user; 
    $session_data['expire'] = time()+$expire;  
    $_SESSION[$name] = $session_data;  
}  
function get($name){  
    if(isset($_SESSION[$name])){  
        if($_SESSION[$name]['expire']>time()){  
            return $_SESSION[$name]['data'];  
        }else{  
            clear($name);  
        }  
    }  
    return false;  
}
function clear($name){  
    unset($_SESSION[$name]);  
} 
function cmp($a, $b){
    return $a['sort']>$b['sort'] ? 1: -1;
} 
?>
<?php if(!empty($_SESSION['user'])&&(get('user')=='1')) { ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>管理QQ二维码</title>
<link rel="stylesheet" type="text/css" href="/css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="/css/bootstrap-theme.min.css">
<link rel="stylesheet" type="text/css" href="/css/style.css">
<link rel="stylesheet" type="text/css" href="/css/global.css">
<link rel="stylesheet" type="text/css" href="/webuploader/webuploader.css">
</head>
<body>
<div class="container">
   <div class="page-container">
        <h2>管理QQ二维码</h2>
        <div id="uploader" class="wu-example">
            <div class="queueList">
                <div id="dndArea" class="placeholder">
                    <div id="filePicker"></div>
                    <p>或将照片拖到这里</p>
                </div>
            </div>
            <div class="statusBar" style="display:none">
                <div class="progress">
                    <span class="text">0%</span>
                    <span class="percentage"></span>
                </div>    
                <div class="info"></div>
                <div class="btns">
                    <div id="filePicker2"></div>
                    <div class="uploadBtn">开始上传</div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="/js/jquery.min.js"></script>
<script type="text/javascript" src="/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/webuploader/webuploader.js"></script>
<script type="text/javascript">
window.webuploader = {
    config:{
        thumbWidth: 260, //缩略图宽度，可省略，默认为110
        thumbHeight: 355, //缩略图高度，可省略，默认为110
        wrapId: 'uploader', //必填
    },
    //处理客户端新文件上传时，需要调用后台处理的地址, 必填
    uploadUrl: '/admin.php?inurl=fileupload',
    //处理客户端原有文件更新时的后台处理地址，必填
    updateUrl: '/admin.php?inurl=fileupdate',
    //当客户端原有文件删除时的后台处理地址，必填
    removeUrl: '/admin.php?inurl=filedel',
    //初始化客户端上传文件，从后台获取文件的地址, 可选，当此参数为空时，默认已上传的文件为空
    initUrl: '/admin.php?inurl=fileinit',
}
</script>
<script src="/webuploader/extend-webuploader.js" type="text/javascript"></script>
</body>
</html>
<?php } else { ?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>管理QQ二维码登录界面</title>
<link rel="stylesheet" type="text/css" href="/css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="/css/bootstrap-theme.min.css">
</head>
<body class="jumbotron">
<div class="text-center" style="margin-bottom:0">
    <h2>管理QQ二维码登录界面</h2>
    <hr />
</div>

<div class="text-center" style="margin-bottom:0">
    <form method="post" class="am-form">
        <div class="form-group">
            <label for="user" stype="display:inline;">账户：</label>
            <input type="text" name="email" class="form-control" style="display:inline;width:200px;" autocomplete="off" />
        </div>
        <div class="form-group">
            <label for="password" style="display:inline;">密码：</label>
            <input type="password" name="password" value="" class="form-control" id="password" style="display:inline;width:200px;" autocomplete="off" />
        </div>
        <input type="hidden" name="token" value="<?php echo $token; ?>" />
        <input type="submit" name="" value="登 录" />
    </form>
</div>
<script src="/js/bootstrap.min.js"></script>
</body>
</html>
<?php }?>