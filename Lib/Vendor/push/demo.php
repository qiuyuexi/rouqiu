<?php

header("Content-Type: text/html; charset=utf-8");

require_once(dirname(__FILE__) . '/' . 'IGt.Push.php');
require_once(dirname(__FILE__) . '/' . 'igetui/IGt.AppMessage.php');
require_once(dirname(__FILE__) . '/' . 'igetui/IGt.APNPayload.php');
require_once(dirname(__FILE__) . '/' . 'igetui/template/IGt.BaseTemplate.php');
require_once(dirname(__FILE__) . '/' . 'IGt.Batch.php');
require_once(dirname(__FILE__) . '/' . 'igetui/utils/AppConditions.php');

//http的域名
define('HOST','http://sdk.open.api.igexin.com/apiex.htm');


//定义常量, appId、appKey、masterSecret 采用本文档 "第二步 获取访问凭证 "中获得的应用配置               
define('APPKEY','zZh6RLqwve5bWtxmTPQRp2');
define('APPID','r7G9Mi6Tq59Kh5qqxk7xdA');
define('MASTERSECRET','UpuNaIjdjn7KeQh0nQIVp5');

//define('BEGINTIME','2015-03-06 13:18:00');
//define('ENDTIME','2015-03-06 13:24:00');

class push{

    private $title;

    private $content;

    private $text;


    public function __construct($title = '通知栏标题',$content = '透传内容',$text = '通知栏内容'){

        $this->title = $title;
        $this->content = $content;
        $this->text = $text;
    }

    //群推接口案例
    
    public function pushMessageToApp(){

        $igt = new IGeTui(HOST,APPKEY,MASTERSECRET);

        //定义透传模板，设置透传内容，和收到消息是否立即启动启用，点击通知打开应用模板
        $ios_template = $this->IGtTransmissionTemplateDemo();

        $and_template = $this->IGtNotificationTemplateDemo();
        //$template = $this->
        //$template = IGtLinkTemplateDemo();
        // 定义"AppMessage"类型消息对象，设置消息内容模板、发送的目标App列表、是否支持离线发送、以及离线消息有效期(单位毫秒)
        $message = new IGtAppMessage();

        $message->set_isOffline(true);
       
        $message->set_offlineExpireTime(10 * 60 * 1000);//离线时间单位为毫秒，例，两个小时离线为3600*1000*2
       
        $message->set_data($ios_template);

        $message->set_phoneTypeList(array('IOS'));

        $appIdList=array(APPID);

        $message->set_appIdList($appIdList);

        $rep = $igt->pushMessageToApp($message,"任务组名");




        $message->set_data($and_template);

        $message->set_phoneTypeList(array('ANDROID'));

        $appIdList=array(APPID);

        $message->set_appIdList($appIdList);

        $rep = $igt->pushMessageToApp($message,"任务组名");


        return $rep;
    }


    //多推接口案例
    function pushMessageToList($cid = array(),$device = NULL){
        
        putenv("gexin_pushList_needDetails=true");
       
        putenv("gexin_pushList_needAsync=true");

        $igt = new IGeTui(HOST, APPKEY, MASTERSECRET);

       //消息模版：
        // 4.NotyPopLoadTemplate：通知弹框下载功能模板
        if(!is_null($device) && $device != '0'){
            $template = $this->IGtTransmissionTemplateDemo();
        }else{
            $template = $this->IGtNotificationTemplateDemo();
        }
        //个推信息体
        $message = new IGtListMessage();
        
        $message->set_isOffline(true);//是否离线
        
        $message->set_offlineExpireTime(3600 * 12 * 1000);//离线时间
        
        $message->set_data($template);//设置推送消息类型
    //    $message->set_PushNetWorkType(1); //设置是否根据WIFI推送消息，1为wifi推送，0为不限制推送
    //    $contentId = $igt->getContentId($message);
        $contentId = $igt->getContentId($message,"toList任务别名功能");   //根据TaskId设置组名，支持下划线，中文，英文，数字

        //接收方1
        $targetList = array();
        foreach ($cid as $key => $value) {
            # code...
            $target1 = new IGtTarget();
        
            $target1->set_appId(APPID);

            $target1->set_clientId($value);
             
            $targetList[] = $target1;

        }
        
        $rep = $igt->pushMessageToList($contentId, $targetList);
        return $rep;
//        var_dump($rep);

  //      echo ("<br><br>");

    }


/**
     * [pushMessageToSingle description]
     * @return [type] [description]
     * @descripe android单推
     */
    
