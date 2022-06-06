<?php
unset($_COOKIE['naiz_web_email']);
unset($_COOKIE['naiz_web_password']);
unset($_COOKIE['naiz_web_user_uid']);
unset($_COOKIE['naiz_web_login_string']);

setcookie('naiz_web_email', null, -1, '/');
setcookie('naiz_web_password', null, -1, '/');
setcookie("naiz_web_user_uid", null, -1, '/');
setcookie("naiz_web_login_string", null, -1, '/');

header('location: login_register');
