<?php
// 应用公共文件

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件


/**
 * 字符串截取，支持中文和其他编码
 */
function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true)
{
    if (function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif (function_exists('iconv_substr')) {
        $slice = iconv_substr($str, $start, $length, $charset);
        if (false === $slice) {
            $slice = '';
        }
    } else {
        $re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("", array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice . '...' : $slice;
}


/**
 * 读取配置
 * @return array
 */
function load_config()
{
    $list = (new \app\index\model\ConfigModel())->select();
    $config = [];
    foreach ($list as $k => $v) {
        $config[trim($v['name'])] = $v['value'];
    }
    return $config;
}


/**
 * 验证手机号是否正确
 * @param number $mobile
 * @author honfei
 */
function is_mobile($mobile)
{
    if (!is_numeric($mobile)) {
        return false;
    }
    return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
}


/**
 * 循环删除目录和文件
 * @param string $dir_name
 * @return bool
 */
function delete_dir_file($dir_name)
{
    $result = false;
    if (is_dir($dir_name)) {
        if ($handle = opendir($dir_name)) {
            while (false !== ($item = readdir($handle))) {
                if ($item != '.' && $item != '..') {
                    if (is_dir($dir_name . DIRECTORY_SEPARATOR . $item)) {
                        delete_dir_file($dir_name . DIRECTORY_SEPARATOR . $item);
                    } else {
                        unlink($dir_name . DIRECTORY_SEPARATOR . $item);
                    }
                }
            }
            closedir($handle);
            if (rmdir($dir_name)) {
                $result = true;
            }
        }
    }

    return $result;
}


//时间格式化1
function format_time($time)
{
    $now_time = time();
    $t = $now_time - $time;
    $mon = (int)($t / (86400 * 30));
    if ($mon >= 1) {
        return '一个月前';
    }
    $day = (int)($t / 86400);
    if ($day >= 1) {
        return $day . '天前';
    }
    $h = (int)($t / 3600);
    if ($h >= 1) {
        return $h . '小时前';
    }
    $min = (int)($t / 60);
    if ($min >= 1) {
        return $min . '分钟前';
    }
    return '刚刚';
}


//时间格式化2
function pinche_time($time)
{
    $today = strtotime(date('Y-m-d')); //今天零点
    $here = (int)(($time - $today) / 86400);
    if ($here == 1) {
        return '明天';
    }
    if ($here == 2) {
        return '后天';
    }
    if ($here >= 3 && $here < 7) {
        return $here . '天后';
    }
    if ($here >= 7 && $here < 30) {
        return '一周后';
    }
    if ($here >= 30 && $here < 365) {
        return '一个月后';
    }
    if ($here >= 365) {
        $r = (int)($here / 365) . '年后';
        return $r;
    }
    return '今天';
}


function get_random_string($len, $chars = null)
{
    if (is_null($chars)) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    }
    mt_srand(10000000 * (double)microtime());
    for ($i = 0, $str = '', $lc = strlen($chars) - 1; $i < $len; $i++) {
        $str .= $chars[mt_rand(0, $lc)];
    }
    return $str;
}


function random_str($length)
{
    //生成一个包含 大写英文字母, 小写英文字母, 数字 的数组
    $arr = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));

    $str = '';
    $arr_len = count($arr);
    for ($i = 0; $i < $length; $i++) {
        $rand = mt_rand(0, $arr_len - 1);
        $str .= $arr[$rand];
    }

    return $str;
}


/**开奖数值个位数前面补0
 * @param $num
 * @return string
 */
function BuLings($num)
{
    if (strlen($num) <= 1) {
        $num = '0' . $num;
    }
    return $num;
}


/**获得彩票期数
 * @param $time
 * @param $type
 * @param $list
 * @return float|int|string|array
 */

function get_type_number($time, $type, $list)
{

    if ($type == '50') {
        //北京赛车PK拾
        $action_no = 44 * ((strtotime(date('Y-m-d', $time)) - strtotime('2019-2-11')) / 3600 / 24) + $list['action_no'] + 729391;
    } elseif ($type == '55') {
        //幸运飞艇
        $list['action_no'] = (new \Zhuxinyuang\common\Xyft())->BuLings($list['action_no']);

        if ($list['action_no'] >= 132) {
            $action_no = date('Ymd', strtotime('-1 day', $time)) . $list['action_no'];
        } else {
            $action_no = date('Ymd', $time) . $list['action_no'];

        }
    } elseif ($type == '99') {
        //极速赛车
        $action_no = ((strtotime(date('Y-m-d', $time)) - strtotime('2017-6-16')) / 3600 / 24 - 1) * 1152 + ($list['action_no'] + 30264272);
    } elseif ($type == '1') {
        //重庆时时彩
        $action_no = date('Ymd', $time) . (new \Zhuxinyuang\common\Cqssc())->BuLings($list['action_no']);

    } elseif ($type == '70') {
        //香港六合彩
        $action_no = $list['action_no'];
    } elseif ($type == '77') {
        //私人彩种
        $action_no = date('Ymd', $time) . $list['action_no'];
    } elseif ($type == '88') {
        //私人彩种
        $action_no = date('Ymd', $time) . $list['action_no'];
    }

    return $action_no;
}

