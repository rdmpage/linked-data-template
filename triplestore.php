<?php

error_reporting(E_ALL);
error_reporting(0); // there is an unexplained error in json-ld php

require_once (dirname(__FILE__) . '/config.inc.php');
require_once (dirname(__FILE__) . '/vendor/autoload.php');

require_once (dirname(__FILE__) . '/context.php');

// SPARQL API wrapper

//----------------------------------------------------------------------------------------
// get
function sparql_get($url, $format = 'application/ld+json')
{
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);   	
	curl_setopt($ch, CURLOPT_HTTPHEADER, 
		array(
			"Accept: " . $format 
			)
		);

	$response = curl_exec($ch);
	if($response == FALSE) 
	{
		$errorText = curl_error($ch);
		curl_close($ch);
		die($errorText);
	}
	
	$info = curl_getinfo($ch);
	$http_code = $info['http_code'];
	
	curl_close($ch);
	
	return $response;
}

//----------------------------------------------------------------------------------------
// post
function sparql_post($url, $format = 'application/ld+json', $data =  null)
{
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  
	curl_setopt($ch, CURLOPT_HTTPHEADER, 
		array(
			"Accept: " . $format
			)
		);
		

	$response = curl_exec($ch);
	if($response == FALSE) 
	{
		$errorText = curl_error($ch);
		curl_close($ch);
		die($errorText);
	}
	
	$info = curl_getinfo($ch);
	$http_code = $info['http_code'];
		
	curl_close($ch);
	
	return $response;
}


//----------------------------------------------------------------------------------------
// DESCRIBE a resource, by default return as JSON-LD
// Fuseki and Blazegraph both recognise application/ld+json but for quads
// Fuseki uses application/n-quads whereas Blazegraph uses text/x-nquads
function sparql_describe($sparql_endpoint, $uri, $format='application/ld+json')
{
	global $context;
	
	$url = $sparql_endpoint;
	
	// Handle hash identifiers
	$uri = str_replace('%23', '#', $uri);	
	
	// encode things that may break SPARQL, e.g. SICI entities
	$uri = str_replace('<', '%3C', $uri);
	$uri = str_replace('>', '%3E', $uri);	
	
	// Query is string
	$data = 'query=' . urlencode('DESCRIBE <' . $uri . '>');
	
	$response = sparql_post($url, $format, $data);
		
	// Fuseki returns nicely formatted JSON-LD, Blazegraph returns array of horrible JSON-LD
	// as first element of an array
	
	$obj = json_decode($response);
	if (is_array($obj))
	{
		$doc = $obj[0];
	
		$compacted = jsonld_compact($doc, $context);
		
		$response = json_encode($compacted, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);		
	}
	

	return $response;
}

//----------------------------------------------------------------------------------------
// CONSTRUCT a resource, by default return as JSON-LD
function sparql_construct($sparql_endpoint, $uri, $format='application/ld+json')
{
	global $context;
	
	$url = $sparql_endpoint;
	
	// Handle hash identifiers
	$uri = str_replace('%23', '#', $uri);	
	
	// encode things that may break SPARQL, e.g. SICI entities
	$uri = str_replace('<', '%3C', $uri);
	$uri = str_replace('>', '%3E', $uri);
	
	// Query is string
	
	// generic CONSTRUCT
	$query = 'PREFIX schema: <http://schema.org/>
   CONSTRUCT {
   ?thing ?p ?o .
}
WHERE {
   VALUES ?thing { <' . $uri . '> }
   ?thing ?p ?o .
}';
	
	// doman specific CONSTRUCT goes here


	$data = 'query=' . urlencode($query);
	
	$response = sparql_post($url, $format, $data);
	
	//echo $response;
	

	// Fuseki returns nicely formatted JSON-LD, Blazegraph returns array of horrible JSON-LD
	// as first element of an array
	
	$obj = json_decode($response);
	if (is_array($obj))
	{
	
		$doc = $obj[0];
		
		$doc = $obj;
		
	
		if (0)
		{
			$data = jsonld_compact($doc, $context);
		}
		else
		{
			$n = count($doc);
			$type = '';
			$i = 0;
			while ($i < $n && $type == '')
			{
				if ($doc[$i]->{'@id'} == $uri)
				{
					if (isset($doc[$i]->{'@type'}))
					{
						$type = $doc[$i]->{'@type'};
					}
					else
					{
						// CETAF uses dc:type!
						if (isset($doc[$i]->{'http://purl.org/dc/terms/type'}))
						{
							$type =  $doc[$i]->{'http://purl.org/dc/terms/type'}[0]->{'@value'};
							
							switch ($type)
							{							
								case 'Specimen':
									$doc[$i]->{'@type'} = 'http://rs.tdwg.org/dwc/terms/Occurrence';
									break;
									
								default:
									$doc[$i]->{'@type'} = 'http://schema.org/Thing';
									break;
							}
							
							$type = $doc[$i]->{'@type'};
							
						}	
						
						// WoRMS
						if (isset($doc[$i]->{'http://purl.org/dc/elements/1.1/type'}))
						{
							$type =  $doc[$i]->{'http://purl.org/dc/elements/1.1/type'}[0]->{'@value'};
							
							switch ($type)
							{							
								case 'ScientificName': // no such thing in DwC
									$doc[$i]->{'@type'} = 'http://rs.tdwg.org/ontology/voc/TaxonName#TaxonName';
									break;
									
								default:
									$doc[$i]->{'@type'} = 'http://schema.org/Thing';
									break;
							}
							
							$type = $doc[$i]->{'@type'};
							
						}					
										
					}
				}
				$i++;
			}
		
		
			$frame = (object)array(
					'@context' => $context,
					'@type' => $type
				);
			
			/*
			echo '<pre>';
			print_r($frame);
			echo '</pre>';
				*/
			
			//exit();
				
			$data = jsonld_frame($doc, $frame);
				
		}
		
		$response = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);		
	}
	

	return $response;
}

