<?php
namespace PFrame\Libs\Extensions;

use PFrame\Libs\Common\Common;
use PFrame\Libs\Common\SLog;
use Phalcon\Mvc\Model\Validator\PresenceOf;
use Phalcon\Mvc\Model\Validator\Email as EmailValidator;
use Phalcon\Mvc\Model\Validator\InclusionIn as InclusionIn;
use Phalcon\Mvc\Model\Validator\ExclusionIn as ExclusionIn;
use Phalcon\Mvc\Model\Validator\StringLength as StringLength;
use Phalcon\Mvc\Model\Validator\Numericality as Numericality;
use Phalcon\Mvc\Model\Validator\Regex as Regex;
use Phalcon\Mvc\Model\Validator\Url as UrlValidator;
use Phalcon\Mvc\Model\Validator\Uniqueness;

class BaseModel extends \Phalcon\Mvc\Model
{
    public static $primaryKey = 'id';
    public $attrAddTime    = 'add_time';
    public $attrAddIp      = 'add_ip';
    public $attrModifyTime = 'modify_time';
    public $attrDelete     = 'is_delete';
    public $metaData;
    public $columns        = array();

    /**
     * 操作
     * @var unknown
     */
    public static $OPT = array(
        0 => 'OP_NONE',
        1 => 'OP_CREATE',
        2 => 'OP_UPDATE',
        3 => 'OP_DELETE',
    );

    public function initialize()
    {
        $this->keepSnapshots(true);//保持快照
        $this->useDynamicUpdate(true);//动态更新
        $this->setup(array(
            'notNullValidations' => true,
        ));
        $this->setColumns();
    }

    /**
     * model 层日志
     * @param $className
     * @param $function
     * @param $line
     * @param $log
     * @param string $logType
     */
    public function modelLog($className, $function, $line, $log, $logType = 'INFO')
    {
        if( $logType == 'ERROR' ) {
            $logType = SLog::ERROR;
        } elseif ( $logType == 'WARNING' ) {
            $logType = SLog::WARNING;
        } elseif ( $logType == 'DEBUG' ) {
            $logType = SLog::DEBUG;
        } elseif ( $logType == 'CRITICAL' ) {
            $logType = SLog::CRITICAL;
        } else {
            $logType = SLog::INFO;
        }

        $modelName = str_replace("\\", "-", get_class($this)); //get_called_class()
        $logPath = TMP_PATH_LOG.date('Y-m-d').'/model_log/'.$modelName.'.log';
        SLog::writeLog($className.'::'.$function.'|'.$log.'('.$line.')', $logType, $logPath);
    }

    /**
     * 自动更新一些变量
     * @return boolean
     */
    public function beforeSave()
    {
        if (isset ($this->attrAddTime) && !empty ($this->attrAddTime)) {
            $this->writeAttribute($this->attrAddTime, time());
        }
        $this->skipAttributesOnUpdate([$this->attrAddTime]);
        if (isset ($this->attrAddIp) && !empty ($this->attrAddIp)) {
            $this->writeAttribute($this->attrAddIp, Common::getClientIp());
        }
        if (isset ($this->attrModifyTime) && !empty ($this->attrModifyTime)) {
            $this->writeAttribute($this->attrModifyTime, time());
        }
        return true;
    }

    /**
     * 重写删除操作
     */
    public function delete()
    {
        $className = str_replace("\\", "-", get_class($this));
        if (isset($this->is_delete)) {//逻辑删除
            $this->is_delete = 1;
            if ($this->save() == false) {//保存状态
                foreach ($this->getMessages() as $message) {
                    $message .= $message;
                }
                $this->modelLog(__CLASS__, __FUNCTION__, __LINE__, 'logic delete failed========' . $className . ':' . $message, 'ERROR');
                return false;
            }
            return true;
        } else {//永久删除
            if (parent::delete() == false) {//物理删除
                foreach ($this->getMessages() as $message) {
                    $message .= $message;
                }
                $this->modelLog(__CLASS__, __FUNCTION__, __LINE__, 'forever delete failed========' . $className . ':' . $message, 'ERROR');
                return false;
            }
            return true;
        }
    }

    /**
     * 永久删除
     */
    public function foreverDelete()
    {
        $className = str_replace("\\", "-", get_class($this));
        if (parent::delete() == false) {//物理删除
            foreach ($this->getMessages() as $message) {
                $message .= $message;
            }
            $this->modelLog(__CLASS__, __FUNCTION__, __LINE__, 'forever delete failed========' . $className . ':' . $message, 'ERROR');
            return false;
        }
        return true;
    }

    /**
     *  Validations and business logic
     *  验证字段是否合法,在具体的model中定义rules
     *    如：public $rules = array(
     *   'name'=>array('filter' => 'Email', 'message' => '邮箱格式不正确！'));
     * @return boolean
     */
    public function validation()
    {
        if (isset($this->rules) && !empty($this->rules)) {
            if (is_array($this->rules) && !empty($this->rules)) {
                foreach ($this->rules as $rk => $rv) {
                    switch ($rv['filter']) {
                        case 'Email' ://检测值是否为合法的email地址
                            $this->validate(new EmailValidator(array(
                                'field' => $rk,
                                "message" => $rv['message']
                            )));
                            break;
                        case 'PresenceOf'://检测字段的值是否为非空
                            $this->validate(
                                new PresenceOf(array(
                                    "field" => $rk,
                                    "message" => $rv['message']
                                ))
                            );
                            break;
                        case 'ExclusionIn'://检测值是否不在列举的范围内
                            $this->validate(new ExclusionIn(array(
                                'field' => $rk,
                                'message' => $rv['message'],
                                'domain' => $rv['option']['domain']
                            )));
                            break;
                        case 'InclusionIn'://检测值是否在列举的范围内
                            $this->validate(new InclusionIn(array(
                                "field" => $rk,
                                'message' => $rv['message'],
                                'domain' => $rv['option']['domain']
                            )));
                            break;
                        case 'Regex'://检测值是否匹配正则表达式
                            $this->validate(new Regex(array(
                                "field" => $rk,
                                'message' => $rv['message'],
                                'pattern' => $rv['option']['pattern']
                            )));
                        case 'StringLength'://检测值的字符串长度
                            $this->validate(new StringLength(array(
                                "field" => $rk,
                                'max' => $rv['option']['max'],
                                'min' => $rv['option']['min'],
                                'messageMaximum' => $rv['option']['Msgmaximum'],
                                'messageMinimum' => $rv['option']['MsgMinimum'],
                            )));
                            break;
                        case 'PhoneNo'://检验手机号
                            $this->validate(new Regex(array(
                                "field" => $rk,
                                'message' => $rv['message'],
                                'pattern' => '/^1[34578]\d{9}$/',
                            )));
                            break;
                        case 'Numericality'://Allows to validate if a field has a valid numeric format
                            $this->validate(new Numericality(array(
                                "field" => $rk,
                                'message' => $rv['message'],
                            )));
                            break;
                        case 'Url':
                            $this->validate(new UrlValidator(array(
                                "field" => $rk,
                                'message' => $rv['message'],
                            )));
                            break;
                        case 'Uniqueness':
                            $this->validate(new Uniqueness(array(
                                "field" => $rk,
                                "message" => $rv['message']
                            )));
                            break;
                        default:
                            break;
                    }
                }
            }
            if ($this->validationHasFailed() == true) {
                return false;
            }
        }

        return true;
    }

    /**
     * 设置表字段数据
     */
    public function setColumns(){
        $this->metaData = $this->toArray();
        $this->columns = array_keys($this->metaData);
    }

}