/** 获得封盘时间 缓存时间600秒
 * @param $time系统时间
 * @param $type彩票类型
 * @return $array返回是数组
 */
function get_action_time($time, $type)
{
    //获得缓存中的开奖时间


    $actionlist = \think\facade\Cache::store('redis')->get('action_list_' . $type);
    if (!$actionlist) {
        $map[] = ['type', '=', $type];
        //判断是否是香港六合彩
        if ($type == '70') {
            $actionlist = (new \app\index\model\XglhcTimeModel())->where($map)->order('action_no asc')->select();
        } else {
            //其他彩票
            $actionlist = (new \app\index\model\LotteryTimeModel())->where($map)->order('action_no asc')->select();
        }
        \think\facade\Cache::store('redis')->set('action_list_' . $type, $actionlist, '600');
    }
    foreach ($actionlist as $key => $value) {
        if ($time >= strtotime($value['action_time']) && $time <= strtotime($value['stop_time'])) {
            $list = $value;
            break;
        }
    }

    return $list;


}

/** 获得彩种赔率
 * @param $type
 * @throws \think\db\exception\DataNotFoundException
 * @throws \think\db\exception\ModelNotFoundException
 * @throws \think\exception\DbException
 */
function get_odds_list($type)
{

    //赔率列表整理
    $oddslist = \think\facade\Cache::store('redis')->get('oddslist_' . $type);
    if (!$oddslist) {
        $oddslist = [];
        $odds = (new \app\index\model\OddsModel())->where(['type' => $type])->select();
        foreach ($odds as $key => $value) {
            $oddslist[$value['id']] = $value;
        }
        \think\facade\Cache::store('redis')->set('oddslist_' . $type, $oddslist, '600');
    }

    return $oddslist;
}

//获得最新开奖号码
function get_auto_find($type)
{
    if ($type == '1') {
        $automodel = new \app\index\model\CqsscAutoModel();
    } else if ($type == '50') {
        $automodel = new \app\index\model\BjscAutoModel();
    } else if ($type == '55') {
        $automodel = new \app\index\model\XyftAutoModel();
    } else if ($type == '70') {
        $automodel = new \app\index\model\XglhcAutoModel();
    } else if ($type == '99') {
        $automodel = new \app\index\model\JsscAutoModel();
    }


    $list = \think\facade\Cache::store('redis')->get('auto_' . $type);
    if (!$list) {
        $list = $automodel->order('number desc')->field('number,data')->find();
        \think\facade\Cache::store('redis')->set('auto_' . $type, $list);
    }
    return $list;

}


/**距离两者时间小时 THINKPHP
 * @param $start
 * @param $end
 * @return string
 */
function prDates($start, $end)
{
    $dt_start = strtotime($start);
    $dt_end = strtotime($end);
    $ymdh = '';
    while ($dt_start <= $dt_end) {
        $ymdh .= $ymdh ? "," . date('Y-m-d', $dt_start) : date('Y-m-d', $dt_start);
        $dt_start = strtotime('+1 day', $dt_start);
    }
    return $ymdh;
}

/**距离两者时间小时
 * @param $start
 * @param $end
 * @return string
 */
function prDatess($start, $end)
{
    $dt_start = strtotime($start);
    $dt_end = strtotime($end);
    $ymdh = '';
    while ($dt_start <= $dt_end) {
//        echo date('Y-m-d',$dt_start)."\n";
        $ymdh .= $ymdh ? "," . "'" . date('Y-m-d', $dt_start) . "'" : "'" . date('Y-m-d', $dt_start) . "'";
        $dt_start = strtotime('+1 day', $dt_start);
    }
    return $ymdh;
}

/**
 * 获取唯一编号
 */
function get_number()
{
    static $i = -1;
    $i++;
    $a = substr(date('YmdHis'), -12, 12);
    $b = sprintf("%02d", $i);
    if ($b >= 100) {
        $a += $b;
        $b = substr($b, -2, 2);
    }
    return $a . $b;
}

/**
 * php获取客户端浏览器信息
 * @param null
 * @return string
 */