//----------------------------------------------------------------------------------------
// QUERY, by default return as JSON
function sparql_query($sparql_endpoint, $query, $format='application/json')
{
	$url = $sparql_endpoint . '?query=' . urlencode($query);
	
	$response = sparql_get($url, $format);

	return $response;
}

//----------------------------------------------------------------------------------------
// CONSTRUCT a stream, by default return as JSON-LD
function sparql_construct_stream($sparql_endpoint, $query, $format='application/ld+json')
{
	global $context;
	
	if (1)
	{
		$response = sparql_get(
			$sparql_endpoint . '?query=' . urlencode($query), 
			$format,
			'query=' . $query
		);
	}
	else
	{
		$response = sparql_post(
			$sparql_endpoint, 
			$format,
			'query=' . $query
		);
	}
		
	$obj = json_decode($response);
	if (is_array($obj))
	{
		$doc = $obj;
		
		// schema
		$context->schema = "http://schema.org/";

		
		// dataFeedElement is always an array
		$dataFeedElement = new stdclass;
		$dataFeedElement->{'@id'} = "schema:dataFeedElement";
		$dataFeedElement->{'@container'} = "@set";
		
		$context->{'dataFeedElement'} = $dataFeedElement;	
		
		$context->DataFeed = "schema:dataFeedElement";
		$context->DataFeedItem = "schema:DataFeedItem";
		$context->name = "schema:name";
		
	
		$frame = (object)array(
			'@context' => $context,
			'@type' => 'schema:DataFeed'
		);
			
		$data = jsonld_frame($doc, $frame);
			
		$response = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);		
	}
	

	return $response;
}

//----------------------------------------------------------------------------------------
// Crude text search in SPARQL
function sparql_search($sparql_endpoint, $search_string, $format='application/ld+json')
{
	$url = $sparql_endpoint;
	
	$query = 'PREFIX schema: <http://schema.org/>
PREFIX dc: <http://purl.org/dc/elements/1.1/>
PREFIX dcterms: <http://purl.org/dc/terms/>
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
CONSTRUCT 
{
<http://example.rss>
	rdf:type schema:DataFeed;
	schema:name "Search";
	schema:dataFeedElement ?item .

	?item rdf:type schema:DataFeedItem .
  
	?item schema:name ?name .
	?item rdf:type ?type .
}
WHERE
{
  VALUES ?text { "' . addcslashes($search_string, '"') . '"  }
  {
  	?item ?p ?text .
  }
  UNION
  {
    ?identifier schema:value ?text .
    ?item schema:identifier ?identifier .    
  }
  
  OPTIONAL 
  {
	  ?item rdf:type ?type .
  }


  {
    ?item schema:name ?name .
  }
  UNION
  {
     ?item dcterms:title ?name .
  } 
  UNION
  {
     ?item dc:title ?name .
  }  
  UNION
  {
     ?item foaf:name ?name .
  }  
}

';

	$response = sparql_post(
		$sparql_endpoint, 
		$format,
		'query=' . $query
	);
		
	$obj = json_decode($response);

	if (is_array($obj))
	{
		$doc = $obj[0];
		
		$doc = $obj;				
		
		$context = (object)array(
			'@vocab' 	=> 'http://schema.org/'	,
			'rdfs' 		=> 'http://www.w3.org/2000/01/rdf-schema#',			
			'dc' 		=> 'http://purl.org/dc/elements/1.1/',
			'dcterms' 	=> 'http://purl.org/dc/terms/',			
			'foaf' 		=> 'http://xmlns.com/foaf/0.1/',							
		);
		
		// dataFeedElement is always an array
		$dataFeedElement = new stdclass;
		$dataFeedElement->{'@id'} = "dataFeedElement";
		$dataFeedElement->{'@container'} = "@set";
		
		$context->{'dataFeedElement'} = $dataFeedElement;	
	
		$frame = (object)array(
			'@context' => $context,
			'@type' => 'http://schema.org/DataFeed'
		);
			
		$data = jsonld_frame($doc, $frame);
	
		
		$response = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);		
	}
	

	return $response;
}




?>
