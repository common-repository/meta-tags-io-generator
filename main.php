<?php
/**
 * Plugin Name:       Meta tags IO generator
 * Description:       Simple meta tags generator
 * Version:           1.4.0
 * Author:            Nik Radevic
 * Author URI:        https://yoomustadd.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 *
 */
 
 //Depends
 
require_once( ABSPATH . 'wp-includes/pluggable.php' );


 
 
 // Constant(s)

 define("MTIOG_row_id", 1); 
 
 /* DB Table Create/drop/funtctions */
 
 //Create 
 
register_activation_hook( __FILE__, 'MTIOG_create_db_table' );
function MTIOG_create_db_table() { 
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();
    $table_name = $wpdb->base_prefix.'MTIOG_meta_tags';
    $query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );

    if ( ! $wpdb->get_var( $query ) == $table_name ) {
      $sql = "CREATE TABLE $table_name (
        id int(11) NOT NULL AUTO_INCREMENT,
        site_name text NOT NULL,
        description text NOT NULL,
        use_description_globally text NOT NULL,
		meta_image_url text NOT NULL,
        use_meta_image_globally text NOT NULL,
        price_title_or text NOT NULL,
        desc_prod_or text NOT NULL,
        prod_img text NOT NULL,
        PRIMARY KEY  (id)
      ) $charset_collate;";

      require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
      dbDelta( $sql );
	
		$wpdb->insert( 
        $table_name, 
        array( 
            'id' => MTIOG_row_id, 
            'site_name' => get_bloginfo( 'name' ), 
            'description' => get_bloginfo( 'description' ),
			'use_description_globally' => 'non-global',			
			'use_meta_image_globally' => 'non-global',			
			'price_title_or' => 'title',			
			'desc_prod_or' => 'short',			
			'prod_img' => 'featured',			
        ) 
    );
	
    }
}



//Drop on plugin delete

register_uninstall_hook( __FILE__, 'MTIOG_remove_db_table' );
function MTIOG_remove_db_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'MTIOG_meta_tags';
    $sql = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query($sql);
     
}  


// DB get values

function MTIOG_get_db_val($col_name){
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->base_prefix.'MTIOG_meta_tags';
	$value = $wpdb->get_var( $wpdb->prepare(" SELECT $col_name FROM $table_name WHERE ID = %d", MTIOG_row_id));
	return $value;
}

// DB Add values
//Meta image url column is the attachment ID generated through file upload, validation is done through file upload.
function MTIOG_add_to_db($meta_image_url) {
	
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->base_prefix.'MTIOG_meta_tags';
	
	//sanitize vars
	
	$site_name = sanitize_text_field($_POST["site_name"]);
	$description = sanitize_textarea_field($_POST["description"]);
	$desc_global_or = sanitize_text_field($_POST["desc_global_or"]);
	$img_global_or = sanitize_text_field($_POST["img_global_or"]);
	$price_title_or = sanitize_text_field($_POST["price_title_or"]);
	$desc_prod_or = sanitize_text_field($_POST["desc_prod_or"]);
	$prod_img = sanitize_text_field($_POST["prod_img"]);
	
	
	if(MTIOG_is_db_empty()) {

		$sql = $wpdb->prepare("INSERT INTO `$table_name` (`id`, `site_name`, `description`, `use_description_globally`, `meta_image_url`, `use_meta_image_globally`, `price_title_or`, `desc_prod_or`, `prod_img`) values (%d, %s, %s, %s, %s, %s, %s, %s, %s)", MTIOG_row_id, $site_name, $description, $desc_global_or, $meta_image_url, $img_global_or, $price_title_or, $desc_prod_or, $prod_img);

		$wpdb->query($sql);
		 //$wpdb->insert($table_name, array('id' => MTIOG_row_id, 'site_name' => sanitize_text_field($_POST["site_name"]), 'description' => sanitize_textarea_field($_POST["description"]), 'use_description_globally' => sanitize_text_field($_POST["desc_global_or"]), 'meta_image_url' => sanitize_text_field($meta_image_url), 'use_meta_image_globally' => sanitize_text_field($_POST["img_global_or"]), 'price_title_or' => sanitize_text_field($_POST["price_title_or"]), 'desc_prod_or' => sanitize_text_field($_POST["desc_prod_or"]), 'prod_img' => sanitize_text_field($_POST["prod_img"])));
     } else {
		 $delete = $wpdb->query("TRUNCATE TABLE `$table_name`");
        //$wpdb->update($table_name, array('id' => MTIOG_row_id, 'site_name' => sanitize_text_field($_POST["site_name"]), 'description' => sanitize_textarea_field($_POST["description"]), 'use_description_globally' => sanitize_text_field($_POST["desc_global_or"]), 'meta_image_url' => sanitize_text_field($meta_image_url), 'use_meta_image_globally' => sanitize_text_field($_POST["img_global_or"]), 'price_title_or' => sanitize_text_field($_POST["price_title_or"]), 'desc_prod_or' => sanitize_text_field($_POST["desc_prod_or"]), 'prod_img' => sanitize_text_field($_POST["prod_img"])), array( 'id' => MTIOG_row_id ));
		$sql = $wpdb->prepare("INSERT INTO `$table_name` (`id`, `site_name`, `description`, `use_description_globally`, `meta_image_url`, `use_meta_image_globally`, `price_title_or`, `desc_prod_or`, `prod_img`) values (%d, %s, %s, %s, %s, %s, %s, %s, %s)", MTIOG_row_id, $site_name, $description, $desc_global_or, $meta_image_url, $img_global_or, $price_title_or, $desc_prod_or, $prod_img);

		$wpdb->query($sql);
     }
}


