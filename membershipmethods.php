<?php
global $wpr_membersettings;
$membertax = array(
	array(
		'id'=> 'wpr_membership',
		'general_single' => 'Membership',
		'general_plural' => 'Memberships',
		'column_id' => 'memcat',
		'column_name' => 'Membership',
		'hierarchical' => true,
		'public' => false,
		'show_ui' => true,
		'show_in_nav_menus' => false,
		'show_tagcloud' => false,
		'show_admin_column' => true,		
	)
);
/*=====
Use class to generate taxonomy, make this for posts because we know it exists.
Will apply to all other types later.
=====*/
$memship = new GenerateCustType('user', array(), $membertax, true, false);//posts
/*=====
Here we assign the taxonomy to all post types except revisions and this plugin's post types.
=====*/
function give_posts_memberhsip(){
	if(!is_admin()){return;}
	global $wp_post_types;
	$tipes = array("revision","wprmakepages", "wprmakecpts", "wprmakemeta", "wprmakeforms");//"attachment", "nav_menu_item"
	$tipes = apply_filters('wpr_mem_exludetypes', $tipes);
	foreach($wp_post_types as $tipe=>$ob){
		if(in_array($tipe, $tipes)){continue;}
		register_taxonomy_for_object_type('wpr_membership', $tipe);
	}
	//register_taxonomy_for_object_type('wpr_membership', 'user');
/*Array ( if(!['attachment, revision, nav_menu_item']//['page'], ['post'], {type-id} -> name, labels['name']-['singular_name'], description, public, cap=array()*/
}
add_action('wp_loaded', 'give_posts_memberhsip', 3);

