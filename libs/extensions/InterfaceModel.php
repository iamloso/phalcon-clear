<?php
namespace PFrame\Libs\Extensions;

class InterfaceModel extends BaseModel
{
    public $centerConfig;
    public function initialize() {
        $this->centerConfig = \Phalcon\DI::getDefault()->getShared('centerConfig');
        parent::initialize();
    }

    /**
     * 创建一条新数据
     * @param array $data
     * @return bool|array
     */
    public function createData($data = array())
    {
        $this->modelLog(__CLASS__, __FUNCTION__, __LINE__, 'model createData params data:'.json_encode($data, JSON_UNESCAPED_UNICODE), 'ERROR');
        if (empty($data)) {
            $this->modelLog(__CLASS__, __FUNCTION__, __LINE__, 'params user_id is empty!', 'ERROR');
            return false;
        }
        $className = get_class($this);
        $Model = new $className();

        foreach ($data as $key => $value) {
            if (in_array($key, $this->columns)) {
                $Model->$key = $value;
            } else {
                $this->modelLog(__CLASS__, __FUNCTION__, __LINE__, 'column:'.$key.' is unknown', 'ERROR');
                return false;
            }
        }

        if ($Model->create() == false) {
            foreach ($Model->getMessages() as $message){
                $this->modelLog(__CLASS__, __FUNCTION__, __LINE__, 'create failed message:'.$message, 'ERROR');
            }
            return false;
        } else {
            return $Model->toArray();
        }
    }

    /**
     * 更新数据
     * @param array $data
     * @param array $condition
     * @return bool|array
     */
    public function updateData($data = array(), $condition = array())
    {
        $this->modelLog(__CLASS__, __FUNCTION__, __LINE__, 'model updateData params data:'.json_encode($data, JSON_UNESCAPED_UNICODE).' condition:'.json_encode($condition, JSON_UNESCAPED_UNICODE), 'ERROR');
        if (empty($data)) {
            $this->modelLog(__CLASS__, __FUNCTION__, __LINE__, 'update params is empty!', 'ERROR');
            return false;
        }

        if (empty($condition)) {
            $this->modelLog(__CLASS__, __FUNCTION__, __LINE__, 'condition params is empty!', 'ERROR');
            return false;
        }

        $className = get_class($this);
        $Model = new $className();

        $con = '';
        foreach ($condition as $key => $value) {
            $con .= " $key='$value' and";
        }
        $con = trim($con, 'and');
        $Model = $Model->findFirst($con);
        if (empty($Model)) {
            $this->modelLog(__CLASS__, __FUNCTION__, __LINE__, 'condition result is empty!', 'ERROR');
            return false;
        }
        foreach ($data as $key => $value) {
            if (in_array($key, $this->columns)) {
                $Model->$key = $value;
            } else {
                $this->modelLog(__CLASS__, __FUNCTION__, __LINE__, 'column:'.$key.' is unknown', 'ERROR');
                return false;
            }
        }

        if ($Model->update() == false) {
            foreach ($Model->getMessages() as $message){
                $Model->modelLog(__CLASS__, __FUNCTION__, __LINE__, 'save failed message:'.$message, 'ERROR');
            }
            return false;
        } else {
            return $Model->toArray();
        }
    }


    /**
     * 根据条件,获取一条数据
     * @param array $condition
     * @return array|bool
     */
    public function getOneData($condition = array())
    {
        if (empty($condition)) {
            $this->modelLog(__CLASS__, __FUNCTION__, __LINE__, 'condition params is empty!', 'ERROR');
            return false;
        }
        $Model = $this->findFirst($condition);
        if (empty($Model)) {
            $this->modelLog(__CLASS__, __FUNCTION__, __LINE__, 'condition get result is empty!', 'ERROR');
            return false;
        }
        return $Model->toArray();
    }

    /**
     * 获取多条数据
     * @param $condition
     * @return bool
     */
    public function getData($condition)
    {
        if (empty($condition)) {
            $this->modelLog(__CLASS__, __FUNCTION__, __LINE__, 'condition params is empty!', 'ERROR');
            return false;
        }

        $Model = $this->find($condition);
        if (empty($Model)) {
            $this->modelLog(__CLASS__, __FUNCTION__, __LINE__, 'condition get result is empty!', 'ERROR');
            return false;
        }
        return $Model->toArray();
    }

