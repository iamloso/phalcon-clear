## phalcon-clear: 一套前端与后端可以分离部署phalcon框架简明设计结构.


### 功能说明:

    * 支持前端interface与后端admin独立部署和独立task任务.
    * 支持前端与后端数据库分离.
    * 支持静态资源分离并独立部署.
    * 引入composer, 支持composer安装与升级phalcon-clear, 真正做到一点维护, 多点使用.
    * 支持model/server类文件实例化, 引用InitializeService/InitializeModel可实例任意model/server类文件. 建议使用,便于类文件维护.
    * 引入trait 特性, 支持controller和service端共享.
    * 支持yar rpc 服务, 无缝对接.
    

### 安装说明
#### 安装composer:
    1. /usr/local/php/bin/php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
    2. /usr/local/php/bin/php -r "if
       (hash_file('SHA384', 'composer-setup.php') ===
       '669656bab3166a7aff8a7506b8cb2d1c292f042046c5a994c43155c0be6190fa0355160742ab2e1c88d40d5be660b410')
        { echo 'Installer verified'; } else { echo 'Installer corrupt';
       unlink('composer-setup.php'); } echo PHP_EOL;"
    3. 切换到root 账户,  sudo su
    4. /usr/local/php/bin/php  composer-setup.php  --install-dir=/usr/local/bin/   --filename=composer
    5. 测试是否成功,  切换到普通账户执行 composer -v

#### 下载 pframe/phalcon-clear

1. htdocs 目录创建composer.json文件, 写入已下配置.
```json
   {
       "repositories": [
            "packagist": {
            "type": "composer",
            "url": "https://packagist.phpcomposer.com"
        }
      ],
      "require": {
           "composer/installers": "~1.0",
           "oomphinc/composer-installers-extender" : "~1.0",
           "pframe/phalcon-clear": "dev-master"
      },
       "extra": {
           "installer-types": ["library"],
           "installer-paths": {
                "project-clear":   ["type:library"]
          }
      },
      "config": {
              "vendor-dir": "./project-clear/vendor/"
      }
   }

```
* Tip: project-clear 支持自定义目录




3. 当前目录(与composer.json同级), 执行下面命令, 下载phalcon-clear框架.

`composer install --ignore-platform-reqs --prefer-dist --no-dev`

4. cd phalcon-clear目录 git pull 可以维护自身框架升级. 执行 cd phalcon-clear composer update 更新引入包.


#### 安装 pframe/phalcon-clear

1. htdocs 目录创建packages.json文件, 写入已下配置.
```json
    {
        "packages": {
            "phalcon_base": {
                "dev-master": {
                    "name": "pframe/phalcon-clear",
                    "source": {
                        "reference": "46e488d396cd88f047546bb130244093093db823",
                        "type": "git",
                        "url": "git@10.20.104.54:yun/phalcon_base.git"
                    },
                    "type": "project",
                    "version": "dev-master"
                }
            }
        }
    }
```

2. 安装phalcon-clear并创建项目,  执行下面命令.
`composer create-project pframe/phalcon-clear project-name dev-master --repository-url=packages.json --ignore-platform-reqs`
`Do you want to remove the existing VCS (.git, .svn..) history? [Y,n]? Y`

3. 执行 cd project-name composer update 更新引入包.

### 已加载composer包:
    * mysqlsyn/mysqlsyn: sql语句维护

### YAR RPC 使用规范
1. 参照nginx/nginx.conf部署RPC解析.
2. 创建Controller文件, 编写简单代码.  代码示例 rpc/controllers/TestController.php , 该文件initAction 为初始化服务重要方法. testAction 为具体服务入口.
   编写service类, 理论上文件名/方法名,可与controller文件保存一致.
3. 服务注册填写服务商唯一标识/服务名称/服务地址/服务参数, 示例参考libs/extensions/ServiceConfig.php
4. 服务调用示例代码`$result = $this->rpc->request('service_merchant', 'service_name', ["test"=>'aa']);` 或 new YarClient.php


### 项目底层框架更新
* Tip: 什么时候需要在项目中更新底层框架?
> 1. 在phalcon-clear中引入新的开源包, 需要在项目中同步该包.
> 2. phalcon-clear框架本身优化及功能升级, 需要在项目中使用最新特性.


项目框架更新需要在phalcon-clear 框架, 执行update.sh脚本, 同时指定需要更新项目名称. 例如:
`cd phalcon-clear sh update.sh assentcenter`

#### 更新说明
 * Tip: 框架在项目中创建后, 下面目录及文件不在默认更新
> * framework
> * libs
> * vendor



### 相关文档
    * composer命令手册: http://docs.phpcomposer.com
    * composer官网地址: https://getcomposer.org/
    * packagist镜像地址:https://pkg.phpcomposer.com/
    * packagist官方地址:https://packagist.org
    * php-fig官网地址:  http://www.php-fig.org/





