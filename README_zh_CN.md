<p align="center">
<a href="https://apidoc.pp-lang.tech"><img src="https://apidoc.pp-lang.tech/logo.png" width="400" alt="Laravel Apidoc"></a>
</p>

# Laravel Apidoc

Laravel Apidoc 是一个基于 [Laravel](https://laravel.com/) 的Api接口文档生成工具


## 安装

通过 Composer 进行安装

```bash
composer require larafly/apidoc
```

运行如下命令来安装接口文档工具

```sh
php artisan apidoc:install
```

现在你可以访问您的应用url`http://localhost:8000/apidoc` 来使用`Laravel Apidoc`了


## 配置文件

发布配置文件

```sh
php artisan vendor:publish --tag=larafly-apidoc
```


`larafly-apidoc.php` 文件说明

```php

<?php

return [
    # 接口文档访问地址
    'route' => env('API_DOC_ROUTE', 'apidoc'),
    # 格式化日期
    'datetime_format' => 'Y-m-d H:i:s',
    # 接口文档撰写人
    'author' => env('GENERATOR_AUTHOR', 'system'),
    # 生产环境是否显示文档，默认为不显示
    'is_show' => env('API_DOC_SHOW', false),
];

```

在`.env`中配置`GENERATOR_AUTHOR=您的名字`，来进行创建人的配置

## 更新记录

查看 [changelog](changelog.md) 获取更新记录

MIT. Please see the [license file](license.md) for more information.