    /**
     * sum操作
     * @param array $condition
     * @return bool|array
     */
    public function sumData($condition = array())
    {
        $this->modelLog(__CLASS__, __FUNCTION__, __LINE__, 'model sumData params data:'.json_encode($condition, JSON_UNESCAPED_UNICODE), 'ERROR');

        if (empty($condition)) {
            $this->modelLog(__CLASS__, __FUNCTION__, __LINE__, 'condition params is empty!', 'ERROR');
            return false;
        }

        $className = get_class($this);
        $Model = new $className();

        $result = $Model->sum($condition);

        return $result;
    }

    /**
     * SQL insert 一条或多条数据
     *
     * @param
     *            array 单条：$fields =>array("id"=>"123","name"=>"abc",....),key为表列表,vulue为要插入的值
     *            多条：$fields =>array(
     *            array("id"=>"123","name"=>"abc",....),key为表列表,vulue为要插入的值
     *            array("id"=>"234","name"=>"bbc",....),key为表列表,vulue为要插入的值
     *            array("id"=>"456","name"=>"cbc",....),key为表列表,vulue为要插入的值
     *            )
     * @return boolean
     */
    public function insertData($fields) {
        $this->modelLog(__CLASS__, __FUNCTION__, __LINE__, 'model insertData params data:'.json_encode($fields, JSON_UNESCAPED_UNICODE), 'INFO');
        if (empty ( $fields )) {
            return false;
        }
        try {
            // 数据库
            $db = $this->getDI ()->getdb ();
            $table = $this->getSource ();

            // 插入一条还是多条判断
            if (count ( $fields ) == count ( $fields, 1 )) { // 一维数组，插入一条数据
                $keys = array_keys ( $fields );
            } else { // 二维数组,插入多条数据
                $keys = array_keys ( $fields [0] );
            }
            // 插入列
            $columns = $values = '';
            foreach ( $keys as $k => $v ) {
                $columns .= $k != count ( $keys ) - 1 ? '`' .$v. '`' . ',' : '`'. $v. '`';
                $values .= $k != count ( $keys ) - 1 ? ':' . $v . ',' : ':' . $v;
            }

            $sql = "INSERT IGNORE INTO " . $table . " (" . $columns . ") " . "VALUES (" . $values . ")";
            $this->modelLog(__CLASS__, __FUNCTION__, __LINE__, 'sql:'.$sql, 'INFO');
            $dh = $db->prepare ( $sql );
            if (count ( $fields ) != count ( $fields, 1 )) {
                foreach ( $fields as $action ) {
                    $success = $dh->execute ( $action );
                    if ($success == false){
                        return false;
                    }
                }
            } else {
                $success = $dh->execute ( $fields );
            }

            return $success;
        } catch (\Exception $e) {
            $this->modelLog(__CLASS__, __FUNCTION__, __LINE__, 'DB:insert data function failed:'.$e->getMessage(), 'ERROR');
            return false;
        }
    }

    /**
     * 批量更新
     * @param $data
     * @param $condition
     * @return array
     */
    public function  updateDataMulti($data = array(), $condition = array()){
        $this->modelLog(__CLASS__, __FUNCTION__, __LINE__, 'model updateDataMulti params data:'.json_encode($data, JSON_UNESCAPED_UNICODE).' condition:'.json_encode($condition, JSON_UNESCAPED_UNICODE), 'ERROR');
        if (empty($data)) {
            $this->modelLog(__CLASS__, __FUNCTION__, __LINE__, 'update params is empty!', 'ERROR');
            return false;
        }

        if (empty($condition)) {
            $this->modelLog(__CLASS__, __FUNCTION__, __LINE__, 'condition params is empty!', 'ERROR');
            return false;
        }

        $setString = '';
        foreach ($data as $key => $value) {
            if (in_array($key, $this->columns)) {
                $setString .= " $key='$value', ";
            } else {
                $this->modelLog(__CLASS__, __FUNCTION__, __LINE__, 'column:'.$key.' is unknown', 'ERROR');
                return false;
            }
        }

        $setString = trim($setString, ', ');

        $con = '';
        foreach ($condition as $key => $value) {
            $con .= " $key='$value' and";
        }
        $con = trim($con, 'and');

        $Connection = \Phalcon\DI::getDefault()->get('db');
        $sql = "update {$this->getSource()} set $setString where $con ";
        $result = $Connection->execute($sql);

        return $result;
    }

}
