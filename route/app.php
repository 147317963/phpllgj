<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;

Route::get('think', function () {
    return 'hello,ThinkPHP6!';
});

Route::get('hello/:name', 'index/hello');

Route::group('', function () {
    Route::get('/getOdds','/Odds/getOdds');
})->allowCrossDomain(['Access-Control-Allow-Origin'=>'http://127.0.0.7','Access-Control-Allow-Credentials'=>'true']);



Route::get('/DeleteOdds', '/DeleteCache/deleteOdds'); //删除赔率缓存