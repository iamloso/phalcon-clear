## phalcon-clear: 一套前端与后端可以分离部署phalcon框架简明设计结构.


### 功能说明:

    * 支持前端interface与后端admin独立部署和独立task任务.
    * 支持前端与后端数据库分离.
    * 支持静态资源分离并独立部署.
    * 引入composer, 支持composer安装与升级phalcon-clear, 真正做到一点维护, 多点使用.
    * 支持model/server类文件实例化, 引用InitializeService/InitializeModel可实例任意model/server类文件. 建议使用,便于类文件维护.
    * 引入trait 特性, 支持controller和service端共享.

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

#### 安装pframe/phalcon-clear

1. htdocs 目录创建composer.json文件, 写入已下配置.
```json
   {
       "repositories": [
          {
              "type": "vcs",
              "url": "git@10.20.104.54:yun/phalcon_base.git"
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
                "project-name":   ["type:library"]
          }
      },
      "config": {
              "vendor-dir": "./project-name/vendor/"
          }

   }

```
* Tip: project-name 配置成your项目名称

2. 当前目录(与composer.json同级), 执行下面命令, 等待片刻phalcon-clear框架安装成.

`composer update --ignore-platform-reqs --prefer-dist --no-dev`
* Tip: 框架升级及引入包, 同样执行该命令.

3. 当前项目.gitignore 追加一行 vendor/, 忽略该目录版本控制.

### 已加载composer包:
    * mysqlsyn/mysqlsyn: sql语句维护

### 相关文档
    * composer命令手册: http://docs.phpcomposer.com









