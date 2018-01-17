功能简介：
--------------
 * 后台UI样式库：基于Metronic，删除了原来的jQuery等依赖，只留下bootstrap和metronic的样式，前端采用Webpack+Vue的方式，自带文件上传、图片上传、富文本编辑、时间选择器等多个组件。
 * 用户和权限管理：基于角色的权限管理系统，一个用户可以有多个角色，配置更灵活。
 * 用户验证：支持账号密码、apikey两种验证方式，通过链式验证统一在一起，保证同一个API可以支持多种方式调用。
 * 内容管理：提供图文内容、图片相册内容、视频内容三种最常见内容的管理，\\\\TODO 音频内容，可下载附件内容。
 * 图片处理：上传前图片在浏览器端自动压缩，可直接使用10M等未处理高清大图。上传后可根据需要，提供各种尺寸，质量的缩略图。
 * \\\\TODO：CRUD代码生成，等官方MakerBundle完善些的时候再做，可以节约不少工作量。

安装和配置：
--------------

一. 安装 [Composer](https://getcomposer.org/doc/00-intro.md)，[NPM](https://docs.npmjs.com/getting-started/installing-node)。

二. 下载代码 git clone git@github.com:efeencheung/dminit.git

三. 安装依赖包

```sh
cd dminit
composer install //安装到最后如果询问选p

cd public/admin
npm install
```
四. 构建前端脚本

```sh
npm run build //生产模式
OR
npm run watch //开发模式

cd ../../
```

五. 打开.env配置数据库连接信息，然后创建数据库，数据表

```sh
php bin/console doctrine:database:create
php bin/console doctrine:schema:create
```

六. 配置服务器，开发环境，虚拟主机之类

七. 添加管理员用户 
打开config/packages/security.yaml
注释掉末尾的权限管理代码

```sh
    access_control:
        # - { path: ^/loggeduser, roles: ROLE_USER }
        # - { path: ^/wxuser, roles: ROLE_USER }
        # - { path: ^/user/, roles: ROLE_ADMIN }
        # - { path: ^/article/, roles: ROLE_ADMIN, methods: [ POST, PUT, DELETE ] }
        # - { path: ^/tag/, roles: ROLE_ADMIN, methods: [ POST, PUT, DELETE ] }
```

进入 http://localhost/admin/index.html#/user/add 添加管理员用户，添加完后删掉之前的注释

```sh
    access_control:
        - { path: ^/loggeduser, roles: ROLE_USER }
        - { path: ^/wxuser, roles: ROLE_USER }
        - { path: ^/user/, roles: ROLE_ADMIN }
        - { path: ^/article/, roles: ROLE_ADMIN, methods: [ POST, PUT, DELETE ] }
        - { path: ^/tag/, roles: ROLE_ADMIN, methods: [ POST, PUT, DELETE ] }
```


八. 然后清空一下缓存，修改文件夹权限，接下来就可以访问了

```sh
php bin/console cache:clear
/* 保证下面这几个文件夹Web用户有可写权限 */
var/cache
var/log
public/upload
public/media
```

http://localhost/admin/index.html#/login

安装过程只在Mac下进行了测试，估计Linux下也没什么问题，有问题欢迎抛砖，Windows由于最近很少用了，有时间再写对应的安装过程