//DB check if table is empty
function MTIOG_is_db_empty() {
	global $wpdb;
	$table_name = $wpdb->base_prefix.'MTIOG_meta_tags';
    $result = $wpdb->get_results("SELECT id from $table_name WHERE `id` = 1 ");
    if(count($result) == 0)
    {
        return true;
    }
    else
    {
        return false;

    }
}





//Add error code to paramaters 

function MTIOG_customquery_var( $vars ){
  $vars[] = "error_code";
  return $vars;
}
add_filter( 'query_vars', 'MTIOG_customquery_var' );



//Submenu page - settings

add_action( 'admin_menu', 'meta_tags_subpage_info' );
function meta_tags_subpage_info() {
    $parent_slug = 'options-general.php';
	$page_title = 'Meta Tags';
    $menu_title = 'Meta Tags';
    $capability = 'manage_options';
    $menu_slug  = 'MTIOG_meta_tags';
    $function   = 'MTIOG_meta_tags'; 
    $position   = 15;
	add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function, $position ); 
  }
  
  
  //Function get clean domain
  
  function MTIOG_meta_domain_v() {
   $protocols = array( 'http://', 'https://', 'http://www.', 'https://www.', 'www.' );
   return str_replace( $protocols, '', site_url() );
}
  
//Meta tags DB page
  if( !function_exists("MTIOG_meta_tags") ) { function MTIOG_meta_tags(){
	
	//scirpts 
	
	wp_enqueue_script( 
        'MTIOG_script_handle',                         
        plugins_url( '/assets/js/custom.js', __FILE__ ), 
        array( 'jquery' )                             // Dependancies
    );
	wp_enqueue_script( 
        'MTIOG_script_handle_2',                         // 
        plugins_url( '/assets/js/uikit.min.js', __FILE__ ), 
        array( 'jquery' )                             // Dependancies
    );
	
	wp_enqueue_script( 
        'MTIOG_script_handle_icon',                         // 
        plugins_url( '/assets/js/uikit-icons.min.js', __FILE__ ), 
        array( 'jquery' )                             // Dependancies
    );
	wp_enqueue_style( 
        'MTIOG_script_handle_css',                         
        plugins_url( '/assets/css/uikit.min.css', __FILE__ ),  
    );
	wp_enqueue_style( 
        'MTIOG_script_handle_css2',                         
        plugins_url( '/assets/css/custom.css', __FILE__ ),  
    );
	
	   if(isset($_POST['MTIOG_add_to_db'])){
		   
	//radio buttons validation get only these values
	// if not escape
	$accepted_radio_values = array('title','descr', 'none', 'cont', 'short', 'global', 'featured', 'non-global');
	
	$check_String = sanitize_text_field($_POST["desc_global_or"]);
	if(!in_array($check_String, $accepted_radio_values)) {
		wp_redirect( admin_url() .'/options-general.php?page=MTIOG_meta_tags&error_code=03');
		exit();
	}
	
	$check_String = sanitize_text_field($_POST["img_global_or"]);
	if(!in_array($check_String, $accepted_radio_values)) {
		wp_redirect( admin_url() .'/options-general.php?page=MTIOG_meta_tags&error_code=03');
		exit();
	}
	
	$check_String = sanitize_text_field($_POST["price_title_or"]);
	if(!in_array($check_String, $accepted_radio_values)) {
		wp_redirect( admin_url() .'/options-general.php?page=MTIOG_meta_tags&error_code=03');
		exit();
	}
	
	$check_String = sanitize_text_field($_POST["desc_prod_or"]);
	if(!in_array($check_String, $accepted_radio_values)) {
		wp_redirect( admin_url() .'/options-general.php?page=MTIOG_meta_tags&error_code=03');
		exit();
	}
	
	$check_String = sanitize_text_field($_POST["prod_img"]);
	if(!in_array($check_String, $accepted_radio_values)) {
		wp_redirect( admin_url() .'/options-general.php?page=MTIOG_meta_tags&error_code=03');
		exit();
	}
	
	//radio buttons validation get only these values
	// END
	
	if ( $_FILES['meta_image_file']['name'] ) {
	
	//string validations
	
	
	
	//file upload validation included 
	$meta_image_url = MTIOG_file_upload();
	}
	
	else {
	$meta_image_url =	MTIOG_get_db_val('meta_image_url');
	}
	MTIOG_add_to_db($meta_image_url);
	
 } 
	// end escripts

    $page_url = get_admin_url() . '/admin.php?page=MTIOG_meta_tags';

    $site_name_db = MTIOG_get_db_val('site_name');
    $description_db = MTIOG_get_db_val('description');
    $use_description_globally_db = MTIOG_get_db_val('use_description_globally');
    $meta_image_url_db = MTIOG_get_db_val('meta_image_url');
    $use_meta_image_globally_db = MTIOG_get_db_val('use_meta_image_globally');
    $price_title_or = MTIOG_get_db_val('price_title_or');
    $desc_prod_or = MTIOG_get_db_val('desc_prod_or');
    $prod_img = MTIOG_get_db_val('prod_img');
	

	
	// WOO CONDITIONS
	$woo_desc = 'This is probably the best blue t-shirt ever!';	
	$woo_title = $site_name_db . ' - Blue t-shirt';
	$wooimg_cl = 'nondyn';
	$woo_price_cl =' ';
	$woo_desc_global_cl = ' ';

	if  ($desc_prod_or == 'cont') {
		$woo_desc = "(Main content) Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus in pulvinar lorem, sed pharetra est. Quisque lobortis ac magna in accumsan. Pellentesque interdum, leo id ultrices suscipit, lorem mauris mattis ante, nec mollis urna quam ac odio. ";
	}
	else if  ($desc_prod_or == 'none') {
		$woo_desc = '';
	}
	else if  ($desc_prod_or == 'global') {
		$woo_desc = $description_db;
		$woo_desc_global_cl = "global_desc_woo";
	}
	$woo_img = plugins_url( '/images/prod-placehold.jpeg', __FILE__ );
	if  ($prod_img == 'global') {
		$woo_img = wp_get_attachment_url($meta_image_url_db);
		$wooimg_cl = 'ch-dyn';
	}
	if ($price_title_or == 'title') {
		$woo_title = $woo_title . ' (10,50 &euro;)';
		$woo_price_cl = 'MTIOG_price_title'; 
	}
	else if ($price_title_or == 'descr') {
		if  ($desc_prod_or == 'none') {
			$woo_desc = '10,50&euro;';
		}
		else {
			$woo_desc = '10,50&euro; - ' . $woo_desc ;
		}
		
		$woo_price_cl = 'MTIOG_price_descr'; 
	}
	
	
	
	$woocommerce_is = 'non-woo';
	if( function_exists( 'MTIOG_woo_is_active' )) { $woocommerce_is = 'woo-active';}	
	if (strlen($description_db) > 240) {$desc_short = substr($description_db,0,240) . '...';}
	else {$desc_short = $description_db;}
	
	$error_msg = '';
	
	if ( isset( $_GET['error_code'] ) ) {
	$error_msg = 'Image format is not allowed! Please upload an image with one of the following extensions: <b>png, jpg, jpeg, svg</b>';

		
	}
	
    ?>   
	<div class="uk-section">
	<div class="uk-container uk-width-expand">
	<h1 class="MTIOG_h MTIOG_h1">Meta Tags Generator</h1> 
	<div class="uk-child-width-1-2@m uk-child-width-1-1@s uk-grid-divider" uk-grid>
		<div class="cols-plg col1">
			<div class="MTIOG_error_msg">
				<span class="error_text"><?php echo esc_html($error_msg); ?></span>
			</div>
			<form class="MTIOG_form" method="post" enctype="multipart/form-data">
				<div class="form_n_div">
					<label  for="site_name">Title:</label>
					<input placeholder="Site title" value="<?php echo esc_html($site_name_db); ?>" id="site_name" name="site_name" class="uk-input MTIOG_input" required>
				</div>
				<div  class="form_n_div">
					<label  for="description">Description:</label>
					<textarea placeholder="Site Description" id="description" name="description" class="MTIOG_textarea uk-textarea" rows="4" cols="50" required><?php echo esc_html($description_db); ?></textarea>
				</div>
			<fieldset>
				<legend>Use the description globally or use a teaser/excerpt for WP Posts?</legend>
		
					<div>
						<label><input type="radio" id="global-desc" name="desc_global_or" value="global" class="uk-radio MTIOG-radio"  checked>Global</label>
					</div>
					
						<div>
						<label><input type="radio" id="non-global-desc" name="desc_global_or" value="non-global" class="uk-radio MTIOG-radio">Use teaser/excerpt for WP Posts</label>
					
					</div>
				</fieldset>
				<div  class="form_n_div file_div ">
					<label  for="meta_image_url_db">Global meta image <span class="recommended_n">(Recommend 1200×628)</span> </label>
					<?php if (!empty($meta_image_url_db)) { ?>
					<?php } ?>
					<div id="filediv" class="MTIOG_file_div" uk-form-custom="target: true">
						<input  type="file" id="meta_image_url" name="meta_image_file" class="MTIOG_input_file" aria-label="Custom controls">
						<input id="bgimagefile"style="background-image:url(<?php echo esc_url_raw(wp_get_attachment_url($meta_image_url_db)); ?>)" class="uk-input uk-form-width-medium" type="text" aria-label="Custom controls" disabled>
						<div id="uploaddiv" class="upload_icon">
						<span id="flowicon" uk-icon="icon:upload;ratio:2"></span>
						<div class="upload_text">Drag & Drop or Click</div>
						</div>
					</div>
					
				</div>
			<fieldset>
				<legend>Use meta-image globally or use the featured image for WP posts?</legend>
		
					<div>
						<label><input type="radio" id="global-img" name="img_global_or" value="global" checked class="uk-radio MTIOG-radio" >Global</label>
					</div>
					
						<div>
						<label><input type="radio" id="non-global-img" name="img_global_or" value="non-global" class="uk-radio MTIOG-radio">Use featured image for WP Posts</label>
					</div>
				</fieldset>
				<input class="uk-button uk-button-default MTIOGBtn" type="submit" name="MTIOG_add_to_db" value="Save Settings">
				<hr class="">
				<h3 class="uk-h3">WooCommerce</h3>
				<?php if( !function_exists( 'MTIOG_woo_is_active' )) { ?>
				<div class="uk-alert-danger" uk-alert>
				<a class="uk-alert-close" uk-close></a>
				<p>Meta tags generator - WooCommerce integration plugin is required to apply any changes on WooCommerce products meta data. Feel free to explore all WooCommerce options using the demo box!<br> (You need to click 'save settings')</p>
				<a class="uk-button uk-button-default MTIOGBtn" href="https://yoomustadd.com/product/meta-tags-generator-woocommerce-integration/" target="_blank">Plugin Page</a>
				
				</div>
				<?php } ?>
				<div class="woocommerce-form <?php echo esc_attr($woocommerce_is); ?>">
					<fieldset>
				<legend>Add price on title or description of meta card?</legend>
		
					<div>
						<label><input type="radio" id="pricetitle" name="price_title_or" value="title" class="uk-radio MTIOG-radio"  checked>Title</label>
					</div>
					
						<div>
						<label><input type="radio" id="pricedesc" name="price_title_or" value="descr" class="uk-radio MTIOG-radio">Description</label>
					
					</div>
					<div>
						<label><input type="radio" id="pricenone" name="price_title_or" value="none" class="uk-radio MTIOG-radio">None</label>
					
					</div>
				</fieldset>
					<fieldset>
				<legend>Use product WP Content Box or WC Product short description box for meta card content?</legend>
		
					<div>
						<label><input type="radio" id="contdesc" name="desc_prod_or" value="cont" class="uk-radio MTIOG-radio"  checked>WP Content Box(main content)</label>
					</div>
					
						<div>
						<label><input type="radio" id="contshort" name="desc_prod_or" value="short" class="uk-radio MTIOG-radio">WC Product short description(WC)</label>
					
					</div>
					<div>
						<label><input type="radio" id="contnone" name="desc_prod_or" value="none" class="uk-radio MTIOG-radio">No Description</label>
					
					</div>
					<div>
						<label><input type="radio" id="contglobal" name="desc_prod_or" value="global" class="uk-radio MTIOG-radio">Global Description</label>
					
					</div>
				</fieldset>
				<fieldset>
				<legend>Use meta-image on products or use product featured image?</legend>
		
					<div>
						<label><input type="radio" id="glprod-img" name="prod_img" value="global" checked class="uk-radio MTIOG-radio" >Use global image for products</label>
					</div>
					
						<div>
						<label><input type="radio" id="feprod-img" name="prod_img" value="featured" class="uk-radio MTIOG-radio">Use featured image for products</label>
					</div>
				</fieldset>
				</div> 
				<div>
				<?php wp_nonce_field( 'upload_MTIOG_file', 'MTIOG_nonce', true, true ); ?>
					<input class="uk-button uk-button-default MTIOGBtn" type="submit" name="MTIOG_add_to_db" value="Save Settings">
					
				</div>
			</form>
		</div>
		<div class="cols-plg col2">
			<h2 class="uk-h2">Preview</h2>
			<h5 class="uk-h5">Search engines</h5>
			<div class="MTIOG-card-google">
				<span class="title-card title-google"><?php echo esc_html($site_name_db); ?></span>
				<div class="card-url card-google-url">
					<span class="google-url-full"><?php echo esc_url(get_site_url());?></span>
					<span class="google-arrow"></span>
				</div>
				<span class="google-description">
					<?php echo esc_html($desc_short); ?>
				</span>
          </div>
		  <h5 class="uk-h5 ">Social Media</h5>
	
          <div class="MTIOG-card-smedia">
            <div class="card-image-smedia bgimage-g" style="background-image: url(<?php echo esc_url_raw(wp_get_attachment_url($meta_image_url_db)); ?>)"></div>
            <div class="card-smedia-all-text">
              <span class="card-smedia-cdomain"><?php echo esc_html(MTIOG_meta_domain_v()); ?></span>
              <div class="card-smedia-clean-text">
                <div style="margin-top:5px">
                  <div class="card-smedia-title"><?php echo esc_html($site_name_db); ?></div>
                </div>
                <span class="card-smedia-content"><?php echo esc_html($description_db); ?></span>
              </div>
            </div>
          </div>
		  <!-- WOO Integration section -->
		  <hr class="">
		  <h3 class="uk-h3">WooCommerce</h3>
		  <h5 class="uk-h5">Search engines</h5>
			<div class="MTIOG-card-google">
				<span class="title-card-woo title-google"><?php echo esc_html($woo_title); ?></span>
				<div class="card-url card-google-url">
					<span class="google-url-full"><?php echo esc_url(get_site_url()) . '/products/demo-tshirt'; ?></span>
					<span class="google-arrow"></span>
				</div>
				<span class="woo-description">
					<?php echo $woo_desc; ?>
				</span>
          </div>
		  <h5 class="uk-h5 <?php echo esc_attr($woo_price_cl);?> <?php echo esc_attr($woo_desc_global_cl); ?>">Social Media</h5>
	
          <div class="MTIOG-card-smedia">
            <div class="card-image-smedia bgimage-woo <?php echo esc_attr($wooimg_cl); ?>" style="background-image: url(<?php echo esc_url_raw($woo_img); ?>)"></div>
            <div class="card-smedia-all-text">
              <span class="card-smedia-cdomain"><?php echo esc_html(MTIOG_meta_domain_v()); ?></span>
              <div class="card-smedia-clean-text">
                <div style="margin-top:5px">
                  <div class="card-smedia-title-woo"><?php echo esc_html($woo_title); ?></div>
                </div>
                <span class="woo-description"><?php echo esc_html($woo_desc); ?></span>
              </div>
            </div>
          </div>
		</div>
		
		
	</div>
	</div>
	<?php 
	//placeholder scripts based on db values
	?> <script> <?php
	if ($use_description_globally_db == 'non-global'){ ?> var nonglobaldesc = document.getElementById("non-global-desc"); nonglobaldesc.checked = true; <?php }
	if ($use_meta_image_globally_db == 'non-global'){ ?> var nonglobalimg = document.getElementById("non-global-img"); nonglobalimg.checked = true; <?php }
	if ($price_title_or == 'descr'){ ?>  var pricetitleor = document.getElementById("pricedesc"); pricetitleor.checked = true; <?php }
	else if ($price_title_or == 'none'){ ?> var pricetitleor = document.getElementById("pricenone"); pricetitleor.checked = true; <?php }
	if ($desc_prod_or == 'short'){ ?> var shortdec = document.getElementById("contshort"); shortdec.checked = true; <?php }
	else if ($desc_prod_or == 'none'){ ?> var shortdec = document.getElementById("contnone"); shortdec.checked = true; <?php }
	else if ($desc_prod_or == 'global'){ ?> var shortdec = document.getElementById("contglobal"); shortdec.checked = true; <?php }
	if ($prod_img == 'featured'){ ?> var gealimg = document.getElementById("feprod-img"); gealimg.checked = true; <?php }
	?> </script> 
	</div>
	
	<?php
	}}
  
