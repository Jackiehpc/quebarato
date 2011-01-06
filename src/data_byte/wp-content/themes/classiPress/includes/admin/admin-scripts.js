/*
 * Admin jQuery functions
 * Written by AppThemes
 *
 * Copyright (c) 2010 App Themes (http://appthemes.com)
 *
 * Built for use with the jQuery library
 * http://jquery.com
 *
 * Version 1.4
 *
 */

// <![CDATA[

jQuery(document).ready(function() {


    /* initialize the tooltip feature */
    jQuery("a").easyTooltip();

    /* initialize the form validation */
    jQuery("#mainform").validate({errorClass: "invalid"});

    /* add fade animation to admin page option saves */
    jQuery("div#message.updated").delay(1500).fadeOut(1500);

    /* admin option pages tabs */
    jQuery("#tabs-wrap").tabs({fx: {opacity: 'toggle', duration: 200}});

    /* strip out all the auto classes since they create a conflict with the calendar */
    jQuery('#tabs-wrap').removeClass('ui-tabs ui-widget ui-widget-content ui-corner-all')
    jQuery('ul.ui-tabs-nav').removeClass('ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all')
    jQuery('div#tabs-wrap div').removeClass('ui-tabs-panel ui-widget-content ui-corner-bottom')


    /* initialize the datepicker feature */
    jQuery( ".datepicker" ).datepicker({
        showOn: 'button',
        dateFormat: 'yy-mm-dd',
        buttonImageOnly: true,
        buttonText: '',
		buttonImage: '../wp-includes/images/blank.gif' // calling the real calendar image in the admin-style.css. need a blank placeholder image b/c of IE.
    });



});




    /* show/hide rows based on selected values */
    //    var oldD="";
    //    var d="";
    //    function show(o){
    //        if(oldD!="") oldD.style.display='none';
    //        if(o.selectedIndex>0){
    //            d=document.getElementById(o[o.selectedIndex].value);
    //            d.style.display='table-row';
    //            oldD=d;
    //        }
    //    }

    /* dashboard loader script */
    jQuery(function () {
        jQuery('.insider').hide(); //hide all the content boxes on the page
    });

    var i = 0; //initialize
    var int = 0; //IE fix
    jQuery(window).bind("load", function() { //The load event will only fire if the entire page or document is fully loaded
        var int = setInterval("doThis(i)",500); //500 is the fade in speed in milliseconds
    });

    function doThis() {
        var item = jQuery('.insider').length; //count the number of elements on the page
        if (i >= item) { // Loop through the elements
            clearInterval(int); //When it reaches the last element the loop ends
        }
        jQuery('.insider:hidden').eq(0).fadeIn(500); //fades in the hidden elements one by one
        i++; //add 1 to the count
    }
    /* end dashboard loader script */


// ]]>
