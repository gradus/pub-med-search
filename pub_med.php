<?php

//Posts from Navigation Links

$search_field = $_POST['search_field'];
$retstart=0;
if(!isset($_POST['retstart']))
$retstart =0;
else
$retstart = $_POST['retstart'];



?>
	
	<!-- Form -->
	<h2>Search Pub Med Below</h2>
  <p> Try narrowing your search terms for more specific results </p>
  <p> ex. Prograf Inflammatory bowel disease Duke University </p>
	<form name="search" method="post" action="pub_med.php">
	<input type="text" name="search_field" size="40">

	<input type="submit" value="Submit">
	</form>
    <!-- End Form-->

<?php
$utils = "http://www.ncbi.nlm.nih.gov/entrez/eutils";
$db     = "pubmed";
$query  = str_replace(" ", "+", $search_field);
$report = "abstract";
$esearch = $utils . "/esearch.fcgi?db=" . $db . "&retmax=&usehistory=y&term=";
$url = $esearch . "" . $query;


//Set up a CURL request for xml
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HEADER,0); //Change this to a 1 to return headers
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$data = curl_exec($ch);
curl_close($ch);

//Simle XML Object
$xml = new SimpleXMLElement($data);

//xpath data 
$pmids = $xml->IdList->xpath("Id");
$count = $xml->xpath("Count");
$QueryKey = $xml->xpath('QueryKey');
$WebEnv = $xml->xpath("WebEnv");
$retstarts = $xml->xpath("RetStart");

if(!isset($retstart))
$retstart = $retstarts[0];

$retmax=1;

$efetch = $utils."/efetch.fcgi?rettype=". $report ."". "&retmode=html&retstart=". $retstart ."". "&retmax=". $retmax ."". "&db=". $db ."". "&query_key=". $QueryKey[0] ."". "&WebEnv=". $WebEnv[0] ."". "";

//Set up a CURL request for html text
$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_URL, $efetch);
curl_setopt($ch2, CURLOPT_HEADER,0); //Change this to a 1 to return headers
curl_setopt($ch2, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
$returned_html = curl_exec($ch2);
curl_close($ch2);
print "<br />";
print "Total Results: " . $count[0] . "<br /><br />";
print $returned_html;

//Every Thing below is for previous next navigation $query is from form post  at the top.

$next = $retstart+1;
$previous = $retstart-1;

print "<style>form{padding:0;margin:0;}</style>\n\n";
print "<form action='pub_med.php' id='frm_previous' method='post'>\n";
print "<input type='hidden' name='search_field' value='$query' />\n";
print "<input type='hidden' name='retstart' value='$previous' />\n";
print "</form>\n";
print "<a href='#' onclick=\"javascript: document.getElementById('frm_previous') .submit(); return false;\">Previous</a>\n";
print "\n";
print "<form action='pub_med.php' id='frm_next' method='post'>\n";
print "<input type='hidden' name='search_field' value='$query' />\n";
print "<input type='hidden' name='retstart' value='$next' />\n";
print "</form>\n";
print "<a href='#' onclick=\"javascript: document.getElementById('frm_next') .submit(); return false;\">Next</a>\n";


?>









