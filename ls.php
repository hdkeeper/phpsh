<?php

function unpack_perms_triplet( $t)
{
  $retval  = ($t & 4) ? 'r' : '-';
  $retval .= ($t & 2) ? 'w' : '-';
  $retval .= ($t & 1) ? 'x' : '-';
  return $retval;
}


function unpack_perms( $perms)
{
  $retval = 
  unpack_perms_triplet( ($perms & 0700) >> 6) .
  unpack_perms_triplet( ($perms & 070) >> 3) .
  unpack_perms_triplet(  $perms & 7);
  
  if ($perms & 06000) $retval = 's'.$retval;
  elseif ($perms & 01000) $retval = 't'.$retval;
  else $retval = '-'.$retval;
  
  return $retval;
}

// void main()

$target = $_SERVER['QUERY_STRING'];
$me = basename( $_SERVER['PHP_SELF']);
if ($target == '') $target = './';

if (is_file( $target))
{
    // List this file as text
    $f = fopen( $target, "rt");
    echo "<pre>";
    while (!feof($f))
    {
	$buf = fgets( $f, 4096);
	// Suppress HTML&PHP tags
	$buf = str_replace( "<", "&lt;", $buf);
	echo $buf;
    }
    echo "</pre>";
    fclose($f);
    // Fetch this file as is
//    if (false === readfile( $target))
//	echo "Failed to open file $target\n";
}
elseif (is_dir( $target))
{
    // View this directory
    if (chdir( $target))
    {
	$dir = getcwd();
	if ($dir{strlen($dir)-1} != '/') $dir .= '/';
	clearstatcache();
	if ($handle = opendir( $dir)) 
	{
?>	

<html>
<head>
    <title>$dir</title>
    <style>
	body { font-family: 'Courier New', Courier, mono; font-size: 12px;}
	a { text-decoration: none}
	td { font-family: 'Courier New', Courier, mono; font-size: 12px;}
    </style>
</head>
<body>
    Listing files in <?=$dir?>:<br><br>
    <table>
    <tr>
	<td>Type</td>
	<td>Permission</td>
	<td>User</td>
	<td>Group</td>
	<td>Last modified</td>
	<td align=right>Size</td>
	<td>Filename</td>
    </tr>

<?php    
    	    while (false !== ($file = readdir( $handle))) 
	    {
        	$ft = filetype( $dir.$file);
		$fp = unpack_perms( fileperms( $dir.$file));
    		$fs = filesize( $dir.$file);
		$afu = posix_getpwuid( fileowner( $dir.$file));
		$afg = posix_getgrgid( filegroup( $dir.$file));
		$fu = $afu['name'];
		$fg = $afg['name'];
		$fd = date( "d-m-y H:m" , filemtime( $dir.$file));
		$ref = $me.'?'.$dir.$file;
		$fn = "<a href='".$ref."'>".$file."</a>";
		if ($ft == 'link') $fn .= " -> ".readlink( $dir.$file);
		if (is_dir( $dir.$file)) $fn = "<u><b>".$fn."</b></u>";
		echo "<tr><td>$ft</td><td>$fp</td><td>$fu</td><td>$fg</td><td>$fd</td><td align=right>$fs</td><td>$fn</td></tr>\n";
	    }
	    closedir( $handle);
	    echo "</table>\n"; 
	    echo "Free space: ".diskfreespace( $dir)." bytes\n";
	    echo "</body></html>\n";
	}
	else echo "Failed to open $dir\n";
    }
    else echo "Cannot chdir to $target\n";
}
else echo "Don't know what to do with $target\n";

?>