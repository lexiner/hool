<?php

namespace lexiner\htool\com;

 /**
 * 注意 本类仅适用于PHP7.0版本以上   
 * 请注意：mongoDB 支持版本 3.2+
 * mongo具体参数参考： https://docs.mongodb.com/manual/reference/command/
 */
class Mongodb {
    private $manager;
    private $dbname='yun';
    /**
     * 创建实例
     * @param  string $confkey
     * @return object
     */
    public function __construct($dns='',$dbname=''){
        if($dns){
          $this->manager = new MongoDB\Driver\Manager($dns);
        }else{
          $this->manager = new MongoDB\Driver\Manager('mongodb://localhost:27017');
        }
        if($dbname){
          $this->dbname = $dbname;
        }
    }
    /**
     * 插入
     */
    public function insert($table,$data){
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->insert($data);
        $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
        $res = $this->manager->executeBulkWrite($this->dbname.'.'.$table, $bulk, $writeConcern);
        return $res?true:false;
    }
    public function insert_batch($table,$data)
    {
        $bulk = new MongoDB\Driver\BulkWrite;
        foreach ($data as $val){
            $bulk->insert($val);
        }
        $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
        $res = $this->manager->executeBulkWrite($this->dbname.'.'.$table, $bulk, $writeConcern);
        return $res?true:false;
    }
    /**
     * 查询
     * eg:['age' => 24]]
     * eg；$options = [
     *      'projection' => ['_id' => 0], //不输出_id字段
     *      'sort' => ['leavetime'=>-1] //根据user_id字段排序 1是升序，-1是降序
     *   ];
     */
    public function select($table,$filter,$options=array()){
        !$filter && dieError('param of filter is error');
        $options['projection']=['_id' => 0];
        $query = new MongoDB\Driver\Query($filter, $options); //查询请求
        $cursor = $this->manager->executeQuery($this->dbname.'.'.$table, $query);
        $result = [];
        foreach($cursor as $doc) {
          $result[] = (array)$doc;
        }
        return $result;
    }
    /**
     * 修改
     * eg:$condition=['name' => 'JetWu5']
     * eg:$set_array= ['$set' => ['age' => 30, 'promise' => 'always smile!']]
     */
    public function update($table,$condition=array(),$set_array=array()){
        !$condition && dieError('param of condition is error');
        !$set_array && dieError('param of set_array is error');
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->update(
          $condition,
          $set_array
        );
        $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);//可选，修改确认
        $res = $this->manager->executeBulkWrite($this->dbname.'.'.$table, $bulk, $writeConcern);
        return $res?true:false;
    }
    /**
     * 删除
     * eg:$condition=['name' => 'JetWu5']
     * if $condition==[] then delete all table documents!
     */
    public function delete($table,$condition=[]){
        !is_array($condition) && dieError('param of condition is error');
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->delete($condition);
        $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);//可选，修改确认
        $res = $this->manager->executeBulkWrite($this->dbname.'.'.$table, $bulk, $writeConcern);
        return $res?true:false;
    }
    function exec($opts) {
        $cmd = new MongoDB\Driver\Command($opts);
        $cursor =  $this->manager->executeCommand($this->dbname, $cmd);
        $result = [];
        foreach($cursor as $doc) {
          $result[] = (array)$doc;
        }
        return $result;
    }
}
