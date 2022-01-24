<?php
if(!\Ode\Auth::getInstance()->isAdmin()) {
    header("Location: " . \Ode\Manager::getInstance()->friendlyAction("index"));
}

switch(\Ode\Manager::getInstance()->getMode()) {
    default:
        switch(\Ode\Manager::getInstance()->getTask()) {
            default:
                $users = \Ode\DBO\User::getAll();
                
                $user_types = \Ode\DBO\User\Type::getAllActive();
                
                \Ode\View::getInstance()->assign('usertypes', $user_types);
                \Ode\View::getInstance()->assign('users', $users);
                break;
        }
        break;
    case 'type':
        switch(\Ode\Manager::getInstance()->getTask()) {
            default:
                break;
            case 'update':
                $user_id = $_POST['user_id'];
                $type_id = $_POST['type_id'];

                if($type_id == 0) {
                    $sth = \Ode\DBO::getInstance()->prepare("DELETE FROM " . \Ode\DBO\User\Type\Cnx::TABLE . " WHERE user_id = :user_id");
                    $sth->bindParam(':user_id', $user_id, PDO::PARAM_STR, 50);

                    try {
                        $sth->execute();

                        Util::json(true);
                    } catch(\Exception $e) {
                        Util::json(false);
                    }

                    exit();
                }
                
                $cnx = \Ode\DBO\User\Type\Cnx::getOneByUser($user_id);
                
                if($cnx == false) {
                    $sth = \Ode\DBO::getInstance()->prepare("INSERT INTO " . \Ode\DBO\User\Type\Cnx::TABLE . " (user_id, type_id) VALUES (:user, :type)");
                    $sth->bindParam(":user", $user_id, PDO::PARAM_STR, 50);
                    $sth->bindParam(":type", $type_id, PDO::PARAM_INT, 11);
                } else {
                    $sth = \Ode\DBO::getInstance()->prepare("UPDATE " . \Ode\DBO\User\Type\Cnx::TABLE . " SET type_id = :type WHERE user_id = :user");
                    $sth->bindParam(":type", $type_id, PDO::PARAM_INT, 11);
                    $sth->bindParam(":user", $user_id, PDO::PARAM_STR, 50);
                }

                try {
                    $sth->execute();
                    
                    Util::json(true);
                } catch(\Exception $e) {
                    Util::json(false);
                }
                
                exit();
                break;
        }
        break;
    case 'task':
        switch(\Ode\Manager::getInstance()->getTask()) {
            default:
                
                break;
            case 'remove':
                $logs = \Ode\DBO::getInstance()->prepare("DELETE FROM " . \Ode\DBO\Hflog::TABLE_NAME . " WHERE user_id = :user_id");
                $logs->bindParam(":user_id", $_POST['id'], PDO::PARAM_STR, 50);
                $logs->execute();
                
                $typeCnx = \Ode\DBO::getInstance()->prepare("DELETE FROM " . \Ode\DBO\User\Type\Cnx::TABLE . " WHERE user_id = :id");
                $typeCnx->bindParam(":id", $_POST['id'], PDO::PARAM_STR, 50);
                $typeCnx->execute();
                
                $meta = \Ode\DBO::getInstance()->prepare("DELETE FROM " . \Ode\DBO\User\Metadata::TABLE_NAME . " WHERE relational = :id");
                $meta->bindParam(":id", $_POST['id'], PDO::PARAM_STR, 50);
                $meta->execute();
                
                $irc = \Ode\DBO::getInstance()->prepare("DELETE FROM " . \Ode\DBO\User\IRC::TABLE_NAME . " WHERE user_id = :id");
                $irc->bindParam(":id", $_POST['id'], PDO::PARAM_STR, 50);
                $irc->execute();
                
                $user = \Ode\DBO::getInstance()->prepare("DELETE FROM " . \Ode\DBO\User::TABLE_NAME . " WHERE id = :id");
                $user->bindParam(":id", $_POST['id'], PDO::PARAM_STR, 50);
                $user->execute();
                
                Util::json($_POST);
                exit();
                break;
        }
        break;
}
?>