//File upload 
function MTIOG_file_upload() {
	if ( ! isset( $_POST['MTIOG_add_to_db'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['MTIOG_nonce'], 'upload_MTIOG_file' ) ) {
		wp_die( esc_html__( 'Nonce mismatched', 'theme-text-domain' ) );
	}
	$allowed_extensions = array( 'jpg', 'jpeg', 'png', 'svg' );
	$file_type = wp_check_filetype( $_FILES['meta_image_file']['name'] );
	$file_extension = $file_type['ext'];
	if ( ! in_array( $file_extension, $allowed_extensions ) ) {
		
		wp_redirect( admin_url() .'/options-general.php?page=MTIOG_meta_tags&error_code=02');
	}
	require_once( ABSPATH . 'wp-admin/includes/image.php' );
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	require_once( ABSPATH . 'wp-admin/includes/media.php' );
	$attachment_id = media_handle_upload( 'meta_image_file' , 0);
	if ( is_wp_error( $attachment_id ) ) {
		wp_die( $attachment_id->get_error_message() );
	} else {
		return $attachment_id;
	}
}  
  
  
 // Verify nonce hook
 
 //add_action( 'init', 'MTIOG_file_upload' );



/* ADD VALUES TO <HEAD> */
add_action('wp_head', 'MTIOG_add_meta_tags_head');
function MTIOG_add_meta_tags_head(){
	
	$site_name_db = MTIOG_get_db_val('site_name');
    $description_db = MTIOG_get_db_val('description');
    $use_description_globally_db = MTIOG_get_db_val('use_description_globally');
    $meta_image_url_db = MTIOG_get_db_val('meta_image_url');
    $use_meta_image_globally_db = MTIOG_get_db_val('use_meta_image_globally');
	
	if ((get_post_type() === 'page' || get_post_type() === 'post') && $use_description_globally_db == 'non-global' && !is_front_page()) {
		if (!empty(get_the_title())) {$site_name_db = get_the_title();}
		if (!empty(get_the_excerpt())) {$description_db = get_the_excerpt();}
		
		
	}
	
	if ((get_post_type() === 'page' || get_post_type() === 'post') && $use_meta_image_globally_db == 'non-global' && !is_front_page()) {
		if (!empty(get_the_post_thumbnail_url())) {$meta_image_url_db = get_post_thumbnail_id();}
	}
	
		if (get_post_type() === 'product' && function_exists( 'MTIOG_woo_is_active' )){
			if (!empty(get_the_title())) {$site_name_db = $site_name_db . ' - ' . get_the_title();}
			$price_title_or = MTIOG_get_db_val('price_title_or');
			$desc_prod_or = MTIOG_get_db_val('desc_prod_or');
			$prod_img = MTIOG_get_db_val('prod_img');
		
			$product = wc_get_product( get_the_ID());
			if ($prod_img == 'featured') {
				if( function_exists('MTIOG_get_prod_img')) { 
					$meta_image_url_db = MTIOG_get_prod_img($meta_image_url_db);
				}
			}
		
			if  ($desc_prod_or == 'cont') {
				if( function_exists('MTIOG_get_prod_desc')) { 
					$description_db = MTIOG_get_prod_desc($description_db);
				}
			}
			else if  ($desc_prod_or == 'short') {
				if( function_exists('MTIOG_get_prod_desc')) { 
					$description_db = MTIOG_get_prod_desc($description_db);
				}
			}
			else if  ($desc_prod_or == 'none') {
				if( function_exists('MTIOG_get_prod_desc_none')) { 
					$description_db = MTIOG_get_prod_desc_none();
				}
			}
			if ($price_title_or == 'title') {
				if( function_exists('MTIOG_get_price_a_title')) { 
					$site_name_db = MTIOG_get_price_a_title($site_name_db, $product);
				}
			}
			else if ($price_title_or == 'descr') {
				if  ($desc_prod_or == 'none') {
					if( function_exists('MTIOG_get_price_none_descr')) { 
						$description_db = MTIOG_get_price_none_descr($product);
					}
				}
				else {
					if( function_exists('MTIOG_get_price_a_descr')) { 
						$description_db = MTIOG_get_price_a_descr($description_db, $product);
					}
				}
			}
		}
		
		
	
?>
<!-- Primary Meta Tags -->
<title><?php echo esc_html(htmlentities($site_name_db)); ?></title>
<meta name="title" content="<?php echo esc_html(htmlentities($site_name_db)); ?>">
<meta name="description" content="<?php echo esc_html(htmlentities(strip_tags($description_db))); ?>">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="<?php echo esc_html(MTIOG_meta_domain_v()); ?>">
<meta property="og:title" content="<?php echo esc_html(htmlentities($site_name_db)); ?>">
<meta property="og:description" content="<?php echo esc_html(htmlentities(strip_tags($description_db))); ?>">
<meta property="og:image" content="<?php echo esc_url_raw(wp_get_attachment_url($meta_image_url_db)); ?>">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="<?php echo esc_html(MTIOG_meta_domain_v()); ?>">
<meta property="twitter:title" content="<?php echo esc_html(htmlentities($site_name_db)); ?>">
<meta property="twitter:description" content="<?php echo esc_html(htmlentities(strip_tags($description_db))); ?>">
<meta property="twitter:image" content="<?php echo esc_url_raw(wp_get_attachment_url($meta_image_url_db)); ?>">
<?php
};




add_filter( 'plugin_action_links_meta-tags-io-generator/main.php', 'MTIOG_settings_link' );
function MTIOG_settings_link( $links ) {
	// Build and escape the URL.
	$url = esc_url( add_query_arg(
		'page',
		'MTIOG_meta_tags',
		get_admin_url() . 'admin.php'
	) );
	// Create the link.
	$settings_link = "<a href='$url'>" . __( 'Settings' ) . '</a>';
	// Adds the link to the end of the array.
	array_push(
		$links,
		$settings_link
	);
	return $links;
}//end MTIOG_settings_link()