<?php
return array(
	//'配置项'=>'配置值'
                        'MYSQLDUMP_PATH'=>'/usr/local/mysql/bin/mysqldump',
                        'MYSQL_PATH'=>'/usr/local/mysql/bin/mysql',
                        'SESSION_OPTIONS'=>array('domain'=>'.ky169.xyz'),
                        'COOKIE_DOMAIN'=>'.ky169.xyz',
                        'DB_TYPE'   => 'mysql', // 数据库类型
                        'DB_HOST'   => '127.0.0.1', // 服务器地址
                        'DB_NAME'   => 'ad', // 数据库名
                        'DB_USER'   => 'root', // 用户名
                        'DB_PWD'    => 's9787531', // 密码
                        'DB_PORT'   => 3307, // 端口
                        'DB_PREFIX' => 'tp_', // 数据库表前缀
                        'DB_CHARSET'=> 'utf8', // 字符集
                        'CHECK_ROOT'=> true,  //开启rbac权限
                        'OPEN_UP'   => true,  //后台是否开启开发者栏目
                        'SAFE_KEYS' => 'AKB-SKE-NMB-HKT',
                        'URL_MODEL'             =>  2,
                        'FILE_PATH'=>'http://www.ky169.xyz/tp/Uploads/',
                        'MODULE_ALLOW_LIST'     =>  array('Home','Admin'),
                        'DEFAULT_MODULE'        =>  'Home',
);