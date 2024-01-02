<?php
require_once('sessionConfig.php');

if (session_status() === PHP_SESSION_NONE) 
    startSession();

session_unset();
destroySession();

http_response_code(200);
header('Location: https://' . $_SERVER['SERVER_NAME']);
exit();

?>