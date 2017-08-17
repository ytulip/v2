<?php
include app('path') . "/../../../libs/Nessql/core.libs.php";
include app('path') . "/../../../libs/Easyapi/func.check.php";
include app('path') . "/../../../tool/decrypt.php";
//include_once("../../../libs/Nessql/core.libs.php");
//include_once("../../../libs/Easyapi/func.check.php");
//include_once("../../../tool/decrypt.php");

//global  $tbl_rooms = "rooms";
//global  $tbl_user = "user";
//global  $tbl_gift = "gift";
//global  $tbl_gift_record = "gift_record";

// 必传参数
// $param = array("gift_id", "room_id", "audio_set", "user_id");
$param = array("key");
// 必传参数 end

check_param($param, false, function($p){
    $tbl_rooms = "rooms";
    $tbl_user = "user";
    $tbl_gift = "gift";
    $tbl_gift_record = "gift_record";

    // 解析参数
    $p = decrypt_bak($p->key);
    // 解析参数 end

    // 排除打赏给自己
    if ($p['user_id'] == $p['zhuobo_id']) {
        ResultBean::with(503, "can't reward to yourself")->dieJson();
    }
    // 排除打赏给自己 end

    $user = mysql_fetch_arr($tbl_user, array("*"), "id={$p['user_id']}");
    if(!$user){
        ResultBean::with(501, "no user info")->dieJson();
    }else{
        $user = $user[0];
    }

    $zhuboUser = mysql_fetch_arr($tbl_user, array("*"), "id={$p[zhubo_id]}");
    if(!$zhuboUser){
        ResultBean::with(501, "no user info")->dieJson();
    }else{
        $zhuboUser = $zhuboUser[0];
    }

    $gift = mysql_fetch_arr($tbl_gift, array("*"), "id={$p[gift_id]}");
    if(!$gift){
        ResultBean::with(502, "no gift info")->dieJson();
    }else{
        $gift = $gift[0];
    }

    $giftCount = intval($p['count']);
    if(!($giftCount > 0)) {
        ResultBean::with(502, "no gift info")->dieJson();
    }
    $giftPrice = $gift->gift_price * $giftCount;

    if($user->volley >= $giftPrice){
        mysql_update_dic($tbl_user, array("volley"=>($user->volley - $giftPrice),"has_used_volley"=>$user->has_used_volley + $giftPrice), "id={$p[user_id]}");

        //TODO:用户等级跃迁
        $config = mysql_fetch_arr("sys_config",array("*"),"id=4")[0];
        $confgValue = json_decode($config->config_value);

        $levelTotal = $user->has_used_volley + $giftPrice;
        $vip_level = 0;
        foreach ( $confgValue as $item ) {
            if($levelTotal >= $item->volley ) {
                $vip_level = $item->level;
                continue;
            }
            break;
        }
        mysql_update_dic($tbl_user, array('vip_level'=>$vip_level), "id={$p[user_id]}");

        // 记录到cash_flow表
        $detail = "打赏礼物(".$gift->gift_name."){$giftCount}个给主播(".$zhuboUser->name.")花费".$giftPrice."忙豆";
        $sql = "INSERT INTO cash_flow (userid,cash,detail) VALUES (".$p[user_id].",".$giftPrice.",'".$detail."')";
        mysql_query_sql($sql);
        // 记录到cash_flow表 end


        // 更新主播余额
        $favorPrice = $giftPrice * 100;
        $sql = "UPDATE user set volley=volley+".$giftPrice.",favor=favor + {$favorPrice} ,favor_gift= favor_gift + {$favorPrice} where id=".$zhuboUser->id."";
        mysql_query_sql($sql);
        // 更新主播余额 end

        // 记录到cash_flow表
        $detail = "获得".$user->name."打赏的礼物(".$gift->gift_name."){$giftCount}个".$giftPrice."忙豆";
        $type = 2;
        $sql = "INSERT INTO cash_flow (userid,cash,detail,type) VALUES (".$zhuboUser->id.",".$giftPrice.",'".$detail."',".$type.")";
        mysql_query_sql($sql);
        // 记录到cash_flow表 end

        mysql_insert_dic($tbl_gift_record, array(
            "gift_id"=>$p[gift_id],
            "to_user"=>$zhuboUser->id,
            "user_id"=>$p[user_id],
            "count"=>$giftCount
        ));
        ResultBean::with(200, "ok")->dieJson();
    }else{
        ResultBean::with(201, "volley insufficient")->dieJson();
    }
});
?>