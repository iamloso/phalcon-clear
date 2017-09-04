<?php
namespace PFrame\Libs\Plugins\Cache;

class RedisCache
{
    /**
     * redis对象
     */
    public $redis;

    function __construct ($DbConfig)
    {
        $this->redis = new \Redis();
        $this->redis->connect($DbConfig->redis->host, $DbConfig->redis->port);
        if(!empty($DbConfig->redis->auth)){
            $this->redis->auth($DbConfig->redis->auth);
        }
        $this->redis->select($DbConfig->redis->selectdb);
        $this->redis->setOption(\Redis::OPT_PREFIX, $DbConfig->redis->prefix);
    }


    /**
     * 选择连接数据库
     * @param int $db
     * @return bool
     */
    public function selectDb($db=1){
        return $this->redis->select($db);
    }

    /**
     * 设置值
     * @param string $key 索引
     * @param string|array $value 保存的数据
     * @param int $timeOut 有效时间,单位秒
     * @return bool
     */
    public function set ($key, $value, $timeOut = 0)
    {
        $value = json_encode($value, TRUE);
        $retRes = $this->redis->set($key, $value);
        if ($timeOut > 0)
            $this->redis->setTimeout($key, $timeOut);
        return $retRes;
    }

    /**
     * 通过KEY获取数据
     * @param string $key 索引
     * @return array
     */
    public function get ($key)
    {
        $result = $this->redis->get($key);
        return json_decode($result, TRUE);
    }

    /**
     * 删除一条数据
     * @param string $key 索引
     */
    public function delete ($key)
    {
        return $this->redis->delete($key);
    }

    /**
     * 清空数据
     */
    public function flushAll ()
    {
        return $this->redis->flushAll();
    }

    /**
     * 数据入队列
     *
     * @param string $key
     *            KEY名称
     * @param string|array $value 入队列数据
     * @param bool $right  是否从右边开始入
     * @return bool
     */
    public function push ($key, $value, $right = true)
    {
        $value = json_encode($value);
        return $right ? $this->redis->rPush($key, $value) : $this->redis->lPush($key, $value);
    }

    /**
     * 数据出队列
     *
     * @param string $key
     * @param bool $left 是否从左边开始出数据
     * @return mixed
     */
    public function pop ($key, $left = true)
    {
        $val = $left ? $this->redis->lPop($key) : $this->redis->rPop($key);
        return json_decode($val);
    }

    /**
     * 获取列表中元素的个数
     * @param unknown $key
     * @return int
     */
    public function llen($key) {
        return $this->redis->llen($key);
    }
    
    /**
     * 获取在存储于列表的key索引的元素
     * 索引是从0开始的，所以0表示第一个元素，1第二个元素等等。
     * 负指数可用于指定开始在列表的尾部元素。这里，-1表示最后一个元素，-2指倒数第二个等等
     * @param unknown $key
     * @param unknown $index
     * @return mixed
     */
    public function lindex($key,$index) {
        $value = $this->redis->lindex($key,$index);
        return json_decode($value);
    } 
    
    /**
     * 让列表只保留指定区间内的元素，不在指定区间之内的元素都将被删除
     * @param unknown $key
     * @param unknown $start
     * @param unknown $stop
     * @return bool
     */
    public function ltrim($key, $start, $stop) {
        return $this->redis->ltrim($key, $start, $stop);
    }
    /**
     * 数据自增
     *
     * @param string $key
     * @return bool
     */
    public function increment ($key)
    {
        return $this->redis->incr($key);
    }

    /**
     * 数据自减
     *
     * @param string $key
     * @return bool
     */
    public function decrement ($key)
    {
        return $this->redis->decr($key);
    }

    /**
     * key是否存在，存在返回ture
     *
     * @param string $key
     * @return bool
     */
    public function exists ($key)
    {
        return $this->redis->exists($key);
    }

    /**
     * 设置值
     *
     * @param string $flag
     *            hash名称
     * @param string $key
     *            KEY名称
     * @param string|array $value
     *            获取得到的数据
     * @param int $timeOut
     *            时间
     * @return mixed
     */
    public function hset ($flag, $key, $value, $timeOut = 0)
    {
        $value = json_encode($value, TRUE);
        $retRes = $this->redis->hset($flag, $key, $value);
        return $retRes;
    }

