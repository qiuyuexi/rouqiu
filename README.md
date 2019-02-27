自己写的简单的小框架。
封装了一些常用的方法，之前写的太挫,索性重写了

### php cgi

范例:
#### index.php 
```php
<?php
require_once "vendor/autoload.php";

$rootPath = __DIR__; //根据实际环境
\Rq\Common\Init::init($rootPath);
\Rq\Common\Init::dispatch('api');

    
```

#### nginx配置
url/controller/action?param
```php
server{
        listen 80;
        server_name rouqiu.test.com;
        root /www/rouqiu;
        index index.php index.html;
        location /{
            try_files $uri $uri/ /index.php?$query_string;
        }
        location ~ \.php$ {
                try_files $uri $uri/ =404;
                fastcgi_pass   127.0.0.1:9000;
                fastcgi_index  index.php;
                fastcgi_param  SCRIPT_FILENAME $document_root$fastcgi_script_name;
                include        fastcgi_params;
        }
        error_log  /www/log/rouqiu.test.com.err;
        access_log /www/log/rouqiu.test.com.log;
   }
```
### php cli
php cli.php --uri=URI --post=$_POST --get=$_GET

### 待完善
- [x] 数据库
- [x] redis
- [x] memcache
- [x] 单元测试
- [ ] ORM
- [ ] 路由优化
- [ ] apcu
### 文件配置实例
###### mysql 
``` php 
    <?php
    
    return [
        'master' => [
            'dbname' => 'test',
            'host' => '127.0.0.1',
            'port' => 3306,
            'user' => 'root',
            'password' => '',
            'time_out' => 3,
            'charset' => 'utf8mb4'
        ],
        'slave_list' => [
            [
                'dbname' => 'test',
                'host' => '127.0.0.1',
                'port' => 3306,
                'user' => 'root',
                'password' => '',
                'time_out' => 3,
                'charset' => 'utf8mb4'
            ]
        ]
    ];
```

##### mc
``` php
    <?php
    
    return [
        ['127.0.0.1', 11211, 100],
        ['127.0.0.1', 11211, 100],
        ['127.0.0.1', 11211, 100]
    ];
```
##### log
``` php
    <?php
    
    return [
        'dir' => '/www/rouqiu/log'
    ];

```