<?php
/*=====
Uses class to generate new taxonomy
=====*/
$memsets = array(
		'memusercats1' => array(
			'name' => 'User Memberships',
			'description' => 'User membership categories',
			'optionname' => 'usermemdefs',
		),
);
/*=====
Uses class to generate new sub menu
=====*/
$usermempage = new GenerateSettingsPages($memsets, 'User Members', 'User Membership', 'submenu', 'users.php', '', 'wpr-memgo');

/*=====
Hooks into the class to prevent actually creating the page innards
=====*/
function memForUser($args){
	if($args['pagetitle'] == "User Members"){return false;}
	return true;
}
add_filter('wpr_pagemaker_pagecall_continue', 'memForUser', 10);
add_filter('wpr_pagemaker_settingsinit', 'memForUser', 10);
/*=====
Checks for a passed get param and pagenow to determin
if the settings page we just created and redirects to taxonomy edit screen.
This is used to create a custom link on a submenu.
=====*/
function redirect_usermemtax(){
  global $pagenow;
  if(!empty($_GET['page'])){
	if($pagenow == "users.php" && $_GET['page'] == "wpr-memgo"){
	  $relu = admin_url() . 'edit-tags.php?taxonomy=wpr_membership';
      wp_safe_redirect($relu);
      exit;
	}
  }
}
add_action('init', 'redirect_usermemtax');
/*=====
This correctst the parent menu item for the taxonomy page redirect
we used above.  It then adds some javascript to highlight the sub menu
under users.
=====*/
function fix_user_mem_page($parent_file = ''){
	global $pagenow;
	if(!empty($_GET[ 'taxonomy' ]) && $_GET['taxonomy'] == 'wpr_membership' && $pagenow == 'edit-tags.php'){
		$parent_file = 'users.php';
		echo '<script>jQuery(document).ready(function(){jQuery("#menu-users").find("a[href$=\"page=wpr-memgo\"]").addClass("current").parents("li").addClass("current");});</script>';
	}
	return $parent_file;
}
add_filter('parent_file', 'fix_user_mem_page');
/*=====
Here we add the use category list to the user profile pages.  Editable only by admins.
HOOK: apply_filters('wpr_profilecats_beforemake', true, $user); //Do whatever here and return false if you don't want code to execute.
=====*/
function extra_user_profile_fields_mems($user){ 
	if(!current_user_can('manage_options')){return;}
	$tocontinue = apply_filters('wpr_profilecats_beforemake', true, $user);
	if($tocontinue === false){return;}
	if(!is_string($user)){
?>
	<h3><?php _e("User Membership", "wpr"); ?></h3>
	<table class="form-table">
	<tr>
	<th><label><?php _e("Set Membership", "wpr"); ?></label></th>
	<td>
<?php
	}
	$terms = get_terms('wpr_membership', array('orderby'=>'name','order'=>'ASC','hide_empty'=>false));//,'fields'=>'id=>name'
	if(is_string($user)){
		$ray = explode(",", $user);
		$userterms = wp_get_object_terms((int)$ray[0], 'wpr_membership', array('type'=>$ray[1]));
	}else{
		$userterms = wp_get_object_terms($user->ID, 'wpr_membership', array('type'=>'user'));
	}
	//wp_update_term($term_id, $taxonomy, $args);//add term_group with this
	//term_id, name, slug, term_group, term_taxonomy_id, taxonomy, description, parent, count
	$hmel = '';

	$hmel .= '<ul style="list-style-type: none;max-height: 150px;overflow-y: auto;width:100%;display: inline-block;background: #FFF;padding: 10px 50px 10px 5px;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;">';

	$pf = array();
	if(is_array($terms)){
		for($o=0;$o<count($terms);$o++) {
			$terms[$o] = (array)$terms[$o];
			$terms[$o]['wpr_level'] = 0;
			$terms[$o] = (object)$terms[$o];
		}
		$pf = getme_tax_ordered($terms);
	}
	$i=0;
	foreach($pf as $term){
	  $isselc = '';
	  $stylz = '';
	  foreach($userterms as $ut){if($ut->term_id == $term->term_id){$isselc = "checked";break;}}
	  $marg = $term->wpr_level * 7;
	  $stylz = 'margin-left:' . $marg . 'px;';
      
	  $hmel .= '<li style="list-style-type:none;padding:2px 0px;width: 100%;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box;';
	  $hmel .= $stylz . '">';
	  $hmel .= '<input type="checkbox" name="umemz[]" value="' . $term->term_id . '" ' . $isselc . '>';
	  $hmel .= '<span>' . $term->name . '</span>';
	  $hmel .= '</li>';
	  $i++; 
	}
	$hmel .= '</ul>';
	if(is_string($user)){return $hmel;}else{echo $hmel;}
	
	?>
	</td>
	</tr>
	</table>
	<?php 
}
add_action( 'show_user_profile', 'extra_user_profile_fields_mems' );
add_action( 'edit_user_profile', 'extra_user_profile_fields_mems' );
/*=====
Saves the defined categories created above.  No hooks here, if you used the
hook above, you can just call the hooks in your functions to modify saving.
=====*/
function save_user_memcatz($user_id){
	if(!current_user_can('edit_user', $user_id)){return false;}
	if(isset($_POST['umemz'])){
		$intids = array_map('intval', $_POST['umemz']);
		wp_set_object_terms($user_id, $intids, 'wpr_membership', false);
	}else{
		wp_set_object_terms($user_id, array(), 'wpr_membership', false);
	}
}
add_action( 'personal_options_update', 'save_user_memcatz' );
add_action( 'edit_user_profile_update', 'save_user_memcatz' );
/*=====
Create the user column in the edit users screen
=====*/
function edit_users_custmem($columns){return array_merge($columns, array("usermms"=>"User Members"));}
add_filter("manage_users_columns", "edit_users_custmem", 10);
/*=====
Populate the user columns with filterable links to the user category
=====*/
function inside_users_mems($ret, $column, $user_id){
	$userterms = wp_get_object_terms((int)$user_id, 'wpr_membership', array('type'=>'user'));
	$toret = '';
	$x=0;
	if($userterms){
	  foreach($userterms as $cat){
		if($x >0){$toret .= ", ";}
		$toret .= '<a href="?' .$cat->taxonomy . '=' . $cat->term_id . '&paged=1" >' . $cat->name . '</a>';
		$x++;
	  }
	}else{$toret = "not assigned.";}
	if($column == "usermms"){
		return $toret;
	}
	return $ret;
}
add_filter("manage_users_custom_column", "inside_users_mems", 10, 3);
/*=====
Make sure we add the taxonomy in the query when filtering users by our tax.
=====*/
function admin_users_mem_filter($query){
	global $pagenow,$wp_query;
	if(isset($_GET['memwhatdo'])){return $query;}
	if(is_admin() && $pagenow=='users.php' && isset($_GET['wpr_membership']) && !empty($_GET['wpr_membership'])) {
	$taxid = $_GET['wpr_membership'];
	$taxes = explode(",", $taxid);
	$taxes = array_map('intval', $taxes);
		$terms = array();
	foreach($taxes as $taxid){$term = get_term($taxid, 'wpr_membership');$terms[] = $term->term_taxonomy_id;}
	$ttaxes = implode(",", $terms);
	global $wpdb;
		if (!is_null($taxid)){
		$query->query_from .= " INNER JOIN {$wpdb->term_relationships} ON " . 
			"{$wpdb->users}.ID={$wpdb->term_relationships}.object_id AND {$wpdb->term_relationships}.term_taxonomy_id IN ({$ttaxes})";		
		}
	}
	return $query;
}
add_filter( 'pre_user_query', 'admin_users_mem_filter' );
/*=====
Add a filter selection to the user edit screen at the top, also add a custom assign button for easy access.
=====*/
function restrict_users_by_mem(){
	$terms = get_terms('wpr_membership', array('orderby'=>'name','order'=>'ASC','hide_empty'=>false));
	$pt = array();
	if(is_array($terms)){
		for($o=0;$o<count($terms);$o++) {
			$terms[$o] = (array)$terms[$o];
			$terms[$o]['wpr_level'] = 0;
			$terms[$o] = (object)$terms[$o];
		}
		$pt = getme_tax_ordered($terms);
	}
	?>
	<select id="ucatmems" name="wpr_membership[]" style="float: none;max-height:40px;" multiple>
		<option value=""><?php _e('All User Members', 'wpr'); ?></option>
	<?php
	foreach($pt as $p){
		?><option value="<?php echo $p->term_id; ?>"><?php echo $p->name; ?></option><?php
	}
	?>
	<option value="wpr_unset">Remove All</option>
	</select>
	<input type="hidden" id="memwhatdo" name="memwhatdo" value=""/>
	<input id="post-query-submit-um" class="button" type="button" value="Filter" name="" style="margin-right: 0px;">
	<input id="assignumem" class="button" type="button" value="Assign" name="">
	<script>
	jQuery(document).ready(function(){
		jQuery("#post-query-submit-um").click(function(e){
			e.preventDefault();
			jQuery("#memwhatdo").val("mem_filter");
			jQuery('form').submit();
		});
		jQuery("#assignumem").click(function(e){
			e.preventDefault();
			jQuery("#memwhatdo").val("mem_assign");
			jQuery('form').submit();
		});
	});
	</script>
	<?php   
}
add_action( 'restrict_manage_users', 'restrict_users_by_mem' );
/*=====
We are creating a method to add users for either javascript ajax or form submission
for the buttons we added above.
=====*/
function bulk_user_memadd($ursrs=array()){
	$frompost = false;
	if(empty($ursrs)){$frompost = true;$ursrs = $_POST['ursrs'];}
	if(empty($ursrs) || !$ursrs){
		if($frompost){
			echo json_encode(array("results"=>"bad", "od"=>"Values not supplied."));
			die();
		}else{return "values";}
	}
	if($frompost){$categs = $_POST['categs'];}
	else{$categs = $_GET['wpr_membership'];}
	if(!is_array($categs)){
		$categs = (array)$categs;
	}
	if(!current_user_can('edit_user', get_current_user_id())){
		if($frompost){
			echo json_encode(array("results"=>"bad", "od"=>"You do not have this privilege."));
			die();
		}else{return "privilege";}
	}
	$categs = array_filter($categs);
	if(!$ursrs || empty($ursrs) || !$categs || empty($categs)){
		if($frompost){
			echo json_encode(array("results"=>"bad", "od"=>"Values not supplied."));
			die();
		}else{return "values";}
	}
	if(in_array("wpr_unset", $categs)){	
		foreach($ursrs as $user){
			$user = (int)$user;
			wp_set_object_terms($user, array(), 'wpr_membership', false);
		}
	}
	else{
		$categs = array_map('intval', $categs);
		foreach($ursrs as $user){
			$user = (int)$user;
			wp_set_object_terms($user, $categs, 'wpr_membership', false);
		}
	}	
	if($frompost){
		echo json_encode(array("results"=>"good", "od"=> "The categories have been assigned."));
		die();
	}else{return true;}
}
add_action('wp_ajax_bulkuseradd', 'bulk_user_memadd');
/*=====
Here we handle the get params sent with the form, which include the 
custom filter/add category thing we created two methods up.  This method
calls the bulk user catadd method because that method can be used for this or ajax.
=====*/
function users_list_addmem(){
	//$wp_list_table = _get_list_table('WP_Posts_List_Table');
	//$action = $wp_list_table->current_action();
	global $pagenow;
	if($pagenow == "users.php" && isset($_GET['wpr_membership']) && isset($_GET['memwhatdo'])){
		if($_GET['memwhatdo'] == 'mem_assign'){
			if(!isset($_GET['users'])){return;}
			$rre = bulk_user_memadd($_GET['users']);
			$didgood = "yes";
			if(is_string($rre)){$didgood = $rre;}
			$sendback = add_query_arg(array('moresults' => $didgood), admin_url() . "users.php");
			wp_redirect($sendback); 
			exit();
		}elseif($_GET['memwhatdo'] == 'mem_filter'){
			$categs = $_GET['wpr_membership'];
			if(!isset($categs) || empty($categs)){
				$sendback = add_query_arg(array('moresults' => 'No params set'), admin_url() . "users.php");
				wp_redirect($sendback);
				exit();
			}
			if(!is_array($categs)){
				$categs = (array)$categs;
			}
			$categs = array_filter($categs);
			if(in_array("wpr_unset", $categs)){for($i=0;$i<count($categs);$i++){if($categs[$i] == "wpr_unset"){unset($categs[$i]);}}}
			if(empty($categs)){return;}
			$sendback = add_query_arg(array('wpr_membership'=>implode(",", $categs), 'paged'=>'1'), admin_url() . "users.php");
			wp_redirect($sendback);
			exit();
		}
	} return;
}
add_action('load-users.php', 'users_list_addmem');
/*=====
Notify the users of success or failure of adding the category from the user edit screen.
=====*/
function user_mem_notices(){ 
  global $pagenow; 
  if(is_admin() && $pagenow=='users.php' && isset($_REQUEST['moresults']) && $_REQUEST['moresults'] != ''){
	if($_REQUEST['moresults']){
		switch($_REQUEST['moresults']){
			case "yes":
				$message = __("User categories edited.", "wpr");
			break;
			case "privilege":
				$message = __("You lack privileges for this.", "wpr");
			break;
			case "values":
				$message = __("There are values missing from this request.", "wpr");
			break;
			default:
				$message = __("There was an error with the request.", "wpr");
		}
		if($_REQUEST['moresults'] == "yes"){$message = __("User Categories Edited", "wpr");}
		echo '<div class="updated"><p>' . $message . '</p></div>';
	}
  }
}
add_action('admin_notices', 'user_mem_notices'); 
/*=====
General method that will be used externally to update a user memberships (payments, etc...)
=====*/
function updateUserMems($user = 0, $categs = array(), $passed="id", $append = false){
	if($user === 0){$user = get_current_user_id();}
	$isuser = get_user_by('id', (int)$user);
	if(!$user || $isuser === false){return "User does not exist";}
	if(!$isuser instanceof WP_User){return "User does not exist";}
	if(!is_array($categs)){$categs = explode(",", $categs);}
	if(empty($categs)){return "No categories supplied.";}
	if($passed == "id"){$categs = array_map('intval', $categs);}
	$categs = array_unique($categs);
	$terms = wp_set_object_terms($user, $categs, 'wpr_membership', $append);
	return $terms;
}//if(is_string($terms)){}elseif(is_wp_error($terms)){$terms->get_error_message();}else{}
?>