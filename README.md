# KA客户版 Demo PHP版

## 运行要求
* PHP >= 5.6.0，<= 7.4.33
* composer

## 使用说明
1. 配置php运行环境，可使用IDEA的php插件

2. 下载代码

    ```
    git clone -b master http://192.168.26.50/shaojian/ka-demo-php.git
    ```

3. 安装依赖

    ```
    composer install
    ```

4. 如存在Sm4包依赖重复冲突的情况，修改vendor/lpilp/guomi/src/smecc/SM4OLD/Sm4.php的类名为Sm4OLD