function get_broswer()
{
    $user_OSagent = $_SERVER['HTTP_USER_AGENT'];
    if (strpos($user_OSagent, "Maxthon") && strpos($user_OSagent, "MSIE")) {
        $visitor_browser = "Maxthon(Microsoft IE)";
    } elseif (strpos($user_OSagent, "Maxthon 2.0")) {
        $visitor_browser = "Maxthon 2.0";
    } elseif (strpos($user_OSagent, "Maxthon")) {
        $visitor_browser = "Maxthon";
    } elseif (strpos($user_OSagent, "Edge")) {
        $visitor_browser = "Edge";
    } elseif (strpos($user_OSagent, "Trident")) {
        $visitor_browser = "IE";
    } elseif (strpos($user_OSagent, "MSIE")) {
        $visitor_browser = "IE";
    } elseif (strpos($user_OSagent, "MSIE")) {
        $visitor_browser = "MSIE";
    } elseif (strpos($user_OSagent, "NetCaptor")) {
        $visitor_browser = "NetCaptor";
    } elseif (strpos($user_OSagent, "Netscape")) {
        $visitor_browser = "Netscape";
    } elseif (strpos($user_OSagent, "Chrome")) {
        $visitor_browser = "Chrome";
    } elseif (strpos($user_OSagent, "Lynx")) {
        $visitor_browser = "Lynx";
    } elseif (strpos($user_OSagent, "Opera")) {
        $visitor_browser = "Opera";
    } elseif (strpos($user_OSagent, "MicroMessenger")) {
        $visitor_browser = "WeiXinBrowser";
    } elseif (strpos($user_OSagent, "Konqueror")) {
        $visitor_browser = "Konqueror";
    } elseif (strpos($user_OSagent, "Mozilla/5.0")) {
        $visitor_browser = "Mozilla";
    } elseif (strpos($user_OSagent, "Firefox")) {
        $visitor_browser = "Firefox";
    } elseif (strpos($user_OSagent, "U")) {
        $visitor_browser = "Firefox";
    } else {
        $visitor_browser = "Other Browser";
    }
    return $visitor_browser;


//    $sys = $_SERVER['HTTP_USER_AGENT']; //获取用户代理字符串
//    if (stripos($sys, "Firefox/") > 0) {
//        preg_match("/Firefox\/([^;)]+)+/i", $sys, $b);
//        $exp[0] = "Firefox";
//        $exp[1] = $b[1]; //获取火狐浏览器的版本号
//
//    } elseif (stripos($sys, "Maxthon") > 0) {
//        preg_match("/Maxthon\/([\d\.]+)/", $sys, $aoyou);
//        $exp[0] = "傲游";
//        $exp[1] = $aoyou[1];
//    } elseif (stripos($sys, "MSIE") > 0) {
//        preg_match("/MSIE\s+([^;)]+)+/i", $sys, $ie);
//        $exp[0] = "IE";
//        $exp[1] = $ie[1]; //获取IE的版本号
//
//    } elseif (stripos($sys, "OPR") > 0) {
//        preg_match("/OPR\/([\d\.]+)/", $sys, $opera);
//        $exp[0] = "Opera";
//        $exp[1] = $opera[1];
//    } elseif (stripos($sys, "Edge") > 0) {
//        //win10 Edge浏览器 添加了chrome内核标记 在判断Chrome之前匹配
//        preg_match("/Edge\/([\d\.]+)/", $sys, $Edge);
//        $exp[0] = "Edge";
//        $exp[1] = $Edge[1];
//    } elseif (stripos($sys, "Chrome") > 0) {
//        preg_match("/Chrome\/([\d\.]+)/", $sys, $google);
//        $exp[0] = "Chrome";
//        $exp[1] = $google[1]; //获取google chrome的版本号
//
//    } elseif (stripos($sys, 'rv:') > 0 && stripos($sys, 'Gecko') > 0) {
//        preg_match("/rv:([\d\.]+)/", $sys, $IE);
//        $exp[0] = "IE";
//        $exp[1] = $IE[1];
//    } else {
//        $exp[0] = "未知浏览器";
//        $exp[1] = "";
//    }
//    return $exp[0] . '(' . $exp[1] . ')';
}

/**
 * php获取客户端操作系统信息
 * @param null
 * @return string
 */
