自己写的简单的小框架。封装了一些常用的方法，慢慢的完善它

1.
入口文件index.php
地址index.php?C=Controller&M=Method
如果是继承BaseController  
那么数据格式为json

2.
Config/config.php
里面设置了数据库基本配置，控制器目录，项目名称

3.
Driver 
里面有mysqli，redis,错误记录函数，正则，图片验证码，邮件驱动。

4.
Common 
common.php 公共函数，里面包含一些常用的函数，
start.php  引用的一些方法文件等

5.
Public 
里面包含系统错误日记，数据库错误日记等

6.Vendor
引用的第三方类

7
使用composer 自动加载所需的驱动类

