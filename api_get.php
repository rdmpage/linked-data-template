<?php

// Get JSON-LD for one item

error_reporting(E_ALL);

require_once(dirname(__FILE__) . '/config.inc.php');
require_once(dirname(__FILE__) . '/triplestore.php');


$uri = 'http://worldcat.org/issn/1000-3142';


if (isset($_REQUEST['uri']))
{
	$uri = $_REQUEST['uri'];
}


header ("Content-type: application/ld+json");


$callback = '';
if (isset($_GET['callback']))
{
	$callback = $_GET['callback'];
}

if ($callback != '')
{
	echo $callback . '(';
}
echo sparql_construct($config['sparql_endpoint'], $uri);
if ($callback != '')
{
	echo ')';
}


?>
