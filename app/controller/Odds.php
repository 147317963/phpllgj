<?php
declare(strict_types=1);

namespace app\controller;



use think\facade\Cache;

class Odds extends Base
{




   public function index() {

       if(request()->isGet()){

           $type = request()->get('type')? request()->get('type'):99;

           $object = new \app\model\Odds();

           $result = Cache::store('redis')->get(config('canchelist.odds.name').$type);


           if($result == null){

               $result = $object->where(['type'=>$type])->column('*','id');
//               dump($this->list);
               Cache::store('redis')->set(config('canchelist.odds.name').$type,$result,config('canchelist.odds.expire'));
           }

           $arr=[];

           foreach ($result as $key=>$value){

               $arr[$value['group']][$value['name']]=$value;

           }

           $data=[
             'code'=>200,
             'data'=>  $arr
           ];


           return json($data);
       }

   }

    /**删除赔率缓存
     *
     */
   public function delete(){

      Cache::store('redis')->delete(config('canchelist.odds.name').'*');
   }
}