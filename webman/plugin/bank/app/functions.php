<?php
// +----------------------------------------------------------------------
// | bank [ 数字银行插件 ]
// +----------------------------------------------------------------------
// | Author: bank <bank@example.com>
// +----------------------------------------------------------------------

namespace plugin\bank\app\functions;

use Webman\Route;

/**
 * 快速路由定义
 * @param string $controller 控制器名称
 * @param string $prefix 路由前缀
 */
function fastRoute(string $controller, string $prefix = ''): void
{
    // 将驼峰命名转换为控制器类名
    $controllerClassName = ucfirst($controller) . 'Controller';
    $controllerClass = "plugin\\bank\\app\\controller\\{$controllerClassName}";
    
    if (empty($prefix)) {
        // 将驼峰命名转换为小写下划线格式作为路由前缀
        $prefix = strtolower(preg_replace('/([A-Z])/', '_$1', lcfirst($controller)));
        $prefix = ltrim($prefix, '_');
    }
    
    // 检查控制器是否存在
    if (!class_exists($controllerClass)) {
        return;
    }
    
    $reflection = new \ReflectionClass($controllerClass);
    
    // 数据列表 GET /controller
    if ($reflection->hasMethod('index')) {
        Route::get("/{$prefix}", [$controllerClass, 'index']);
    }
    
    // 保存数据 POST /controller
    if ($reflection->hasMethod('save')) {
        Route::post("/{$prefix}", [$controllerClass, 'save']);
    }
    
    // 修改状态 PUT /controller/changeStatus (静态路由，必须在变量路由之前)
    if ($reflection->hasMethod('changeStatus')) {
        Route::put("/{$prefix}/changeStatus", [$controllerClass, 'changeStatus']);
    }
    
    // 导入数据 POST /controller/import
    if ($reflection->hasMethod('import')) {
        Route::post("/{$prefix}/import", [$controllerClass, 'import']);
    }
    
    // 导出数据 POST /controller/export
    if ($reflection->hasMethod('export')) {
        Route::post("/{$prefix}/export", [$controllerClass, 'export']);
    }
    
    // 更新数据 PUT /controller/{id} (变量路由，必须在静态路由之后)
    if ($reflection->hasMethod('update')) {
        Route::put("/{$prefix}/{id}", [$controllerClass, 'update']);
    }
    
    // 读取数据 GET /controller/{id}
    if ($reflection->hasMethod('read')) {
        Route::get("/{$prefix}/{id}", [$controllerClass, 'read']);
    }
    
    // 删除数据 DELETE /controller/{id}
    if ($reflection->hasMethod('destroy')) {
        Route::delete("/{$prefix}/{id}", [$controllerClass, 'destroy']);
    }
}