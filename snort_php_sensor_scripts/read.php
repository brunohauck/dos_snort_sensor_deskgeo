<?php 

print_r("--------- inicio do arquivo read --------------\n");

$fh = fopen('snort.u2.1402328120', 'rb');

if( ! $fh ){
	throw new Exception ("Arquivo nao abriu");
}

$buffer = unpack( "Nmagic_number/vversion_major/vversion_minor/lthiszone/Vsigfigs/Vsnaplen/Vnetwork", fread( $fh, 24));

print_r("--------- vair imprimir os primeiros 24 bites do arquivo ------ ");

print_r( $buffer );

?>

