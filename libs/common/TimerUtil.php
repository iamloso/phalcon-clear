<?php
namespace PFrame\Libs\Common;

class TimerUtil
{
    /**
     * 使用树形方式统计时存储的数据
     *
     * @var array
     */
    private static $_stat = array();
    /**
     * 使用树形方式统计的时候，记录当前路径
     *
     * @var array
     */
    private static $_path = array();
    /**
     * 使用平面方式统计的时候存储的数据
     *
     * @var array
     */
    private static $_items = array();

    const TREE_LABELS_KEY = -1; //防止和其他label重复，所以使用-1做索引
    const TREE_ROOT_KEY = 0; //防止和其他label重复使用0做索引
    
    /**
     * 创建一个计时器，在内部记录其信息，并返回创建好的计时器，计时器创建完毕就开始计时。
     *
     * @param string $label
     * @return stdObject
     */
    public static function start ( $label )
    {
        $item = new \stdClass();
        $item->label = $label;
        $item->start = $item->stop = null;
        self::$_items[] = $item;
        $stat = &self::$_stat;
        foreach (self::$_path as $timer)
        {
            $key = $timer->label;
            $cnt = $stat[self::TREE_LABELS_KEY][$key];
            if ($cnt > 1) $key .= $cnt;
            $stat = &$stat[$key];
        }

        if (empty($stat[self::TREE_LABELS_KEY]))
        {
            $stat[self::TREE_LABELS_KEY] = array();
        }

        $localLabels = &$stat[self::TREE_LABELS_KEY];
        //记录当前记录下$label的个数，重命名已有重复的$label
        if (isset($localLabels[$label]))
        {
            if ($localLabels[$label] == 1)
            {
                $old_item = $stat[$label];
                unset($stat[$label]);
                $stat[$label . "1"] = $old_item;
            }
            $localLabels[$label] ++;
            $label = $label . $localLabels[$label];
            $stat[$label][self::TREE_ROOT_KEY] = $item;
        }
        else
        {
            $localLabels[$label] = 1;
            $stat[$label][self::TREE_ROOT_KEY] = $item;
        }
        self::$_path[] = $item;
        $item->start = microtime(1);
        return $item;
    }

    /**
     * 停止一个之前申请的计时器
     * 注意：必须按照计时器的逻辑顺序来停止，否则树形统计结果是错误的。
     *
     * @param string label
     */
    public static function stop ($label)
    {
        $len = count(self::$_path) - 1;
        if ($len < 0) return;
        $timer = null;
        for ($i = $len; $i >= 0; -- $i)
        {
            $timer = self::$_path[$i];
            if ($label == $timer->label)
            {
                $timer->stop = microtime(1);
                if ($i != $len)
                {
                    trigger_error("error stop timer '$label'", E_USER_WARNING);
                }   
                break;
            }
        }

        //将结束的计时器从当前路径去掉
        while (! empty(self::$_path))
        {
            $timer = array_pop(self::$_path);
            if (! $timer->stop)
            {
                array_push(self::$_path, $timer);
                break;
            }
        }
    }

    /**
     * 计算一个计时器的记录时间差
     *
     * @param stdObject $timer
     * @return int
     */
    private static function ellapse ($timer)
    {
        if (! $timer->stop) $timer->stop = microtime(1);
        return (int)(round(($timer->stop - $timer->start) * 1000000, 0));
    }

    /**
     * 转换树形统计结果中的数据
     *
     * @param array $arr
     * @return array
     */
    private static function calc ($arr)
    {
        unset($arr[self::TREE_LABELS_KEY]);
        foreach ($arr as &$o)
        {
            if (is_object($o))
            {
                $o = self::ellapse($o);
            }
            elseif (is_array($o))
            {
                $o = self::calc($o);
            }
        }
        if (count($arr) == 1 && isset($arr[self::TREE_ROOT_KEY])) return $arr[self::TREE_ROOT_KEY];
        return $arr;
    }

    /**
     * 给出所有计时器的树形统计结果，进行序列化输出
     *
     * @return string
     */
    public static function tree ()
    {
        $struct['timers'] = self::calc(self::$_stat);
        return $struct;
    }

    /**
     * 给出所有计时器的一维统计结果，用分隔符进行分割
     *
     * @param string $separator
     * @return string
     */
    public static function flat ($separator = '||')
    {
        $stats = array();
          
        $label = array();
        foreach (self::$_items as $o)
        {
            $name = $o->label;
            if (isset($label[$name]))
            {
                $label[$name]['total'] += 1;
                $label[$name]['current'] = 1;
            }
            else
            {
                $label[$name]['total'] = 1;
            }
        }
        
        $fmt = "%s=%d";
        foreach (self::$_items as $o)
        {
            $name = $o->label;
            if (1 == $label[$name]['total'])
            {
                $stats[] = sprintf($fmt, $name, self::ellapse($o));
            }
            else
            {
                $cur = $label[$name]['current'] ++;
                $stats[] = sprintf($fmt, $name . $cur, self::ellapse($o));
            }
        }

        return implode($separator, $stats);
    }

    /**
     * 清除所有计时器
     *
     */
    public static function reset ()
    {
        self::$_path = array();
        self::$_stat = array();
        self::$_items = array();
    }
}