    /**
     * 设置值
     *
     * @param string $flag
     *            hash名称
     * @param string $key
     *            KEY名称
     * @return mixed
     */
    public function hget ($flag, $key)
    {
        return $this->redis->hGet ($flag, $key);
    }

    /**
     * 设置值
     * 返回名称为h的hash中元素个数
     *
     * @param string $flag
     *            hash名称
     * @return int
     */
    public function hlen ($flag)
    {
        return $this->redis->hlen($flag);
    }

    /**
     * 设置值
     * 删除名称为h的hash中键为key1的域
     *
     * @param string $flag
     *            hash名称
     * @param string $key
     *            KEY名称
     * @return bool
     */
    public function hdel ($flag, $key)
    {
        return $this->redis->hdel($flag, $key);
    }

    /**
     * 设置值
     * 返回名称为key的hash中所有键
     *
     * @param string $flag
     *            hash名称
     * @return bool
     */
    public function hkeys ($flag)
    {
        return $this->redis->hkeys($flag);
    }

    /**
     * 设置值
     * 返回名称为h的hash中所有键对应的value
     *
     * @param string $flag
     *            hash名称
     * @return bool
     */
    public function hvals ($flag)
    {
        return $this->redis->hvals($flag);
    }

    /**
     * 设置值
     * 返回名称为h的hash中所有的键（field）及其对应的value
     *
     * @param string $flag
     *            hash名称
     * @return bool
     */
    public function hgetall ($flag)
    {
        return $this->redis->hgetAll($flag);
    }

    /**
     * 设置多个字段的值
     * @param string $flag
     * @param related array $RelateArray
     * @return bool
     */
    public function hmset($flag, $RelateArray)
    {
        return $this->redis->hMset($flag, $RelateArray);
    }
    /**
     * 设置值
     * 名称为h的hash中是否存在键名字为a的域
     *
     * @param string $flag
     *            hash名称
     * @return bool
     */
    public function hexists ($flag, $key)
    {
        return $this->redis->hexists($flag, $key);
    }

    /**
     * 设置值
     *
     * @param string $key
     *            KEY名称
     * @param string $value
     *            获取得到的数据
     * @return bool
     */
    public function sadd ($key, $value)
    {
        return $this->redis->sadd($key, $value);
    }

    /**
     * 删除一条数据
     *
     * @param string $key
     *            KEY名称
     * @param $value 值
     * @return bool
     */
    public function srem ($key, $value)
    {
        return $this->redis->srem($key, $value);
    }

    /**
     * value是否存在key，存在返回ture
     *
     * @param string $key
     *            KEY名称
     * @return bool
     */
    public function sismember ($key, $value)
    {
        return $this->redis->sismember($key, $value);
    }

    /**
     * 返回名称为key的set的元素个数
     *
     * @param string $key
     *            名称
     */
    public function scar ($key)
    {
        return $this->redis->scar($key);
    }

    /**
     * 返回名称为key的set的所有元素
     *
     * @param string $key
     *            名称
     * @return bool
     */
    public function smembers ($key)
    {
        return $this->redis->smembers($key);
    }

    /**
     * ******** zadd operation**********
     */
    /**
     * 返回名称为key的set的所有元素
     *
     * @param string $key
     *            名称
     * @return bool
     */
    public function zadd ($key, $score, $value)
    {
        return $this->redis->zadd($key, $score, $value);
    }

    /*
     * 对指定元素索引值的增减,改变元素排列次序
    */
    public function zincrby ($key, $score, $value)
    {
        return $this->redis->zincrby($key, $score, $value);
    }

    /*
     * 移除指定元素
    */
    public function zrem ($key, $value)
    {
        return $this->redis->zrem($key, $value);
    }

