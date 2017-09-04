<?php
namespace PFrame\Libs\Common;

use Phalcon\Validation;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\Identical;
use Phalcon\Validation\Validator\ExclusionIn;
use Phalcon\Validation\Validator\InclusionIn;
use Phalcon\Validation\Validator\Regex as RegexValidator;
use Phalcon\Validation\Validator\StringLength as StringLength;
use Phalcon\Validation\Validator\Between;
use Phalcon\Validation\Validator\Confirmation;
use Phalcon\HTTP\Request;

class FormValidation
{
    public $validator;
    public $message = '';
    /**
     * construct函数：添加检验对象
     * @param array $rules
     */
    public function __construct(array $rules)
    {
        $this->validator = new Validation();
        if(is_array($rules) && !empty($rules)) {
            foreach ($rules as $rk => $rv) {
                switch ($rv['filter']) {
                    case 'Email' ://检测值是否为合法的email地址
                        $Email ='Email';
                        $this->validator->add($rk, new Email(array('message' => $rv['message'])));
                        break;
                    case 'PresenceOf'://检测字段的值是否为非空
                        $this->validator->add($rk, new PresenceOf(array('message' => $rv['message'])));
                        break;
                    case 'Identical'://检测字段的值是否和指定的相同
                    $this->validator->add($rk, new Identical(array(
                                'value'   => $rv['option']['value'],
                                'message' => $rv['message']
                        )));
                        break;
                    case 'ExclusionIn'://检测值是否不在列举的范围内
                        $this->validator->add($rk, new ExclusionIn(array(
                                'message' => $rv['message'],
                                'domain' =>  $rv['option']['domain']//array(A,B)
                        )));
                        break;
                    case 'InclusionIn'://检测值是否在列举的范围内
                        $this->validator->add($rk, new InclusionIn(array(
                                'message' => $rv['message'],//'The status must be A or B',
                                'domain' => $rv['option']['domain']//array(A,B)
                        )));
                        break;
                    case 'RegexValidator'://检测值是否匹配正则表达式
                        $this->validator->add($rk, new RegexValidator(array(
                                'pattern' => $rv['option']['pattern'],//'/^[0-9]{4}[-\/](0[1-9]|1[12])[-\/](0[1-9]|[12][0-9]|3[01])$/',
                                'message' => $rv['message'],//'The creation date is invalid'
                        )));
                        
                        break;
                    case 'StringLength'://检测值的字符串长度
                        $this->validator->add($rk, new StringLength(array(
                                'max' => $rv['option']['max'],//50,
                                'min' => $rv['option']['min'],//2,
                                'messageMaximum' => $rv['option']['messageMaximum'],//'We don\'t like really long names',
                                'messageMinimum' => $rv['option']['messageMinimum'],//'We want more than just their initials'
                        )));
                        break;
                    case 'Between'://检测值是否位于两个值之间
                        $this->validator->add($rk, new Between(array(
                                'minimum' => $rv['option']['minimum'],//0,
                                'maximum' => $rv['option']['maximum'],//100,
                                'message' => $rv['message']//The price must be between 0 and 100'
                        )));
                        break;
                    case 'Confirmation': // 检测两个值是否相等
                        $this->validator->add ( $rk, new Confirmation ( array (
                                'message' => $rv['message'],//'Password doesn\'t match confirmation',
                                'with' => $rv['option']['with']//confirmPassword'
                        )));
                        break;
                    case 'PhoneNo'://检验手机号
                        $this->validator->add($rk, new RegexValidator(array(
                            'pattern' => '/^1[34578]\d{9}$/',
                            'message' => $rv['message'],//'The creation date is invalid'
                        )));
                        break;
                    case 'Integer'://检验整数
                        $this->validator->add($rk, new RegexValidator(array(
                            'pattern' => '/^\d{1,10}$/',
                            'message' => $rv['message'],//'The creation date is invalid'
                        )));
                        break;
                    default:
                        echo "验证规则参数不正确！";
                        break;
                }
            }
        }
        
        return true;
    }
    
    public function validate($params)
    {
        $messages = $this->validator->validate ( $params );
        
        if (count ( $messages )) {
            foreach ( $messages as $message ) {//输出第一个错误信息
                $this->message .= $message ;
                break;
            }
            return false;
        }
        
        return true;
    }
    
    public function ErrorOut($messages)
    {
        if (count ( $messages )) {
            foreach ( $messages as $message ) {//输出第一个错误信息
                echo $message;
            }
        }
        
        return true;
    }
/*例子在api中的controller中调用
            $rules = array(
            'name'=>array('filter' => 'PresenceOf', 'message' => '名称不能为空！'),
            'short_name' => array('filter' => 'StringLength',
                    'message' => '请输入指定字符串长度',
                    'option'=> array(
                            'min'=>6,
                            'max'=>10,
                            'messageMaximum'=>'We don\'t like really long names',
                            'messageMinimum'=>'We want more than just their initials')
            )
    );
        if (!$this->validate($rules)) {
            echo $this->getErrorMsg();
        }
     
     */
     
}
