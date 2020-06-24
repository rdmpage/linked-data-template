# Linked Data Template

A template for a simple linked data web site. Assumes that you have a triple store (at the moment only [Blazegraph](https://blazegraph.com) is explicitly supported) and you want a simple web app to enable people to explore that data.

The app supports two kinds of operations, displaying an entity, and simple search. To display an entity (uniquely identified by a URI) we do a SPARQL CONSTRUCT query and generate [JSON-LD](https://json-ld.org). Ideally we use a JSON-LD context that makes the JSON-LD as clean as possible (i.e., avoid as many namespace prefixes as possible), and it is framed so that we get a single document tree. So from a developer’s perspective this is almost a typical JSON document. This JSON-LD is then rendered  using [EJS (Embedded JavaScript templating)](https://ejs.co) templates.

Search is very crude, we simply do a SPARQL query looking for literals that exactly match the query term.

## Using the template

The most important step is to create a ```env.php``` file that defines the environmental variable ```BLAZEGRAPH_URL``` which is the URL for your Blazegraph server (typically http://something:9999). If you are using namespaces in Blazegraph (i.e., you have multiple different data sets on your Blazegraph server then you need to set ```$config['blazegraph-namespace’]``` in ```config.inc.php```	 to that namespace.

At this point you should be able to display an entity, and do a simple search. If you want to entity-specific display (e.g., different views for people) you need to add a custom EJS template and edit the code to recognise people entities. My code assumes that every entity is explicitly typed (i.e., has an ```rdf:type``` predicate).