/*=====
Menus don't really allow for adding fields to the menu items in the admin menu edit screen,
not without creating a custom walker, so we'll add some custom fields to the screen and 
use ajax to do what we want.  Chose not to do custom walker because I want to keep the admin
as is so possible updates to admin code don't effect me.
=====*/
function wpr_create_menu_catchooser(){
	//$output = extra_user_profile_fields(",nav_menu_item");
	//menu-item 
	//attr("id").str.replace("menu-item-")
	$obid = (int)$_POST['obid'];
	$terms = get_terms('wpr_membership', array('orderby'=>'name','order'=>'ASC','hide_empty'=>false));//,'fields'=>'id=>name'
	$obterms = wp_get_object_terms($obid, 'wpr_membership', array('type'=>'nav_menu_item'));
	//wp_update_term($term_id, $taxonomy, $args);
	//term_id, name, slug, term_group, term_taxonomy_id, taxonomy, description, parent, count
	$hmel = '';

	$hmel .= '<ul id="menutaxl" style="list-style-type: none;max-height: 150px;overflow-y: auto;width:100%;display: inline-block;background: #FFF;padding: 10px 50px 10px 5px;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;">';
	
	$pf = array();
	if(!empty($terms) && is_array($terms)){
		for($o=0;$o<count($terms);$o++) {
			$terms[$o] = (array)$terms[$o];
			$terms[$o]['wpr_level'] = 0;
			$terms[$o] = (object)$terms[$o];
		}
		$pf = getme_tax_ordered($terms);
	}
	
	$i=0;
	if(!empty($pf) && is_array($pf)){
		foreach($pf as $term){
			$isselc = '';
			$stylz = '';
			foreach($obterms as $ut){if($ut->term_id == $term->term_id){$isselc = "checked";break;}}
			$marg = $term->wpr_level * 7;
			$stylz = 'margin-left:' . $marg . 'px;';
			
			$hmel .= '<li style="list-style-type:none;padding:2px 0px;width: 100%;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;';
			$hmel .= $stylz . '">';
			$hmel .= '<input type="checkbox" name="ucatz" value="' . $term->term_id . '" ' . $isselc . '>';
			$hmel .= '<span>' . $term->name. '</span>';
			$hmel .= '</li>';
			$i++; 
		}
	}
	$hmel .= '</ul>';
	echo json_encode(array("results"=>"good", "hmel"=>$hmel)); 
	die();
}
add_filter('wp_ajax_menuitemcat', 'wpr_create_menu_catchooser');
/*=====
The method ajax will call in order to update the menu item taxonomy.
Only one menu item allowed at a time in this case.
=====*/
function update_menu_categs(){
	$categs = $_POST['categs'];
	$obj = $_POST['menu_tiem'];
	if(!isset($_POST['menu_tiem'])){
		echo json_encode(array("results"=>"bad", "hmel"=>"Menu Item ID not provided.")); 
		die();
	}
	if(false === is_numeric($obj)){
		echo json_encode(array("results"=>"bad", "hmel"=>"Menu Item ID is incorrect.")); 
		die();
	}
	$obj = (int)$obj;
	if(false === get_post_status($obj)){
		echo json_encode(array("results"=>"bad", "hmel"=>"Menu Item ID does not exist.")); 
		die();
	}
	if(!isset($_POST['categs']) || empty($categs) || !$categs){$categs = array();}
	$hhmel = "Terms have been set.";
	if(!empty($categs)){
			$categs = array_map('intval', $categs);
			$user = (int)$user;
			wp_set_object_terms($obj, $categs, 'wpr_membership', false);
	}else{
			wp_set_object_terms($obj, array(), 'wpr_membership', false);
			$hhmel = "Terms have been removed";
	}
	echo json_encode(array("results"=>"good", "hmel"=>$hhmel)); 
	die();
}
add_filter('wp_ajax_updatemenitem', 'update_menu_categs');
/*=====
Create the actual post meta for the nav menu edit screen.
=====*/
$navboxer = array(array("name" => __('Set Category', 'wpr'), "desc" => __("Check the menu item to the right to set category.", 'wpr'), "id" => "usercateg_menitem", "std" => "", "type" => "navusercat"));
$pmNavMen = new GeneratePostMeta($navboxer, 'nmen_mmbrshp', 'Tax Member Categories', 'ucatz', 'nav-menus','side', 'default');//, $margs
/*=====
Create the actual post meta for the media attachment edit screen.
=====*/
function toggle_Media_Privacy($form_fields, $post){
	$val = (int)get_post_meta($post->ID, "privymeda", true);
	//$val2 = (int)get_post_meta($post->ID, "js_priv_images", true);
	$checked = "";
	if($val){$checked = "checked";}
    $form_fields["privymeda"]["label"] = __("Toggle Privacy", "wpr");
	$form_fields["privymeda"]["input"] = "html";
	$form_fields["privymeda"]["html"] = ' (checked is private):  <input type="checkbox" value="1" name="attachments[' . $post->ID . '][privymeda]" id="attachments[' . $post->ID . '][privymeda]" ' . $checked . ' />';
    $form_fields["privymeda"]["value"] = 1;
	
    return $form_fields;
}
add_filter("attachment_fields_to_edit", "toggle_Media_Privacy", 10, 2);
function save_Toggle_Meda_Priv( $attachment_id ) {
		$ispriv = (int)get_post_meta($attachment_id, 'privymeda', true);
		$value = (int)$_REQUEST['attachments'][$attachment_id]['privymeda'];
		$value = $value?$value:0;
		if($ispriv == $value){return;}
		update_post_meta($attachment_id, 'privymeda', $value);
		wpr_protectMedia($attachment_id, $value);
}
add_action('edit_attachment', 'save_Toggle_Meda_Priv');
/*=====
Using a filter in my GeneratePostMeta class to output javascript on the page the meta is applied to.
=====*/
function nav_fields_addscript($post, $meta_data, $xargs){
	if($meta_data['id'] == "nmen_mmbrshp"){
	?>
	<script>
	jQuery(document).ready(function(){
			jQuery("ul#menu-to-edit .menu-item").each(function(e){
				var miid = jQuery(this).attr("id").replace("menu-item-", "");
				jQuery(this).find(".menu-item-title").after('<span class="disid" style="margin-left:20px;"><input type="checkbox" value="' + miid + '" />' + miid + '</span>');
			});
			jQuery(document).on("click", ".disid input", function(e){
				jQuery(".disid input").not(jQuery(this)).prop("checked", false);
				jQuery(".accordion-section-content").hide();
				var mysect = jQuery("#nmen_mmbrshp").find(".accordion-section-content");
				mysect.show();
				mysect.find("#nav_i_count").val(jQuery(this).val());
				var data = {
					action: 'menuitemcat',
					obid: jQuery(this).val()
				};
				jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', data, 
					function(response) {
						var data = jQuery.parseJSON(response);
						if(data.results == "good"){
							//jQuery('.disid input').prop("checked", false);
							//jQuery("#nav_i_count").val("");
							jQuery("#fillherin").html(data.hmel);
							jQuery("#dropnavhere").html("");
							//location.reload();
						}
						else{alert(data.hmel);}
					}
				);
			});
			//jQuery(document).on('dragover', "#dropnavhere", function(e){alert("dragged");});
			jQuery("#menu_dothing").click(function(e){
				var menutiem = jQuery('#nav_i_count').val();
				if(menutiem == ""){return;}
				var categs = jQuery('#menutaxl input[name="ucatz"]:checked').map(function(){return this.value;}).get();
				if(!menutiem || menutiem.length <= 0){return false;}
				var data = {
					action: 'updatemenitem',
					menu_tiem: menutiem,
					categs: categs
				};
				jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', data, 
					function(response) {
						var data = jQuery.parseJSON(response);
						if(data.results == "good"){
							jQuery("#dropnavhere").html(data.hmel);
						}
						else{alert(data.hmel);}
					}
				);
			});
		})
	</script>
	<?php
	}
}
add_action('wpr_postmeta_afterfields', 'nav_fields_addscript', 10, 3);
/*=====
Creating a new meta field type by hooking into my GeneratePostMeta class.
=====*/
function custom_nav_fields($post_ob_name, $value, $metabox, $post, $optdata, $xargs){
	if($value['type'] == "navusercat"){
	?>
	<div id="count_n_items"><label for="nav_i_count">Item ID</label><input id="nav_i_count" type="text" value="" /></div>
	<div id="dropnavhere" style="width: 100%; height:30px;border: 1px dashed #000;clear:both;margin: 10px 0px;"></div>
	<div id="fillherin"></div>
	<div id="menu_dothing" style="float:right;text-align:center;background:#4C9ED9;color:white;margin:0px auto;display:inline-block;padding:2px 5px;cursor:pointer">
	Submit
	</div>
	<?php
	}
}
add_action('wpr_postmeta_myfield', 'custom_nav_fields', 10, 6);
/*===== FULL ACCESS CONTROL
Restrict all posts that are categorized outside of user's privileges.
USES: excludeTaxList method defined in this plugin's functions.php file.
HOOK: apply_filters('wpr_mem_custq', $query);//may not work, trying to pass query by reference, but if not, send whole thing and reassign
HOOK: apply_filters('wpr_mem_q_exclude', true);//You can disable execution by returning false to this filter.
In order to specify custom queries with taxonomy query, you can do this:
$exclude = excludedTaxList();
$quirgs = array(
	'post_type' => 'post',
	'posts_per_page' => -1,
	'tax_query' => array(
		'relation' => 'AND',
		array(
			'taxonomy' => 'category',
			'field'    => 'slug',
			'terms'    => 'test-cat',
		),
		array(
			'taxonomy' => 'wpr_membership',
			'field'    => 'slug',
			'terms'    => $exclude,
			'operator' => "NOT IN"
		),
	),
);
add_filter('wpr_mem_q_exclude', '__return_false');
$the_query = new WP_Query($quirgs);
remove_filter('wpr_mem_q_exclude', '__return_false');
=====*/
function exclude_category($query){
	if(is_admin()){return;}
	$exclude = array();
	//$passquery = clone $query;
	$query = apply_filters('wpr_mem_custq', $query);
	$continue = apply_filters('wpr_mem_q_exclude', true);
	if($continue === false){return;}
	$exclude = excludedTaxList(0, "both");//"include" "exclude"
	if(empty($exclude["ex"]) && empty($exclude["in"])){return $query;}
	$tax_query = $query->get( 'tax_query' );
	if(!empty($tax_query)){
		$tax_query = array();
		$tax_query['relation'] = 'OR';
		$tax_query[] = array(
			'taxonomy' => 'wpr_membership',
			'field' => 'id',
			'terms'    => $exclude["ex"],
			'operator' => "NOT IN"
		);
		$tax_query[] = array(
			//'relation' => 'OR',
			array(
				'taxonomy' => 'wpr_membership',
				'field' => 'id',
				'terms'    => $exclude["in"],
				'operator' => "IN"
			)
		);
		$query->set('tax_query', $tax_query);
	}else{
		$taxex = array(
			'relation' => 'OR',
			array(
				'taxonomy' => 'wpr_membership',
				'field' => 'id',
				'terms'    => $exclude['ex'],
				'operator' => "NOT IN"
			),
			array(
				//'relation' => 'OR',
				array(
					'taxonomy' => 'wpr_membership',
					'field' => 'id',
					'terms'    => $exclude["in"],
					'operator' => "IN"
				)
			)
		);
		$query->set('tax_query', $taxex);
	}
}
/*========================== FULL ACCESS CONTROL =================================*/
if(!isset($wpr_membersettings["wpr_mem_allorsome"]) || $wpr_membersettings["wpr_mem_allorsome"] != "some"){
	add_action('pre_get_posts', 'exclude_category');
}
/*========================== LIMITED ACCESS CONTROL =================================*/
/* TODO ADD LIMITED ACCESS CONTROL
function modifyPostsAfterQuery($posts){
	$filtered_posts = array();
	foreach($posts as $post){
		if(false === strpos($post->post_title, 'selfie')){
			$filtered_posts[] = $post;
		}
	}
	return $filtered_posts ;
}
//add_filter( 'posts_results', 'modifyPostsAfterQuery' );
function remove_more_link_scroll($link){
	$link = preg_replace( '|#more-[0-9]+|', '', $link );
	return $link;
}
add_filter( 'the_content_more_link', 'remove_more_link_scroll' );
function new_excerpt_more($more) {
    global $post;
	return '<a class="moretag" href="'. get_permalink($post->ID) . '"> Read the full article...</a>';
}
add_filter('excerpt_more', 'new_excerpt_more');
add_filter ( 'the_content' , 'pr_page_restrict' , 50 );
add_filter ( 'the_excerpt' , 'pr_page_restrict' , 50 );
add_filter ( 'comments_array' , 'pr_comment_restrict' , 50 );
?add_filter( 'authenticate', 'pr_authenticate', 50, 3 );
add_filter('comments_open', 'close_comments', 99, 2);
add_action('the_posts', array(&$this, 'show_noaccess_page'), 1 );
*/
if(isset($wpr_membersettings["wpr_mem_allorsome"]) && $wpr_membersettings["wpr_mem_allorsome"] == "some"){
	/*======= TO DO: create limited access content =======*/
	add_action('pre_get_posts', 'exclude_category');
}
/*=====
Restrict all pages that are categorized outside of user's membership privileges.
=====*/
function wpr_restrictPages($posts){
	global $wpr_membersettings;
	if(is_admin() || $_SERVER['REQUEST_URI'] == "/"){return $posts;}
	$nogo = false;
	$gohere = $wpr_membersettings["wpr_mem_redirurl"];
	$gohere = str_replace("%homeurl%", home_url(), $gohere);
	if(empty($gohere)){$gohere = home_url();}
	//$gohere = home_url();
	$wheretogo = apply_filters("wpr_non_member_redirect", $gohere);
	if(!empty($posts)) {	
	  if(count($posts) == 1 && isset($posts[0]->post_type)){
	  	$obj = wp_get_object_terms($posts[0]->ID, 'wpr_membership', array('type'=>$posts[0]->post_type, 'fields'=>'ids'));
		if(empty($obj) || is_wp_error($obj)){return $posts;}
		$user_id = get_current_user_id();
		if(!$user_id && !headers_sent()){			
			wp_safe_redirect($wheretogo);
			exit;
		}else{
			$restricted = get_excluded_taxs($user_id);
			foreach($restricted as $slug){
				if(in_array($slug, $obj)){$nogo = true; break;}
			}
			if($nogo && !headers_sent()){wp_safe_redirect($wheretogo);	exit;}
		}
	  }
	}
	return $posts;
}
add_action('the_posts', 'wpr_restrictPages');
/*=====
Swap files that are in uploads directory to private subdirectory when user identifies them as private files.
=====*/
//print_r(get_post_meta(952));
function wpr_protectMedia($attachment_id=0, $ispriv=0){
	global $wpdb;
	if(!$attachment_id){return;}
	$sizes = array();
	$file = array();
	$filename = '';
	$meta = get_post_meta($attachment_id, '_wp_attachment_metadata', true);
	if(!isset($meta['file'])){
		$meta['file'] = get_post_meta($attachment_id, '_wp_attached_file', true);
		$meta['sizes'] = array();
	}
	$file = explode(DIRECTORY_SEPARATOR, $meta['file']);
	if($file[0] == "protected_uploads"){
		if($ispriv){return;}
		$file[0] = $file[1];
		$file[1] = $file[2];
		$file[2] = $file[3];
	}else{if(!$ispriv){return;}}
	$sizes = $meta['sizes'];
	$filename = $file[2];
	$upload_dir = wp_upload_dir();
	if($ispriv){
		$oldbase = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . $file[0] . DIRECTORY_SEPARATOR . $file[1] . DIRECTORY_SEPARATOR;
		$newbase = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . 'protected_uploads' . DIRECTORY_SEPARATOR . $file[0] . DIRECTORY_SEPARATOR . $file[1] . DIRECTORY_SEPARATOR;
	}else{
		$oldbase = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . 'protected_uploads' . DIRECTORY_SEPARATOR . $file[0] . DIRECTORY_SEPARATOR . $file[1] . DIRECTORY_SEPARATOR;
		$newbase = $upload_dir['basedir'] . DIRECTORY_SEPARATOR . $file[0] . DIRECTORY_SEPARATOR . $file[1] . DIRECTORY_SEPARATOR;
	}
	$urel = str_replace($upload_dir['subdir'], '', $upload_dir['url']);
	if($ispriv){if(!is_dir($newbase)){wp_mkdir_p($newbase);}}
	if(file_exists($newbase . $filename)){$newfilename = wp_unique_filename($newbase, $filename);}else{$newfilename = $filename;}
	if($ispriv){
		$newsubpath = "protected_uploads" . DIRECTORY_SEPARATOR . $file[0] . DIRECTORY_SEPARATOR . $file[1] . DIRECTORY_SEPARATOR . $newfilename;
	}else{
		$newsubpath = $file[0] . DIRECTORY_SEPARATOR . $file[1] . DIRECTORY_SEPARATOR . $newfilename;
	}
	$urel .= "/" . $newsubpath;
	if(file_exists($oldbase . $newfilename)){
		rename($oldbase . $newfilename, $newbase . $newfilename);
	}else{return;}
	$wpdb->query("UPDATE $wpdb->posts SET guid = '$urel' WHERE ID = $attachment_id AND post_type = 'attachment'");
	$meta['file'] = $newsubpath;
	if($ispriv){$meta['sizes'] = array();}
	update_post_meta($attachment_id, '_wp_attachment_metadata', $meta);
	update_post_meta($attachment_id, '_wp_attached_file', $newsubpath);
	foreach($sizes as $size){
		if(file_exists($oldbase . $size['file'])){unlink($oldbase . $size['file']);}
	}	
	/*
	_wp_attachment_metadata	a:5:{s:5:"width";i:200;s:6:"height";i:200;s:4:"file";s:17:"2014/11/anono.png";s:5:"sizes";a:3:{s:9:"thumbnail";a:4:{s:4:"file";s:17:"anono-150x150.png";s:5:"width";i:150;s:6:"height";i:150;s:9:"mime-type";s:9:"image/png";}s:14:"responsive-100";a:4:{s:4:"file";s:17:"anono-100x100.png";s:5:"width";i:100;s:6:"height";i:100;s:9:"mime-type";s:9:"image/png";}s:14:"responsive-150";a:4:{s:4:"file";s:17:"anono-150x150.png";s:5:"width";i:150;s:6:"height";i:150;s:9:"mime-type";s:9:"image/png";}}s:10:"image_meta";a:11:{s:8:"aperture";i:0;s:6:"credit";s:0:"";s:6:"camera";s:0:"";s:7:"caption";s:0:"";s:17:"created_timestamp";i:0;s:9:"copyright";s:0:"";s:12:"focal_length";i:0;s:3:"iso";i:0;s:13:"shutter_speed";i:0;s:5:"title";s:0:"";s:11:"orientation";i:0;}}	
	//http://codex.wordpress.org/Database_Description#Table:_wp_posts	
	// $upload_dir['path'] => C:\path\to\wordpress\wp-content\uploads\2010\05, [url] => http://example.com/wp-content/uploads/2010/05, [subdir] => /2010/05, [basedir] => C:\path\to\wordpress\wp-content\uploads, [baseurl] => http://example.com/wp-content/uploads, [error] =>
	*/
}
//add_action('wp_ajax_privmeda', 'wpr_protectMedia');
/*=======
Adding a query var
=======*/
function protectedRewriteVar($qv){
    $qv[] = 'file_access';
    return $qv;
}
add_action('query_vars','protectedRewriteVar');
/*=======
Restrict media that is categorized outside of user's membership privileges.
=======*/
function intercept_file_request($query_vars){
    if(!isset($query_vars['file_access'])){return $query_vars;}
    global $wpdb;//, $current_user;
	$user_id = get_current_user_id();
	//$user =  wp_get_current_user();
	$upload_dir = wp_upload_dir();
	$safedir = $upload_dir['baseurl'] . DIRECTORY_SEPARATOR . 'protected_uploads' . DIRECTORY_SEPARATOR . $query_vars['file_access'];
    $query = $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s'", $safedir);
    $attachment_id = $wpdb->get_var($query);
    if(!$attachment_id){$query_vars['error'] = '404';return $query_vars;}
	
    $file_post = get_post($attachment_id);
    $file_path = get_attached_file($attachment_id);
    if(!$file_post || !$file_path || !file_exists( $file_path)){$query_vars['error'] = '404';return $query_vars;}
	
	$nogo = false;
	$obj = wp_get_object_terms($attachment_id, 'wpr_membership', array('type'=>'attachment', 'fields'=>'ids'));
	//if(empty($obj) || is_wp_error($obj)){}
	if(!$user_id){$query_vars['error'] = '404';return $query_vars;}//|| !$user
	else{
		$restricted = get_excluded_taxs($user_id);
		if(!empty($obj)){
			foreach($restricted as $slug){
				if(in_array($slug, $obj)){$nogo = true;}
			}
		}
		if(!is_admin()){
			if($nogo){$query_vars['error'] = '404';return $query_vars;}
		}elseif(!current_user_can('manage_options')){//$user->has_cap
			if($nogo){$query_vars['error'] = '404';return $query_vars;}
		}
	}
	if(!file_exists($file_path)){$query_vars['error'] = '404';return $query_vars;}
	/*$filename = basename($file_path);
	$file_extension = strtolower(substr(strrchr($filename,"."),1));	
	switch( $file_extension ) {
		case "gif": $ctype="image/gif"; break;
		case "png": $ctype="image/png"; break;
		case "jpeg":
		case "jpg": $ctype="image/jpg"; break;
		default:
	}*/
	//- turn off compression on the server
	//@apache_setenv('no-gzip', 1);
	//@ini_set('zlib.output_compression', 'Off');
	header('Content-Description: File Transfer');
	//header('Content-Type: application/octet-stream');//http://www.iana.org/assignments/media-types/media-types.xhtml
	header( 'Content-Type: ' . $file_post->post_mime_type );
	header('Content-Disposition: inline; filename="'.basename($file_path).'"');//render attachment inline
	header('Content-Transfer-Encoding: binary'); 
	header("Expires: -1");//0
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	header('Content-Length: ' . filesize($file_path));
	//header('Connection: Keep-Alive');
	header('Connection: close'); 
	
	ob_clean();
	//ob_flush();
	flush();
	readfile($file_path);
	
	exit();
}
add_filter('request', 'intercept_file_request');
/*=======
This is the activation hook called from this plugin's functions.php file.
=======*/
function createFiles(){
	$upload_dir =  wp_upload_dir();
	$rewrt = <<<REW
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteRule ^(.*)$ /index.php?file_access=$1 [R=301,L]
</IfModule>
SetEnv no-gzip dont-vary
REW;
	$files = array(
		array('dir' => $upload_dir['basedir'] . '/protected_uploads', 'file' => '.htaccess', 'content' => $rewrt),
		array('dir' => $upload_dir['basedir'] . '/protected_uploads', 'file' => 'index.html', 'content' => ''),
	);
	foreach($files as $file){
		if(wp_mkdir_p( $file['dir'] ) && !file_exists(trailingslashit($file['dir']) . $file['file'])){
			if($file_handle = @fopen(trailingslashit($file['dir']) . $file['file'], 'w')){
				fwrite($file_handle, $file['content']);
				fclose($file_handle);
			}
		}
	}
}
/*=======
This is what woocommerce is using for multisite.  Must re-review code and try to support multisite.
TODO: SUPPORT MULTISITE
=======*/
function multisiteProtectFile($rewrite){
	if(!is_multisite()){return $rewrite;}
	$rule  = "\n# Copied from WooCommerce - Protect Files from ms-files.php\n\n";
	$rule .= "<IfModule mod_rewrite.c>\n";
	$rule .= "RewriteEngine On\n";
	$rule .= "RewriteCond %{QUERY_STRING} file=protected_uploads/ [NC]\n";
	$rule .= "RewriteRule /ms-files.php$ - [F]\n";
	$rule .= "</IfModule>\n\n";
	return $rule . $rewrite;
}
//add_filter('mod_rewrite_rules', 'multisiteProtectFile');
/*=====
May use if caching allows users to see restricted content.
=====*/
function dont_cachethe_headers(){
	if(!is_user_logged_in()){nocache_headers();}
}
//add_action('send_headers', 'dont_cachethe_headers');
/*=====
query.php good hooks for after tax query These filter values can be returned as is, but they pass the query object by reference
if ( !$q['suppress_filters'] ) {
'posts_where', 'posts_join', 'posts_where_paged', 'posts_groupby', 'posts_join_paged', 'posts_orderby', 'posts_distinct', 'post_limits', 'posts_fields', 'posts_clauses', 'posts_results', 'the_posts'

add_filter('the_posts', '');
fallback for below (custom plugin queries...);

add_filter("plugin_action_links_$wpr_amplify_basename", 'pr_filter_plugin_actions' );
*/
/*============================ MEMBERSHIP EXCLUDED LIST ==================================*/
function get_excluded_taxs($user_id, $exorin="exclude"){
	$postterms = get_terms('wpr_membership', array('orderby'=>'name','order'=>'ASC','hide_empty'=>false));	
	$userhasterms = wp_get_object_terms($user_id, 'wpr_membership', array('type'=>'user', 'fields'=>'ids'));
	$orderedu = array();
	if(!empty($postterms) && is_array($postterms)){
		for($o=0;$o<count($postterms);$o++) {
			$postterms[$o] = (array)$postterms[$o];
			$postterms[$o]['wpr_level'] = 0;
			$postterms[$o] = (object)$postterms[$o];
		}
		$orderedu = getme_tax_ordered($postterms);
	}
	$useable = array();
	$checkchild = false;
	$clevel = 0;
	if(!empty($orderedu) && is_array($orderedu)){
		foreach($orderedu as $i=>$term){
			if(in_array($term->term_id, $userhasterms)){
				if(!in_array($term->term_id, $useable)){$useable[] = $term->term_id;}
				$checkchild = true;
				$clevel = $term->wpr_level;
				$cparent = $term->parent;
				/*
				//THIS CALCULATES PARENT TAX'S; WE DON'T ACTUALLY WANT THIS.
				//IF PARENT TAX IS ASSIGNED TO POST AND USER IS CHILD THEY SHOULD NOT HAVE ACCESS
				//THE IDEA CAN GET A LITTLE OBSCURE. SIMPLY, USERS CAN ACCESS POSTS WITH SAME OR LESSER MEMBERSHIP LEVEL
				if($clevel > 0){
					$x = $i;
					for($x; $x>=0;$x--){
						if($orderedu[$x]->wpr_level < $clevel){
							if(!in_array($orderedu[$x]->term_id, $useable) && $orderedu[$x]->term_id == $cparent){
								$useable[] = $orderedu[$x]->term_id;
								$cparent = $orderedu[$x]->parent;
							}
							if($orderedu[$x]->parent == 0){break;}
						}	
					}
				}*/
			}
			else{
				if($checkchild){
					if($term->wpr_level > $clevel){$useable[] = $term->term_id;}
					else{$checkchild = false;}
				}
			}		
		}
	}
	$exclude = array();
	if($exorin == "include"){return $useable;}
	if(is_array($postterms) && !empty($postterms)){
		foreach($postterms as $key=>$vvl){
			if(in_array($vvl->term_id, $useable)){continue;}
			$exclude[] = $vvl->term_id;
		}
	}
	if($exorin == "both"){return array("in" => $useable, "ex" => $exclude);}
	return $exclude;
}
function excludedTaxList($user_id = 0, $exorin="exclude"){
	$retset = array();
	if($user_id === 0){$user_id = get_current_user_id();}
	if(!$user_id){
		switch($exorin){
			case "exclude":
				$retset = get_terms('wpr_membership', array('orderby'=>'name','order'=>'ASC','hide_empty'=>false,'fields'=>'ids'));
				if(!is_array($retset)){$retset = array();}
			break;
			case "include":
				$retset = array();
			break;
			case "both":
				$retset["ex"] = get_terms('wpr_membership', array('orderby'=>'name','order'=>'ASC','hide_empty'=>false,'fields'=>'ids'));
				if(!is_array($retset["ex"])){$retset["ex"] = array();}
				$retset["in"] = array();
			break;
		}
	}else{$retset = get_excluded_taxs($user_id, $exorin);}
	return $retset;
}
/*============================== END MEMBERSHIP EXCLUDED LIST =================================*/
?>