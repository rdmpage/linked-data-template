<?php

$context = new stdclass;
// By default assume that we are using schema.org
$context->{'@vocab'} = "http://schema.org/";

// so things are easy to work with in clients we could rewrite @id and @type
/*
$context->id = '@id';
$context->type = '@type';
*/

// sameAs should be an array
$sameAs = new stdclass;
$sameAs->{'@id'} = "sameAs";
$sameAs->{'@type'} = "@id";
$sameAs->{'@container'} = "@set";

$context->{'sameAs'} = $sameAs;


// --- standard namespaces ---------------------------------------------------------------


$context->dc 		= "http://purl.org/dc/elements/1.1/";
$context->dcterms 	= "http://purl.org/dc/terms/";
$context->foaf		= "http://xmlns.com/foaf/0.1/";
$context->owl 		= "http://www.w3.org/2002/07/owl#";
$context->void 		= "http://rdfs.org/ns/void#";
$context->xsd		= "http://www.w3.org/2001/XMLSchema#";



// --- domain specific namespaces --------------------------------------------------------

// Darwin Core
$context->dwc		= "http://rs.tdwg.org/dwc/terms/";
$context->dwcuri	= "http://rs.tdwg.org/dwc/iri/";

// TDWG
$context->tn = "http://rs.tdwg.org/ontology/voc/TaxonName#";
$context->tcom = "http://rs.tdwg.org/ontology/voc/Common#";
$context->tpc = "http://rs.tdwg.org/ontology/voc/PublicationCitation#";

?>
