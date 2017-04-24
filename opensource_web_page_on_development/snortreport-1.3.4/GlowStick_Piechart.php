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




//Version 0.1 20130803184010 	Fixed correct colors to correct data
//				Fixed fractions that are less then 1deg to not take up the entire pie

class GlowStick_Piechart{

	private $data_ary;
	private $label_ary;
	private $color_ary;

	function __construct($data_ary=array(), $label_ary=array(), $color_ary=array()){
		$this->data_ary = $data_ary;
		$this->label_ary = $label_ary;
		$this->color_ary = $color_ary;
	}

	function setDataAry($input, $label_ary, $color_ary){
		if(!is_array($input) || !is_array($label_ary) || !is_array($color_ary)){
			//echo 'incorrect data type'.PHP_EOL;
			exit();
		}
		if(count($input) != count($label_ary) || count($label_ary) != count($color_ary) ){
			//echo 'data has incorrect number of labels/colors'.PHP_EOL;
			exit();
		}
		$n=count($input);
		$sum = 0;

		for($i=0; $i < $n; $i++){
			if(!is_numeric($input[$i])){
				//data array has non number
				exit();
			}
			if(!is_string($label_ary[$i])){
				exit();
			}
			if(!is_int($color_ary[$i]) || $color_ary[$i] < 0 || $color_ary[$i] > 0xFFFFFF){
				exit();
			}
			$this->data_ary[] = doubleval($input[$i]);
			$sum += doubleval($input[$i]);
			$this->label_ary[] = strval($label_ary[$i]);
			$this->color_ary[] = array(($color_ary[$i] >> 16) & 0xFF, ($color_ary[$i] >> 8) & 0xFF, $color_ary[$i] & 0xFF);
		}
		$percent_data_array = array();
		foreach($this->data_ary as $data){
			$percent_data_array[] = $data/$sum;
		}
		$this->data_ary = $percent_data_array;
	}

	function toPNG($height, $width){
		$im = @imagecreate($width, $height) or die("Cannot Initialize new GD image stream");
		$background_main_color = imagecolorallocate($im, 0xCC, 0xCC, 0x99);
		$graph_label_color = imagecolorallocate($im, 0x00, 0x00, 0x00);
		$graph_plot_border_color = imagecolorallocate($im, 0x00, 0x00, 0x00);
		$text_color = imagecolorallocate($im, 0x00, 0x00, 0x00);
		
		$n=count($this->color_ary);

		$x_low = 0.03;
		$x_high = 0.97;
		$y_low = 0.03;
		$y_high = 0.97;
		$pie_percent = 0.80;
		$font_size = 5;
		$font_spacing = intval(floor($height/12));
		$font_vertical_start = $height*0.10;
		$font_horz_start = $width * 0.60;

		$center_x = ($width > $height)?intval(floor($height/2)):intval(floor($width/2));
		$center_y = ($width > $height)?intval(floor($height/2)):intval(floor($height/2));
		
		$circle_radius = ($width > $height)?(intval(floor($height*$pie_percent))):(intval(floor($width*$pie_percent)));
		$running_sum = 0;

//fwrite(STDERR, var_export($this->data_ary, true));
		$colors = array();
		for($i=0; $i<$n; $i++){
			$colors[] = imagecolorallocate($im, $this->color_ary[$i][0], $this->color_ary[$i][1], $this->color_ary[$i][2]);
		}

		for($i=0; $i<$n; $i++){
			if($this->data_ary[$i] < (1/360)){
				//less then 1 degree we skip because imagefilledarc wants to
				//fill the whole chart which is wrong
				continue;
			}
			if($i != $n - 1){
				imagefilledarc($im, $center_x, $center_y, $circle_radius, $circle_radius, 
					$running_sum*360, ($running_sum+$this->data_ary[$i])*360, $colors[$i], IMG_ARC_PIE);
				$running_sum += $this->data_ary[$i];
			}else{
				imagefilledarc($im, $center_x, $center_y, $circle_radius, $circle_radius, 
					$running_sum*360, 0, $colors[$i], IMG_ARC_PIE);
				$running_sum += $this->data_ary[$i];
			}
		}
		imageellipse($im, $center_x, $center_y, $circle_radius, $circle_radius, $graph_label_color);
		$legdend_left_edge = $center_x - $circle_radius;

		for($i=0; $i<$n; $i++){
			imagestring(
				$im, 
				$font_size, 
				intval(
					$font_horz_start
				), 
				intval(
					$i * ($font_size + $font_spacing)+$font_vertical_start
				),
				strval($this->label_ary[$i]), 
				$text_color
			);
			$color = imagecolorallocate($im, $this->color_ary[$i][0], $this->color_ary[$i][1], $this->color_ary[$i][2]);

			imagefilledrectangle($im, intval($font_horz_start)-$font_size, 
					intval($i*($font_size+$font_spacing)+$font_vertical_start)+$font_size, 
					intval($font_horz_start)-$font_size*2, 
					intval($i*($font_size+$font_spacing)+$font_vertical_start)+$font_size*2, 
					$colors[$i]
				);
			imagerectangle($im, intval($font_horz_start)-$font_size, 
					intval($i*($font_size+$font_spacing)+$font_vertical_start)+$font_size, 
					intval($font_horz_start)-$font_size*2, 
					intval($i*($font_size+$font_spacing)+$font_vertical_start)+$font_size*2, 
					$graph_plot_border_color
				);

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
for($i=0; $i<4; $i++){
	$data[] = intval(floor(mt_rand(0, 10000)/(2*$i+1)));
}

$data = array(999327, 992000, 0, 10000);

$gspc = new GlowStick_Piechart();
$gspc->setDataAry(
		$data, 
		array('TCP('.$data[0].')', 'UDP('.$data[1].')', 'ICMP('.$data[2].')', 'PORTSCAN('.$data[3].')'), 
		array(0xCDC673, 0xEEC591, 0xFFF68F, 0xA52A2A)
	);
echo $gspc->toPNG(200, 350);*/
//reset && clear && php /path/to/GlowStick_Piechart.php > test.png && eog test.png && rm test.png
?>
