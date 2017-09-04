<?php
namespace PFrame\Libs\Common;

/**
 * 金融计算工具类
 **/
class Finance
{
    const DAY_OF_YEAR = 360;    //金融计算通常将一年作为360天计算
    const MONTH_OF_YEAR = 12;   //一年中的月数
    const RATE_DIGIT = 5; //利率位数
    private static $DAY_OF_YEAR = 360; //金融计算通常将一年作为360天计算

    /**
     * 设置一年天数
     * @param $day
     */
    public static function setDayOfYear($day)
    {
        self::$DAY_OF_YEAR = $day;
    }
    /**
     * 根据还款方式将年化利率转换为期间利率
     *
     * @param integer $repay_mode 还款方式
     * @param float $rate 年化借款利率
     * @param int $period 借款期限, 单位以还款方式为准
     * @param bool $is_round 结果是否四舍五入，默认为true
     * @param array $loan_type_enum
     *
     * @return array
     **/
    public static function convertToPeriodRate($repay_mode, $rate, $period, $is_round=true, $loan_type_enum) {
        $period = $period >= 0 ? $period : 0;
        $LOAN_TYPE_ENUM = $loan_type_enum;
        if($repay_mode == $LOAN_TYPE_ENUM['BY_ONCE_TIME_BY_DAY']){
            $period_rate = $period / self::$DAY_OF_YEAR * $rate;
        } else {
            $period_rate = $period / self::MONTH_OF_YEAR * $rate;
        }
        return $is_round ? round($period_rate, self::RATE_DIGIT) : $period_rate;
    }

    /**
     * 计算逾期罚息金额
     *
     * @param float $principal 本金
     * @param int $day 逾期天数
     * @param float $rate 年化利率
     * @param float $overdue_rate 逾期罚息系数
     *
     * @return float 逾期罚息金额
     **/
    public static function overdue($principal, $day, $rate, $overdue_rate)
    {
        return round($principal * ($day / self::$DAY_OF_YEAR * $rate * $overdue_rate), 2);
    }

    /**
     * PMT年金计算方法
     * [贷款本金×月利率×（1+月利率）^还款月数]÷[（1+月利率）^还款月数－1]
     * @param $i float 期间收益率
     * @param $n int 期数
     * @param $p float 本金
     * @return float 每期应还金额
     */
    public static function getPmtMoney($i, $n, $p) {
        return $p * $i * pow((1 + $i), $n) / ( pow((1 + $i), $n) -1);
    }


    /**
     * PMT每期应还本金计算方法
     * 等额本息还贷第n个月还贷本金：贷款本金*月利率(1+月利率)^(还款月数-1)/[(1+月利率)^还款总期数-1]
     * @param $i float 期间收益率
     * @param $n int 期数
     * @param $p float 本金
     * @param $periods_index 当前第几期
     * @return float 每期应还金额
     */
    public static function getPmtPrincipal($i, $n, $p,$periods_index) {
        return $p * $i * pow((1 + $i), $periods_index-1) / ( pow((1 + $i), $n) -1);
    }
}
