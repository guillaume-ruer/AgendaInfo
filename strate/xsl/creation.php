<?php
$xml = fopen('doc_xml.xml', 'w+');

$doc_xml = '<tout>';
fwrite($xml, $doc_xml);

for($i= 0; $i < 10000; $i++)
{
	$doc_xml = '
	<element id="'.rand(1,10000).'" >
		<description>Bidon ma description, qu\'est ce que tu croiais? </description>
		<titre>Bidon</titre>
	</element>
	';

	fwrite($xml, $doc_xml);
}

$doc_xml = "\n</tout>";
fwrite($xml, $doc_xml );

fclose($xml);
