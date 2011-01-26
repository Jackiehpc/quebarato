<?php


error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

include_once '../../../../wp-config.php';



$blogusers = get_users_of_blog();


echo "<table border='1' cellpadding='5'>
		<thead>
			<th>COD</th>
			<th>email</th>
			<th>XML</th>
		</thead>
		<tbody>
	";	 

foreach ($blogusers as $usr) {
	
	$meta = unserialize($usr->meta_value);
	if(isset($meta['contributor']) && $meta['contributor'] == 1){
			$user_id = $usr->user_id;
			echo "<tr>";
				echo "<td>{$usr->user_id}</td>";
				echo "<td>{$usr->user_email}</td>";
				echo "<td><a href='xmlQueBarato.php?dl=1&uid={$user_id}'>DOWNLOAD</a></td>";
			echo "</tr>";
			
			include 'xmlQueBarato.php';			
			
	}
}

echo "</tbody>
<table>";
