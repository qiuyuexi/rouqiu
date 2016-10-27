<?php
    

    /**
    *@param  int  1付款 2 退款  4 参加活动订单号
    *@return string 
    *@descripe 生成订单号.唯一的32位字符串
    */

    function uuid($type = 3){

        $str = time();
        
        $str = md5($str.mt_rand());
        
        $str = md5(substr($str.time().md5(mt_rand()),mt_rand(0,30)));
        
        $str = substr($str,0,strlen($str)-1);
        
        $str = $str.$type;
        
        return $str;
    }



    /**
    *
    *@param url 获取数据的地址
    *@return array 服务器返回的结果
    *@descripe 使用post方式 到指定的url 下采集数据
    */

    function postXml($xml_data,$url){
        
        $header[] = "Content-type: text/xml";  //定义content-type为xml,注意是数组
        
        $ch = curl_init ($url);
        
        curl_setopt($ch, CURLOPT_URL, $url);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        
        curl_setopt($ch, CURLOPT_POST, 1);
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_data);
        
        $result = curl_exec($ch);
        
        return $result;
    }
    

    /**
    *
    *@param url 获取数据的地址
    *@return array 服务器返回的结果
    *@descripe 使用get方式 到指定的url 下采集数据
    */

    function getCurl($url){
        
        $ch = curl_init();  
        
        curl_setopt($ch, CURLOPT_URL,$url);  
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); 
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);  
        
        $result =  curl_exec($ch);  
        curl_close ($ch);  
        
        return $result; 
    }
    

    /**
    *
    *@param url 推送数据的地址
    *@return array  服务器返回的结果
    *@descripe  使用post方式 发送数据到指定的url下  采集数据
    */

    function postCurl($url,$data){
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $back_info = curl_exec($ch);
        
        if (curl_errno($ch)){
        
          $back_info =  curl_error($ch);
          writeLog($back_info);  
          $back_info = jsonEncode(array('status'=>false,'msg'=>$back_info));  
        }
        
        curl_close($ch);
        
        return $back_info;
    } 


    /**
    *@param stirng url 在线图片地址
    *@param string name 本地图片保存路径
    *@descripe 下载在线图片保存至本地
    */

    function uploadImg($url,$filename){
        
        //获取微信图片
        $url = $url;
        
        $curl = curl_init($url);
        
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
        
        $imageData = curl_exec($curl);
        
        curl_close($curl);
        
        $tp = @fopen($filename,'a');
        
        fwrite($tp, $imageData);
        
        fclose($tp);
        
        if(file_exists($filename)){
        
            return true;
        
        }else{
        
            return false;
        
        }
    }


    /**
    *@param array  要加密的数据
    *@param int   加密的方法
    *@descripe 封装Jso要n_encode
    */

    function jsonEncode($data,$param = null){
        
        if(is_null($param)){
           
            return json_encode($data);
        
        }else{


        }
    }


    /**
    *@param array 要解密的数据
    *@param int  解密的方法
    *@descripe 封装Json_decode
    * 
    */

    function jsonDecode($data,$param = null){
      
        if(is_null($param)){
      
            return json_decode($data,true);
      
        }else{


        }
    }




    /**
    *@param string  日志内容
    *@descripe 系统日志生成
    */

    function  writeLog($str = 'is null'){
        
        $log_dir = __DIR__.'/../Public/Log';//sql 日志文件目录

        if(!is_dir($log_dir)){

            mkdir($log_dir,0755,true);

        }

        $day_path = $log_dir.'/'.date('Y-m-d');

        if(!is_dir($day_path)){

            mkdir($day_path,0755,true);

        }

        $log_path = $day_path.'/'.date('Y-m-d H').'.txt';//sql 日志文件路径

        $time = date('Y-m-d H:i:s');

        if(is_array($str)){

            $str = var_export($str,true);

        }
        
        $msg = "{$time}:message:".PHP_EOL.$str.PHP_EOL; 

        $handel = fopen($log_path, 'a+');

        fwrite($handel,$msg);

        fclose($handel);

        return 'msg'.$str;

    }


    /**
    *@param data array 转换成sql更新语句的数组
    *@param table string 要更新的表
    *@param $key  array sql 语句更新的key
    *@return  string  生成的sql语句
    *@descripe 将数组转化成sql语句
    */

    function updateString($data,$table,$key){

        $sql = '';

        if(count($data) > 0){

            $str = '';

            foreach ($data as $k => $value) {

                $value = trim($value);
                $value = addslashes($value);
                $value = stripslashes($value);
                $value = htmlspecialchars($value);
                $value = strip_tags($value);
                $str .= "{$k} = '{$value}',";

            }

            $keys = array_keys($key);

            $keys = $keys[0];

            $str = substr($str,0,strlen($str) - 1);

            $sql = "update %s set %s where %s = '%s' ";

            $sql = sprintf($sql,$table,$str,$keys,$key[$keys]);

        }
        return $sql;
    }


    /**
    *@param array 转换成sql插入语句的数组
    *@param string 要插入数据的表 
    *@return  string  生成的sql语句
    *@descripe 将数组转化成sql语句
    */

    function insertString($data = array(),$table = null){
    
        $sql = '';
    
        if(count($data) > 0 && !is_null($table)){
    
            $keys = '';
            $values = '';
            $sql = '';
    
            foreach ($data as $key => $value) {
    
                    $value = trim($value);
                    $value = addslashes($value);
                    $value = stripslashes($value);
                    $value = htmlspecialchars($value);
                    $value = strip_tags($value);
                    $keys .=  " {$key},";
                    $values .= " '{$value}',";
    
            }
    
            $keys = substr($keys,0,strlen($keys) - 1);
    
            $values = substr($values,0,strlen($values) - 1);

            $sql = " insert into %s(%s) values(%s)";
    
            $sql = sprintf($sql,$table,$keys,$values);
    
        }
    
        return $sql;
    }


    /**
     * [getIp description]
     * @return [type] [description]
     * @descripe 获取用户的ip
     */
    function getIp(){ 

        $onlineip=''; 

        if(getenv('HTTP_CLIENT_IP')&&strcasecmp(getenv('HTTP_CLIENT_IP'),'unknown')){ 

            $onlineip=getenv('HTTP_CLIENT_IP'); 

        } elseif(getenv('HTTP_X_FORWARDED_FOR')&&strcasecmp(getenv('HTTP_X_FORWARDED_FOR'),'unknown')){ 

            $onlineip=getenv('HTTP_X_FORWARDED_FOR'); 
    
        } elseif(getenv('REMOTE_ADDR')&&strcasecmp(getenv('REMOTE_ADDR'),'unknown')){ 
    
            $onlineip=getenv('REMOTE_ADDR'); 
    
        } elseif(isset($_SERVER['REMOTE_ADDR'])&&$_SERVER['REMOTE_ADDR']&&strcasecmp($_SERVER['REMOTE_ADDR'],'unknown')){ 
    
            $onlineip=$_SERVER['REMOTE_ADDR']; 
    
        } 
    
        return $onlineip; 
    
    }

    
    /**
    *@param string sql 执行的sql语句
    *@descripe 将语句发生错误的sql写入数据库
    *
    */
    
    function sqlLog($sql){
    
        $log_dir = __DIR__.'/../Public/sqlLog';//sql 日志文件目录
    
        if(!is_dir($log_dir)){
    
            mkdir($log_dir,0766,ture);
    
        }
    
        $day_path = $log_dir.'/'.date('Y-m-d');//每天的日志文件目录
    
        if(!is_dir($day_path)){
    
            mkdir($day_path,0766,true);
    
        }
    
        $log_path = $day_path.'/'.date('Y-m-d H').'.txt';//sql 日志文件路径
    
        $time = date('Y-m-d H:i:s');
    
        $msg = "{$time}:{$sql}".PHP_EOL;
    
        $handel = fopen($log_path, 'a+');
    
        fwrite($handel,$msg."\r\n");
    
        fclose($handel);
    
    }
    

    /**
    *@param 数据 转换编码的数据
    *
    */ 
    
    function setCharset($data){
    
        return iconv('utf-8','gb2312', $data);
    
    }


    /**
    *@param data 导出的数据
    *@param fileNme  导出文件的文件名
    */ 
    function  exportCsv($filename,$data){
        
        header("Content-type:text/csv");  
        header("Content-Disposition:attachment;filename=".$filename);  
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');  
        header('Expires:0');  
        header('Pragma:public');  
        echo $data;  
        
    }


    /**
     * [fileType description]
     * @param  [type] $path [description]文件路径
     * @return [type]       [description]
     * 检测文件的路径 只能避免部分 
     */
    
    function checkType($path = null){
        $file = fopen($path, "rb");  
        $bin = fread($file, 2); //只读2字节  
        fclose($file);  
        $str_info = @unpack("C2chars", $bin);  
        $type_code = intval($str_info['chars1'].$str_info['chars2']);  
        $file_type = '';  
        switch ($type_code) {  
            case 7790:  
                $file_type = 'exe';  
                break;  
            case 7784:  
                $file_type = 'midi';  
                break;  
            case 8297:  
                $file_type = 'rar';  
                break;          
            case 8075:  
                $file_type = 'zip';  
                break;  
            case 255216:  
                $file_type = 'jpg';  
                break;  
            case 7173:  
                $file_type = 'gif';  
                break;  
            case 6677:  
                $file_type = 'bmp';  
                break;  
            case 13780:  
                $file_type = 'png';  
                break;  
            default:  
                $file_type = 'unknown';  
        }  
        //Fix  
        if ($str_info['chars1']=='-1' AND $str_info['chars2']=='-40' ) return 'jpg';  
        if ($str_info['chars1']=='-119' AND $str_info['chars2']=='80' ) return 'png';    
        return $file_type;  
    }
