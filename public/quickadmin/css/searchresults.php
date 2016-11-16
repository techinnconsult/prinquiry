<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="utf-8">

<meta http-equiv="X-UA-Compatible" content="IE=edge">

<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="/wpradmin/template/wwidx/css/font-awesome/css/font-awesome.min.css">

<link href="/wp-content/plugins/wp-realty/css/bootstrap.min.css" rel="stylesheet">

<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>

<script src="js/bootstrap.min.js"></script>

<style>

.icon selected {

    color:<?php echo get_theme_mod( 'plugin_links_color' ); ?>;

}

</style>

</head>

<body>

<div class="con">

  <div id="col-md-12 shortcode-col listing_wrapper" data-org="12">

<div class="user_loginw"><a href="/my-account/" title="User Login" rel="nofollow" style="color:<?php echo get_theme_mod('idx_text_color'); ?>;">User Login <i class="fa fa-user"></i><span class="hide"></span></a></div></div>

   <div class="clear"></div>

   <div class="clear"></div>

 

<center>

  <div class="searchresultsnav" style="font-weight:bold; font-size:18px; padding-bottom: 5px; color:<?php echo get_theme_mod('idx_text_color'); ?>">Your Search Returned <span style=" color:#FF0000;">{searchresultsnav_searchcount}</span> Properties.</div>

  <!--<form method='post'>

Save as:

<input type='text' value='{favorite_name}' name='favorite_name' />

<input type='submit' value='Add to favorite' name='addfavorite'>

</form>

-->

</center>

<br />

<div class="searchresultsnav_searchcountl" style="width:380px; float:left; color:<?php echo get_theme_mod('idx_text_color'); ?>">Viewing&nbsp;{searchrestultsnav_range}&nbsp;of <strong>{searchresultsnav_searchcount}</strong> Properties &nbsp;

  <ul class="searchresults">

    <li>{searchresultsnav_prev_button}</li>

    {searchresultsnav_pages length=10}

    <li>{searchresultsnav_next_button}</li>

  </ul>

</div>

<div class="searchresultsnav_searchcount2" style="float:right; padding-right:65px; color:<?php echo get_theme_mod('idx_text_color'); ?>"><strong>Sort Search Results:</strong>

  <ul class="searchresults">

    <li><a href="{searchresultsort field_name='Price'}">Price</a></li>

    <li><a href="{searchresultsort field_name='Bedrooms'}">Beds</a></li>

    <li><a href="{searchresultsort field_name='Bathrooms'}">Baths</a></li>

    <li><a href="{searchresultsort field_name='SquareFootage'}">SqFt</a></li>

  </ul>

</div>



<div class="listing_filter_select listing_filter_views">

  <div id="grid_view" class="" onclick="demo();"> <i class="fa fa-th"></i> </div></div>

  

  <div class="listing_filter_select listing_filter_views">

    <div id="list_view" class="icon_selected" style="color:<?php echo get_theme_mod('plugin_links_color'); ?>;"> <i class="fa fa-bars"></i> </div></div>

</div>  

</div>

<div class="clear"></div>



{searchresults}

<?php $listing_id = "{listing field='listingsdb_id'}"; ?>

<?php 







include_once($_SERVER['DOCUMENT_ROOT']."/wp-blog-header.php");

global $wpdb;

$qry = "SELECT * FROM wp_realty_regfields WHERE regfields_id=1";

$results = $wpdb->get_results( $qry );

$views = 0;

foreach($results as $arr)

{

	$views = $arr->regfields_rank_col;

}



$qry = "SELECT * FROM wp_realty_listingsdb WHERE listingsdb_id=".$listing_id;

$listingInfo = $wpdb->get_results( $qry );



$qry = "SELECT * FROM wp_realty_offices WHERE office_code='".$listingInfo[0]->office_code."'";

$officeInfo = $wpdb->get_results( $qry );

$checkarr = array('NULL', '0', 'no', 'n/a', 'None', 'other');

?>



<div class="col-md-3 shortcode-col listing_wrapper" data-org="3">

 <div class="property_listing">



  <a href="{full_link_to_listing}" title="{listing field='title'}"><img src="{listing_image_thumb_url}" alt="{listing field='title'}" style="border-bottom: 2px solid<?php echo get_theme_mod( 'plugin_borders_color' ); ?>"></a><br />

<h4><a href="{full_link_to_listing}" style="color:<?php echo get_theme_mod( 'plugin_links_color' ); ?>"><?php $titleString = "{listing field='title'}";  $lowercaseTitle = strtolower($titleString); $ucTitleString = ucwords($lowercaseTitle); echo "$ucTitleString"; ?></h4>

   <div class="sep"></a></div> 





      <div class="listing_details the_grid_view" style="margin-top:5px;">

       <div style="propertyvalues_gridview">

 <?php 

$var=get_theme_mod('mls_board', '');

$template_arr = array('wy.tbor');

$mlss = '{listing field="MLS"}';

$listingId = '{listing field="ListingID"}';

if(in_array($var,$template_arr)){

	$mls = $listingId; // '{listing field="ListingID"}';

}else{

	$mls = $mlss;

}

