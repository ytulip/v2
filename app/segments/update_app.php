<?php
header("Content-type: text/html; charset=utf-8");
session_start();
if(!isset($_SESSION['admin'])){
    die("非法操作");
}
?>

<?php
  if(isset($_REQUEST['version'])) {
      $versionModel = \App\Model\SysConfig::find(1);
      $versionModel->config_value = $_REQUEST['version'];
      $versionModel->save();


      $firstKey = '';
      foreach ($_FILES as $key => $val) {
          $firstKey = $key;
          break;
      }
      if($firstKey !== '') {
          move_uploaded_file($_FILES[$firstKey]['tmp_name'], storage_path() . '/../public/apk/' . $_FILES[$firstKey]['name']);
          $versionModel = \App\Model\SysConfig::find(2);
          $versionModel->config_value = 'http://' . $_SERVER['HTTP_HOST'] . '/listenbook/apis/v2/public/apk' . $_FILES[$firstKey]['name'];
          $versionModel->save();
      }
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
      <p>当前版本:<span><?php echo \App\Model\SysConfig::find(1)->config_value?></span></p>
      <p>apk下载路径:<span><?php echo \App\Model\SysConfig::find(2)->config_value?></span></p>
       <br/>
       <br/>
      <p>上传最新版本</p>
        <form method="post" enctype="multipart/form-data">
            <p>版本号:<input type="text" name="version"/></p>
            <p>apk包上传:<input type="file" name="path"/></p>
            <button style="display: inline-block;line-height: 27px;padding: 0 8px;background-color: #8c8c8c;cursor: pointer;">修改</button>
        </form>
    </div>

</div>
</body>
</html>