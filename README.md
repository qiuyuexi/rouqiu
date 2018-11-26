自己写的简单的小框架。
封装了一些常用的方法，之前写的太挫,索性重写了

## nginx 配置
url/controller/action?param
```php
server{
        listen 80;
        server_name rouqiu.test.com;
        root /www/rouqiu;
        if (!-e $request_filename)
        {
                rewrite ^/(.+)$ /index.php last;
        }
        location / {
                index index.php index.html;
        }
        location ~ \.php$ {
                try_files $uri $uri/ =404;
                fastcgi_pass   127.0.0.1:9000;
                fastcgi_index  index.php;
                fastcgi_param  SCRIPT_FILENAME $document_root$fastcgi_script_name;
                include        fastcgi_params;
        }
   }
```
## 待完成
* 数据库
* redis
* memcache
* 单元测试
....
