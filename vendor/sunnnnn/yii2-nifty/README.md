# yii2-nifty

## 搭建yii2框架下的后台管理系统全过程 ##

 - 安装yii2框架
 
    basic版本：
    ```
    composer create-project --prefer-dist yiisoft/yii2-app-basic nifty
    ```
    
    advanced版本：
    ```
    composer create-project --prefer-dist yiisoft/yii2-app-advanced nifty
    ```

 - 初始化框架
 
    ```
    cd nifty   //进入框架根目录
    init       //初始化
    开发环境选择0，正式环境选择1，yes
    ```
    如果根目录下没有vendor文件夹，请使用 composer install 命令来安装。
    需事先安装composer全局插件：composer global require "fxp/composer-asset-plugin:*"

 - 安装admin插件
    
    ```
    composer require sunnnnn/yii2-nifty
    ```

 - 根据yii版本，将目录/vendor/sunnnnn/yii2-nifty/下的文件复制覆盖到yii框架下
    
    > advanced版本，将目录下advanced目录下的文件复制覆盖到对象位置，

    > basic版本，则将basic目录下所有文件复制覆盖到对象位置， 	
    
    > (config文件可不用覆盖，根据注释添加到原config文件中)

 - 新建一个数据库：yii-nifty（名字自己取）
 
    > basic版本，在 config/db.php 配置数据库链接，

    > advanced版本，在 common/config/main-local.php 配置数据库链接

 - 添加数据表及数据
 
    ```
    yii migrate --migrationPath=@sunnnnn/nifty/auth/migrations/       //windows下
    
    php yii migrate --migrationPath=@sunnnnn/nifty/auth/migrations/   //linux下
    
    yes  //等待完成
    ```

 - 直接访问： http://您的域名/site/login页面，用户名admin、密码123456

 - gii操作
    > 使用gii生成model：最下方的Code Template选项，选择 sunnnnn-nifty-model

    > 使用gii生成curd：最下方的Code Template选项，选择 sunnnnn-nifty-curd

 - 其他
    [yii2-nifty的使用][1]


  [1]: http://www.sunnnnn.com/article/yii2-nifty