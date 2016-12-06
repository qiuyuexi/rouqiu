<?php

use  \Driver\db;

use \Driver\mypdo;

use \Driver\myredis;

use \Driver\email;

use \Driver\preg;

use \Driver\verify;

class BaseController
{

    public function __construct ()
    {


    }


    /**
     * @descripe 获取pdo 驱动
     * @return mixed
     */
    public  function  getMypdoHandle(){
        return mypdo::init();
    }


    /**
     * @descripe mysqli 驱动
     * @return db|null
     */
    public  function getMysqlHandle(){

        return db::init();
    }


    /**
     * @descripe redis 驱动
     * @param int $no
     */
    public  function getRedisHandle(){
        return myRedis::init();
    }


    /**
     * @descripe 正则表达式驱动
     * @return preg
     */
    public  function getPregHandle(){
        return new preg();
    }

    /**
     * @descripe 验证码驱动
     * @return verify
     */
    public  function  getVerifyHandle(){
        return new verify();
    }

    /**
     * [__call description]
     * @param  [type] $methods [description]
     * @param  [type] $args    [description]
     * @return [type]          [description]
     */

    public function __call ($methods, $args)
    {


        $args = implode(',', $args);

        writeLog("unknow $methods,$args");

        return false;
    }


    /**
     * @descripe 邮件驱动
     * @return email
     */
    public  function  getEmailHandle(){
        return new email();
    }



    public function index ()
    {

        echo 'what are you nong sha lei';
    }


    public  function  sendEmail(){
        $email = new email();
        $email->setBody('http://www.baidu.com');
        $email->setAddress('357748841@qq.com');
        $email->setSubject('测试');

        $email->send();
    }
    public  function  tt(){
        $a = $this->getVerifyHandle();
        var_dump( $a->getVerify());
    }
}
