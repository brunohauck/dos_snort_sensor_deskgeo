<?php

// Snort Report 1.3.4
// Copyright (C) 2000-2013 Symmetrix Technologies, LLC.
// September 9, 2013
//
// This program is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program; if not, write to the Free Software
// Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
//

class GlowStick_Histogram{
	private $data_ary = array();
	private $data_ary_err = array();
	private $data_ary_x = array();
	private $histo_steps;
	private $histo_max;
	private $histo_min;
	private $histo_type = 'timestamp';
	private $version = '1.0.2'; 
	//20130811224213 1.0.2 
	//20130406143800 1.0.1
	//20130320181100 1.0.0

	function __construct(){
		
	}

	function setDataType($type){
		if($type === 'timestamp'){
			$this->histo_type = 'timestamp'; //For labeling x
		}else{
			die('The only accepted data-types are: \'timestamp\'');
		}
	}

	/*  $input:	array of numbers
	 *  $width:	float, channel width OR int, number of steps
	 *  $start_x:	force minimum x value
	 *  $end_x:	force maximum x value
	 */ 
	function setDataAry($input, $width, $start_x=null, $end_x=null){
		if(! is_array($input)){ // Needs to be an array
			return null;
		}
		if( ( is_int($width) && $width <= 1 ) || ( is_float($width) && $width <= 0 ) ){ 
			return null; //if not true, there'd be only one (or less) channels or div by zero
		}
		if(!is_null($start_x) && !is_null($end_x)){
			$this->histo_max = max($start_x, $end_x); // Doesnt matter which is higher or lower
			$this->histo_min = min($start_x, $end_x);
		}else if(is_null($start_x) && !is_null($end_x)){
			$this->histo_max = $end_x;
			$this->histo_min = min($input);
		}else if(!is_null($start_x) && is_null($end_x)){
			$this->histo_max = max($input);
			$this->histo_min = $start_x;
		}else {
			$this->histo_max = max($input);
			$this->histo_min = min($input);
		}
		if(is_null($this->histo_max) || is_null($this->histo_min)){
			//check to make sure these aren't still null or anything
			echo 'Error: GlowStick_Histogram: setDataAry: histo_min/histo_max still null.'.PHP_EOL;
			exit();
		}
		if(is_int($width)){
			$this->histo_steps = $width;
			$delta = ( $this->histo_max - $this->histo_min ) / ($this->histo_steps);
		}else if(is_float($width)){
			if(($this->histo_max - $this->histo_min)/$width < 1.0){ // there'd only be one channel, with everything in it
				return null;
			}
			$this->histo_steps = intval( ( $this->histo_max - $this->histo_min ) / $width );
			if( ( $this->histo_max - $this->histo_min ) % $width > 0){
				$this->histo_steps += 1; //add one more step if there is a fraction of a step more
			}
			$delta = $width;
		}else{
			return null;
		}
		$this->data_ary = array_fill(0, $this->histo_steps, 0);
		$this->data_ary_x = array();
		for($i = 0; $i < $this->histo_steps; $i++){
			$this->data_ary_x[] = ( $delta * $i ) + $this->histo_min;
		}
		// array(0, 4, 7, 9.9, 17, 6, 14), float(10.0) => array(5, 2)
		foreach($input as $item){
			if(!is_numeric($item)){ //each item in the array needs to be numeric
				return null;
			}
			$histo_fractional_index = ( $item - $this->histo_min ) / $delta;
			if(intval($histo_fractional_index) < $this->histo_steps && intval($histo_fractional_index) >= 0){
				$this->data_ary[intval($histo_fractional_index)] += 1;
			}else if(intval($histo_fractional_index) == $this->histo_steps && 
					intval($histo_fractional_index) - $histo_fractional_index == 0){
				$this->data_ary[intval($histo_fractional_index)-1] += 1;
			}
		}
		for($i = 0; $i < $this->histo_steps; $i++){
			$this->data_ary_err[] = sqrt($this->data_ary[$i]);
		}
	}

