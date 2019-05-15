<?php
echo exec('/sbin/ifconfig | grep "addr:"', $IPS);
?>