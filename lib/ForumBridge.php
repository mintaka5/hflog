<?php
define('IN_PHPBB', true);
define('PHPBB_INSTALLED', true);
global $phpbb_root_path;
global $phpEx;
global $db;
global $config;
global $user;
global $auth;
global $cache;
global $template;

$phpbb_root_path = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'forum' . DIRECTORY_SEPARATOR;
$phpEx = substr(strchr(__FILE__, '.'), 1);

require_once $phpbb_root_path . 'config.php';

require($phpbb_root_path . 'common.'.$phpEx);
$user->session_begin();
$auth->acl($user->data);
$user->setup();

require($phpbb_root_path . 'includes/functions_user.'.$phpEx);

function bbRegister(Ode_DBO_User_Model $user, $password) {
    $row = array(
        'username' => $user->username,
        'user_password' => md5(trim($password)),
        'user_email' => $user->email,
        'group_id' => 2,
        'user_timezone' => 0,
        'user_dst' => 1,
        'user_lang' => 'en',
        'user_type' => 0,
        'user_actkey' => '',
        'user_dateformat' => 'D M d, Y g:i a',
        'user_style' => 1,
        'user_regdate' => time()
    );
    
    $newUserId = user_add($row);
    
    return $newUserId;
}

function bbLogin(Ode_DBO_User_Model $ode_user, $password) {
    global $auth, $user;
    
    $result = $auth->login($ode_user->username, trim($password));
    
    if($result['status'] == LOGIN_ERROR_USERNAME) {
        bbRegister($ode_user, $password);
        
        $result = $auth->login($ode_user->username, trim($password));
    }
    
    if($result['status'] == LOGIN_SUCCESS) {
        return true;
    }
    
    return false;
}

function bbLogout() {
    global $user;
    
    $user->session_kill();
    $user->session_begin();
}
?>
