<div id="gmap" class="mapblock">

    <p class="gmap-title"><?php _e('Item Location', 'appthemes');?></p>

        <!-- don't remove this div. It's where the google map appears -->
        <div id="map"></div>
    
        <?php
        // get the api key and country specific google maps url
        $gmaps_key = get_option('cp_gmaps_key');
        $gmaps_loc = get_option('cp_gmaps_loc');
        // get the alternate map url if there is one
        $gmaps_loc_txt = get_option('cp_gmaps_loc_txt');
        $gmaps_encode = get_option('cp_gmaps_encode');

        // check to see if ad is legacy or not and then assemble the map address
        if (get_post_meta($post->ID, 'location', true))
            $make_address = get_post_meta($post->ID, 'location', true);
        else
            $make_address = get_post_meta($post->ID, 'cp_street', true) . '&nbsp;' . get_post_meta($post->ID, 'cp_city', true) . '&nbsp;' . get_post_meta($post->ID, 'cp_state', true) . '&nbsp;' . get_post_meta($post->ID, 'cp_zipcode', true);


        // if admin uses a different url, then use that instead of the drop-down selected
        if (empty($gmaps_loc_txt)) $gmaps_url = $gmaps_loc; else $gmaps_url = $gmaps_loc_txt;
        ?>

        <script src="<?php echo $gmaps_url; ?>/maps?file=api&amp;v=3&amp;oe=<?php echo $gmaps_encode; ?>&amp;key=<?php echo $gmaps_key; ?>" type="text/javascript"></script>
        <script type="text/javascript">var address = "<?php echo $make_address; ?>";</script>

        <?php 
        // call the google maps javascript 
        cp_google_maps_js($gmaps_loc);
        ?>

</div>




<?php
// Google map on single page
function cp_google_maps_js($gmaps_loc) {
?>
<script type="text/javascript">
    //<![CDATA[

    // Check to see if this browser can run the Google API
    if (GBrowserIsCompatible()) {

        var gmarkers = [];
        var htmls = [];
        var to_htmls = [];
        var from_htmls = [];
        var i=0;

        var marker_address = "<p style='font-family:Arial; font-size:11px;'><strong><?php the_title(); ?></strong><br>" + address + "</p>";

        // A function to create the marker and set up the event window
        function createMarker(point,name,html) {
            var marker = new GMarker(point, markerOptions);

            // The info window version with the "to here" form open
            to_htmls[i] = html + '<p style="font-family:Arial; font-size:75%;"><?php _e('Directions:', 'appthemes');?> <b><?php _e('To here', 'appthemes');?><\/b> - <a href="javascript:fromhere(' + i + ')"><?php _e('From here', 'appthemes');?><\/a>' +
                '<br><?php _e('Start address:', 'appthemes');?><form action="<?php echo $gmaps_loc; ?>/maps" method="get" target="_blank">' +
                '<input type="text" size="40" maxlength="40" name="saddr" id="saddr" value="" /><br/><br/>' +
                '<input class="lbutton" value="<?php _e('Get Directions', 'appthemes');?>" type="submit"><br/><br/><br/></p>' +
                '<input type="hidden" name="daddr" value="' + address + '"/>';

            // The info window version with the "to here" form open
            from_htmls[i] = html + '<p style="font-family:Arial; font-size:75%;"><?php _e('Directions:', 'appthemes');?> <a href="javascript:tohere(' + i + ')"><?php _e('To here', 'appthemes');?><\/a> - <b><?php _e('From here', 'appthemes');?><\/b>' +
                '<br><?php _e('End address:', 'appthemes');?><form action="<?php echo $gmaps_loc; ?>/maps" method="get"" target="_blank">' +
                '<input type="text" size="40" maxlength="40" name="daddr" id="daddr" value="" /><br/><br/>' +
                '<input class="lbutton" value="<?php _e('Get Directions', 'appthemes');?>" type="submit"><br/><br/><br/></p>' +
                '<input type="hidden" name="saddr" value="' + address + '"/>';

            // The inactive version of the direction info
            html = html + '<p style="font-family:Arial; font-size:75%;"><?php _e('Directions:', 'appthemes');?> <a href="javascript:tohere('+i+')"><?php _e('To here', 'appthemes');?><\/a> - <a href="javascript:fromhere('+i+')"><?php _e('From here', 'appthemes');?><\/a></p>';

            GEvent.addListener(marker, "click", function(){
				marker.openInfoWindowHtml(html, {
  maxWidth:10
})
			});
			gmarkers[i]=marker;
			htmls[i]=html;
			i++;
			return marker
			}

        // functions that open the directions forms
        function tohere(i){
			gmarkers[i].openInfoWindowHtml(to_htmls[i])
		}
		
		function fromhere(i){
			gmarkers[i].openInfoWindowHtml(from_htmls[i])
		}

        // Display the map, with some controls and set the initial location
        var map = new GMap2(document.getElementById("map"));
        map.addControl(new GSmallZoomControl3D());
        map.addControl(new GOverviewMapControl());
		
		// Create our custom  marker icon
		var newIcon = new GIcon(G_DEFAULT_ICON);
		newIcon.image = "<?php echo get_bloginfo('template_directory') ?>/images/red-flag.png";
		newIcon.iconSize = new GSize(32, 32);
 
		newIcon.shadow = "<?php echo get_bloginfo('template_directory') ?>/images/red-flag-shadow.png";
		newIcon.shadowSize = new GSize(32, 32);
					
		// Set up our GMarkerOptions object
		markerOptions = { 
			icon:newIcon 
		};

        geocoder = new GClientGeocoder();
        geocoder.getLatLng(
        address,
        function(point) {
            if (!point) {
                document.getElementById("map_canvas").innerHTML = "<p style='font-family:Arial; font-size:75%;'>" + address + " <strong><?php _e('Address was not found', 'appthemes');?></strong></p>";
            } else {
                map.setCenter(point, 13);
                var marker = new GMarker(point);
                var marker = createMarker(point,'', marker_address)
                map.addOverlay(marker);
                GEvent.trigger(marker, "click");
            }
        }
    );

    }

    // display a warning if the browser was not compatible
    else {
        alert("<?php _e('Sorry, the Google Maps API is not compatible with this browser', 'appthemes');?>");
    }
    //]]>
</script>


<?php

}

?>