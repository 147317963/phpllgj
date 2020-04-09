<?php


namespace app\controller\v1;



use app\model\Sc;
use think\facade\Cache;
use Zhuxinyuang\common\Xyft;

class History extends Base
{
   public function index(){


       if(request()->isGet()){

           $type = request()->get('type')? request()->get('type'):99;


           $xyft   =  new Xyft();


           $object =  new Sc();
//
           $result = Cache::store('redis')->get(config('canchelist.opencode.name').$type);
//
           if($result == null){
               $result = $object->where(['type'=>$type])->limit(50)->order('issue desc')->select();
               Cache::store('redis')->set(config('canchelist.opencode.name').$type,$result,config('canchelist.opencode.expire'));
           }
           $arr=[];

           foreach ($result as $key=>$value){


               $datas = explode(",", $value['open_code']);
               $hm[0] = (int)$datas[0];
               $hm[1] = (int)$datas[1];
               $hm[2] = (int)$datas[2];
               $hm[3] = (int)$datas[3];
               $hm[4] = (int)$datas[4];
               $hm[5] = (int)$datas[5];
               $hm[6] = (int)$datas[6];
               $hm[7] = (int)$datas[7];
               $hm[8] = (int)$datas[8];
               $hm[9] = (int)$datas[9];

               $codes['ball_1']['hm']=$hm[0];
               $codes['ball_1']['ds']=$xyft->Ds($hm[0]);
               $codes['ball_1']['dx']=$xyft->Dx($hm[0]);
               $codes['ball_1']['lh']=$xyft->Auto($hm,4);


               $codes['ball_2']['hm']=$hm[1];
               $codes['ball_2']['ds']=$xyft->Ds($hm[1]);
               $codes['ball_2']['dx']=$xyft->Dx($hm[1]);
               $codes['ball_2']['lh']=$xyft->Auto($hm,5);

               $codes['ball_3']['hm']=$hm[2];
               $codes['ball_3']['ds']=$xyft->Ds($hm[2]);
               $codes['ball_3']['dx']=$xyft->Dx($hm[2]);
               $codes['ball_3']['lh']=$xyft->Auto($hm,6);

               $codes['ball_4']['hm']=$hm[3];
               $codes['ball_4']['ds']=$xyft->Ds($hm[3]);
               $codes['ball_4']['dx']=$xyft->Dx($hm[3]);
               $codes['ball_4']['lh']=$xyft->Auto($hm,7);

               $codes['ball_5']['hm']=$hm[4];
               $codes['ball_5']['ds']=$xyft->Ds($hm[4]);
               $codes['ball_5']['dx']=$xyft->Dx($hm[4]);
               $codes['ball_5']['lh']=$xyft->Auto($hm,8);

               $codes['ball_6']['hm']=$hm[5];
               $codes['ball_6']['ds']=$xyft->Ds($hm[5]);
               $codes['ball_6']['dx']=$xyft->Dx($hm[5]);


               $codes['ball_7']['hm']=$hm[6];
               $codes['ball_7']['ds']=$xyft->Ds($hm[6]);
               $codes['ball_7']['dx']=$xyft->Dx($hm[6]);


               $codes['ball_8']['hm']=$hm[7];
               $codes['ball_8']['ds']=$xyft->Ds($hm[7]);
               $codes['ball_8']['dx']=$xyft->Dx($hm[7]);


               $codes['ball_9']['hm']=$hm[8];
               $codes['ball_9']['ds']=$xyft->Ds($hm[8]);
               $codes['ball_9']['dx']=$xyft->Dx($hm[8]);


               $codes['ball_10']['hm']=$hm[9];
               $codes['ball_10']['ds']=$xyft->Ds($hm[9]);
               $codes['ball_10']['dx']=$xyft->Dx($hm[9]);


               $codes['gyh']['zh']=$xyft->auto($hm,1);
               $codes['gyh']['dx']=$xyft->auto($hm,2);
               $codes['gyh']['ds']=$xyft->auto($hm,3);

               $arr[$key]=$value;
               $arr[$key]['codes']=$codes;





           }

           $data=[
             'code'=>200,
             'data'=>$arr,
              'message'=>'获取成功'
           ];
//           header("HTTP/1.1 401 Unauthorized");
           return json($data);
       }
   }

   public function delete(){

       Cache::store('redis')->delete(config('canchelist.opencode.name').'*');
   }
}