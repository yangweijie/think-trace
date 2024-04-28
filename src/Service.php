<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace think\trace;

use think\facade\Db;
use think\Service as BaseService;

class Service extends BaseService
{
    public function register()
    {
        $this->app->middleware->add(TraceDebug::class);
    }

    public function boot()
    {
        // 服务启动
        Db::listen(function ($sql, $time, $master){
            if (0 === strpos($sql, 'CONNECT:')) {
                trace($sql, 'sql');
                return;
            }

            // 记录SQL
            if (is_bool($master)) {
                // 分布式记录当前操作的主从
                $master = $master ? 'master|' : 'slave|';
            } else {
                $master = '';
            }
            $current = [];
            $handle = app('think\exception\Handle');
            if(!str_contains($sql, 'CONNECT') && !str_contains($sql, 'FULL COLUMNS')) {
                $stack = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 20);
                foreach ($stack as $k => $v) {
                    if(isset($v['file']) && !str_contains($v['file'], 'vendor')){
                        $current = $v;
                        $filePath = $v['file'];
                        if ($handle->isWin() && stripos($filePath, '/mnt') !== false) {
                            $filePath = str_replace('/mnt/', '', $filePath);
                            $filePathArr = explode('/', $filePath);
                            $filePathArr[0] .= ':';
                            $filePath = implode('/', $filePathArr);
                            $current['file'] = $filePath;
                        }
                        break;
                    }
                }
            }
            trace([
                'sql' => $sql,
                'info'=> ' [ ' . $master . 'RunTime:' . $time . 's ]',
                'file'=>$current['file']??'',
                'line'=>$current['line']??'',
                'jump'=> $current? $handle->getEditorHref($current['file'], $current['line']):'',
            ], 'sql');
        });
    }
}
