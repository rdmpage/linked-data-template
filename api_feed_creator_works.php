<?php

// Get JSON-LD for one item

error_reporting(E_ALL);

require_once(dirname(__FILE__) . '/config.inc.php');
require_once(dirname(__FILE__) . '/triplestore.php');

$uri = 'https://orcid.org/0000-0002-2168-0514';

if (isset($_REQUEST['uri']))
{
	$uri = $_REQUEST['uri'];
}

$query = 'PREFIX schema: <http://schema.org/>
	PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
	CONSTRUCT 
	{
		<http://example.rss> rdf:type schema:DataFeed . 
		<http://example.rss> schema:name "Publications" .
		<http://example.rss> schema:dataFeedElement ?item .

		?item rdf:type schema:DataFeedItem .
		?item rdf:type ?item_type .
		?item schema:name ?name .
		?item schema:datePublished ?datePublished .
		?item schema:description ?description .	
			
		# identifier
 		?item schema:identifier ?identifier .
		?identifier rdf:type schema:PropertyValue .
		?identifier schema:propertyID ?identifier_id .
		?identifier schema:value ?identifier_value .
 							
	}
	WHERE
	{
		VALUES ?creator { <' . $uri . '> }
	
  		?item schema:creator ?creator .
  		?item schema:name ?name .
		?item rdf:type ?item_type .
		
		OPTIONAL
		{
			?item schema:description ?description .
		}

		OPTIONAL
		{
			?item schema:datePublished ?datePublished .
		}				
		
		OPTIONAL
		{
			?item schema:identifier ?identifier .		
			?identifier schema:propertyID ?identifier_id .
			?identifier schema:value ?identifier_value .		
		}  				

  		
}';


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
echo sparql_construct_stream($config['sparql_endpoint'], $query);
if ($callback != '')
{
	echo ')';
}


?>
