<?php


/**
 * [encryptedPwd description]
 * @param  [type] $pwd    [description]
 * @param  [type] $string [description]
 * @return [type]         [description]
 * @descripe对密码进行加密
 */

function encryptedPwd ($pwd = NULL, $string = NULL)
{

    return md5($pwd.$string.time());

}

/**
 * [delData description]
 * @param  [type] $value [description]
 * @return [type]        [description]
 * @descripe 过滤数据
 */

function filterData ($data)
{

    if (is_array($data)) {

        foreach ($data as $key => $value) {

            $value = trim($value);//移除字符串两侧的字符

            $value = htmlspecialchars($value);//过滤字符 单引号等

            $data[$key] = strip_tags($value);//剥去字符串中的 HTML、XML 以及 PHP 的标签
        }
    } else {

        $data = trim($data);

        $data = htmlspecialchars($data);

        $data = strip_tags($data);
    }

    return $data;
}


/**
 *
 * @param  int  随机数
 * @return string
 * @descripe 生成唯一的32位字符串
 */

function uuid ($type = 3)
{

    $str = time();

    $str = md5($str . mt_rand());

    $str = md5(substr($str . time() . md5(mt_rand()), mt_rand(0, 30)));

    $str = substr($str, 0, strlen($str) - 1);

    $str = $str . $type;

    return $str;
}


/**
 *
 * @param url 获取数据的地址
 * @return array 服务器返回的结果
 * @descripe 使用post方式 到指定的url 下采集数据
 */

function postXml ($xml_data, $url)
{

    $header[] = "Content-type: text/xml";  //定义content-type为xml,注意是数组

    $ch = curl_init($url);

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
 * @param url 获取数据的地址
 * @return array 服务器返回的结果
 * @descripe 使用get方式 到指定的url 下采集数据
 */

function getCurl ($url)
{

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}


/**
 *
 * @param url 推送数据的地址
 * @return array  服务器返回的结果
 * @descripe  使用post方式 发送数据到指定的url下  采集数据
 */

function postCurl ($url, $data)
{

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

    if (curl_errno($ch)) {

        $back_info = curl_error($ch);
        writeLog($back_info);
        $back_info = jsonEncode(array('status' => false, 'msg' => $back_info));
    }

    curl_close($ch);

    return $back_info;
}


/**
 * @param stirng url 在线图片地址
 * @param string name 本地图片保存路径
 * @descripe 下载在线图片保存至本地
 */

function uploadImg ($url, $filename)
{

    //获取微信图片
    $url = $url;

    $curl = curl_init($url);

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

    $imageData = curl_exec($curl);

    curl_close($curl);

    $tp = @fopen($filename, 'a');

    fwrite($tp, $imageData);

    fclose($tp);

    if (file_exists($filename)) {

        return true;

    } else {

        return false;

    }
}


/**
 * @param array  要加密的数据
 * @param int   加密的方法
 * @descripe 封装Jso要n_encode
 */

function jsonEncode ($data, $param = null)
{

    if (is_null($param)) {

        return json_encode($data);

    } else {


    }
}


/**
 * @param array 要解密的数据
 * @param int  解密的方法
 * @descripe 封装Json_decode
 *
 */

function jsonDecode ($data, $param = null)
{

    if (is_null($param)) {

        return json_decode($data, true);

    } else {


    }
}


/**
 * @param string $str需要写入日志的内容
 * @return string
 */
function writeLog ($str = 'is null')
{

    $log_dir = __DIR__ . '/../Public/Log';//sql 日志文件目录

    if (!is_dir($log_dir)) {

        mkdir($log_dir, 0755, true);

    }

    $day_path = $log_dir . '/' . date('Y-m-d');

    if (!is_dir($day_path)) {

        mkdir($day_path, 0755, true);

    }

    $log_path = $day_path . '/' . date('Y-m-d H') . '.txt';//sql 日志文件路径

    $time = date('Y-m-d H:i:s');

    if (is_array($str)) {

        $str = var_export($str, true);

    }

    $msg = "{$time}:message:" . PHP_EOL . $str . PHP_EOL;

    $handel = fopen($log_path, 'a+');

    fwrite($handel, $msg);

    fclose($handel);

    return 'msg' . $str;

}

/**
 * [getIp description]
 * @return [type] [description]
 * @descripe 获取用户的ip
 */
function getIp ()
{

    $onlineip = '';

    if (getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {

        $onlineip = getenv('HTTP_CLIENT_IP');

    } elseif (getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {

        $onlineip = getenv('HTTP_X_FORWARDED_FOR');

    } elseif (getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {

        $onlineip = getenv('REMOTE_ADDR');

    } elseif (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {

        $onlineip = $_SERVER['REMOTE_ADDR'];

    }
    return $onlineip;
}


/**
 * @param 数据 转换编码的数据
 *
 */

function setCharset ($data)
{

    return iconv('utf-8', 'gb2312', $data);

}


/**
 * @param data 导出的数据
 * @param fileNme  导出文件的文件名
 */
function exportCsv ($filename, $data)
{

    header("Content-type:text/csv");
    header("Content-Disposition:attachment;filename=" . $filename);
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

function checkType ($path = null)
{
    $file = fopen($path, "rb");
    $bin = fread($file, 2); //只读2字节
    fclose($file);
    $str_info = @unpack("C2chars", $bin);
    $type_code = intval($str_info['chars1'] . $str_info['chars2']);
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

    if ($str_info['chars1'] == '-1' AND $str_info['chars2'] == '-40') return 'jpg';
    if ($str_info['chars1'] == '-119' AND $str_info['chars2'] == '80') return 'png';
    return $file_type;
}
