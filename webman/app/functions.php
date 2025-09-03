<?php
/**
 * Here is your custom functions.
 */
if (!function_exists('ll')) {
    function ll($data, $tag = 'All', $style = true)
    {
        $path = public_path() . DIRECTORY_SEPARATOR. 'logs' . DIRECTORY_SEPARATOR . $tag . DIRECTORY_SEPARATOR;
        if (!is_dir($path)) {
            try {
                mkdir($path, 0755, true);
            } catch (\Exception $e) {
                return;
            }
        }

        $fullPath =  $path . date('Ymd', time()) . '.log';
        if (is_file($fullPath) && floor(2097152) <= filesize($fullPath)) {
            try {
                rename($fullPath, dirname($fullPath) . DIRECTORY_SEPARATOR . time() . '-' . basename($fullPath));
            } catch (\Exception $e) {
            }
        }

        $style && file_put_contents($fullPath, date('Y-m-d H:i:s', time()) . "\r\n", 8);
        file_put_contents($fullPath, json_encode($data, 64 | 256) . "\r\n", 8);
        $style && file_put_contents($fullPath, str_pad("\r\n", 64, '-', STR_PAD_LEFT), 8);
    }
}
if (!function_exists('consoleLog')) {
    function consoleLog($data, $type = 0)
    {
        if ($type == 1) {
            ll($data, 'consoleLog');
        }
        if (!is_string($data)) $data = json_encode($data, 64 | 256);
        if(is_cli()) $hang = "\r\n";
        else $hang = "<br/>";
        echo date('Y/m/d H:i:s') . ' ' . $data . $hang;
    }
}
if (!function_exists('deleteFile')) {
    function deleteFile($filePath)
    {
        try {
            // 检查参数是否为空
            if (empty($filePath)) {
                return "错误：文件路径不能为空";
            }

            // 检查文件是否存在
            if (!file_exists($filePath)) {
                return "错误：文件 {$filePath} 不存在";
            }

            // 检查文件是否可写（有权限删除）
            if (!is_writable($filePath)) {
                return "错误：没有权限删除文件 {$filePath}";
            }

            // 执行删除操作
            if (unlink($filePath)) {
                return true;
            } else {
                return "错误：删除文件 {$filePath} 失败";
            }
        } catch (Exception $e) {
            return "错误：发生异常 - " . $e->getMessage();
        }
    }
}
if (!function_exists('removeValue')) {
    function removeValue($array, $valueToRemove) {
        return array_filter($array, function($value) use ($valueToRemove) {
            return $value != $valueToRemove;
        });
    }
}
if (!function_exists('getRecentTuesdayAndFridayDates')) {
    function getRecentTuesdayAndFridayDates() {
        // 获取当前日期时间
        $now = new DateTime();

        // 获取今天是星期几 (1-7, 1=Monday, 7=Sunday)
        $todayDayOfWeek = $now->format('N');

        // 计算最近的周五
        if ($todayDayOfWeek == 5) {
            $lastFriday = clone $now; // 今天就是周五
        } else {
            $daysToLastFriday = ($todayDayOfWeek - 5 + 7) % 7;
            $lastFriday = clone $now;
            $lastFriday->modify("-$daysToLastFriday days");
        }

        // 计算最近的周二
        if ($todayDayOfWeek == 2) {
            $lastTuesday = clone $now; // 今天就是周二
        } else {
            $daysToLastTuesday = ($todayDayOfWeek - 2 + 7) % 7;
            $lastTuesday = clone $now;
            $lastTuesday->modify("-$daysToLastTuesday days");
        }

        // 将两个日期放入数组
        $dates = [
            $lastFriday->format('Y-m-d 10:00:00'),
            $lastTuesday->format('Y-m-d 10:00:00')
        ];

        // 按日期从近到远排序
        rsort($dates);

        return $dates;
    }

}
if (!function_exists('getRecentMondayDates')) {
    function getRecentMondayDates() {
        // 获取当前日期时间
        $now = new DateTime();

        // 获取今天是星期几 (1-7, 1=Monday, 7=Sunday)
        $todayDayOfWeek = $now->format('N');

        // 计算最近的周一
        if ($todayDayOfWeek == 1) {
            $lastMonday = clone $now; // 今天就是周一
        } else {
            $daysToLastMonday = ($todayDayOfWeek - 1 + 7) % 7;
            $lastMonday = clone $now;
            $lastMonday->modify("-$daysToLastMonday days");
        }

        // 计算上一个周一
        $previousMonday = clone $lastMonday;
        $previousMonday->modify('-7 days');

        // 将两个日期放入数组
        $dates = [
            $lastMonday->format('Y-m-d 10:00:00'),
            $previousMonday->format('Y-m-d 10:00:00')
        ];

        // 按日期从近到远排序
        rsort($dates);

        return $dates;
    }
}