<?php
namespace app\controller\v1;


use app\BaseController;
use app\Request;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use think\facade\Cache;
use Zhuxinyuang\common\Ip;


class Login extends BaseController
{





    /**
     * post: 登陆
     * path: login
     * method: user
     * param: username - {string} 用户名
     * param: password - {string} 密码
     */
    public function user()
    {




        $nowTime = time();
        $username = input('username');
        $password = input('password');


        //解密密码然后cmd5 加密
        $key = "1234567890654321";
        $iv = "1234567890123456";
        $password = openssl_decrypt(base64_decode($password), "AES-128-CBC", $key, OPENSSL_RAW_DATA, $iv);
        //验证变量是否符合要求
        $validate = new MemberValidate();
        $result = $validate->failException(false)->scene('user')->check(compact('username', 'password'));
        if (true !== $result) {
            return json(['code' => config('code.erro'), 'msg' => $validate->getError()]);
        }



        //获取当前用户信息以便核实
       $user = $this->getCache(config('code.member.info'),$username);

        //判断密码正确和禁止登陆
        if (empty($user) ) {
            $data['code'] = config('code.erro');
            $data['msg'] = '账号或密码错误';
            return json($data);
        }else if ($user['password'] != md5($password)) {
            $data['code'] = config('code.erro');
            $data['msg'] = '账号或密码错误';
            return json($data);
        } else if ($user['status'] == 2) {
            $data['code'] = config('code.erro');
            $data['msg'] = '账号禁止登录';
            return json($data);
        }

        //生成token
        $signer = new Sha256();
        //设置header和payload，以下的字段都可以自定义
        $Builder = (new Builder())->setIssuer('www.llgj.vip')//发布者
        ->setAudience('www.llgj.vip')//接收者
        ->setId("abc", true)//对当前token设置的标识
        ->setIssuedAt($nowTime)//token创建时间
        ->setExpiration($nowTime + config('code.login.expire'))//过期时间
        ->setNotBefore($nowTime)//当前时间在这个时间前，token不能使用
        ->set('uid', $user['id'])//自定义数据
        ->set('username', $user['username']) //自定义数据
        ->sign($signer,config('code.secret'))//设置签名
        ->getToken();
        $token['token'] = (string) $Builder;//获取加密后的token，转为字符串



        //更新用户一些信息到缓存 以便使用
        $user->token= md5($token['token']);
        //有动作就更新
//        $user['update_time'] = date('Y-m-d h:i:s', $nowTime);


        $user->allowField(['token'])->save();


        Cache::store('redis')->set(config('code.member.info').$username,$user,config('code.member.expire'));



        //登录信息写入记录
        $data = [
            'os' => get_os(),
            'browser' => get_broswer(),
            'msg' => '登入成功!',
            'uid' => $user['id'],
            'username' => $user['username'],
            'date' => date('Y-m-d', $nowTime),
            'ip' => request()->ip(),
            'address' => implode(",", Ip::find(request()->ip())),
        ];
        (new MemberLoginMsgModel())->data($data, true)->save();



        //获取过期时间
        $data = [
            'code' => config('code.succeed'),
            'token' => $token['token'],
            'exp' => $nowTime + config('code.member.expire'),
            'msg' => '登入成功!',
        ];
        return json($data);


    }
    /**
     * post: 注册
     * path: login
     * method: register
     * param: username - {string} 用户名
     * param: password - {string} 密码
     */
    public function register(){



    }



    private function getCache(string $keys,string $username)
    {
        $result = Cache::store('redis')->get($keys . $username);

        if ($result == null) {
            $result =  (new MemberModel())->where(['username' => $username])->find();
            if ($result != null) {
                //不是空的值就缓存
                Cache::store('redis')->set($keys.$username, $result,config('code.member.expire'));


            }
        }
        return $result;
    }




}