<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->get('/', function () use ($app) {
    return $app->version();
});

$app->get('/app/version',function(){
    header("Access-Control-Allow-Origin: *");
    $versionModel = \App\Model\SysConfig::find(1);
    $pathModel = \App\Model\SysConfig::find(2);
    echo json_encode(['status'=>true,'data'=>['version'=>$versionModel->config_value,'path'=>$pathModel->config_value]]);
    exit;
});

$app->get('/cms/update_app',function(){
    include app('path') . '/segments/update_app.php';
});

$app->post('/cms/update_app',function(){
    /*
     * 处理post请求
     */
    include app('path') . '/segments/update_app.php';
});

$app->get('/cms/charge_set',function(){
    include app('path') . '/segments/charge_set.php';
});

$app->post('/cms/charge_set',function(){
    /*
     * 处理post请求
     */
    include app('path') . '/segments/charge_set.php';
});

$app->get('/charge_set',function(){
    header("Access-Control-Allow-Origin: *");
    $versionModel = \App\Model\SysConfig::find(3);
    echo json_encode(['status'=>true,'data'=>['version'=>"充值1元RMB兑换". round($versionModel->config_value/100,2) ."忙豆，充值后不可提现，感谢您的支持"]],JSON_UNESCAPED_UNICODE);
    exit;
});


$app->get('/danmu_search',function(){
    header("Access-Control-Allow-Origin: *");
    $audioId = $_REQUEST['audio_id'];
    $res = \Illuminate\Support\Facades\DB::table('danmu')->join('user','user.id','=','danmu.user_id')->where('audio_id',$audioId)->selectRaw('images,name,audio_id,msg,user_id,send_second')->get();
    foreach ($res as $key=>$val) {
        $res[$key]->text = '<img  src="'."http://{$_SERVER['HTTP_HOST']}/listenbook/disk/{$val->images}".'"/><div class="text-desc"><span class="name-label">'.((mb_strlen($val->name)>8)?(mb_substr($val->name,8) . '...'):$val->name).'</span><span>'.$val->msg.'</span></div>';
    }
    echo json_encode(['status'=>true,'data'=>$res],JSON_UNESCAPED_UNICODE);
    exit;
});


$app->post('/danmu',function(){
    header("Access-Control-Allow-Origin: *");
    $audioId = intval(isset($_REQUEST['audio_id'])?$_REQUEST['audio_id']:0);
    $userId = intval(isset($_REQUEST['user_id'])?$_REQUEST['user_id']:0);
    $msg = isset($_REQUEST['msg'])?$_REQUEST['msg']:'';

    $danmu = new \App\Model\Danmu();
    $danmu->user_id = $userId;
    $danmu->audio_id = $audioId;
    $danmu->send_second = $_REQUEST['time'];
    $danmu->msg = $msg;
    $danmu->save();
    $res = \Illuminate\Support\Facades\DB::table('danmu')->join('user','user.id','=','danmu.user_id')->where('audio_id',$audioId)->where('danmu.id',$danmu->id)->selectRaw('images,name,audio_id,msg,user_id,send_second')->get();
    foreach ($res as $key=>$val) {
        $res[$key]->text = '<img  src="'."http://{$_SERVER['HTTP_HOST']}/listenbook/disk/{$val->images}".'"/><div class="text-desc"><span class="name-label">'.((mb_strlen($val->name)>8)?(mb_substr($val->name,8) . '...'):$val->name).'</span><span>'.$val->msg.'</span></div>';
    }
    echo json_encode(['status'=>true,'data'=>$res],JSON_UNESCAPED_UNICODE);
    exit;
});


$app->post('/vip',function(){
    header("Access-Control-Allow-Origin: *");
    $userId = intval(isset($_REQUEST['user_id'])?$_REQUEST['user_id']:0);
    $price = floatval(isset($_REQUEST['price'])?$_REQUEST['price']:0);

    $user = \App\Model\User::find($userId);
    $config = \App\Model\SysConfig::find(4);
    $saleConfig = json_decode($config->config_value);

    $sale = 10;
    if(!$user->role) {
        foreach ($saleConfig as $item) {
            if ($item->level == $user->vip_level) {
                $sale = $item->sale;
                break;
            }
        }
    }


    echo json_encode(['status'=>true,'data'=>['price'=>round($price * ($sale * 10 / 100),2)]],JSON_UNESCAPED_UNICODE);
    exit;
});

$app->post('/bang',function(){
    header("Access-Control-Allow-Origin: *");
    $selectStr = "id,name,images,favor,favor_cash,favor_gift";
   $normal = \Illuminate\Support\Facades\DB::table('user')->orderBy('favor','desc')->limit(50)->selectRaw($selectStr)->where('favor','>','0')->get();
   $cash = \Illuminate\Support\Facades\DB::table('user')->orderBy('favor_cash','desc')->limit(50)->where('favor_cash','>','0')->selectRaw($selectStr)->get();
   $gift = \Illuminate\Support\Facades\DB::table('user')->orderBy('favor_gift','desc')->limit(50)->where('favor_gift','>','0')->selectRaw($selectStr)->get();

   foreach ($normal as $key=>$val) {
       $normal[$key]->bang_price = $val->favor;
       $normal[$key]->bang_price_format = number_format($normal[$key]->bang_price);
       $normal[$key]->image_path = env('IMGURL') . $val->images;
   }

   foreach ($cash as $key=>$val) {
       $cash[$key]->bang_price = $val->favor_cash;
       $cash[$key]->bang_price_format = number_format($cash[$key]->bang_price);
       $cash[$key]->image_path = env('IMGURL') . $val->images;
   }

   foreach ($gift as $key=>$val) {
       $gift[$key]->bang_price = $val->favor_gift;
       $gift[$key]->bang_price_format = number_format($gift[$key]->bang_price);
       $gift[$key]->image_path = env('IMGURL') . $val->images;
   }

    echo json_encode(['status'=>true,'data'=>['normal'=>$normal,'cash'=>$cash,'gift'=>$gift]],JSON_UNESCAPED_UNICODE);
    exit;
});


$app->post('/give_zhubo_gift',function(){
    header("Access-Control-Allow-Origin: *");
    include app('path') . '/segments/give_gift.php';
});