// include($_SERVER['DOCUMENT_ROOT']."/wpradmin/template/wwidx/mlsarray/mlsarray.php");

      ?>

   <ul>

<div class="mls" a href="{full_link_to_listing}" style="margin-left:5px;">MLS#: <?php echo $mls; ?><br />

<div class="grid_fields" style="margin-top:5px;">{if {!listing field='Bedrooms'}}<span class="inforoom"><i class="fa fa-bed" aria-hidden="true"></i> {listing field='Bedrooms'}</span>{endif}&nbsp;&nbsp; {if {!listing field='Bathrooms'}}<span class="infobath"> {listing field='Bathrooms'}</span>{endif}&nbsp;&nbsp; {if {!listing field='SquareFootage'}}<span class="infosize"><i class="fa fa-user" aria-hidden="true"></i> {listing field='SquareFootage'}<sup>2</sup></span>{endif}

<div class="remarks" style="margin-top:5px;">{!listing field='Remarks' limit='90'}...</div>

<?php 

$var=get_theme_mod('mls_board', '');

$template_arr = array('az.tar');

$listingoffice = '<div class="aztarlistingoffice" style="font-size:9px;">Courtesy of: {listing field="listing_office_name"}</div>';

if(in_array($var,$template_arr)){

	echo "$listingoffice";

}

?>

</div></div>

 </ul>

   <div class="city_grid" style="color:<?php echo get_theme_mod( 'plugin_links_color' ); ?>; margin-left:5px;"><h4>{listing field='City'}</h4></div>

      </div></div>





      <div class="listing_details the_list_view" style="display:none; margin-top:5px;">

       <div class="propertyvalues_listview"> 

<div class="mls" a href="{full_link_to_listing}" style="margin-left:15px;">MLS#: {listing field='MLS'}<br />

<div class="list_fields" style="margin-top:5px;">{if {!listing field='Bedrooms'}}<span class="inforoom"><i class="fa fa-bed" aria-hidden="true"></i> {listing field='Bedrooms'}</span>{endif}&nbsp;&nbsp; {if {!listing field='Bathrooms'}}<span class="infobath"> {listing field='Bathrooms'}</span>{endif}&nbsp;&nbsp; {if {!listing field='SquareFootage'}}<span class="infosize"><i class="fa fa-user" aria-hidden="true"></i> {listing field='SquareFootage'}<sup>2</sup></span>{endif}

<div class="remarks" style="margin-top:5px;">{!listing field='Remarks' limit='150'}...</div>

<?php 

$var=get_theme_mod('mls_board', '');

$template_arr = array('az.tar');

$listingoffice = '<div class="aztarlistingoffice" style="font-size:9px;">Courtesy of: {listing field="listing_office_name"}</div>';

if(in_array($var,$template_arr)){

	echo "$listingoffice";

}

?></div></div>

<div class="city_list" style="color:<?php echo get_theme_mod( 'plugin_links_color' ); ?>; margin-left:5px;"<h4>{listing field='City'}</h4></div>

</div></div>

    

<div class="listing_unit_price_wrapper"><b style="color:<?php echo get_theme_mod( 'plugin_links_color' ); ?>; margin-left:5px;">{listing field='price'} </b><span class="price_label"></span></a>



        

         <div class="listing_actions">

          <div class="share_unit"> 

         <?php

$var = "{listing field='PictureCount'}";

if($var >= '10')

$var = '10';

?>

<!-- AddToAny BEGIN -->
<div class="a2a_kit a2a_kit_size_32 a2a_default_style" data-a2a-url="{full_link_to_listing}&cc=321"><!-- &cc=321 clear cache for fb -->
<a class="share a2a_dd" href="{full_link_to_listing}"><span class="hide"></span></a></div>
<!-- AddToAny END -->

</div>

          <a class="fav" href="{adddel_favorite_href}" target="_blank" title="Add this listing to your favorites" rel="nofollow" data-mls="{listing field='MLS'}"><span class="hide"></span></a></div>

      </div>

     </div>

	</div>

{/searchresults}

</div> 



<script>

$('#list_view').click(function(){

        $(this).toggleClass('icon_selected');

         $('#listing_ajax_container').addClass('ajax12');

         $('#grid_view').toggleClass('icon_selected');

        

         

         $('.listing_wrapper').hide().removeClass('col-md-4').removeClass('col-md-3').addClass('col-md-12').fadeIn(400) ;

         $('.the_grid_view').fadeOut(10,function() {

            $('.the_list_view').fadeIn(300);

         });      

     })

     

     $('#grid_view').click(function(){ 

        var class_type;

         class_type = $('.listing_wrapper:first-of-type').attr('data-org');

         $(this).toggleClass('icon_selected');

         $('#listing_ajax_container').removeClass('ajax12');

         $('#list_view').toggleClass('icon_selected');

         $('.listing_wrapper').hide().removeClass('col-md-12').addClass('col-md-'+class_type).addClass('col-md-3').fadeIn(400);

       

         $('.the_list_view').fadeOut(10,function(){

		 $('.the_grid_view').fadeIn(300);

         });    

     })

         

</script>  

</body>

</html>