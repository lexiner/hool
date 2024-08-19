<?php

namespace plugin\htool\com;

/**
 * 数组处理类
 */
class Arr
{
    /**
     * 数据树形化
     * @param array $data 数据
     * @param string $childrenname 子数据名
     * @param string $keyName 数据key名
     * @param string $pidName 数据上级key名
     * @return array
     */
    public static function makeTree(array $data, string $childrenname = 'children', string $keyName = 'id', string $pidName = 'parent_id')
    {
        $list = [];
        foreach ($data as $value) {
            $list[$value[$keyName]] = $value;
        }
        $tree = []; //格式化好的树
        foreach ($list as $item) {
            if (isset($list[$item[$pidName]])) {
                $list[$item[$pidName]][$childrenname][] = &$list[$item[$keyName]];
            } else {
                $tree[] = &$list[$item[$keyName]];
            }
        }
        return $tree;
    }
    /**
     * 对象转数组
     * @param $object
     * @return array|mixed
     */
    public static function object2array($object)
    {
        $array = [];
        if (is_object($object)) {
            foreach ($object as $key => $value) {
                $array[$key] = $value;
            }
        } else {
            $array = $object;
        }
        return $array;
    }
    
}