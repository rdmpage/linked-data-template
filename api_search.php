<?php

// Get JSON-LD for one item

error_reporting(E_ALL);

require_once(dirname(__FILE__) . '/config.inc.php');
require_once(dirname(__FILE__) . '/triplestore.php');

$q = 'TAXONOMY OF BEGONIA ALBOMACULATA AND DESCRIPTION OF TWO NEW SPECIES ENDEMIC TO PERU';

if (isset($_REQUEST['q']))
{
	$q = $_REQUEST['q'];
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
echo sparql_search($config['sparql_endpoint'], $q);
if ($callback != '')
{
	echo ')';
}


?>