	function toPNG($height, $width){
		$im = @imagecreate($width, $height) or die("Cannot Initialize new GD image stream");
		$background_main_color = imagecolorallocate($im, 0xCC, 0xCC, 0x99);
		$graph_label_color = imagecolorallocate($im, 0x00, 0x00, 0x00);
		$graph_bar_color = imagecolorallocate($im, 0xCD, 0xC6, 0x73);
		$graph_bar_border_color = imagecolorallocate($im, 0x00, 0x00, 0x00);
		$graph_plot_color = imagecolorallocate($im, 0xBB, 0xBB, 0xBB);
		$graph_plot_color_interior = imagecolorallocate($im, 0xFF, 0xF6, 0x8F);
		$graph_plot_border_color = imagecolorallocate($im, 0x00, 0x00, 0x00);
		$text_color = imagecolorallocate($im, 0x00, 0x00, 0x00);

		$count_index = 0;
		$y_max = max($this->data_ary);
		$width_outer_margin = 0.25;
		$height_outer_margin = 0.25;
		$width_inner_margin = 0.01;
		$plot_width_margin = 0.01;
		$plot_height_margin = 0.01;
		$font_size = 5;

		imagefill($im, 0, 0, $background_main_color);
		imagefilledrectangle(
				$im, 
				intval($plot_width_margin*$width), 
				intval($plot_height_margin*$height), 
				intval($width - $plot_width_margin*$width), 
				intval($height - $plot_height_margin*$height), 
				$graph_plot_color
			);
		imagerectangle(
				$im, 
				intval($plot_width_margin*$width), 
				intval($plot_height_margin*$height), 
				intval($width - $plot_width_margin*$width), 
				intval($height - $plot_height_margin*$height), 
				$graph_plot_border_color
			);
		$log_y_max = pow(10, intval(log($y_max, 10)));

		for($i = 0; $i < $y_max/$log_y_max; $i++){ // Horizontal Grid Lines
			if($i != intval($y_max/$log_y_max)){
				$x1 = intval(
						$width * $width_outer_margin / 2 
						- $width_inner_margin * $width / 2
					);
				$y1 = intval(
						$height 
						- $i * $log_y_max / $y_max * $height * ( 1.0 - $height_outer_margin ) 
						- $height * $height_outer_margin / 2
					);
				$x2 = intval(
						$width * ( 1.0 - $width_outer_margin )
						+ $width * $width_outer_margin / 2 
						+ $width * $width_inner_margin / 2
					);
				$y2 = intval(
						$height 
						- ( $i + 1 ) * $log_y_max / $y_max * $height * ( 1.0 - $height_outer_margin )
						- $height * $height_outer_margin / 2
					);
			}else{
				$frac = ($y_max/$log_y_max) - intval($y_max/$log_y_max);
				$x1 = intval(
						$width * $width_outer_margin / 2 
						- $width_inner_margin * $width / 2
					);
				$y1 = intval(
						$height 
						- $i * $log_y_max / $y_max * $height * ( 1.0 - $height_outer_margin ) 
						- $height * $height_outer_margin / 2
					);
				$x2 = intval(
						$width * ( 1.0 - $width_outer_margin )
						+ $width * $width_outer_margin / 2 
						+ $width * $width_inner_margin / 2
					);
				$y2 = intval(
						$height 
						- ( $i + $frac ) * $log_y_max / $y_max * $height * ( 1.0 - $height_outer_margin )
						- $height * $height_outer_margin / 2
					);
			}
			imagefilledrectangle($im, $x1, $y1, $x2, $y2, $graph_plot_color_interior);
			imagerectangle($im, $x1, $y1, $x2, $y2, $graph_plot_border_color);
		}

		$grid_height =  $height - ( ( ( 0 / $y_max ) * $height * ( 1.0 - $height_outer_margin ) ) + $height * $height_outer_margin / 2 ) ;
		$plot_height = $height - $plot_height_margin*$height;
		$grid_width_start = $width * $width_outer_margin / 2 + $width_inner_margin * $width / 2;
		$x_label_width = ( 
					$width * ( 1.0 - $width_outer_margin )
					+ ( $width * $width_inner_margin / 2 )
				) / ( $this->histo_steps );

		for($i = 0; $i < $this->histo_steps; $i++){ //X labels
			if($this->histo_type === 'timestamp'){
				$mint = $this->data_ary_x[0];
				$maxt = $this->data_ary_x[count($this->data_ary_x)-1];
				$difft = $maxt - $mint;
				//1 hour --v
				if( $difft <= 60*60 ){
					if(intval(date("s", $this->data_ary_x[$i])) == 0 )
					{
						$tmp_x_label = date("H:i", $this->data_ary_x[$i]);
					}else{ 	//Having seconds displayed is too many characters for the plot
						//Get rid of? idk
						$tmp_x_label = date("H:i:s", $this->data_ary_x[$i]);
					}
				//3 hours --v
				}else if( $difft <= 60*60*3 ){
					$tmp_x_label = date("H:i", $this->data_ary_x[$i]);
				//6 hours --v
				}else if( $difft <= 60*60*6 ){
					$tmp_x_label = date("H:i", $this->data_ary_x[$i]);
				//12 hours --v
				}else if( $difft <= 60*60*12 ){
					$tmp_x_label = date("H:i", $this->data_ary_x[$i]);
				//24 hours --v
				}else if( $difft <= 60*60*24 ){
					$tmp_x_label = date("H:i", $this->data_ary_x[$i]);
				//48 hours --v
				}else if( $difft <= 60*60*48 ){
					$tmp_x_label = date("jS-H", $this->data_ary_x[$i]);
				//72 hours --v
				}else if( $difft <= 60*60*72){
					$tmp_x_label = date("jS-H", $this->data_ary_x[$i]);
				//7 days --v
				}else if( $difft <= 60*60*24*7 ){
					$tmp_x_label = date("M-d", $this->data_ary_x[$i]);
				//14 days --v
				}else if( $difft <= 60*60*24*14 ){
					$tmp_x_label = date("M-d", $this->data_ary_x[$i]);
				//30 days --v
				}else if( $difft <= 60*60*24*31 ){
					$tmp_x_label = date("M-d", $this->data_ary_x[$i]);
				//all --v
				}else{
					$tmp_x_label = 'null';
				}
			}else{
				$tmp_x_label = 'null';
			}
			imagestringup(
					$im, 
					$font_size, 
					intval($x_label_width * $i + $grid_width_start), 
					intval( $plot_height - $width_inner_margin * $width / 2),
					$tmp_x_label, 
					$text_color
				);
		}

		//Y Labels
		imagestring(
				$im, 
				$font_size, 
				intval(
					( $plot_width_margin * $width + $grid_width_start ) / 2
				), 
				intval( $grid_height ),
				"0",
				$text_color
			);
		imagestring(
				$im, 
				$font_size, 
				intval(
					( $plot_width_margin * $width + $grid_width_start ) / 2
				), 
				intval(
					$height 
					- ( ( $height * ( 1.0 - $height_outer_margin ) ) 
					+ $height * $height_outer_margin / 2 )
				),
				strval($y_max), 
				$text_color
			);

		foreach($this->data_ary as $item){
			imagefilledrectangle(
					$im,
					intval( 
						( $count_index / $this->histo_steps ) * $width * ( 1.0 - $width_outer_margin ) 
						+ $width * $width_outer_margin / 2
						+ $width_inner_margin * $width / 2
					), 
					intval(
						$height
						- ( $item / $y_max ) * $height * ( 1.0 - $height_outer_margin )
						- $height * $height_outer_margin / 2
					), 
					intval(
						($count_index + 1) / ( $this->histo_steps ) * $width * ( 1.0 - $width_outer_margin )
						+ $width * $width_outer_margin / 2
						- $width * $width_inner_margin / 2
					), 
					intval(
						$height * ( 1.0 - $height_outer_margin )
						+ $height * $height_outer_margin / 2
					), 
					$graph_bar_color
				);
			imagerectangle(
					$im,
					intval( 
						( $count_index / $this->histo_steps ) * $width * ( 1.0 - $width_outer_margin ) 
						+ $width * $width_outer_margin / 2
						+ $width_inner_margin * $width / 2
					), 
					intval(
						$height
						- ( $item / $y_max ) * $height * ( 1.0 - $height_outer_margin )
						- $height * $height_outer_margin / 2
					), 
					intval(
						($count_index + 1) / ( $this->histo_steps ) * $width * ( 1.0 - $width_outer_margin )
						+ $width * $width_outer_margin / 2
						- $width * $width_inner_margin / 2
					), 
					intval(
						$height * ( 1.0 - $height_outer_margin )
						+ $height * $height_outer_margin / 2
					), 
					$graph_bar_border_color
				);
			$count_index += 1;
		}	
		//OUTPUT PICTURE
		ob_start();
		imagepng($im);
		$png_bytes = ob_get_contents();
		ob_end_clean();
		imagedestroy($im);
		return $png_bytes;
	}
}

/*$data = array(); //lets make some data
$t = time();
for($i=0; $i<1000; $i++){
	$data[] = mt_rand(0, 3600*24)+$t-3600*24;
}

$gsh = new GlowStick_Histogram();
$gsh->setDataAry($data, intval(24), $t-3600*24, $t);
echo $gsh->toPNG(600, 800);//*/
?>
