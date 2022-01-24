<?php
class Validation {
	public static function emailExists($value) {
		$sql = "SELECT user.id
                        FROM users AS user
                        WHERE email = :email
                        LIMIT 0,1";
		$sth = \Ode\DBO::getInstance()->prepare($sql);
		$sth->bindValue(":email", $value, PDO::PARAM_STR);
		
		try {
			$sth->execute();
			
			$result = $sth->fetchColumn();
			
			if($result != false) {
				return false;
			}
		} catch(\Exception $e) {
			error_log($e->getTraceAsString(), E_ERROR);
		}
		
		return true;
	}
        
        public static function usernameExists($value) {
            $sql = "SELECT a.id
                    FROM " . \Ode\DBO\User::TABLE_NAME . " AS a
                    WHERE a.username = " . \Ode\DBO::getInstance()->quote(trim($value), PDO::PARAM_STR) . "
                    LIMIT 0,1";
            $id = \Ode\DBO::getInstance()->query($sql)->fetchColumn();
            //Util::debug($id);
            if($id != false) {
                return false;
            }
            
            return true;
        }
}
?>
