<?php
// +----------------------------------------------------------------------
// | Description: redis 缓存封装使用
// +----------------------------------------------------------------------
// +----------------------------------------------------------------------
namespace lexiner\htool\com;

class Redis
{
    protected static $handler = null;
    protected $options = [
        'host' => '127.0.0.1',
        'port' => 6379,
        'password' => null,
        'select' => 0,
        'timeout' => 0,//关闭时间 0:代表不关闭
        'expire' => 0,
        'persistent' => false,
        'prefix' => '',
    ];

    public function __construct($options = [])
    {
        if (!extension_loaded('redis')) {   //判断是否有扩展(如果你的apache没reids扩展就会抛出这个异常)
            throw new \BadFunctionCallException('not support: redis');
        }
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        $func = $this->options['persistent'] ? 'pconnect' : 'connect';     //判断是否长连接
        self::$handler = new \Redis;
        self::$handler->$func($this->options['host'], $this->options['port'], $this->options['timeout']);

        if ('' != $this->options['password']) {
            self::$handler->auth($this->options['password']);
        }

        if (0 != $this->options['select']) {
            self::$handler->select($this->options['select']);
        }
    }

    /**
     * 写入缓存
     * @param string $key 键名
     * @param string $value 键值
     * @param int $exprie 过期时间 0:永不过期
     * @return bool
     */
    public static function set($key, $value, $exprie = 3600*24*2)
    {
        if ($exprie == 0) {
            $set = self::$handler->set($key, $value);
        } else {
            $set = self::$handler->setex($key, $exprie, $value);
        }
        return $set;
    }
    public static function del($key)
    {
        $set = self::$handler->del($key);
        return $set;
    }
    /**
     * 缓存+1
     * @param string $key 键名
     * @param string $value 键值
     * @param int $exprie 过期时间 0:永不过期
     * @return bool
     */
    public static function incr($key)
    {

        $set = self::$handler->INCR($key);

        return $set;
    }
    /**
     * 读取缓存
     * @param string $key 键值
     * @return mixed
     */
    public static function get($key)
    {
        $fun = is_array($key) ? 'Mget' : 'get';
        return self::$handler->{$fun}($key);
    }

    /**
     * 获取值长度
     * @param string $key
     * @return int
     */
    public static function lLen($key)
    {
        return self::$handler->lLen($key);
    }

    /**
     * 将一个或多个值插入到列表头部
     * @param $key
     * @param $value
     * @return int
     */
    public static function LPush($key, $value, $value2 = null, $valueN = null)
    {
        return self::$handler->lPush($key, $value, $value2, $valueN);
    }
    /**
     * 将一个值插入到列表尾部
     * @param $key
     * @param $value
     * @return int
     */
    public static function RPush($key, $value)
    {
        return self::$handler->RPush($key, $value);
    }
    /**
     * 根据索引查询列表元素
     * @param $key
     * @param $value
     * @return int
     */
    public static function LINDEX($key, $index)
    {
        return self::$handler->LINDEX($key, $index);
    }
    /**
     * 移出并获取列表的第一个元素
     * @param string $key
     * @return string
     */
    public static function lPop($key)
    {
        return self::$handler->lPop($key);
    }

    /**
     * 获取列表中从$star开始到$end 的值
     * @param string $key
     * @param string $star   开始的索引
     * @param string $key    结束的索引
     * @return string
     */
    public static function LRANGE($key,$star,$end)
    {
        return self::$handler->LRANGE($key,$star,$end);
    }
    /**
     * 删除列表中的全部值
     * @param string $key
     * @param string $value   值
     *
     * @return string
     */
    public static function lrem($key)
    {
        //dump($value);die;
        return self::$handler->LTRIM($key,1,0);
    }

}
