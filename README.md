# think-trace

用于ThinkPHP6+的页面Trace扩展，支持Html页面和浏览器控制台两种方式输出。

## 安装

~~~
composer require topthink/think-trace
~~~

## 配置

安装后config目录下会自带trace.php配置文件。

type参数用于指定trace类型，支持html和console两种方式。

## 升级功能

### type 为 Html 时，文件显示的地址可以直接跳转ide ，显示里sql产生的文件地址
### 调试tab 显示内容格式化

### type 为Console sql 显示发生的文:件行号。