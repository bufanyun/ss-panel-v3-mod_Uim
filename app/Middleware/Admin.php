<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Services\Auth as AuthService;

class Admin
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {

        //$response->getBody()->write('BEFORE');
        $user = AuthService::getUser();
        if (!$user->isLogin) {
            $newResponse = $response->withStatus(302)->withHeader('Location', '/auth/login');
            return $newResponse;
        }

        if (!$user->isAdmin()) {
            $newResponse = $response->withStatus(302)->withHeader('Location', '/user');
            return $newResponse;
        }

        //非超管则需要验证路由权限
        if($user->id > 1){
            $this->AdminRoutes();
        }
        $response = $next($request, $response);
        return $response;
    }

    public function AdminRoutes(){
        $prefix = '/admin';
        //非超管的访问权限设置
        $whiteList = [
            $prefix,

            $prefix .'/detailed',
            $prefix .'/detailed/ajax',

            //卡密管理
            $prefix . '/km',
            $prefix . '/km/ajax',
            $prefix . '/km/create',
            $prefix . '/km/log',
            $prefix . '/km/log_ajax',
            $prefix . '/km/txt',
//            $prefix . '/km/create',

            //帮助中心
            $prefix .'/help',

            '/user',
            //站点配置
            $prefix .'/config',
            $prefix .'/config/edit_email',
            $prefix .'/config/dupdate',
            $prefix .'/config/cupdate',
            $prefix .'/config/update_cash',
            $prefix .'/config',

            //节点
//            $prefix .'/node',
//            $prefix .'/node/create',
//            $prefix .'/node/ajax',

            //商品
            $prefix .'/shop',
            $prefix .'/shop/ajax',
//            $prefix .'/shop/create',
            $prefix .'/shop/edit',
            $prefix .'/shop/edit_post',

            //用户
            $prefix .'/user',
//            $prefix .'/user/edit',
            $prefix .'/user/',
            $prefix .'/user/changetouser',
            $prefix .'/user/ajax',
//            $prefix .'/user/create',
//            $prefix .'/user/buy',

            //代理
            $prefix .'/agent/lists',
            $prefix .'/agent/lists_ajax',
            $prefix .'/agent/lists_edit_post',
            $prefix .'/agent/lists_edit',
            $prefix .'/agent/lists_create',
            $prefix .'/agent/lists_add',
            $prefix .'/agent/type',
            $prefix .'/agent/type_ajax',
            $prefix .'/agent/type_edit',
            $prefix .'/agent/type_edit_post',
            $prefix .'/agent/income',
            $prefix .'/agent/income_ajax',
            $prefix .'/agent/cash',
            $prefix .'/agent/cash_ajax',
            $prefix .'/agent/cash_post',
            $prefix .'/agent/detect',

        ];


        $REQUEST_URI = explode('?',$_SERVER['REQUEST_URI'])[0]; //过滤参数
        if(!in_array($REQUEST_URI,$whiteList)){
            $arr = explode("/",$_SERVER['REQUEST_URI']);

            //如果是多级路由 则过滤掉参数
            if(count($arr) > 4){
                //例如/admin/shop/1/edit  array ( 0 => '', 1 => 'admin', 2 => 'shop', 3 => '1', 4 => 'edit', )
                $newUrl = "{$arr[0]}/{$arr[1]}/{$arr[2]}/{$arr[4]}";
                if(in_array($newUrl,$whiteList)){
                    return true;
                }
            }

//            var_export($arr);
//            var_export('Permission denied :' . var_export($_SERVER));
//            echo '<p>';
//            var_export('whiteList:'.var_export($whiteList));
//            echo '<p>';
//            var_export('REQUEST_URI:'.$_SERVER['REQUEST_URI']);
//            exit;
            return header("Location: /403");
        }

    }


//    protected static function
}