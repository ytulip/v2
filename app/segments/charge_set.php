<?php
header("Content-type: text/html; charset=utf-8");
session_start();
if(!isset($_SESSION['admin'])){
    die("非法操作");
}
?>

<?php
if(isset($_REQUEST['percent'])) {
    $versionModel = \App\Model\SysConfig::find(3);
    $versionModel->config_value = $_REQUEST['percent'];
    $versionModel->save();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=8">
    <meta http-equiv="Expires" content="0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Cache-control" content="no-cache">
    <meta http-equiv="Cache" content="no-cache">

    <title>用户管理</title>

    <link href="../../../../cms/css/common.css" rel="stylesheet" />
    <script type="text/javascript" src="../../../../cms/js/jquery.min.js"></script>
</head>


<body>
<div class="flex-row">

    <?php include("../../../cms/common_left.php"); ?>
    <div class="content">
        <p>当前充值100元相当于充值忙豆<span><?php echo \App\Model\SysConfig::find(3)->config_value?></span>个</p>
        <br/>
        <br/>
        <p>上传最新版本</p>
        <form method="post" enctype="multipart/form-data">
            <p>修改充值100元相当于充值忙豆<input type="text" name="percent"/>个</p>
            <button style="display: inline-block;line-height: 27px;padding: 0 8px;background-color: #8c8c8c;cursor: pointer;">修改</button>
        </form>
    </div>

</div>
</body>
</html>