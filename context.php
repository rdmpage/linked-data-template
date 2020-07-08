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

// Remember, schema.org is the default so we don't declare that here

$context->cnt		= "http://www.w3.org/2011/content#";
$context->dc 		= "http://purl.org/dc/elements/1.1/";
$context->dcterms 	= "http://purl.org/dc/terms/";
$context->foaf		= "http://xmlns.com/foaf/0.1/";
$context->owl 		= "http://www.w3.org/2002/07/owl#";
$context->rdf		= "http://www.w3.org/1999/02/22-rdf-syntax-ns#";
$context->rdfs		= "http://www.w3.org/2000/01/rdf-schema#";
$context->void 		= "http://rdfs.org/ns/void#";
$context->xsd		= "http://www.w3.org/2001/XMLSchema#";


// --- domain specific namespaces --------------------------------------------------------

// Darwin Core
$context->dwc		= "http://rs.tdwg.org/dwc/terms/";
$context->dwcuri	= "http://rs.tdwg.org/dwc/iri/";
$context->dwcFP		= "http://filteredpush.org/ontologies/oa/dwcFP#";

// Bibliographic
$context->bibo		= "http://purl.org/ontology/bibo/";
$context->cito		= "http://purl.org/spar/cito/";
$context->fabio		= "http://purl.org/spar/fabio/";

// TDWG
$context->sdd 		= "http://tdwg.org/sdd#";
$context->spm 		= "http://rs.tdwg.org/ontology/voc/SpeciesProfileModel";
$context->tn 		= "http://rs.tdwg.org/ontology/voc/TaxonName#";
$context->tcom 		= "http://rs.tdwg.org/ontology/voc/Common#";
$context->tpc 		= "http://rs.tdwg.org/ontology/voc/PublicationCitation#";

// Plazi
$context->trt 		= "http://plazi.org/vocab/treatment#";


?>
