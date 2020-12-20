<?php
require_once('config.php');

class db{
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pwd = DB_PWD;
    private $db_name = DB_NAME;

    public $link;
    public $error;

  
    public function connect(){
        $dsn = "mysql:host=".$this->host.";dbname=".$this->db_name;
        $pdo = new PDO($dsn, $this->user, $this->pwd);

        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    
    }
    function fetch($table_name, $select_array=[]){ 
        $response['success'] = 0;
        
        $select = '';
        foreach($select_array as $key=>$value){
            
            if(empty($select) && $select == ''){
                $select = "$key=:$key";
            }else{
                $select .= " AND $key=:$key";
            }
        }
        $sql ="SELECT * FROM `$table_name` WHERE $select";       

        $result = $this->connect()->prepare($sql); 
        
        foreach($select_array as $key => &$value){  

            $value = $this->cleanData($value);
             
            $result->bindParam($key, $value);    
                    
        }          

        $result->execute();
        $results = $result -> fetch();
        $row_count = $result->rowCount();                      

        if($row_count > 0)
        {
            $results['success'] = 1;                

        }else{
            $results['success'] = 0;                
        }

        return $results;
    }
    function fetch_all($table_name, $where_array = [], $orderby = 0, $limit = 0){ 
        $response['success'] = 0;
        $rule = '';

        if(isset($where_array) && count($where_array) >= 1 ){

            $where = '';
            foreach($where_array as $key=>$value){
                
                if(empty($where) && $where == ''){
                    $where = "$key=:$key";
                }else{
                    $where .= " AND $key=:$key";
                }
            }


            $rule ="WHERE $where"; 
        }

        if( isset($orderby) && $orderby !== 0 ){
            if(!empty($rule)){
                $rule .=" ORDER BY $orderby";
            }else{
                $rule ="ORDER BY $orderby";
            }
        }

        if(isset($limit) && $limit !== 0 ){
            if(!empty($rule)){
                $rule .=" LIMIT $limit";
            }else{
                $rule ="LIMIT $limit";
            }            
        }        
        
        $sql = "SELECT * FROM `$table_name` $rule";


        $result = $this->connect()->prepare($sql); 
        
        if(isset($where_array) && count($where_array) >= 1){
            
            foreach($where_array as $key => &$value){  
                $value = $this->cleanData($value);
             
                $result->bindParam($key, $value);    
                        
            }             
        }

        $result->execute();

        $row_count = $result->rowCount();

        if($row_count > 0){
            $results = $result -> fetchAll();    
        }else{
            $results = 0;                
        }

        return $results;
    }
    
    function insert($table_name, $values_array = []){   

        $response = 0;       

        foreach($values_array as $key=>$value){
            if(empty($select)){
                $select = "$key";
                $values = ":$key";                
            }else{
                $select .= ",$key";
                $values .= ",:$key";                
            }
        }      
        
        $sql ="INSERT INTO `$table_name`($select) VALUES ($values)";    
   
        $result = $this->connect()->prepare($sql);

        foreach($values_array as $key => &$value){ 
            // $value = $this->cleanData($value);
             
            $result->bindParam($key, $value);    
                    
        }  

        $execute = $result->execute();              
    
        if($execute==0){            
              
            return $response = 0;           
        }else{
             
            return $response = 1;         
        }
    
        return $response;
    }    
      

    function update($table_name, $values = [], $where_array = []){

        foreach($values as $key=>$value){
            if(empty($data)){                
                $data = '`'.$key.'`'.'='.":".$key;
            }else{                
                $data .= ','.'`'.$key.'`'.'='.":".$key;
            }           
            
        }     

        $where = '';
        foreach($where_array as $key=>$value){
            
            if(empty($where) && $where == ''){
                $where = "$key=:$key";
            }else{
                $where .= " AND $key=:$key";
            }
        } 

        $sql ="UPDATE `$table_name` SET $data WHERE $where";   

        $result = $this->connect()->prepare($sql);

        foreach($values as $key => &$value){  
            // $value = $this->cleanData($value); 
                      ; 
            $result->bindParam($key, $value);                     
        } 
        foreach($where_array as $key => &$value){
            // $value = $this->cleanData($value);               
            $result->bindParam($key, $value);                     
        }  
        
        $execute = $result->execute();                                

            if($execute !== 0)
            {
                $response = 1;                

            }else{
                $response = 0;                
            }
            
            return $response;           
    }
 
    function delete($table_name, $where_array = []){  
        
        $where = '';
        foreach($where_array as $key=>$value){
            
            if(empty($where) && $where == ''){
                $where = "$key=:$key";
            }else{
                $where .= " AND $key=:$key";
            }
        } 
       

        $sql ="DELETE FROM `$table_name` WHERE $where";   

        $result = $this->connect()->prepare($sql);
        foreach($where_array as $key => &$value){  
            $value = $this->cleanData($value);             
            $result->bindParam($key, $value);                     
        } 
        $execute = $result->execute();

        if($execute==0){         
            $response = 0;                          
        }else{   
           
            $response = 1;                      
        }
        return $response;
        
    }   
    
    function cleanData($data){
        $data = str_replace("\\","\\\\","$data");
        $data = str_replace("'","\'","$data");
        $data = str_replace('"','\"',"$data");

        return $data;
    }
    function escHtml($data){
        $data = htmlspecialchars($data);
        $data = strip_tags($data);
        $data = stripslashes($data);

        $data = stripcslashes($data);

        $data = str_replace("\\","\\\\","$data");
        $data = str_replace("'","\'","$data");
        $data = str_replace('"','\"',"$data");
        
        $data = str_replace('-','',"$data");
        $data = str_replace('--','',"$data");
        $data = str_replace(';','',"$data");
        $data = str_replace('<script>','',"$data");
        $data = str_replace('</script>','',"$data");

        return $data;
    }

}
$db = new db();