function get_os($agent = '')
{
    $agent = empty($agent) ? $_SERVER['HTTP_USER_AGENT'] : $agent;
    $os = false;
    if (preg_match('/win/i', $agent) && strpos($agent, '95')) {
        $os = 'Windows 95';
    } else if (preg_match('/win 9x/i', $agent) && strpos($agent, '4.90')) {
        $os = 'Windows ME';
    } else if (preg_match('/win/i', $agent) && preg_match('/98/i', $agent)) {
        $os = 'Windows 98';
    } else if (preg_match('/win/i', $agent) && preg_match('/nt 6.0/i', $agent)) {
        $os = 'Windows Vista';
    } else if (preg_match('/win/i', $agent) && preg_match('/nt 6.1/i', $agent)) {
        $os = 'Windows 7';
    } else if (preg_match('/win/i', $agent) && preg_match('/nt 6.2/i', $agent)) {
        $os = 'Windows 8';
    } else if (preg_match('/win/i', $agent) && preg_match('/nt 10.0/i', $agent)) {
        $os = 'Windows 10'; //添加win10判断
    } else if (preg_match('/win/i', $agent) && preg_match('/nt 5.1/i', $agent)) {
        $os = 'Windows XP';
    } else if (preg_match('/win/i', $agent) && preg_match('/nt 5/i', $agent)) {
        $os = 'Windows 2000';
    } else if (preg_match('/win/i', $agent) && preg_match('/nt/i', $agent)) {
        $os = 'Windows NT';
    } else if (preg_match('/win/i', $agent) && preg_match('/32/i', $agent)) {
        $os = 'Windows 32';
    } else if (preg_match('/linux/i', $agent)) {
        $os = 'Linux';
    } else if (preg_match('/unix/i', $agent)) {
        $os = 'Unix';
    } else if (preg_match('/sun/i', $agent) && preg_match('/os/i', $agent)) {
        $os = 'SunOS';
    } else if (preg_match('/ibm/i', $agent) && preg_match('/os/i', $agent)) {
        $os = 'IBM OS/2';
    } else if (preg_match('/Mac/i', $agent) && preg_match('/PC/i', $agent)) {
        $os = 'Macintosh';
    } else if (preg_match('/PowerPC/i', $agent)) {
        $os = 'PowerPC';
    } else if (preg_match('/AIX/i', $agent)) {
        $os = 'AIX';
    } else if (preg_match('/HPUX/i', $agent)) {
        $os = 'HPUX';
    } else if (preg_match('/NetBSD/i', $agent)) {
        $os = 'NetBSD';
    } else if (preg_match('/BSD/i', $agent)) {
        $os = 'BSD';
    } else if (preg_match('/OSF1/i', $agent)) {
        $os = 'OSF1';
    } else if (preg_match('/IRIX/i', $agent)) {
        $os = 'IRIX';
    } else if (preg_match('/FreeBSD/i', $agent)) {
        $os = 'FreeBSD';
    } else if (preg_match('/teleport/i', $agent)) {
        $os = 'teleport';
    } else if (preg_match('/flashget/i', $agent)) {
        $os = 'flashget';
    } else if (preg_match('/webzip/i', $agent)) {
        $os = 'webzip';
    } else if (preg_match('/offline/i', $agent)) {
        $os = 'offline';
    } else if (preg_match('/iPhone/i', $agent)) {
        $os = 'iPhone';
    } else if (preg_match('/iPad/i', $agent)) {
        $os = 'iPad';
    } else {
        $os = '未知操作系统';
    }
    return $os;
}

//判断是否是Ip
function isOk_ip($ip)
{
    if (preg_match('/^((?:(?:25[0-5]|2[0-4]\d|((1\d{2})|([1-9]?\d)))\.){3}(?:25[0-5]|2[0-4]\d|((1\d{2})|([1 -9]?\d))))$/', $ip)) {
        return 1;
    } else {
        return 0;
    }


}


/**帐变信息
 * @param $uid   帐变的ID
 * @param $username  帐变用户名
 * @param $type      帐变状态
 * @param $money     帐变金额
 * @param $cause_type  帐变后的金额
 * @param $info      帐变附属信息
 * @param $date      年月日
 * @return bool
 */
function money_log($uid, $username, $type, $money, $cause_type, $info, $date = '')
{

//    dump($date);
//    $map['uid']=$uid;
//    $map['username']=$username;
//    $map['type']=$type;
//    $map['money']=$money;
//
//
//        $map['date']=$date;
//
//
//    $map['cause_type']=$cause_type;
//    $map['info']=$info;

    $moneymodel = new \app\index\model\MoneyLogModel();


    $moneymodel->uid = $uid;
    $moneymodel->username = $username;
    $moneymodel->type = $type;
    $moneymodel->money = $money;
    $moneymodel->member_money = (new \app\index\model\MemberModel())->where(['id' => $uid])->value('money');
    $moneymodel->cause_type = $cause_type;
    $moneymodel->info = $info;

    $moneymodel->date = $date ? $date : date('Y-m-d');

    delete_member_cache($username);

    return $moneymodel->save();

}

/*
 * 删除用户缓存
 */
function delete_member_cache($username)
{

    \think\facade\Cache::store('redis')->rm(config('code.member') . $username);
}



//function swoole_exit($msg)
//{
//    //php-fpm的环境
//
//    if (php_sapi_name()=='fpm-fcgi'|| php_sapi_name()=='cgi-fcgi')
//    {
//        exit($msg);
//    }
//    //swoole的环境
//    else
//    {
//        throw new Swoole\ExitException($msg);
//    }
//}