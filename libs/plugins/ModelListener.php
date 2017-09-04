<?php

namespace PFrame\Libs\Plugins;

use PFrame\Libs\Common;
use PFrame\Libs\Common\SLog;
use Phalcon\Mvc\User\Plugin;

class ModelListener extends  Plugin{
    
    /**
     * 忽略的model
     * @var unknown
     */
    public static $skipModel = array('OperationLog');
    
    /**
     *忽略更改的字段
     * @var unknown
     */
    public static $skipFields = array('modify_time');
    /**
     * 删除字段
     * @var unknown
     */
    public $attr_delete = 'is_delete';
    
    /**
     * 操作
     * @var unknown
     */
    public static  $OPT = array(
            0 => 'OP_NONE',
            1 => 'OP_CREATE',
            2 => 'OP_UPDATE',
            3 => 'OP_DELETE',
    ) ;
    
    /**
     * 操作监听函数
     * @param unknown $event
     * @param unknown $model
     * @return boolean
     */
    public function OPChange($event, $model) {
        $Type = $event->getType();
        $ClassFullName = get_class($model);
        $classNameArray = explode('\\', $ClassFullName);
        $ClassName = end($classNameArray);
        //忽略
        if (in_array($ClassName,self::$skipModel)) {
            return true;
        }
        
        $data = array();
        
        $OM = $model->getOperationMade ();
//         $ClassName = strtolower($ClassName);

        switch ($Type) {
            case 'afterSave':
                $is_delete = 0;
                if ($model->hasSnapshotData ()) {//是否有快照
                    $snapData= $model->getSnapshotData ();//获取快照数据（修改之前的数据）
                    $changeFields = $model->getChangedFields ();//获取改变的字段名称
                    
                    //处理的改变前后的数据
                    if (is_array($changeFields) && $changeFields)
                    {
                        //逻辑删除特殊处理
                        if (in_array($this->attr_delete, $changeFields) && $model->{$this->attr_delete} == 1) {
                            $is_delete = 1;
                        }
                        //收集数据
                        foreach ($changeFields as $field) {
                            if (in_array($field, self::$skipFields)) {
                               continue;
                            }
                            $id = isset($snapData['id']) &&  $snapData['id'] ? $snapData['id'] :(isset($snapData['user_id']) && $snapData['user_id'] ? $snapData['user_id'] : 0 );
                            $data['old']['id'] = $data['new']['id'] = $id;
                            $data['old'][$field] = $snapData[$field];
                            $data['new'][$field] = $model->$field;
                        }
                        
                    }
                }
                
                //如果没有数据改变，返回
                if (empty($data)) {
                   return ;
                }
                
                $data['class'] = $ClassName;
                $data['operation'] = $is_delete ? self::$OPT[3] : self::$OPT[$OM] ;//逻辑删除特殊处理
                break;
                
            case 'afterDelete':
                if ($model->hasSnapshotData ()) {//是否有快照
                    $snapData= $model->getSnapshotData ();//获取快照数据（修改之前的数据）
                    $data['operation'] = self::$OPT[$OM] ;
                    $data['old'] = $snapData;
                    $data['new'] = '';
                    $data['class'] = $ClassName;
                }
                break;
                
            case 'afterCreate':
                $data['operation'] = self::$OPT[$OM] ;
                $data['class'] = $ClassName;
                $data['old'] = '';
                $data['new'] = $model->toArray();
                break;
                
            default:
                return ;
        }
        if ($data) {
//             $this->operationLog->extraLog[$ClassName] = $data;
            $this->operationLog->extraLog = $data;
            $this->operationLog->writeOptLog();
        }
    }
}