    /*
     * zrange 按位置次序返回表中指定区间的元素
    * $redis->zrange(‘zset1′,0,1); //返回位置0和1之间(两个)的元素
    * $redis->zrange(‘zset1′,0,-1);//返回位置0和倒数第一个元素之间的元素(

            相当于所有元素)
    */
    public function zrange ($key, $score, $value)
    {
        return $this->redis->zrange($key, $score, $value);
    }

    /*
     * zrangebyscore/zrevrangebyscore 按顺序/降序返回表中指定索引区间的元素
    * $redis->zadd(‘zset1′,3,’ef’); $redis->zadd(‘zset1′,5,’gh’);
    * $redis->zrangebyscore(‘zset1′,2,9); //返回索引值2-9之间的元素

    array(‘ef’,'gh’)
    * //参数形式 $redis->zrangebyscore(‘zset1′,2,9,’withscores’);
    * //返回索引值2-9之间的元素并包含索引值 array(array(‘ef’,3),array

            (‘gh’,5))
    * $redis->zrangebyscore(‘zset1′,2,9,array(‘withscores’
            * =>true,’limit’=>array(1, 2))); //返回索引值2-9之间的元

    素,’withscores’
    * =>true表示包含索引值; ‘limit’=>array(1,
            * 2),表示最多返回2条,结果为array(array(‘ef’,3),array(‘gh’,5))
    */
    public function zrangebyscore ($key, $score, $value)
    {
        return $this->redis->zrangebyscore($key, $score, $value);
    }


    /*
     * zunionstore/zinterstore 将多个表的并集/交集存入另一个表中
    * $redis->zunionstore(‘zset3′,array(‘zset1′,’zset2′,’zset0′));
    * //将’zset1′,’zset2′,’zset0′的并集存入’zset3′ //其它参数
    * $redis->zunionstore(‘zset3′,array(‘zset1′,’zset2′),array

            (‘weights’ =>
                    * array(5,0)));//weights参数表示权重，其中表示并集后值大于5的元素排在前

    ，大于0的排在后
    * $redis->zunionstore(‘zset3′,array(‘zset1′,’zset2′),array

            (‘aggregate’ =>
                    * ‘max’));//’aggregate’ => ‘max’或’min’表示并集后相同的元素是取

    大值或是取小值
    */
    public function zunionstore ($key, $array1, $array2)
    {
        return $this->redis->zunionstore($key, $array1, $array2);
    }

    /*
     * zcount 统计一个索引区间的元素个数
    * $redis->zcount(‘zset1′,3,5);//2
    * $redis->zcount(‘zset1′,’(3′,5));//’(3′表示索引值在3-5之间但不含

            3,同理也可以使用’(5′表示上限为5但不含5
                    */
    public function zcount ($key, $array1)
    {
        return $this->redis->zcount($key, $array1);
    }

    /*
     * zcard 统计元素个数 $redis->zcard(‘zset1′);//4
    */
    public function zcard ($key)
    {
        return $this->redis->zcard($key);
    }

    /*
     * zscore 查询元素的索引 $redis->zscore(‘zset1′,’ef’);//3
    */
    public function zscore ($key, $value)
    {
        return $this->redis->zscore($key, $value);
    }

    /*
     * //zremrangebyscore 删除一个索引区间的元素 $redis->zremrangebyscore(‘

             zset1′,0,2);
    * //删除索引在0-2之间的元素(‘ab’,'cd’),返回删除元素个数2
    */
    public function zremrangebyscore ($key, $value1, $value2)
    {
        return $this->redis->zremrangebyscore($key, $value1, $value2);
    }

    /*
     * zrank/zrevrank 返回元素所在表顺序/降序的位置(不是索引)
    * $redis->zrank(‘zset1′,’ef’);//返回0,因为它是第一个元素;zrevrank则

    返回1(最后一个)
    */
    public function zrank ($key, $value)
    {
        return $this->redis->zrank($key, $value);
    }

    /*
     * zremrangebyrank 删除表中指定位置区间的元素 $redis->zremrangebyrank(‘

             zset1′,0,10);
    * //删除位置为0-10的元素,返回删除的元素个数2
    */
    public function zremrangebyrank ($key, $value1, $value2)
    {
        return $this->redis->zremrangebyrank($key, $value1, $value2);
    }
}