    function pushMessageToSingle($cid = NULL,$device = NULL){
        writeLog($cid,$device);
        $igt = new IGeTui(HOST,APPKEY,MASTERSECRET);

        //消息模版：
        // 4.NotyPopLoadTemplate：通知弹框下载功能模板
        if(!is_null($device)){
            $template = $this->IGtTransmissionTemplateDemo();
        }else{
            $template = $this->IGtNotificationTemplateDemo();
        }
        
       
        //定义"SingleMessage"
        $message = new IGtSingleMessage();

        $message->set_isOffline(true);//是否离线

        $message->set_offlineExpireTime(3600*12*1000);//离线时间
        
        $message->set_data($template);//设置推送消息类型
        
        $message->set_PushNetWorkType(0);//设置是否根据WIFI推送消息，2为4G/3G/2G，1为wifi推送，0为不限制推送
        
        //接收方
        $target = new IGtTarget();
        $target->set_appId(APPID);
        $target->set_clientId($cid);

        $rep = '';
        try {
            $rep = $igt->pushMessageToSingle($message, $target);

        }catch(RequestException $e){
            $requstId =e.getRequestId();
            //失败时重发
            $rep = $igt->pushMessageToSingle($message, $target,$requstId);
        }
        return $rep;
    }


    /**
     * [IGtNotificationTemplateDemo description]
     * 点击通知打开应用模板,安卓模板
     */
    public function IGtNotificationTemplateDemo(){


        $template =  new IGtNotificationTemplate();
        
        $template->set_appId(APPID);                   //应用appid
        
        $template->set_appkey(APPKEY);                 //应用appkey
    
        $template->set_transmissionType(0);            //透传消息类型
        
        $template->set_transmissionContent($this->content);//透传内容
        
        $template->set_title($this->title);                  //通知栏标题
        
        $template->set_text($this->text);     //通知栏内容
        
        $template->set_logo("");                       //通知栏logo
        
        $template->set_logoURL("");                    //通知栏logo链接
        
        $template->set_isRing(true);                   //是否响铃
        
        $template->set_isVibrate(true);                //是否震动
        
        $template->set_isClearable(true);              //通知栏是否可清除
        
        return $template;
    }


        /**
         * [pushAPN description]
         * @return [type] [description]
         * @descripe ios 单推
         */

        public function IGtTransmissionTemplateDemo(){
           
            $template =  new IGtTransmissionTemplate();
            
            //应用appid
            $template->set_appId(APPID);
            
            //应用appkey
            $template->set_appkey(APPKEY);
            
            //透传消息类型
            $template->set_transmissionType(0);
            
            //透传内容
            $template->set_transmissionContent("测试离线");
                
            $apn = new IGtAPNPayload();

            $alertmsg=new DictionaryAlertMsg();
            
            $alertmsg->body="body";
            
            $alertmsg->actionLocKey="解锁";
            
            $alertmsg->locKey=$this->text;
            
            $alertmsg->locArgs=array("locargs");
            
            $alertmsg->launchImage="launchimage";
   
    //       IOS8.2 支持
           
            $alertmsg->title = $this->title;
   
            $alertmsg->titleLocKey= $this->title;

            $alertmsg->titleLocArgs=array("TitleLocArg");


            $apn->alertMsg=$alertmsg;
   
            $apn->badge=0;
   
            $apn->sound= " ";
   
            $apn->add_customMsg("payload","阿波罗度上市");
   
            $apn->contentAvailable=1;
            
            $apn->category="ACTIONABLE";
            
            $template->set_apnInfo($apn);    
            
            return $template;
        }

}


