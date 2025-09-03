# webman

[![License](https://img.shields.io/github/license/walkor/webman)](https://github.com/walkor/webman/blob/master/LICENSE)
[![webman](https://img.shields.io/badge/powered%20by-webman-green)](https://github.com/walkor/webman)
[![PHP Version Require](http://poser.pugx.org/workerman/webman-framework/require/php)](https://packagist.org/packages/workerman/webman-framework)

`webman` 是一个基于 [workerman](https://www.workerman.net) 开发的超高性能PHP框架。

## 特性

*   **高性能**: 基于 Workerman 开发，性能远超传统 PHP-FPM 框架。
*   **高并发**: 支持海量并发连接。
*   **常驻内存**: 一次加载，多次复用，避免了传统框架重复初始化和文件加载的开销。
*   **快速启动**: 简单的核心，启动速度快。
*   **多协议支持**: 支持 HTTP, WebSocket, 以及各种自定义协议。
*   **组件生态**: 丰富的 workerman/webman 组件生态。

## 安装

```bash
composer create-project workerman/webman
```

## 快速开始

1.  **启动服务**

    ```bash
    php start.php start
    ```
    (Windows 用户使用 `php windows.php` 启动)

2.  **创建控制器**

    在 `app/controller/Index.php` (如果不存在，请创建) 中写入以下代码:

    ```php
    <?php
    namespace app\controller;

    use support\Request;

    class Index
    {
        public function index(Request $request)
        {
            return response('hello webman');
        }
    }
    ```

3.  **访问**

    在浏览器中访问 `http://127.0.0.1:8787`，您应该能看到 `hello webman`。

## 学习资源

*   [官方网站](https://www.workerman.net/webman)
*   [完整文档](https://webman.workerman.net)
*   [社区问答](https://www.workerman.net/questions)
*   [应用市场](https://www.workerman.net/apps)

## 致谢

`webman` 的发展离不开所有贡献者和赞助商的支持。

*   [致谢列表](https://www.workerman.net/doc/webman/thanks.html)
*   [赞助商](https://www.workerman.net/sponsor)

## 贡献

欢迎任何形式的贡献，包括但不限于：

*   提交 issue
*   提交 pull request
*   分享使用经验

## License

The webman is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
