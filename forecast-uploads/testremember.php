<?php
session_name('__cyc');
session_start();

print_r($_COOKIE);
echo "<br><br>";

$params = session_get_cookie_params();
print_r($params);
echo "<br><br>".session_name()."<br><br>";
session_set_cookie_params(time()+3600, '/', '.example.com'); 

setcookie("TestCookie", "cookie", time()+3600);
echo "<pre>";
print_r($_COOKIE);
echo "</pre>";
echo "<br><br>";