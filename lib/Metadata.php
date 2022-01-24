<?php
class Metadata {
    const COLUMNS = 'a.id, a.meta_key, a.meta_value, a.relational';
    private $tableName;
        
    public function __construct($table_name) {
        $this->setTableName($table_name);
    }
    
    public function add($relational, $key, $value, $unique = false) {
        $existing = false;
        if($unique == true) {
            $existing = $this->get($relational, $key, $unique);
        }
        
        if($existing == false) {
            \Ode\DBO::getInstance()->beginTransaction();
            $sth = \Ode\DBO::getInstance()->prepare("INSERT INTO " . $this->getTableName() . " (relational, meta_key, meta_value) VALUES (:relational, :key, :value)");
            $sth->bindParam(":key", $key, \PDO::PARAM_STR);
            $sth->bindParam(":value", $value, \PDO::PARAM_STR);
            $sth->bindParam(":relational", $relational);
            try {
                $sth->execute();
            } catch(\Exception $e) {
                error_log($e->getTraceAsString(), 0);
                return false;
            }
            
            $new_id = \Ode\DBO::getInstance()->query("SELECT LAST_INSERT_ID()")->fetchColumn();
            
            \Ode\DBO::getInstance()->commit();
            
            return $new_id;
        } else {
            $update = $this->update($existing->id, $value);
            if($update == false) {
                return false;
            }
            
            return $existing->id;
        }
    }
    
    public function update($id, $value) {
        $sth = \Ode\DBO::getInstance()->prepare("UPDATE " . $this->getTableName() . " SET meta_value = :value WHERE id = :id");
        $sth->bindParam(":value", $value, \PDO::PARAM_STR);
        $sth->bindParam(":id", $id);
        try {
            $sth->execute();
        } catch(\Exception $e) {
            return false;
        }
        
        return true;
    }
    
    public function get($relational, $key, $single = false) {
        $q = \Ode\DBO::getInstance()->query("
            SELECT " . self::COLUMNS . "
            FROM " . $this->getTableName() . " AS a
            WHERE a.meta_key = " . \Ode\DBO::getInstance()->quote($key, \PDO::PARAM_STR) . "
            AND a.relational = " . \Ode\DBO::getInstance()->quote($relational) . "
        ");
        
        if($single === false) {
            return $q->fetchAll(\PDO::FETCH_OBJ);
        } else {
            return $q->fetchObject();
        }
    }
    
    public function getValue($relational, $key) {
        $r = $this->get($relational, $key, true);
        
        if($r == false) {
            return false;
        }
        
        return $r->meta_value;
    }
    
    public function getValues($relational, $key) {
        $r = $this->get($relational, $key);
        
        if(empty($r)) {
            return false;
        }
        
        $v = array();
        foreach($r as $s) {
            $v[] = $s->meta_value;
        }
        
        return $v;
    }
    
    public function getByValue($relational, $key, $value) {
        $metadata = \Ode\DBO::getInstance()->query("
            SELECT " . self::COLUMNS . "
            FROM " . $this->getTableName() . " AS a
            WHERE a.meta_key = " . \Ode\DBO::getInstance()->quote($key, \PDO::PARAM_STR) . "
            AND a.meta_value = " . \Ode\DBO::getInstance()->quote($value, \PDO::PARAM_STR) . "
            AND a.relational = " . \Ode\DBO::getInstance()->quote($relational) . "
            LIMIT 0,1
        ")->fetchObject();
        
        return $metadata;
    }
    
    public function getTableName() {
        return $this->tableName;
    }

    public function setTableName($tableName) {
        $this->tableName = $tableName;
    }
}
?>
