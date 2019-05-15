
<p>Session Status</p>
<p><?php 
ini_set('session.gc_maxlifetime', 28800);
$sesion_anterior= session_get_cookie_params(); 
	$maxlifetime = ini_get("session.gc_maxlifetime");?></p>
<br>

<pre>
	<?php print_r($sesion_anterior); ?>	
</pre>

<p>SESION</p>
<p><?php echo $maxlifetime; ?></p>

<?php phpinfo(); ?>
