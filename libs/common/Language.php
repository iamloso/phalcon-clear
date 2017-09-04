<?php
namespace PFrame\Libs\Common;

/**
 * 多语言支持
 *
 */
class Language
{
    /**
     * @var string 当前语言
     */
    public static $current_lang = 'zh-cn';

    /**
     * @var array 按package, file结构进程内缓存避免重复IO
     */
    public static $lang;
    public static $package = 'messages';

    /**
     * 设置当前的语言
     *
     * @param string 格式：zh-cn
     * @return string
     */
    public static function set_current_lang($lang)
    {
        self::$current_lang = strtolower(str_replace(array(' ', '_'), '-', $lang));
    }


    /**
     * 获取单个或多项目
     *
     * @param $key
     * @return string
     */
    public static function text($key)
    {
        self::load($key);
        $found = self::$lang[$key];
        return $found;
    }

    /**
     *
     * @param string $key key
     * @param string $val1 参数1，用于替换lang[$key]中的%s
     * @param string $val2 参数2
     *  ...   可以有多个参数
     * @return string
     */
    public static function dynamic_text($key, $val1, $val2 = null)
    {
        $args = func_get_args();
        array_shift($args);
        $text = self::text($key);
        /* 把字符串中%数字%替换成(%数字$),指定参数匹配占位符的顺序，默认为顺序匹配：%1$s对应$args中的第一个元素*/
        return vsprintf(preg_replace('/%(\d)%/', '%$1$s', strval($text)), $args);
    }

    public static function _($key)
    {
        return self::text($key);
    }

    /**
     * 返回指定语言和分组的所有信息
     *
     * @param string $key 需要载入的语言
     * @return array
     */
    public static function load($key)
    {
        if (empty($key)) {
            exit("language key is required");
        }

        // 预判
        if (isset(self::$lang)) {
            return;
        }

        // 处理路径
        $path = ROOT_PATH . DIRECTORY_SEPARATOR . 'libs/plugins' . DIRECTORY_SEPARATOR . 'i18n' . DIRECTORY_SEPARATOR;

        $path .= self::$current_lang . DIRECTORY_SEPARATOR . self::$package . ".php";
        // 只在APPPATH中查找
        if (!file_exists($path)) {
            exit("language file \"" . self::$current_lang . ":{" . self::$package . "}\" not exists");
        } else {
            $lang = include_once $path;
        }
        // 保存
        self::$lang = $lang;
    }

    protected static function split_key($key)
    {
        //安全起见，将连续的多个"."认为是一个"."
        $path = explode('.', preg_replace('#\.{2,}#', '.', $key));
        $actural_key = array_pop($path);
        $package = implode(DIRECTORY_SEPARATOR, $path);
        return array($package, $actural_key);
    }
}