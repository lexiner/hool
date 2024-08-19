<?php

namespace plugin\htool\com;

/**
 * 字符处理类
 */
class Str
{
    /**
     * 手机号码脱敏
     */
    public static function mobileMask($phone){
        return $phone ? ( substr($phone, 0, 3) . str_repeat("*", 4) . substr($phone, -4) ) : $phone;
    }
    /**
     * 下划线转驼峰
     */
    public static function camelize($uncamelized_words,$separator='_')
    {
        $uncamelized_words = $separator. str_replace($separator, " ", strtolower($uncamelized_words));
        return ltrim(str_replace(" ", "", ucwords($uncamelized_words)), $separator );
    }
    // 过滤掉emoji表情
    public static function filter_emoji($str)
    {
        $str = preg_replace_callback(    //执行一个正则表达式搜索并且使用一个回调进行替换
            '/./u',
            function (array $match) {
                return strlen($match[0]) >= 4 ? '' : $match[0];
            },
            $str);
        return $str;
    }
    /**
     * 身份证验证
     * @param $card
     * @return bool
     */
    public static function check_card($card)
    {
        $city = [11 => "北京", 12 => "天津", 13 => "河北", 14 => "山西", 15 => "内蒙古", 21 => "辽宁", 22 => "吉林", 23 => "黑龙江 ", 31 => "上海", 32 => "江苏", 33 => "浙江", 34 => "安徽", 35 => "福建", 36 => "江西", 37 => "山东", 41 => "河南", 42 => "湖北 ", 43 => "湖南", 44 => "广东", 45 => "广西", 46 => "海南", 50 => "重庆", 51 => "四川", 52 => "贵州", 53 => "云南", 54 => "西藏 ", 61 => "陕西", 62 => "甘肃", 63 => "青海", 64 => "宁夏", 65 => "新疆", 71 => "台湾", 81 => "香港", 82 => "澳门", 91 => "国外 "];
        $tip = "";
        $match = "/^\d{6}(18|19|20)?\d{2}(0[1-9]|1[012])(0[1-9]|[12]\d|3[01])\d{3}(\d|X)$/";
        $pass = true;
        if (!$card || !preg_match($match, $card)) {
            //身份证格式错误
            $pass = false;
        } else if (!$city[substr($card, 0, 2)]) {
            //地址错误
            $pass = false;
        } else {
            //18位身份证需要验证最后一位校验位
            if (strlen($card) == 18) {
                $card = str_split($card);
                //∑(ai×Wi)(mod 11)
                //加权因子
                $factor = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
                //校验位
                $parity = [1, 0, 'X', 9, 8, 7, 6, 5, 4, 3, 2];
                $sum = 0;
                $ai = 0;
                $wi = 0;
                for ($i = 0; $i < 17; $i++) {
                    $ai = $card[$i];
                    $wi = $factor[$i];
                    $sum += $ai * $wi;
                }
                $last = $parity[$sum % 11];
                if ($parity[$sum % 11] != $card[17]) {
                    //                        $tip = "校验位错误";
                    $pass = false;
                }
            } else {
                $pass = false;
            }
        }
        if (!$pass) return false;/* 身份证格式错误*/
        return true;/* 身份证格式正确*/
    }
    /**
     * 地址验证
     * @param string $link
     * @return false|int
     */
    public static function check_link(string $link)
    {
        return preg_match("/^(http|https|ftp):\/\/[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+[\/=\?%\-&_~`@[\]\’:+!]*([^<>\”])*$/", $link);
    }
    /**
     * 手机号验证
     * @param $phone
     * @return false|int
     */
    public static function check_phone($phone)
    {
        return preg_match("/^1[3456789]\d{9}$/", $phone);
    }
    /**
     * 匿名处理处理用户昵称
     * @param $name
     * @return string
     */
    public static function anonymity($name, $type = 1)
    {
        if ($type == 1) {
            return mb_substr($name, 0, 1, 'UTF-8') . '**' . mb_substr($name, -1, 1, 'UTF-8');
        } else {
            $strLen = mb_strlen($name, 'UTF-8');
            $min = 3;
            if ($strLen <= 1)
                return '*';
            if ($strLen <= $min)
                return mb_substr($name, 0, 1, 'UTF-8') . str_repeat('*', $min - 1);
            else
                return mb_substr($name, 0, 1, 'UTF-8') . str_repeat('*', $strLen - 1) . mb_substr($name, -1, 1, 'UTF-8');
        }
    }
     /**
     * 分级排序
     * @param $data
     * @param int $pid
     * @param string $field
     * @param string $pk
     * @param string $html
     * @param int $level
     * @param bool $clear
     * @return array
     */
    public static function sort_list_tier($data, $pid = 0, $field = 'pid', $pk = 'id', $html = '|-----', $level = 1, $clear = true)
    {
        static $list = [];
        if ($clear) $list = [];
        foreach ($data as $k => $res) {
            if ($res[$field] == $pid) {
                $res['html'] = str_repeat($html, $level);
                $list[] = $res;
                unset($data[$k]);
                sort_list_tier($data, $res[$pk], $field, $pk, $html, $level + 1, false);
            }
        }
        return $list;
    }
    /**
     * 时间戳人性化转化
     * @param $time
     * @return string
     */
    public static function time_tran($time)
    {
        // 计算当前时间与给定时间之间的差值
        $t = time() - $time;

        $f = array(
            '31536000' => '年',
            '2592000' => '个月',
            '604800' => '星期',
            '86400' => '天',
            '3600' => '小时',
            '60' => '分钟',
            '1' => '秒'
        );
        foreach ($f as $k => $v) {
            // 如果除法结果$c不等于0，且$k为整数
            if (0 != $c = floor($t / (int)$k)) {
                // 返回$c . $v . '前'
                return $c . $v . '前';
            }

        }
    }
    /**
     * sql 参数过滤
     * @param string $str
     * @return mixed
     */
    public static function sql_filter(string $str)
    {
        $filter = ['select ', 'insert ', 'update ', 'delete ', 'drop', 'truncate ', 'declare', 'xp_cmdshell', '/add', ' or ', 'exec', 'create', 'chr', 'mid', ' and ', 'execute'];
        // 转为大写
        $toupper = array_map(function ($str) {
            return strtoupper($str);
        }, $filter);
        return str_replace(array_merge($filter, $toupper, ['%20']), '', $str);//去掉所有非法字符
    }
    /**
     * 获取毫秒数
     * @return float
     */
    public static function msectime()
    {
        list($msec, $sec) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
    }
    /**
     * 时间格式化
     */
    public static function date_form($time,$special='')
    {
        if($special){
            return date($special,$time);
        }
        return date('Y-m-d H:i:s',$time);
    }
}