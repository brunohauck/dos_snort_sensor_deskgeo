<?php 

print_r("--------- inicio --------------\n");

$fh = fopen('snort.u2.1402329144', 'rb');

if( ! $fh ){
	throw new Exception ("Arquivo nao abriu");
}

$buffer = unpack( "Nmagic_number/vversion_major/vversion_minor/lthiszone/Vsigfigs/Vsnaplen/Vnetwork", fread( $fh, 24));

print_r("--------- vair imprimir os primeiros 24 bites do arquivo -------\n");

print_r( "Magic number: 0x%s, Version: %d,%d, Snaplen: %d\n",
	dechex($buffer['magic_number']),
	       $buffer['version_major'],
	       $buffer['version_minor'],
	       $buffer['snaplen'] );

print_r("---------- lendo os pacotes -------------\n");

$frame = 1;
while(( $data = fread( $fh, 16))) {
	// Lendo o cabecalho do pacote
	$buffer = unpack ( "Vts_sec/Vts_usec/Vincl_len/Vorig_len", $data);

	// Lendo o pacote raw data
	$packetData = fread ($fh, 24 );

	printf( "Frame: %d, Packetlen: %d, Captured %d\n",
		$frame,
		$buffer['orig_len'],
		$buffer['incl_len'] );
	$frame++;
}
fclose( $fh );
print_r("---------- fim da leitura do arquivo ------------");

?>

