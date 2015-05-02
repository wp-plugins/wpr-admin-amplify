<?php
if(!class_exists('GenerateCustType')){
class GenerateCustType{

	protected $typeid;
	protected $typargs;
	protected $taxnm;
	protected $newtype;
	protected $textdomain;

/*=====
Constructor. Initialize class vars, do some checking to make sure the proper things are set.
=====*/
	public function __construct($typeid, $typargs=array(), $taxnm = array(), $filter=false, $newtype=true, $textdomain = "wpr"){
		$reserved = array("post", "page", "attachment", "revision", "nav_menu_item", "action", "order", "theme");
		if($newtype && in_array($typeid, $reserved)){return new WP_Error('post_type_error', "Invalid post name, the post name you used is reserved.");}

		$this->typeid = $typeid;
		$this->textdomain = apply_filters("wpr_custtypes_textdomain", $textdomain);
		$this->typargs = $typargs;
		$this->newtype = $newtype;
		$this->taxnm = $taxnm;
		
		
		if($newtype){
			if(!isset($typargs['general_single']) || empty($typargs['general_single'])){
			  return new WP_Error('value_error', "You must supply a general_single and name value for " . $typeid . " post type.");
			}
			if(!isset($typargs['general_plural']) || empty($typargs['general_plural'])){
			  return new WP_Error('value_error', "You must supply a general_plural and name value for " . $typeid . " post type.");
			}			
			add_action('init', array($this, 'create_Custmz'));
		}
		$dontuse = array("attachment", "attachment_id", "author", "author_name", "calendar", "cat", "category", "category__and", "category__in", "category__not_in", "category_name", "comments_per_page", "comments_popup", "customize_messenger_channel", "customized", "cpage", "day", "debug", "error", "exact", "feed", "hour", "link_category", "m", "minute", "monthnum", "more", "name", "nav_menu", "nonce", "nopaging", "offset", "order", "orderby", "p", "page", "page_id", "paged", "pagename", "pb", "perm", "post", "post__in", "post__not_in", "post_format", "post_mime_type", "post_status", "post_tag", "post_type", "posts", "posts_per_archive_page", "posts_per_page", "preview", "robots", "s", "search", "second", "sentence", "showposts", "static", "subpost", "subpost_id", "tag", "tag__and", "tag__in", "tag__not_in", "tag_id", "tag_slug__and", "tag_slug__in", "taxonomy", "tb", "term", "theme", "type", "w", "withcomments", "withoutcomments", "year");
		
		if(count($taxnm) > 0){
			foreach($taxnm as $acat){
				if(empty($acat['id'])){
					return new WP_Error('value_error', "You must define an id for the taxonomies.");
				}
				if(in_array($acat['id'], $dontuse)){
					return new WP_Error('value_error', "The id " . $acat['id'] . " is not recommended because it can conflict with query params.");
				}
				if(empty($acat['general_single'])){
					return new WP_Error('value_error', "You must supply a general_single name value for " . $acat['id'] . " taxonomy.");
				}
				if( empty($acat['general_plural'])){
					return new WP_Error('value_error', "You must supply a general_plural name value for " . $acat['id'] . " taxonomy.");
				}
				if(empty($acat['hierarchical'])){
					return new WP_Error('value_error', "You define whether " . $acat['id'] . " is hierarchical.");
				}
			}
			add_action('init', array($this, 'create_custm_taxonomizers'));
		}
		
		if($filter){
			foreach($taxnm as $acat){
			 if(empty($acat['column_id']) || empty($acat['column_name'])){
			   return new WP_Error('value_error', "Both column_id and column_name must be set for " . $acat['id'] . " if you wish to supply columns in edit.php.");
			 }
			}
			
			$custcolfilt = $this->typeid;
			$custcolact = $this->typeid . "_";
			if($this->typeid == "posts"){$custcolfilt = "post"; $custcolact = "";}
			add_filter("manage_edit-" . $custcolfilt . "_columns", array($this, "edit_columns_custm"));
			add_action("manage_" . $custcolact . "posts_custom_column" , array($this, "inside_custm_columns"), 10, 2);
		}
	}
/*=====
Hooked from constructor. Registers the post type. 
=====*/
	public function create_Custmz(){
		$fixedar = $this->structurizearray('post', $this->typargs);
		$a = register_post_type($this->typeid, $fixedar);
	}
/*=====
Hooked from constructor. Registers the taxonomy. 
Extra parameter can be added to any of the tax arrays - "not_working" - can be set to true.
This will use the register_taxonomy_for_object_type.  Used if there is a problem with getting tax menu to show up
in built in types.
=====*/
	public function create_custm_taxonomizers(){
	  $deftipes = array("post", "page", "attachment", "revision", "nav_menu_item");
	  $useabletype = $this->typeid;
	  foreach($deftipes as $acttype){if($acttype . "s" == $this->typeid){$useabletype = $acttype;}}
	  foreach($this->taxnm as $acat){
		$fixedar = $this->structurizearray('tax', $acat);
		$b = register_taxonomy($acat['id'], $useabletype, $fixedar);
		if(isset($acat['not_working']) && $acat['not_working'] == true){register_taxonomy_for_object_type($acat['id'], $useabletype);}
	  }		
	}
/*=====
This just presents some default params if none are set and makes sure the correct params are being sent.
HOOK: apply_filters("wpr_custtypes_newpostarray", $newpostarray, $arr);//change the posttype params. $newpostarray=final array;$arr= initial params
HOOK: apply_filters("wpr_custtypes_taxarray", $taxarray, $arr);//change the tax params. $taxarray=final array;$arr= initial params
=====*/
	protected function structurizearray($type, $arr){
		$s = $arr['general_single'];
		$p = $arr['general_plural'];
		$sc = sanitize_title_for_query($s);
		$pc = sanitize_title_for_query($p);
		if($type="post"){
			$newpostarray = array();
			//Too many issues with capabilities and opens up a whole nother plugin for user roles and cpt management
			/*$capabilities = array('edit_post' => 'edit_' . $sc, 'edit_posts' => 'edit_' . $pc, 'edit_others_posts' => 'edit_others_' . $pc, 'edit_private_posts' => 'edit_private_' . $pc, 'edit_published_posts' => 'edit_published_' . $pc, 'publish_posts' => 'publish_' . $pc, 'read_post' => 'read_' . $sc, 'read_private_posts' => 'read_private_' . $pc, 'delete_post' => 'delete_' . $sc, 'delete_posts' => 'delete_' . $pc, 'delete_private_posts' => 'delete_private_' . $pc, 'delete_published_posts' => 'delete_published_' . $pc, 'delte_others_posts' => 'delete_others_' . $pc);*/
			
			$main = array("label", "description", "public", "exclude_from_search", "publicly_queryable", "show_ui", "show_in_nav_menus", "show_in_menu", "show_in_admin_bar", "menu_position", "menu_icon", "capability_type", "capabilities", "map_meta_cap", "hierarchical", "supports", "register_meta_box_cb", "taxonomies", "has_archive", "permalink_epmask", "rewrite", "query_var", "can_export", "_builtin", "_edit_link");
			
			$labels = array("name", "singular_name", "menu_name", "name_admin_bar", "all_items", "add_new", "add_new_item", "edit_item", "new_item", "view_item", "search_items", "not_found", "not_found_in_trash", "parent_item_colon");
			
			$defaultnames = array("name" => $p, "singular_name" => $s, "menu_name" => $s, "name_admin_bar" => $s, "all_items" => "All " . $p, "add_new" => "Add New", "add_new_item" => "Add New " . $s, "edit_item" => "Edit " . $s, "new_item" => "New " . $s, "view_item" => "View " . $s, "search_items" => "Search " . $p, "not_found" => $s . " not found.", "not_found_in_trash" => "No " . $p . " found in Trash.", "parent_item_colon" => 'Parent: ' . $s);
			
			
			//"capability_type" => array($sc, $pc), "capabilities"=> $capabilities, "map_meta_cap" => true,  
			$defaultvals = array("label"=> $p, "public" => true, "show_in_menu" => true, "show_in_admin_bar" => true, "supports" => array("title","editor","author","thumbnail","excerpt","trackbacks","custom-fields","comments","revisions","page-attributes","post-format"), "has_archive" => true);

			$context = array("name"=> "post type general name", "singular_name"=> "post type singular name", "add_new"=>$s, "menu_name"=> "admin menu", "name_admin_bar"=> "add new on admin bar");
			
			foreach($main as $key){
				if(isset($arr[$key])){
					if($key == "label"){$newpostarray[$key] = _($arr[$key], $this->textdomain);continue;}
					$newpostarray[$key] = $arr[$key];
				}elseif(isset($defaultvals[$key])){
					$newpostarray[$key] = $defaultvals[$key];
				}
			}
			foreach($labels as $label){				
				if(!empty($arr[$label])){					
					if(in_array($label, $context)){
						$newpostarray['labels'][$label] = apply_filters("wpr_custtypes_context_cpt", _x($arr[$label], $context[$label], $this->textdomain), $label, $arr[$label]);
						continue;
					}
					$newpostarray['labels'][$label] = __($arr[$label], $this->textdomain);
				}else{
					if(in_array($label, $context)){
						$newpostarray['labels'][$label] = apply_filters("wpr_custtypes_context_cpt", _x($defaultnames[$label], $context[$label], $this->textdomain), $label, $defaultnames[$label]);
						continue;
					}
					$newpostarray['labels'][$label] = __($defaultnames[$label], $this->textdomain);
				}
			}
			return apply_filters("wpr_custtypes_newpostarray", $newpostarray, $arr);
		}
		elseif($type="tax"){
			$taxarray = array();
			$main = array("label", "public", "show_ui", "show_in_nav_menus", "show_tagcloud", "meta_box_cb", "show_admin_column", "hierarchical", "update_count_callback", "query_var", "rewrite",  "capabilities", "sort", "_builtin");
			
			$labels = array("name", "singular_name", "menu_name", "all_items", "edit_item", "view_item", "update_item", "add_new_item", "new_item_name", "parent_item", "parent_item_colon", "search_items", "popular_items", "separate_items_with_commas", "add_or_remove_items", "choose_from_most_used", "not_found");
			
			$defaultnames = array("name" => $p, "singular_name" => $s, "menu_name" => $s, "all_items" =>"All " . $p, "edit_item" => "Edit " . $s, "view_item" => "View " . $s, "update_item" => "Update " . $s, "add_new_item" => "Add New " . $s, "new_item_name" => "New " . $s . " Name", "parent_item" => "Parent " . $s, "parent_item_colon" => "Parent: " . $s , "search_items" => "Search " . $p, "popular_items" => "Popular " . $p, "separate_items_with_commas" => "Separate " . $p . " with commas", "add_or_remove_items" => "Add or remove " . $p, "choose_from_most_used" => "Choose from the most used " . $p, "not_found" => $s . " not found.");
			
			$defaultvals = array("label"=> $p, "public" => true, "show_admin_column" => true, "sort"=>true);
			
			foreach($main as $key){
				if(isset($arr[$key])){
					if($key == "label"){$taxarray[$key] = __($arr[$key], $this->textdomain);continue;}
					$taxarray[$key] = $arr[$key];
				}elseif(isset($defaultvals[$key])){
					$taxarray[$key] = $defaultvals[$key];
				}
			}
			foreach($labels as $label){
				if(!empty($arr[$label])){
					if($label == "name" || $label == "singular_name"){
						$sp = ($label == "name")?$p:$s;
						$taxarray['labels'][$label] = apply_filters("wpr_custtypes_context_tax", _x($arr[$label], $sp, $this->textdomain), $label, $arr[$label]);
						continue;
					}
					$taxarray['labels'][$label] = __($arr[$label], $this->textdomain);
				}else{
					if($label == "name" || $label == "singular_name"){
						$sp = ($label == "name")?$p:$s;
						$taxarray['labels'][$label] = apply_filters("wpr_custtypes_context_tax", _x($defaultnames[$label], $sp, $this->textdomain), $label, $defaultnames[$label]);
						continue;
					}
					$taxarray['labels'][$label] = __($defaultnames[$label], $this->textdomain);
				}
			}
			return apply_filters("wpr_custtypes_taxarray", $taxarray, $arr);
		}
	}
/*=====
Hooked from constructor. If filter was set to true, user wants this to be listed as a column in the post type edit page. 
Here we add the column by id and define a name.
HOOK: apply_filters('wpr_custtypes_set_columns', $reray, $columns, $retarray); You can edit the return. 
$reray=final array, $columns=passes params, $retarray=adjusted params
=====*/
	public function edit_columns_custm($columns){
		if(!$columns['id']){
			$retarray = array('id' => __('ID', $this->textdomain));
		}
		foreach($this->taxnm as $cat){
			if($cat['column_id']){
				$retarray[$cat['column_id']] = $cat['column_name'];
			}
		}
		$reray =  array_merge($columns, $retarray);
		return apply_filters('wpr_custtypes_set_columns', $reray, $columns, $retarray);				
	}
/*=====
Hooked from constructor. If filter was set to true, user wants this to be listed as a column in the post type edit page.
Here we set the column output.
HOOK: apply_filters('wpr_custtypes_column_content', $toret, $column, $post_id, $terms); You can edit return.
$toret=returned val to be echoed(send string, do not echo); $column=passed column, $post_id... duh, $terms=list of terms for post type.
=====*/
	public function inside_custm_columns($column, $post_id){	
		foreach($this->taxnm as $cat){
			if($cat['column_id'] == $column){
				$terms = wp_get_post_terms($post_id, $cat['id']);
					$x = 0;
					$toret = '';
					foreach($terms as $term){
						if($x >0){$toret .= ", ";}
						$toret .= '<a href="' . get_bloginfo("wpurl") . '/wp-admin/edit.php?post_type=' . get_post_type($term->id) . '&' .  $cat['id'] . '=' . $term->slug . '" >' . $term->name . '</a>';
						$x++;
					}
					echo apply_filters('wpr_custtypes_column_content', $toret, $column, $post_id, $terms);
			}
		}
		if($column == 'id'){	
				echo $post_id;
		}			
	}
}
//http://codex.wordpress.org/Function_Reference/register_post_type
//http://codex.wordpress.org/Function_Reference/register_taxonomy
}
/*
$acat['not_working'] == true
apply_filters("wpr_custtypes_textdomain", $textdomain);
$newpostarray['labels'][$label] = apply_filters("wpr_custtypes_context_cpt", _x($arr[$label], $context[$label], $this->textdomain), $label, $arr[$label]);
$newpostarray['labels'][$label] = apply_filters("wpr_custtypes_context_cpt", _x($defaultnames[$label], $context[$label], $this->textdomain), $label, $defaultnames[$label]);
return apply_filters("wpr_custtypes_newpostarray", $newpostarray, $arr);
$taxarray['labels'][$label] = apply_filters("wpr_custtypes_context_tax", _x($arr[$label], $sp, $this->textdomain), $label, $arr[$label]);
$taxarray['labels'][$label] = apply_filters("wpr_custtypes_context_tax", _x($defaultnames[$label], $sp, $this->textdomain), $label, $arr[$label]);
return apply_filters("wpr_custtypes_taxarray", $taxarray, $arr);
return apply_filters('wpr_custtypes_set_columns', $reray, $columns, $retarray);
echo apply_filters('wpr_custtypes_column_content', $toret, $column, $post_id, $terms);

	public function add_post_caps() {
		global $wp_rewrite;
		$s = sanitize_title_for_query($this->typargs['general_single']);
		$p = sanitize_title_for_query($this->typargs['general_plural']);
		$a = get_role("administrator");
		$b = get_role("editor");
		$c = get_role("author");
		$d = get_role("contributor");
		$e = get_role("subscriber");
		 
		$a->add_cap('edit_others_' . $p);$b->add_cap('edit_others_' . $p);
		$a->add_cap('edit_private_' . $p);$b->add_cap('edit_private_' . $p);
		$a->add_cap('read_private_' . $p);$b->add_cap('read_private_' . $p);
		$a->add_cap('delete_private_' . $p);$b->add_cap('delete_private_' . $p);
		$a->add_cap('delete_others_' . $p);$b->add_cap('delete_others_' . $p);
		
		$a->add_cap('publish_' . $p);$b->add_cap('publish_' . $p);$c->add_cap('publish_' . $p);
		$a->add_cap('edit_published_' . $p);$b->add_cap('edit_published_' . $p);$c->add_cap('edit_published_' . $p);
		$a->add_cap('delete_published_' . $p);$b->add_cap('delete_published_' . $p);$c->add_cap('delete_published_' . $p);
		
		$a->add_cap('delete_' . $s);$b->add_cap('delete_' . $s);$c->add_cap('delete_' . $s);$d->add_cap('delete_' . $s);		
		$a->add_cap('delete_' . $p);$b->add_cap('delete_' . $p);$c->add_cap('delete_' . $p);$d->add_cap('delete_' . $p);
		$a->add_cap('edit_' . $s);$b->add_cap('edit_' . $s);$c->add_cap('edit_' . $s);$d->add_cap('edit_' . $s);
		$a->add_cap('edit_' . $p);$b->add_cap('edit_' . $p);$c->add_cap('edit_' . $p);$d->add_cap('edit_' . $p);
		
		$a->add_cap('read_' . $s);$b->add_cap('read_' . $s);$c->add_cap('read_' . $s);$d->add_cap('read_' . $s);$ea->add_cap('read_' . $s);
		
		$wp_rewrite->flush_rules(false);
	}
*/
?>