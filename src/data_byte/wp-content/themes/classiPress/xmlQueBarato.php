<?php
$xmlPath = '../../uploads/xml/';
if(!is_dir($xmlPath)){
	mkdir($xmlPath);
}

include_once 'fileDownload.php';


$xmlstr = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<ad:Ads
xmlns:ad="http://www.quebarato.com.br/Ads"
xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
xsi:schemaLocation="http://www.quebarato.com.br/Ads
http://www.quebarato.com.br/Ad.xsd">
</ad:Ads>
XML;
?><?php

// Reporting E_NOTICE can be good too (to report uninitialized
// variables or catch variable name misspellings ...)
	error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
	
	include_once '../../../wp-config.php';


	$xo = new SimpleXMLElement($xmlstr);
			
	$user_id = isset( $user_id ) ? $user_id :  $_GET['uid'];
	$user_data = get_userdata($user_id);
	
	// var_dump($user_data->user_email);die;
	
	query_posts("author={$user_id}&post_type=ad_listing");
	
	while (have_posts()) : the_post(); 
	
		$post_id = get_the_ID();
	  
	  
		$arrImages =& get_children('post_type=attachment&post_mime_type=image&post_parent=' . $post_id );
	  	// echo '<pre>';
		//var_dump($arrImages);
		// die;
	  
		$price_value = get_post_meta($post_id, 'cp_price', true);
		$product_state = get_post_meta($post_id, 'cp_estado_do_produto' , true);
		$zip_code = get_post_meta($post_id, 'cp_zipcode' , true);
		
		$price_value = str_replace('.', "", $price_value);
		$price_value = str_replace(',', ".", $price_value);
		
		$price_value =  (string) number_format( (float) $price_value, 2 , '.','0');
		
		$zip_code = preg_replace( "/[^0-9]/", "", $zip_code);
		
		$ad = $xo->addChild('Ad');
			$detail = $ad->addChild('Details');
				$detail->addChild('Title', get_the_title());
				$detail->addChild('Description',get_the_content());
				$condition = $detail->addChild('ItemCondition');
					$condition->addAttribute('value', $product_state);
				
				$price = $detail->addChild('Price');
					$price->addAttribute('currency', 'BRL');
					$price->addAttribute('value', $price_value);
				
			$addr = $ad->addChild('Address');
				// $addr->addAttribute('xsi:type', 'ad:BR');
				$addr->addAttribute('xsi:type','ad:BR','http://www.w3.org/2001/XMLSchema-instance');
				$addr->addChild('zip', $zip_code);
							
			$category = $ad->addChild('Category');
				$category->addAttribute('value', '104');    ///   CASAR COM OS ID's do QUEBARATO
			
			if(sizeof($arrImages)>0){	
				$img = $ad->addChild('Pictures');
					foreach ($arrImages as $imgageObject)
						$img->addChild('PictureURI', $imgageObject->guid);
			}
	endwhile;

$file_path = $xmlPath .  $user_data->user_email . ".xml";
$fh = fopen( $file_path , 'w' );

fwrite($fh, $xo->asXML());

fclose($fh);


if(isset($_REQUEST['dl'])){
	
	set_time_limit(0);	
	output_file($file_path, $user_data->user_email . '.xml', 'text/xml');
	
}

















