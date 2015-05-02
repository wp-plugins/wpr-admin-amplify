<?php
defined('ABSPATH') or die("No script kiddies please!");
/*
Plugin Name: WPR Admin Amplify
Plugin URI: http://worldpressrevolution.com
Description: User interface for creating custom posty types, custom fields, custom admin pages, forms ect...
Version: 0.0.1
Author: Aryan Duntley
Author URI: http://worldpressrevolution.com
License: GPLv2 or later
*/

$wpr_amplify_file = dirname(dirname(__FILE__)) . '/functions.php';
$wpr_amplify_basename = plugin_basename($wpr_amplify_file);

function wpramplify_admin_scripts($hook) {
	//if('edit.php' != $hook){return;}
		wp_register_script( 'wprcusadminscript', plugins_url('/js/cusadmin.js', __FILE__), array('jquery'));
	wp_register_script( 'timetodatepicker', plugins_url('/js/jq.addtimetodate.js', __FILE__), array('jquery','jquery-ui-core', 'jquery-ui-datepicker','jquery-ui-slider'), '');
	if(!wp_script_is('jquery-ui-core', 'enqueued')){wp_enqueue_script('jquery-ui-core');}
	if(!wp_script_is('jquery-ui-datepicker', 'enqueued')){wp_enqueue_script('jquery-ui-datepicker');}
	if(!wp_script_is('jquery-ui-slider', 'enqueued')){wp_enqueue_script('jquery-ui-slider');}
	if(!wp_script_is('jquery-ui-draggable', 'enqueued')){wp_enqueue_script('jquery-ui-draggable');}
	if(!wp_script_is('jquery-ui-droppable', 'enqueued')){wp_enqueue_script('jquery-ui-droppable');}
	if(!wp_script_is('jquery-ui-sortable', 'enqueued')){wp_enqueue_script('jquery-ui-sortable');}
	if(!wp_script_is('jquery-ui-resizable', 'enqueued')){wp_enqueue_script('jquery-ui-resizable');}
	if(!wp_script_is('jquery-ui-selectable', 'enqueued')){wp_enqueue_script('jquery-ui-selectable');}
	
	if(!wp_script_is('underscore', 'enqueued')){wp_enqueue_script('underscore');}	
	if(!wp_script_is('backbone', 'enqueued')){wp_enqueue_script('backbone');}
	
	
	wp_enqueue_script('timetodatepicker');
	wp_enqueue_script('wprcusadminscript');
	
	$file_for_jav = admin_url('admin-ajax.php');
	$tran_arr = array( 'jaxfile' => $file_for_jav );
	wp_localize_script( 'wprcusadminscript', 'fromphp', $tran_arr );
	
	wp_register_style('wpr_jqueryui_base', plugins_url('/css/jqueryui/jquery-ui.min.css',  __FILE__));
	wp_register_style('wpr_jqueryui_struct', plugins_url('/css/jqueryui/jquery-ui.structure.min.css', __FILE__));
	wp_register_style('wpr_jqueryui_theme', plugins_url('/css/jqueryui/jquery-ui.theme.min.css', __FILE__));
	wp_enqueue_style('wpr_jqueryui_base');
	wp_enqueue_style('wpr_jqueryui_struct');
	wp_enqueue_style('wpr_jqueryui_theme');
	
	wp_register_style('wpr_metabox_style', plugins_url('/css/custadstyle.css',  __FILE__), array('wpr_jqueryui_base', 'wpr_jqueryui_struct', 'wpr_jqueryui_theme'));
	wp_enqueue_style('wpr_metabox_style');
	
	//if($hook == "post-new.php"){
	//	if($_GET['post_type'] == 'wprmakepages' || $_GET['post_type'] == 'wprmakecpts'){
	//		wp_enqueue_script('underscore');
	//		wp_enqueue_script('backbone');
	//	}
	//}
	if($hook == "post-new.php" || ($hook == "post.php")){
		wp_enqueue_script('jquery-ui-droppable');
	}
}
add_action('admin_enqueue_scripts', 'wpramplify_admin_scripts');

require_once('custtypes.php');
require_once('postmeta.php');
require_once('pagemaker.php');


/* List of privileges */
$caparray = array("manage_network", "manage_sites", "manage_network_users", "manage_network_plugins", "manage_network_themes", "manage_network_options", "activate_plugins", "delete_others_pages", "delete_others_posts", "delete_pages", "delete_plugins", "delete_posts", "delete_private_pages", "delete_private_posts", "delete_published_pages", "delete_published_posts", "edit_dashboard", "edit_files", "edit_others_pages", "edit_others_posts", "edit_pages", "edit_posts", "edit_private_pages", "edit_private_posts", "edit_published_pages", "edit_published_posts", "edit_theme_options", "export", "import", "list_users", "manage_categories", "manage_links", "manage_options", "moderate_comments", "promote_users", "publish_pages", "publish_posts", "read_private_pages", "read_private_posts", "read", "remove_users", "switch_themes", "upload_files", "update_core", "update_plugins", "update_themes", "install_plugins", "install_themes", "delete_themes", "edit_plugins", "edit_themes", "edit_users", "create_users", "delete_users", "unfiltered_html", "delete_others_pages", "delete_others_posts", "delete_pages", "delete_posts", "delete_private_pages", "delete_private_posts", "delete_published_pages", "delete_published_posts", "edit_others_pages", "edit_others_posts", "edit_pages", "edit_posts", "edit_private_pages", "edit_private_posts", "edit_published_pages", "edit_published_posts", "manage_categories", "manage_links", "moderate_comments", "publish_pages", "publish_posts", "read", "read_private_pages", "read_private_posts", "unfiltered_html", "upload_files", "delete_posts", "delete_published_posts", "edit_posts", "edit_published_posts", "publish_posts", "read", "upload_files", "delete_posts", "edit_posts", "read");

/*TODO: put all of these into functions with hoods so they can be easily edited */
/* List of available page field types in the free version */
$pagefieldtypes = array("text", "check", "radio", "textarea", "select", "separator", "html", "custom");

/* List of available custom post field types in the free version */
$metafieldtypes = array("radio"=>"Radio Button", "select"=>"Select Box", "multiselect"=>"Multi Select", "multicheck"=>"Multi Checkboxes", "sidebars"=>"Sidebar List", "text"=>"Text Field", "noneditable"=>"Non-editable text", "check"=>"Check Box", "multilist"=>"Multilist", "advancedlist"=>"Advanced List", "textarea"=>"Text Area", "textwys"=>"WYSIWYG Textarea", "datepicker"=>"Date Picker", "timepicker"=>"Time Picker", "separator"=>"Section Separator", "file"=>"File Chooser", "calltoaction"=>"Javascript Call To Action");

/* List of available custom post field types in the free version */
$wprformtypes = array();

/* These are our plugin options */
$wpr_gensettings = get_option("gen_amplify_setngs");
$wpr_membersettings = get_option("membership_amplify_setngs");

/*adminpages_amplify_setngs usercat_amplify_setngs membership_amplify_setngs postmeta_amplify_setngs cpt_amplify_setngs*/
/* 
Used in both membershipmethods.php and usercategory.php.
This is a recursive function that sorts the terms hierarchically along with a child level indicator
@param $terms the result of get_terms()
@param $parent is not necessary when starting the engine, it begins with 0 and will update while iterating 
*/
//$b = build_tree($a);
function getme_tax_ordered($terms, $parent=0){
	$build = array();
    foreach ($terms as $term) {
		if($term->parent == 0){
			//$term = (array)$term;
			//$term->wpr_level = 0;
			//$term = (object)$term;
			$term->wpr_level = 0;
		}
		if($parent == 0 && $term->parent == 0){//places top levels		
			$build[] = $term;
			$b = getme_tax_ordered($terms, $term->term_id);//iterates first child
			if(!empty($b)){
				if(is_array($b)){foreach($b as $t){
					//$t = (array)$t;
					//$t['wpr_level'] += 1;
					//$t = (object)$t;
					$t->wpr_level+=1;
					$build[] = $t;
				}}
				else{$build[] = $b;}
			}
		}		
        elseif($parent == $term->parent){//places subsequent child
			$build[] = $term;
			$b = getme_tax_ordered($terms, $term->term_id);//iterates subsequent children
			if(!empty($b)){
				if(is_array($b)){foreach($b as $t){
					//$t = (array)$t;
					//$t['wpr_level'] += 1;
					//$t = (object)$t;
					$t->wpr_level+=1;
					$build[] = $t;
				}}
				else{$build[] = $b;}
			}
		}
    }
    return $build;
}
/* Check if the user wants user categories */
if($wpr_gensettings["wpr_want_usercats"] && $wpr_gensettings["wpr_want_usercats"] == "1"){
	require_once('usercategory.php');
}
/* Check if the user wants restricted content/membership*/
if($wpr_gensettings["wpr_want_membership"] && $wpr_gensettings["wpr_want_membership"] == "1"){
	require_once('membershipmethods.php');
	require_once('usermembershipstuff.php');
	/*Located in membershipmethods.php, we create the activation actions.*/
	register_activation_hook(__FILE__, 'CreateFiles');
}
//$remvo = array("section1test", "section2test", "inpageohone"); options saved as serialized data

/*
The data below is in preparation for the GenerateSettingsPages class where I create the Amplify Admin options page.
*/
$settings = array(
	'section1' => array(
		'name' => 'General Amplify Settings',
		'tab_name' => 'General',
		'description' => 'Some description about this section',
		'optionname' => 'gen_amplify_setngs',
		'fields' => array(
			array(
				'id' => 'wpr_want_usercats',
				'type' => 'check',//text,check,radio,textarea,select, separator, html
				'label' => 'Allow user category taxonomy for user grouping.',
				'description' => "You can group users based on taxonomy and use the shortcode {NEED SHORTCODE} to access users by this grouping.  Supply the taxonomy slug.",
				'default_value' => '1',
				'grouped' => 'one'
			),
			array(
				'id' => 'wpr_want_membership',
				'type' => 'check',//text,check,radio,textarea,select, separator, html
				'label' => 'Allow restricted content / membership on the site',
				'description' => "This will provide a taxonomy called membership, that when set will restrict content for users who do not have the same or child privilege of this taxonomy type.  Assign a user's membership and then assign a post-type, menu, or media attachment membership to allow/disallow that content to that user or user group. For media, you must checke the private checkbox for this to take effect.",
				'default_value' => '1',
				'grouped' => 'one'
			),
			array(
				'id' => 'wpr_want_emaillogin',
				'type' => 'check',//text,check,radio,textarea,select, separator, html
				'label' => 'Allow email login',
				'description' => "This will allow users to log in with either their username OR their registered email address.",
				'default_value' => '1',
				'grouped' => 'one'
			),
			array(
				'id' => 'wpr_allow_widgphp',
				'type' => 'check',//text,check,radio,textarea,select, separator, html
				'label' => 'Allow php in widgets',
				'description' => "This will allow you to add php with typical php tags (<?php {CODE} ?>} in your text widgets.",
				'default_value' => '1',
				'grouped' => 'one'
			),
			array(
				'id' => 'wpr_allow_widgshorts',
				'type' => 'check',//text,check,radio,textarea,select, separator, html
				'label' => 'Allow shortcodes in widgets',
				'description' => "This will allow you to add shortcodes in your text widgets.",
				'default_value' => '1',
				'grouped' => 'one'
			),
			array(
				'id' => 'sepgen1',
				'type' => 'separator',
				'label' => 'Info',
				'description' => '',
				'default_value' => '',
			),
			array(
				'id' => 'id_genhmel1',
				'type' => 'html',
				'label' => '',
				'description' => '',
				'default_value' => '<p>This is a beta release.  I would like feedback on this plugin including likes, dislikes, feature requests (many I accept will be placed in the paid version), bugs, etc. .  If you like this plugin and it has helped you speed up dev feel free to <span style="display:inline-block;cursor:pointer;" id="mydonatebtn_wpr"><img style="display:inline-block;" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" alt="PayPal - The safer, easier way to pay online!" /><img style="display:inline-block;" alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1" /></span>. There will be video tutorials on features in the near future at <a href="http://worldpressrevolution.com" target="_blank">worldpress revolution</a>.</p><br/><p>I have created this plugin because the classes I use for it are one\'s I\'ve been using for client project for some time.  You must have a system to do these things quickly and efficiently because coding out the details everytime is time consuming and decreases productivity.  So finally, I put it together in a UI and am offering it to everyone else so that they can hopefully improve turn over as well.  I know there are a couple of other really good plugins that do these same things, but I hope mine will eventually be a competitor and amongst the top contenders.</p><br/><p>Please be patient with updates and releases.  I\'m just one guy who has to work and pay the bills like everyone else.  So, until I have a paid version (considering it is useful to enough people) and also, until and if that version begins to generate necessary funds, I can only work on this plugin as I have time.</p><h4>Feature Expectations</h4><ul style="list-style-type:disc"><li>Membership Limited Content: Currently, with the membership functionality, a post/page will simply not appear.  I intend to improve upon the usability by adding controlled content as well. </li><li>Forms Maker: I plan on creating functionality that will compete with gravity forms.  Gravity forms is a great, feature rich plugin, but it is overcomplicated coding and what I believe to be unnecessarily added tables to the WP database. My forms manager will be custom post type based and I believe I may use comments with a custom comment type to store the form submissions, I still have to weigh the benefits between that and a CPT.</li><li>Easy front-end login/logout widget and shortcode with a wp-admin login redirect.  There are many plugins that do this so I may skip it, not really sure, but I know that the wp-login page is hit hard by bots trying to gain access and it\'s best to keep the black hats guessing if you can.</li><li>Admin Menu Control: I will have an admin menu control interface where the site owner can edit what menu items are available to who</li><li>Admin Themeing: I hope to have a solid interface for serious administrative themeing</li><li>Forms Maker Extensions: The forms maker will allow me to expand it\'s interface to numerous other things.  I have ideas of using it for a full e-commerce platform along with the membership.  Woocommerce is a great plugin in my opinion with a great api, but I can make one myself and hey, why not.  We\'ll see.  I visualize the forms maker allowing easy user profile extending and front end profile editing, custom login forms, newsletter sign ups, or pretty much anything that requires user input of some sort.  I have to think of all of these things before I start building it so that I make it as diverse as possible to begin with so that adding features will be simple and pre-planned instead of hacky and a pain in the butt.</li><li>Anything else I haven\'t thought of, I hope you will provide feedback so that I can think about whether it\'s something I would like to include.</li> </ul> <script> jQuery(document).ready(function(){ jQuery("#mydonatebtn_wpr").click(function(e){jQuery("#hidthe2form").submit();}); }); </script>',
			),
			
		)
	),
	'section2' => array(
		'name' => 'Admin Page Creation Settings',
		'tab_name' => 'WPR Pages',
		'description' => 'Here you can find information and setting in regards to creating admin pages.',
		'optionname' => 'adminpages_amplify_setngs',
		'fields' => array(
			array(
				'id' => 'seppage',
				'type' => 'separator',
				'label' => 'Info',
				'description' => '',
				'default_value' => '',
			),
			array(
				'id' => 'id_not_needed1',
				'type' => 'html',
				'label' => '',
				'description' => '',
				'default_value' => '<p>All of the options for creating a page are defined <a href="http://codex.wordpress.org/Function_Reference/add_menu_page#Parameters" target="_blank">here.</a></p><p><h4>Process</h4><ul style="list-style-type:circle;"><li>The only required settings to create a page are a title for the post type and the fields marked required in the general options tab.</li><li>Once you have created a page, you will need to create tabs/sections (they are one and the same in this regard).</li><li>If you want no tabs, then create only one and no actual tabs will appear.</li><li>To have a tabbed admin page, create several sections/tabs.</li><li>Once you have created a tab/section, to access data you will use the option name you used when creating that tab/section by way of WP\'s <a href="http://codex.wordpress.org/Function_Reference/get_option" target="_blank">get option function</a>, typically only the first parameter is needed.</li><li>The option name will be passed to get option.  The return value will be an array of all the elements you create within that tab/section.</li><li>Now that you have a section, you will need to create fields.  You will choose a field type then assign a label, an identifier, description and so on. </li><li>To access any individual field, you will use the identifier you supplied as the array key from the returned value of get_option...  $x = get_option("my_page_option"); echo $x["my_text_field"];</li><li>Each tab/section will use a different option name, One you defined when creating the tab/section.</li><li>Once created, the option names are visible in the title bar of the tab/option list</li><li>Some fields provide you with the ability to create sub-fields (like the radio field).  This allows you to define the different options relative to that field.</li><li>Multi select and multi check are not currently supported.  I have to keep some things for the advanced version so I can pay the bills...</li></ul></p>',
				'grouped' => 'one'
			),
			array(
				'id' => 'seppage2',
				'type' => 'separator',
				'label' => 'Available Hooks',
				'description' => '',
				'default_value' => '',
			),
			array(
				'id' => 'hookspages',
				'type' => 'html',
				'label' => '',
				'description' => '',
				'default_value' => '<div style="background: #EFEFEF"><p><ul style="list-style-type:disc;"><li><pre style="margin: 0px;padding: 0px;display: inline-block;background:#fff;">do_action("wpr_pagemaker_scriptshere", $this->pagemaker_data);</pre><br/>The method this is located in is not used in the plugin, but provides a way for someone to add styles/scripts when their particular admin page is loaded.  This method is called from the add_action("load-{PAGE}", {CALLBACK}) hook.<ol><li>@p1 array: the class data.</li></ol></li><li><pre style="margin: 0px;padding: 0px;display: inline-block;background:#fff;">apply_filters("wpr_pagemaker_{FIELD-TYPE}_field", $html, $args);</pre><br/>Relative to a specific field in a page section/tab, use this hook with the field type you chose to adjust the output of the field.  Be careful here, you have to define the name attribute of any input types, select fields, etc... correctly as well as the stored value.  $args["tagid"] is the input name attribute, $options = get_option($args["name"]); $options[$args["id"]] will give you the stored value.<ol><li>@p1 string: the html output</li><li>@p2 array: the field arguments containing the field name attribute, id for get_option, default val, etc...</li></ol></li><li><pre style="margin: 0px;padding: 0px;display: inline-block;background:#fff;">apply_filters("wpr_pagemaker_validate_fields", $output, $input);</pre><br/>Used inside the callback for register_setting() to sanitize the post data before doing anything with it.<ol><li>@p1 array: key value pair output</li><li>@p2 arrray: key value pair input</li></ol></li><li><pre style="margin: 0px;padding: 0px;display: inline-block;background:#fff;">apply_filters("wpr_pagemaker_sectiondisplay", $html, $args, $this->pagemaker_data);</pre><br/>This outputs the page description, hook into this to edit that and put whatever you want there.<ol><li>@p1 string: The html output</li><li>@p2 array: The args passed to this method come from add_settings_section().  There isn\"t much there, but you can use the title to compare against the ["name"] key/value in the array from @p3.  From there you can get all page data that was set relative to that specific tab/section (like ["description"]).</li><li>@p3 array: the class data.</li></ol></li><li><pre style="margin: 0px;padding: 0px;display: inline-block;background:#fff;">apply_filters("wpr_pagemaker_section_open", $html, $this->pagemaker_data);</pre><br/>The page title and heading at the top of a particular admin page, above the tabs.<ol><li>@p1 string: the html output</li><li>@p2 array: the class data (["pagetitle"] and ["heading"] are used in the plugin)</li></ol></li><li><pre style="margin: 0px;padding: 0px;display: inline-block;background:#fff;">do_action("wpr_pagemaker_pagecallback_end", $this->pagemaker_data, $activetab); </pre><br/>This hook is called after the form and the containing div with class "wrap" is rendered. You can add custom html here, or whatever else you may think is appropriate. In your callback you can check for which section is being displayed (and at the same time test against the correct page) by using something like this  <span style="background:#fff;">if(@p1["settings"][@p2]["optionname"] == {MY_WET_OPTIONNAME}){...}</span><ol><li>@p1 array: The class data.</li><li>@p2 string: name of the active tab</ol></li></ul></p></div>',
			),
		)
	),
	'section3' => array(
		'name' => 'Custom Post Type Creation Settings',
		'tab_name' => 'WPR CPTs',
		'description' => 'Here you can find information and setting in regards to creating custom post types.',
		'optionname' => 'cpt_amplify_setngs',
		'fields' => array(
			array(
				'id' => 'sepcpt',
				'type' => 'separator',
				'label' => 'Info',
				'description' => '',
				'default_value' => '',
			),
			array(
				'id' => 'id_not_needed2',
				'type' => 'html',
				'label' => '',
				'description' => '',
				'default_value' => '<p>All of the options for creating a Custom Post Types are defined <a href="http://codex.wordpress.org/Function_Reference/register_post_type" target="_blank">here.</a></p><p><h4>Process</h4><ul style="list-style-type:circle;"><li>The only required settings to create a Custom Post Type are a title for the post type and the fields marked required in the general options tab.</li><li>You can either choose an existing post type (in which case you do not need to set the rest of the fields) or create a new one.</li><li>The reason for choosing an existing post type would be to create custom taxonomies for that post type.  The options on the CPT Maker edit screen allow you to create custom taxonomies for post types.  So, if you do not want to create a CPT, but want a custom taxonomy for a post type, you would choose the existing post type and add taxonomies to it.</li><li>Once you have created a CPT or chosen an existing post type, you can then choose whether to optionally set some of the advanced settings or customize the labelling for it.  The label options are desctibed <a href="http://codex.wordpress.org/Function_Reference/register_post_type" target="_blank">here</a> under "ARGUMENTS" >> "LABELS".</li><li>Once you have created the CPT or chosen an existing one and set all desired parameters, you can now create custom taxonomies if you choose.</li><li>Once you have created a taxonmy, you must define both a singluar and plurar name.  These are the only required options.  Defaults will be set if nothing else is manipulated.</li><li>If you wish to edit the advanced settings or labeling, documentation can be found <a href="http://codex.wordpress.org/Function_Reference/register_taxonomy" target="_blank">here</a></li></ul></p><div style="margin-top:20px;"><p><b>NOTE:</b><br/>Some advanced settings require the use of a hook to set certain parameters.  In order to prevent the UI from becoming overly complicated, I decided not to include the ability to adjust those parameters there.  In order to edit them, you may hook into hook defined at the bottom of the advanced options tab, or the hook defined at the bottom of the edit page, under the taxonomy list (for, of course, the taxonomy advanced options).</p></div>',
				'grouped' => 'one'
			),
			array(
				'id' => 'sepcpt2',
				'type' => 'separator',
				'label' => 'Available Hooks',
				'description' => '',
				'default_value' => '',
			),
			array(
				'id' => 'hookscpt',
				'type' => 'html',
				'label' => '',
				'description' => '',
				'default_value' => '<div style="background: #EFEFEF"><p><ul style="list-style-type:disc;"><li><pre style="margin: 0px;padding: 0px;display: inline-block;background:#fff;">apply_filters("wpr_custtypes_textdomain", $textdomain);</pre><br/>Allows you to add your own text domain.<ol><li>@p1 string: textdomain name</li></ol></li><li><pre style="margin: 0px;padding: 0px;display: inline-block;background:#fff;">apply_filters("wpr_custtypes_context_cpt", _x($arr[$label], $context[$label], $this->textdomain), $label, $arr[$label]);</pre><br/>Allows you to edit any particular label directly and edit the <a href="http://codex.wordpress.org/I18n_for_WordPress_Developers#Disambiguation_by_context" target="_blank">context</a>.<ol><li>@p1 string: contextually derived and translated label string</li><li>@p2 string: label key name</li><li>@p3 string: label value</li></ol></li><li><pre style="margin: 0px;padding: 0px;display: inline-block;background:#fff;">apply_filters("wpr_custtypes_newpostarray", $newpostarray, $arr);</pre><br/>This is the finalized array passed to register_post_type()<ol><li>@p1 array: finalized array</li><li>@p2 array: values unprepped for finalized array</li></ol></li><li><pre style="margin: 0px;padding: 0px;display: inline-block;background:#fff;">apply_filters("wpr_custtypes_context_tax", _x($arr[$label], $sp, $this->textdomain), $label, $arr[$label]);</pre><br/>Allows you to edit any particular label directly and edit the <a href="http://codex.wordpress.org/I18n_for_WordPress_Developers#Disambiguation_by_context" target="_blank">context</a>.<ol><li>@p1 string: contextually derived and translated label string</li><li>@p2 string: label key name</li><li>@p3 string: label value</li></ol></li><li><pre style="margin: 0px;padding: 0px;display: inline-block;background:#fff;">apply_filters("wpr_custtypes_taxarray", $taxarray, $arr);</pre><br/>This is the finalized array passed to register_taxonomy()<ol><li>@p1 array: finalized array</li><li>@p2 array: values unprepped for finalized array</li></ol></li><li><pre style="margin: 0px;padding: 0px;display: inline-block;background:#fff;">apply_filters("wpr_custtypes_set_columns", $reray, $columns, $retarray);</pre><br/>From the manage_edit-{POSTTYPE}_columns hook, this is where custom columns are added to the post-type list. This plugin uses these to add any custom taxonomies to the listed post type columns for easier access for filtering posts by them.<ol><li>@p1 array: merged array of original and new id/name pairs</li><li>@p2 array: original array</li><li>@p3 array: new values to append</li></ol></li><li><pre style="margin: 0px;padding: 0px;display: inline-block;background:#fff;">apply_filters("wpr_custtypes_column_content", $toret, $column, $post_id, $terms);</pre><br/>From manage_{POSTTYPE}_posts_custom_column, this is where the output for the above custom columns is created.<ol><li>@p1 string: The output for this column.</li><li>@p2 string: The column id created in the hook above.</li><li>@p3 int: the post id</li><li>@p4 array: all the taxonomy terms related to a particular post and assigned to the taxonomy in question.</li></ol></li></ul></p></div>',
			),
		)
	),
	'section4' => array(
		'name' => 'Post Meta Creation Settings',
		'tab_name' => 'WPR Post Meta',
		'description' => 'Here you can find information and setting in regards to creating post meta fields.',
		'optionname' => 'postmeta_amplify_setngs',
		'fields' => array(
			array(
				'id' => 'sepmet',
				'type' => 'separator',
				'label' => 'Info',
				'description' => '',
				'default_value' => '',
			),
			array(
				'id' => 'id_not_needed3',
				'type' => 'html',
				'label' => '',
				'description' => '',
				'default_value' => '<p>All of the options for creating a custom meta box <a http://codex.wordpress.org/Function_Reference/add_meta_box" target="_blank">here.</a></p><p><h4>Process</h4><ul style="list-style-type:circle;"><li>The only required settings to create a Meta Box are a title for the post type and the fields marked required in the general options tab.(Just choose a post type)</li><li>Once the meta box is created, you need to populate with custom meta fields.</li><li>Custom fields are the same thing as post meta.  They are one and the same.  However, with custom meta fields, you can provide a UI for various ways of adding values to your keys instead of just a text box for the key and a text box for the value.</li><li>There are a number of field types currently available to choose from.  Once you set the id and select a field, a new area will appear where the settings for that specific field type can be set.</li><li>Different field types may have different settings options.</li><li>The Call To Action type field is used for ajax callback.  The button will be created and will do nothing on it\'s own. You can assign data attributes to the button ie: data-mydat="thispassed" data-otherdat="foo".  You can define the button text that will be displayed inside of it. You can define a callback function name for the ajax callback that you will have to create.  You can also define a comma separated list of id\'s and the val() of the fields with those id\'s will be sent to the ajax call (id-ing your fields appropriately and carefully will prevent conflict here, use a custom namespace naming convention). Finally, you can add your own custom javascript using the filter "wpr_postmeta_action_field" described below.</li></ul></p>',
				'grouped' => 'one'
			),
			array(
				'id' => 'sepmet2',
				'type' => 'separator',
				'label' => 'Available Hooks',
				'description' => '',
				'default_value' => '',
			),
			array(
				'id' => 'hooksmeta',
				'type' => 'html',
				'label' => '',
				'description' => '',
				'default_value' => '<div style="background: #EFEFEF"><p><ul style="list-style-type:disc;"><li><pre style="margin: 0px;padding: 0px;display: inline-block;background:#fff;">apply_filters("wpr_postmeta_argsedit", $margs);</pre><br/>You can send a custom array of data available throughout the class<ol><li>$margs are any custom arguments passed (which should be none, the UI does not use this).</li></ol></li><li><pre style="margin: 0px;padding: 0px;display: inline-block;background:#fff;">apply_filters("wpr_postmeta_myownthing", true, $this->pagesadded_met, $post, $xargs);</pre><br/>Executed at the beginning of the callback for add_meta_box().<ol><li>@p1 bool: representing whether to continue executing code from this point on.  If false, nothing will happen after this point and code will rely on whatever you do here.</li><li>@p2 array: is array of all data sent to the class including all field data for the meta box.</li><li>@p3 global post object.</li><li>@p4 array: any custom arguments that were sent to the class or hooked.</li></ol></li><li><pre style="margin: 0px;padding: 0px;display: inline-block;background:#fff;">do_action("wpr_postmeta_myfield", $post_ob_name, $value, $metabox, $post, $this->pagesadded_met, $xargs);</pre><br/>A switch statement is used to traverse through the various field types.  The default field type is blank and this hook is placed there.  You can create your own field types using this hook along with the "wpr_postmeta_saving_call" if needed.<ol><li>@p1 string: is the post name that will be sent via http post when the form is saved. It is consists of the general post name and the key ie "postname[keyid]" wher keyid is the id of the field you created.</li><li>@p2 array: This is not named very well, but oh well.  This is actually the string indexed array of all the field parameters (id, title, name, desc, std, etc...) .</li><li>@p3 string|array: This is the value of the meta field, or the $value["std"] (default value) if the meta field has not yet been created.</li><li>@p4 The global post object.</li><li>@p5 array: All the class variables</li><li>@p6 array: any custom array data set by you.</li></ol></li><li><pre style="margin: 0px;padding: 0px;display: inline-block;background:#fff;">do_action("wpr_postmeta_afterfield", $post_ob_name, $value, $metabox, $post, $this->pagesadded_met, $xargs);</pre><br/>This is called after the field has been created.  Do whatever you want here. <ol><li>Exact same params as "wpr_postmeta_myfield" above.</li></ol></li><li><pre style="margin: 0px;padding: 0px;display: inline-block;background:#fff;">do_action("wpr_postmeta_afterfields", $post_ob_name, $post, $this->pagesadded_met, $xargs);</pre><br/>This is called after all fields have been created.<ol><li>@p1 string: post name (same as above two)</li><li>$p2 global post object</li><li>@p3 array: class params same as above two</li><li>@p4 array: additional args same as above 2.</li></ol></li><li><pre style="margin: 0px;padding: 0px;display: inline-block;background:#fff;">apply_filters("wpr_postmeta_saving_call", true, $_POST[$this->pagesadded_met["postnm"]], $this->pagesadded_met, $post_id);</pre><br/>This is called right after the post has been saved. You can let the typical saving do it\'s thing or you can do your own thing then let the code run, or you can do you own thing and stop the code from running. Return true to continue running the rest of the saving code, return false if you have done your own thing in the hook and do not want the code to continue running.  You should test the params to make sure that you are grabbing the correct stuff.  This hook can prevent any meta fields from getting saved at all so use carefully.<ol><li>@p1 bool: continue with following code or no.</li><li>@p2 array: the entire posted array from the save post form.</li><li>@p3 array: the class params</li><li>@p4 int: the post id.</li></ol></li><li><pre style="margin: 0px;padding: 0px;display: inline-block;background:#fff;">apply_filters("wpr_postmeta_saving_one", true, $post_id, $dpname, $dpval, $_POST[$this->pagesadded_met["postnm"]], $this->pagesadded_met);</pre><br/>This is within a loop that iterates through the post array from the form.  You can control individual post elements here.  Return true to run the rest of the code (update_post_meta()) or false to not.<ol><li>@p1 bool: whether to continue with updating the post meta for this particular meta field.</li><li>@p2 int: the post id.</li><li>@p3 string: the post name.</li><li>@p4 string: the post value.</li><li>@p5 array: the entire meta post array from the form (only meta box fields part of it)</li><li>@p6 array: the class params</li></ol></li></ul></p></div>',
			),
		)
	),
	'section5' => array(
		'name' => 'Membership Settings',
		'tab_name' => 'Membership',
		'description' => 'Here you can find information and setting in regards to creating user membership and restricted content.',
		'optionname' => 'membership_amplify_setngs',
		'fields' => array(
			array(
				'id' => 'wpr_mem_allorsome',
				'type' => 'radio',//text,check,radio,textarea,select, separator, html
				'label' => 'Restrict all content or some content.',
				'description' => 'All content restriction means that the post or page will not be displayed at all.  Some means that they will be displayed but only certain information will be allowed. (NOTE: "Some" currently not functional.  Expected for a later release)',
				'default_value' => 'all',
				'morethanone' => array(//for radio and select
					array(
						'label'=> 'All',
						'value' => 'all'
					),
					array(
						'label'=> 'Some',
						'value' => 'some'
					)
				),
				'grouped' => 'two'
			),
			array(
				'id' => 'wpr_mem_redirurl',
				'type' => 'text',//text,check,radio,textarea,select, separator, html
				'label' => 'No page access redirect url.',
				'description' => 'Where to redirect users who do not have access to a PAGE.  If you would prefer to use a relative path to your site, use %homeurl% (eg. %homeurl%/no-access)',
				'default_value' => '%homeurl%',
				'grouped' => 'two'
			),			
			array(
				'id' => 'sepmem',
				'type' => 'separator',
				'label' => 'Info',
				'description' => '',
				'default_value' => '',
			),
			array(
				'id' => 'id_not_needed4',
				'type' => 'html',
				'label' => '',
				'description' => '',
				'default_value' => '<h4>Process</h4><p>The membership/restricted content option allows you to restrict post types, pages, menus and media from certain user groups.  There is simply one taxonomy called membership.  You create the membership categories (hierarchically enabled) and then assign users to whichever is appropriate for your needs.  Then you can define which of those membership categories posts, menu items, pages and media should also be assigned to.  If a user is assigned membership category "A" and a post is assigned membership category "B", that user will not have access to that post.  Users can only access things categorized at the same level or lower.  So, if the user is assigned to membership category "Child of B", then the user will have access to posts with membership categories of "Child of B" or lower ( eg:"Grandchild of B"), but they will NOT have access to membership category "B".  <br/><br/>You will have an area in the profile edit page to set membership categories as well as the ability to set membership categories in bulk on the user list page or filter by membership category</p><h4>Media Access Control</h4><p>If the media is set to private, the program is expecting that you will be assigning membership to user groups and therefore any user that is not logged in will not be able to access or view the media.<br/><br/><b>Take note</b> that media files that are not protected may be access controlled by assigning membership categories but only to the attachment page.  If someone accesses the file directly, they will be able to view/download that file.  However, if you protect the file by checking the "Toggle Privacy" checkbox, if a user is not logged in or does not have proper membership category privileges, they will be unable to access the file, whether it be in the site or directly.<br/><br/><b>Take note</b> that if you place an image or file into your content and you change the privacy of that media item, the location of that file WILL CHANGE.  You must either, again toggle the privacy, or change the url.  Using the "add Media" button in the text editor will provide the correct url, but that url changes based on the privacy setting, so if you created a reference to the media item while it was not private and change the media item to private, it will no longer be available at the not private path/url.  If you mark it as private and add the reference, then later change it to not private, it will no longer be available.  You must keep this in mind when both creating the reference and toggling the privacy.</p><h4>Invalid Access Redirect</h4><p>By default, the user is sent to the home page if they attempt to access a page they have no privileges for.  To change this to a custom url, use either the text box above labelled "No access redirect url", or use the filter: <pre style="background:#fff;">add_filters("wpr_non_member_redirect", "goSomeWhereElse"); function goSomeWhereElse($tourl){... return $tourl;}</pre></p><h4>Setting User Membership Categories Programmatically</h4><p>If you are using say, an e-commerce solution (like woocommerce), and you want to hook into a successful order and update the user membership, you can use <pre style="background:#fff;">add_action("after_setup_theme", function(){updateUserMems($user = 0, $categs = array(), $passed="id", $append = false);});</pre>  Here you will set the user id; the categories as an array of either slugs or ids; identify whether you have sent an array of slugs or ids; define whether you want to add to the list of membership categories for the user or replace them with the new set provided. <b>NOTE:</b> This function needs to be called anywhere at the point "after_setup_theme" is executed or later.  That means "init" will also work. If you are using this inside of some other plugin\'s hook, it will likely execute just fine.  If there is a problem using this (meaning nothing happens), then test using it like the example provided (or with "init"). In most cases, everything will be set up and you will be calling this function after some action on the front end so you may not run into any issues at all.</p><h4>Menu Membership</h4><p>The menu edit page will now have check boxes (labelled by menu id) next to the menu name in the menu list.  When you click this box, the membership categories meta box will open to the left and the mebership category list will appear.  You will be able to assign a membership category to that menu item.  Note that the menu id is automatically placed in the text box at the top. If this is missing or changed, you may have unexpected results.  If a user does not have privilege based on the membership category set, the menu item will simply not appear.  The user will still have access to the page otherwise.  If you want to restrict a user from accessing that page, you will need to set the membership category for the page as well.</p>',
				'grouped' => 'one'
			)		
		)
	),
	'section6' => array(
		'name' => 'User Categories',
		'tab_name' => 'Users Cats',
		'description' => 'Here you can find information and setting in regards to creating user categories for grouping.',
		'optionname' => 'usercat_amplify_setngs',
		'fields' => array(
			array(
				'id' => 'sepucat',
				'type' => 'separator',
				'label' => 'Info',
				'description' => '',
				'default_value' => '',
			),
			array(
				'id' => 'id_not_needed7',
				'type' => 'html',
				'label' => '',
				'description' => '',
				'default_value' => '<p><h4>Process</h4>If you chose to activate user categories, you will have the user category sub menu available under the User\'s menu.  There you can manipulate the taxonomy terms for the user categories.  In addition to that, you will have an area in the profile edit page to set categories as well as the ability to set categories in bulk on the user list page.</p><h4>Setting User User Categories Programmatically</h4><p>If you would like to programmatically add/edit your user categories you may use <pre style="background:#fff;">add_action("after_setup_theme", function(){updateUserCategs($user = 0, $categs = array(), $passed="id", $append = false);});</pre>  Here you will set the user id; the categories as an array of either slugs or ids; identify whether you have sent an array of slugs or ids; define whether you want to add to the list of user categories for the user or replace them with the new set provided. <b>NOTE:</b> This function needs to be called anywhere at the point "after_setup_theme" is executed or later.  That means "init" will also work. If you are using this inside of some other plugin\'s hook, it will likely execute just fine.  If there is a problem using this (meaning nothing happens), then test using it like the example provided (or with "init"). In most cases, everything will be set up and you will be calling this function after some action on the front end so you may not run into any issues at all.',
				'grouped' => 'one'
			)
		)
	),
	'section7' => array(
		'name' => 'Form Maker',
		'tab_name' => 'Forms',
		'description' => 'Coming Soon.',
		'optionname' => 'formmaker_amplify_setngs',
		'fields' => array(
			array(
				'id' => 'sepform',
				'type' => 'separator',
				'label' => 'Info',
				'description' => '',
				'default_value' => '',
			),
			array(
				'id' => 'id_not_needed8',
				'type' => 'html',
				'label' => '',
				'description' => '',
				'default_value' => '<p><h4>Process</h4><ul style="list-style-type:circle;"><li></li></ul></p>',
				'grouped' => 'one'
			)
		)
	)
);
$wprAdminAmplify = new GenerateSettingsPages($settings, 'WPR Admin Amplify Settings', 'WPR Admin Amplify', 'menu', '', 'Amplify Your Admin', 'wpr_admin_amplify', plugins_url() . '/wpr-admin-amplify/imgs/amplify16.png', 'manage_options');
add_action('wpr_pagemaker_pagecallback_end', 'wpr_placeMyPay', 10, 2);
function wpr_placeMyPay($dat, $activetab){
	if($dat['settings'][$activetab]['optionname'] == 'gen_amplify_setngs'){
	?>
	<form style="display:none;" id="hidthe2form" action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
	<input type="hidden" name="cmd" value="_s-xclick">
	<input type="hidden" name="hosted_button_id" value="5KB5DMWC8LBRC">
	<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
	<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
	</form>

	<?php
	}
}
//$pagetwo = new GenerateSettingsPages($insettings, 'Inner Page Settings', 'in Settings', 'submenu', 'tools.php', 'Welcome to Transylvania', '', 'default');
/*
These are four arrays that consist of sets of two, each defining custom post type data and the associated custom taxonomy data.
I am creating the custom post types for pagemaker and cptmaker.
*/
$amppaget = array(
	'general_single' => 'WPRPage',
	'general_plural' => 'WPRPages',
	'supports' => array('title'),
	"public" => false,
	"exclude_from_search" => true,
	"publicly_queryable" => false,
	"show_ui" => true,
	"show_in_nav_menus" => false,
	"show_in_menu" => 'wpr_admin_amplify',
	"show_in_admin_bar" => false,
	"has_archive" => false,
	"rewrite" => false,
	"query_var" => false,
);
$ampcptt = array(
	'general_single' => 'WPRCPT',
	'general_plural' => 'WPRCPTs',
	'supports' => array('title'),
	"public" => false,
	"exclude_from_search" => true,
	"publicly_queryable" => false,
	"show_ui" => true,
	"show_in_nav_menus" => false,
	"show_in_menu" => 'wpr_admin_amplify',
	"show_in_admin_bar" => false,
	"has_archive" => false,
	"rewrite" => false,
	"query_var" => false,
);
$ampmetat = array(
	'general_single' => 'WPRMeta',
	'general_plural' => 'WPRMeta',
	'supports' => array('title'),
	"public" => false,
	"exclude_from_search" => true,
	"publicly_queryable" => false,
	"show_ui" => true,
	"show_in_nav_menus" => false,
	"show_in_menu" => 'wpr_admin_amplify',
	"show_in_admin_bar" => false,
	"has_archive" => false,
	"rewrite" => false,
	"query_var" => false,
);
$ampformt = array(
	'general_single' => 'WPRForm',
	'general_plural' => 'WPRForms',
	'supports' => array('title'),
	"public" => false,
	"exclude_from_search" => true,
	"publicly_queryable" => false,
	"show_ui" => true,
	"show_in_nav_menus" => false,
	"show_in_menu" => 'wpr_admin_amplify',
	"show_in_admin_bar" => false,
	"has_archive" => false,
	"rewrite" => false,
	"query_var" => false,
);

$amppagec = array(
	array(
		'id'=> 'wpramplifypagetax',
		'general_single' => 'Amp Page Cat',
		'general_plural' => 'AmpPage Cats',
		'column_id' => 'amppagecat',
		'column_name' => 'Amp Page Cats',
		'hierarchical' => true,
		'public' => false,
		'show_ui' => true,
		'show_in_nav_menus' => false,
		'show_admin_colum' => true,
		'query_var' => false,
		'rewrite' => false,
	)
);
$ampcptc = array(
	array(
		'id'=> 'wpramplifycpttax',
		'general_single' => 'Amp CPT Category',
		'general_plural' => 'Amp CPT Categories',
		'column_id' => 'ampcptcat',
		'column_name' => 'Amp CPT Cats',
		'hierarchical' => true,
		'public' => false,
		'show_ui' => true,
		'show_in_nav_menus' => false,
		'show_admin_colum' => true,
		'query_var' => false,
		'rewrite' => false,
	)
);
$ampmetac = array(
	array(
		'id'=> 'wpramplifymetatax',
		'general_single' => 'Amp Meta Category',
		'general_plural' => 'Amp Meta Categories',
		'column_id' => 'ampmetacat',
		'column_name' => 'Amp Meta Cats',
		'hierarchical' => true,
		'public' => false,
		'show_ui' => true,
		'show_in_nav_menus' => false,
		'show_admin_colum' => true,
		'query_var' => false,
		'rewrite' => false,
	)
);
$ampformc = array(
	array(
		'id'=> 'wpramplifyformtax',
		'general_single' => 'Amp Form Category',
		'general_plural' => 'Amp Form Categories',
		'column_id' => 'ampformcat',
		'column_name' => 'Amp Form Cats',
		'hierarchical' => false,
		'public' => false,
		'show_ui' => false,
		'show_in_nav_menus' => false,
		'show_admin_colum' => false,
		'query_var' => false,
		'rewrite' => false,
	)
);
//wp-admin/edit-tags.php?taxonomy=wpramplifypagetax&post_type=wprmakepages taxonomy=wpramplifycpttax&post_type=wprmakecpts
$wprMakePages = new GenerateCustType('wprmakepages', $amppaget, $amppagec, true);
$wprMakeTypes = new GenerateCustType('wprmakecpts', $ampcptt, $ampcptc, true);
$wprMakeMetas = new GenerateCustType('wprmakemeta', $ampmetat, $ampmetac, true);
$wprMakeForms = new GenerateCustType('wprmakeforms', $ampformt, $ampformc, true);
/*
Becaue I'm adding edit.php pages as submenus of parent menus that are not post types, I have to define their parent.
Otherwise, the "Posts" menu item will be the parent.
*/
function fix_subamp_pages($parent_file = ''){
	global $pagenow;
	if(!empty($_GET[ 'taxonomy' ]) && $pagenow == 'edit-tags.php' && ($_GET['taxonomy'] == 'wpramplifypagetax' || $_GET['taxonomy'] == 'wpramplifycpttax' || $_GET['taxonomy'] == 'wpramplifymetatax')){
		$parent_file = 'wpr_admin_amplify';
		echo '<script>jQuery(document).ready(function(){jQuery("#toplevel_page_wpr_admin_amplify").find("a[href$=\"' . $_GET['post_type'] . '\"]").addClass("current").parents("li").addClass("current");});</script>';
	}
	return $parent_file;
}
add_filter('parent_file', 'fix_subamp_pages');
/*
Making sure my admin subfields are in the correct order.
*/
function reorderAmplifySubs($menu_order){
	global $submenu;
	$last = array_pop($submenu['wpr_admin_amplify']);
	array_unshift($submenu['wpr_admin_amplify'], $last);
	return $menu_order;
}
add_filter('custom_menu_order', 'reorderAmplifySubs');

/*
These two lines of code are all that is needed to create your very own custom fields.
Here we create the pagemaker custom post meta fields.
*/
$wprPagemaker = array(array("name" => __('Create your page', 'wpr'), "desc" => "", "id" => "", "std" => "", "type" => "wprpagemaker"));
$makepageMet = new GeneratePostMeta($wprPagemaker, "allpagemakerdat", "WPR Page Maker", "wpr_made_page_data", 'wprmakepages');

/*
Here we create the cptmaker custom post meta fields.
*/
$wprmakeCPT = array(array("name" => __('Create your CPTs', 'wpr'), "desc" => "", "id" => "", "std" => "", "type" => "wprcptmaker"));
$makepageMet = new GeneratePostMeta($wprmakeCPT, "allcptmakerdat", "WPR CPT Maker", "wpr_made_cpt_data", 'wprmakecpts');

/*
Here we create the metamaker custom post meta fields.
*/
$wprmakeMeta = array(array("name" => __('Create your Custom Meta', 'wpr'), "desc" => "", "id" => "", "std" => "", "type" => "wprmetamaker"));
$makepageMet = new GeneratePostMeta($wprmakeMeta, "allmetamakerdat", "WPR Meta Maker", "wpr_made_meta_data", 'wprmakemeta');

/*
Here we create the formmaker custom post meta fields.
*/
$wprformmaker = array(array("name" => __('Create your Forms', 'wpr'), "desc" => "", "id" => "", "std" => "", "type" => "wprformmaker"));
$makepageMet = new GeneratePostMeta($wprformmaker, "allformmakerdat", "WPR Form Maker", "wpr_made_form_data", 'wprmakeforms');

/*
Generates the pagemaker, cptmaker, metamaker and formmaker custom post type custom fields.
*/
require_once("admin_make_pagemaker.php");
require_once("admin_make_cptmaker.php");
require_once("admin_make_metamaker.php");
require_once("admin_make_formmaker.php");

/*
This function extends the postmeta.php save method
Used for both the pagemaker, cptmaker and metamaker
*/
function filterPagemakerPost($true, $postedDat, $meta_data, $post_id){
	if($meta_data['postnm'] == "wpr_made_page_data" && $meta_data['id'] == "allpagemakerdat"){
		update_post_meta($post_id, "wpr_made_page_data", $postedDat);
		return false;
	}
	if($meta_data['postnm'] == "wpr_made_cpt_data" && $meta_data['id'] == "allcptmakerdat"){
		update_post_meta($post_id, "wpr_made_cpt_data", $postedDat);
		return false;
	}
	if($meta_data['postnm'] == "wpr_made_meta_data" && $meta_data['id'] == "allmetamakerdat"){
		update_post_meta($post_id, "wpr_made_meta_data", $postedDat);
		return false;
	}
	if($meta_data['postnm'] == "wpr_made_form_data" && $meta_data['id'] == "allformmakerdat"){
		update_post_meta($post_id, "wpr_made_form_data", $postedDat);
		return false;
	}
	return true;
}
add_filter('wpr_postmeta_saving_call', 'filterPagemakerPost', 10, 4);

/*
In order to prevent things from breaking, this function hooks into
WP's "before it's all saved" filter to prevent the user from creating an admin page post type
without filling in all the correct fields.  It's an extra step in data validation.  If things are incorrect, the post will
be saved as a draft until the user decides to fill in all the necessary fields and quit trying to break things.
TODO:  MAKE THIS WORK LIKE IT's SUPPOSED TO AND IMPROVE UPON IT FOR ALL USE CASES
*/
function requireMakeFields($data, $raw){
	//$pid = $raw['ID'];
	if(($raw['post_type'] != 'wprmakemeta' && $raw['post_type'] != 'wprmakecpts' && $raw['post_type'] != 'wprmakepages') || $raw['post_status'] == "trash"){return $data;}
	//print_r($raw);
	$error = "";
	/*'post_author','post_date','post_date_gmt','post_content','post_content_filtered','post_title','post_excerpt','post_status','post_type','comment_status','ping_status','post_password','post_name','to_ping','pinged','post_modified','post_modified_gmt','post_parent','menu_order','guid'*/	
	/*'post_status','post_type','post_author','ping_status','post_parent','menu_order','to_ping','pinged','post_password','guid','post_content_filtered','post_excerpt','import_id','post_content','post_title','ID','post_date','post_date_gmt','comment_status','post_name','post_modified','post_modified_gmt','post_mime_type','comment_count','ancestors','post_category','tags_input','filter'*/
	
	/*
	$dontuse = array("attachment", "attachment_id", "author", "author_name", "calendar", "cat", "category", "category__and", "category__in", "category__not_in", "category_name", "comments_per_page", "comments_popup", "customize_messenger_channel", "customized", "cpage", "day", "debug", "error", "exact", "feed", "hour", "link_category", "m", "minute", "monthnum", "more", "name", "nav_menu", "nonce", "nopaging", "offset", "order", "orderby", "p", "page", "page_id", "paged", "pagename", "pb", "perm", "post", "post__in", "post__not_in", "post_format", "post_mime_type", "post_status", "post_tag", "post_type", "posts", "posts_per_archive_page", "posts_per_page", "preview", "robots", "s", "search", "second", "sentence", "showposts", "static", "subpost", "subpost_id", "tag", "tag__and", "tag__in", "tag__not_in", "tag_id", "tag_slug__and", "tag_slug__in", "taxonomy", "tb", "term", "theme", "type", "w", "withcomments", "withoutcomments", "year");
	*/
	
	//sanitize_title_for_query($this->pagemaker_data['menutitle']);
	$allowpublish = true;
	$required = array();
	if($raw['post_type'] == 'wprmakepages'){
		$required = array("post_title");
		$needid = '';
	}
	if($raw['post_type'] == 'wprmakecpts'){
		$required = array("post_title");
		$needid = '';
	}
	if($raw['post_type'] == 'wprmakemeta'){
		$required = array("post_title");
		$needid = '';
	}
	if($raw['post_type'] == 'wprmakeforms'){
		$required = array("post_title");
		$needid = '';
	}
	if(empty($data['post_title'])){
		$allowpublish = false;
		$error .= "You must include a title.  The title will be the name displayed on the menu.";
	}
	if(isset($_POST['meta'])){
	  foreach ($_POST['meta'] as $meta){
	   if(in_array($meta['key'], $required) && empty($meta['value'])){
	   	$allowpublish = false;
		$error .= $meta['key'] . " is a required field.  Please make sure you have all required fields filled in.<br/>";
	   }
	   if($meta['key'] == $needid){
		if(!preg_match('/^[a-z\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/',$meta['value'])){
			$allowpublish = false;
			$error .= "Your ...id must be start with a lower case letter and contain only letters, numbers or underscored.<br/>";
		}
	   }
	  }
	}else{return $data;}
	if(!$allowpublish){if($data['post_status'] == "auto-draft"){return $data;}$data['post_status'] = 'draft';}//$data["error"] = $error;
	return $data;
}
add_action('wp_insert_post_data','requireMakeFields', '99', 2);
/*
This function hooks into the postmeta.php after the post fields are created
It is used to set some global javascript for both the pagemaker and the cptmaker
*/
function addjscriptForPagenCPT($post, $meta_data, $xargs){
	if(($meta_data['postnm'] == "wpr_made_page_data" && $meta_data['id'] == "allpagemakerdat") || ($meta_data['postnm'] == "wpr_made_cpt_data" && $meta_data['id'] == "allcptmakerdat") || ($meta_data['postnm'] == "wpr_made_meta_data" && $meta_data['id'] == "allmetamakerdat")){
		?>
		<script>
		function varvalidate(input, sord){
			var str = input.val();
			if(sord == "slug"){
				var rgxe = new RegExp('^[a-z][0-9a-z_-]*$');
			}else{
				var rgxe = new RegExp('^[a-z][0-9a-zA-Z_-]*$');
			}
			var appropriate = rgxe.test(str);
			if(!appropriate){
				//var nstri = "";
				if(str.length === 1){input.val("");}
				else{
					if(sord == "slug"){str = str.replace(/[^0-9a-z_-]/g, "");}
					else{str = str.replace(/[^0-9a-zA-Z_-]/g, "");}
					if(!rgxe.test(str)){str = "";}
					input.val(str);
				}				
			}
			if(str.length > 20){input.val(str.substring(0, 20));}
		}
		</script>
		<?php
	}
}
add_action('wpr_postmeta_afterfields', 'addjscriptForPagenCPT', 10, 3); 

/*
An Ajax function to make double sure that the requested field is WP MySQL key ready in key/value paired inputs
like options and meta.  I like to use underscores and not hyphens.
*/
add_action('wp_ajax_dosanz', 'washyourhands');
function washyourhands(){
	if(isset($_POST['tosanitize'])){
		$sanded = array();
		$san = $_POST['tosanitize'];
		$sanar = explode("|", $san);
		foreach($sanar as $asan){
			$sanded[] = str_replace("-", "_", sanitize_title_for_query($asan));
		}
		if(count($sanded)> 1){echo json_encode(array("sanitized"=>$sanded));}else{echo json_encode(array("sanitized"=>$sanded[0]));}
		die();
	}
}
/*
Here we access the pages, cpt's and meta post types and turn them into what they are supposed to be.
*/
function WPRmakeMagic(){
	$amplifiers = new WP_Query(array('posts_per_page' => -1, 'post_type' => array('wprmakepages','wprmakecpts','wprmakemeta'), 'post_status' => 'publish', 'order_by' => 'type', 'order' => 'ASC'));
	//$amplifypages = array();
	//$amplifycpts = array();
	$amplifymeta = array();
	foreach($amplifiers->posts as $amp){
		switch($amp->post_type){
			case "wprmakepages":
				//$amplifypages[] = 
				$amppagedat = get_post_meta($amp->ID, "wpr_made_page_data", true);//print_r($amppagedat);
				foreach($amppagedat["sections"] as $sec => $vals){
					//$ampsettings[$sec]["optionname"] = $sec;
					foreach($vals as $sk => $sv){
						if($sk != "fields"){$ampsettings[$sec][$sk] = $sv;}
						else{
							$fi=0;
							foreach($sv as $field){							
								foreach($field as $fk => $fv){
									if($fk != "morethanone"){$ampsettings[$sec]["fields"][$fi][$fk] = $fv?$fv:"";}
									else{
									foreach($fv as $mv){
										$ampsettings[$sec]["fields"][$fi]["morethanone"][] = $mv;
									}
									}
								}
								$fi++;
							}
						}
					}
				}
				//print_r($ampsettings);
				if($amppagedat["menuorsub"] == "menu"){$amppagedat["parentmenu"] = "";}
				$makeapage = new GenerateSettingsPages($ampsettings, $amp->post_title, $amp->post_title, $amppagedat["menuorsub"], $amppagedat["parentmenu"], $amppagedat["heading"], $amp->post_name, $amppagedat["iconurl"], $amppagedat["capability"]);
			break;
			case "wprmakecpts":
				//$amplifycpts[] = 
				$ampcptdat = get_post_meta($amp->ID, "wpr_made_cpt_data", true);
				$newtipe = false;
				if(!empty($ampcptdat["typeid"])){
					$newtipe = true;
					$ptipe = $ampcptdat["typeid"];
				}else{
					$ptipe = $ampcptdat["typeidexisting"];
				}			
				if(empty($ptipe) || empty($ampcptdat["general_single"]) || empty($ampcptdat["general_plural"])){break;}
				$cptdata = array("general_single"=> $ampcptdat["general_single"], "general_plural"=>$ampcptdat["general_plural"]);
				$cpttax = array();
				foreach($ampcptdat as $cpk => $cpv){
					if($cpk == "filter"){continue;}
					if($cpk == "taxonomies"){
						$txn = 0;
						foreach($cpv as $txk => $txv){
							if(empty($ampcptdat["taxonomies"][$txk]["id"])){break;}
							foreach($txv as $xk => $xv){
								if(!isset($txv["hierarchical"])){$cpttax[$txn]["hierarchical"] = false;}
								if($xk == "hierarchical"){
									if($xv === 1 || $xv === "1"){$nxv = true;}
									if($xv === 0 || $xv === "0"){$nxv = false;}
									$cpttax[$txn]["hierarchical"] = $nxv;
								}
								else{if(isset($txv[$xk]) && $txv[$xk] != ""){$cpttax[$txn][$xk] = $xv;}}
							}
							$txn++;
						}
					}else{
						if(isset($ampcptdat[$cpk]) && $ampcptdat[$cpk] != ""){$cptdata[$cpk] = $cpv;}
					}
				}
				$hasTax = empty($cpttax)?false:true;
				$madeTypes = new GenerateCustType($ptipe, $cptdata, $cpttax, $hasTax, $newtipe);
			break;
			case "wprmakemeta":
				$atmp["data"] = get_post_meta($amp->ID, "wpr_made_meta_data", true);
				$atmp["title"] = $amp->post_title;
				$atmp["slug"] = $amp->post_name;
				$atmp["id"] = $amp->ID;
				$amplifymeta[] = $atmp;
				
			break;
		}
	}
	unset($amplifiers);unset($amp);unset($atmp);
	//foreach($amplifypages as $ampPage){}
	//foreach($amplifycpts as $ampCPT){}
	/*
	Array ( [posttype] => post [context] => default [advancedargs] => [fields] => Array ( [testertexohone] => Array ( [id] => testertexohone [type] => text [name] => Write here [desc] => No desc Needed [std] => [decimal] => false ) [testohrad] => Array ( [id] => testohrad [type] => radio [name] => Some Rado [desc] => No Need for Rados [std] => nutter,Yupper|yupper,Nutter|nutter ) ) ) 
	*/
	foreach($amplifymeta as $ampMeta){
		//$ampMeta["data"];//$ampMeta["title"];//$ampMeta["slug"];//$ampMeta["id"];
		$metAdAta = array();
		$meti = 0;
		foreach($ampMeta["data"]["fields"] as $mek => $mev){
			if(empty($ampMeta["data"]["fields"][$mek]["id"])){continue;}
			foreach($mev as $mrk => $mrv){
				$metAdAta[$meti][$mrk] = $mrv;
			}
			$meti++;
		}
		$adargs = array();
		$context = !empty($ampMeta["data"]["context"])?$ampMeta["data"]["context"]:"advanced";
		$priority = !empty($ampMeta["data"]["priority"])?$ampMeta["data"]["priority"]:"default";
		$advancedargs = isset($ampMeta["data"]["advancedargs"])?$ampMeta["data"]["advancedargs"]:"";
		if(!empty($advancedargs)){
			$splitargs = explode(",", $advancedargs);
			foreach($splitargs as $sk => $sv){
				$pieces = explode(":", $sv);
				if(empty($pieces[0]) || !isset($pieces[1])){continue;}
				$adargs[$pieces[0]] = $peices[1];
			}
		}
		//print_r($metAdAta);
		$makeMet = new GeneratePostMeta($metAdAta, $ampMeta["slug"], $ampMeta["title"], "posted_" . $ampMeta["id"] . "_data", $ampMeta["data"]["posttype"], $context, $priority, $adargs);
	}
}
add_action("after_setup_theme", "WPRmakeMagic");
/*
Not using this currently.  May decide to provide another method for accessing data saved from the things
created by the user.  Only use would be to maybe make a shortcode for it.  get_option, and get_post_meta do not
need to be rewritten, the user can learn how to use them just as easily as learning how to use any function
I provide that does essentially the same thing.
*/
function wpr_saved_data($name, $type="meta", $postid=''){
	if($type=="meta"){
		//advancedlist (||, &$) //multilist (||)
	}
	if($type=="option"){
		
	}
}
/*add_action("admin_menu", "fljdf");
function fljdf(){
//$rrt = updateUserMems(4, array(13, 16), "id", true);
//if(is_string($rrt)){echo $rrt;}
//elseif(is_wp_error($rrt)){echo $rrt->get_error_message();}
//else{print_r($rrt);}
}

//$t = wp_set_object_terms(3, array(13, 16), 'wpr_membership', true);
//if(is_wp_error($t)){echo $t->get_error_message();}*/
/*================= TODO Advanced Version

Manage backend users - what they see
Forms back end
Forms front end
User fields back end
User fields front end
Login
Registration

Membership payment gateways

=====================*/
/*===============================================================*/
/*
print_r(get_editable_roles());
global $wpdb;
print_r(get_option($wpdb->prefix . 'user_roles')); 
print_r($pageone->present_vars());

$user = new WP_User( $user_id );
$user->add_cap( 'can_edit_posts' );


function my_the_post_action($post_object){
	// modify post object here
}
add_action('the_post', 'my_the_post_action');


//////////////////////////////
unset( $columns['posts'] );
$columns['users'] = __( 'Users' );
return $columns;
//////////////////////////////

//Prevent Editing of a specified user
https://developer.wordpress.org/reference/hooks/map_meta_cap/
https://core.trac.wordpress.org/browser/tags/4.1/src/wp-includes/capabilities.php#L1344
//This example shows how you can protect the original admin from being edited or deleted by anyone else
add_filter('map_meta_cap', 'prevent_user_edit', 10, 4 );
function prevent_user_edit( $required_caps, $cap, $user_id, $args ){
 
$protected_user = 1; // ID of user not editable
if ( $user_id === $protected_user ) // Don't block caps if current user = protected user
return $required_caps;
 
$blocked_caps = array(
'delete_user',
'edit_user'
);
if ( in_array( $cap, $blocked_caps ) && $args[0] === $protected_user )
$required_caps[] = 'do_not_allow';
 
return $required_caps;
}


user_has_cap and map_meta_cap filters

map_meta_cap returns caps relative to object

User can edit comments, for 30 minutes.

add_filter('map_meta_cap', function($caps, $cap, $user_id, $args){
	if($cap != 'edit_comment'){return $caps;}
	$comment_id = $args[1];
	$c = get_comment($comment_id);
	$user_id = $c->user_id;
	$time = strtotime($c->comment_date_gmt);
	$window = strtotime('-30 minutes');
	if($user_id && $time > $window){return array();}
	return $caps;
}, 10, 3):

Don't let anyone delete users:
add_filter('map_meta_caps'), function($required_caps, $cap){
	if('delete_user' == $cap || 'delete_users' == $cap){
		$required_caps[] = 'do_not_allow';
	}
	return $required_caps;
}, 10, 2);

Only admin can delete published posts:
add_filter('map_meta_cap', function($required_caps, $cap){
	if('delete_post' == $cap){$required_caps[] = 'manage_options';}
	return $required_caps;
}, 10, 2);

Require editors to approve posts:
add_filter('map_meta_caps', function($required_caps, $cap){
	if($cap == 'publish_post' || $cap == 'publish_posts'){$required_caps[] = 'edit_others_posts';}
	return $required_caps;
}, 10, 2);

Read dev api developer.wordpress.org
------------------------
map_meta_cap()
WP_User
WP_Role
register_post_type()
ger_post_type_capabilities()
*/
function wpr_add_header_xua() {
	header( 'X-UA-Compatible: IE=edge,chrome=1' );
}
//add_action( 'send_headers', 'wpr_add_header_xua' );
//foreach(glob(plugin_dir_path( __FILE__ )."widgets/*.php" ) as $wwfile){require_once $wwfile;}
//foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)) as $filename) { echo "$filename\n"; } 

/*===
Set the post revisions unless the constant was set in wp-config.php
===*/
//if (!defined('WP_POST_REVISIONS')) define('WP_POST_REVISIONS', 5);

function wpramplify_jas(){
	if(!is_admin()){
		wp_register_script( 'wpr-customs', plugins_url() . '/wpr-admin-amplify/js/custjis.js', array('jquery'));
		wp_enqueue_script('wpr-customs');
		//wp_enqueue_script('jquery-ui-core');
		//wp_enqueue_script('jquery-effects-core');
	$file_for_jav = admin_url('admin-ajax.php');
	$tdirct = get_template_directory_uri();
	$yuy = str_replace(array("http://", "https://"), "", site_url());
	$yar = explode("/", $yuy);
	if(count($yar)>1){foreach ($yar as $key => $value){if($key !=0){$yuy.= "/" . $value;}}}
	else{$yuy = '';}
	$tran_arr = array( 'jaxfile' => $file_for_jav, 'directory' => $tdirct, 'homepath' => $yuy, 'siteurl'=> site_url(), 'plugins' => plugins_url(), 'custjax' => plugins_url() . "/wpr-admin-amplify/minejax.php", "uidu" => get_current_user_id() );
	wp_localize_script( 'wpr-customs', 'fromphp', $tran_arr );
	}
}
add_action('wp_enqueue_scripts', 'wpramplify_jas');


function wpramplify_execute_php($html){
     if(strpos($html,"<"."?php")!==false){
          ob_start();
          eval('?>'.$html);
          $html=ob_get_contents();
          ob_end_clean();
     }
     return $html;
}
if(isset($wpr_gensettings["wpr_allow_widgphp"]) && $wpr_gensettings["wpr_allow_widgphp"] == "1"){
	add_filter('widget_text','wpramplify_execute_php',100);
}
if(isset($wpr_gensettings["wpr_allow_widgshorts"]) && $wpr_gensettings["wpr_allow_widgshorts"] == "1"){
	add_filter('widget_text', 'do_shortcode');
}

function wpramplify__email_login_authenticate( $user, $username, $password ) {
	if ( is_a( $user, 'WP_User' ) )
		return $user;

	if ( !empty( $username ) ) {
		$username = str_replace( '&', '&amp;', stripslashes( $username ) );
		$user = get_user_by( 'email', $username );
		if ( isset( $user, $user->user_login, $user->user_status ) && 0 == (int) $user->user_status )
			$username = $user->user_login;
	}

	return wp_authenticate_username_password( null, $username, $password );
}
if($wpr_gensettings["wpr_want_emaillogin"] && $wpr_gensettings["wpr_want_emaillogin"] == "1"){
	remove_filter( 'authenticate', 'wp_authenticate_username_password', 20, 3 );
	add_filter( 'authenticate', 'wpramplify__email_login_authenticate', 20, 3 );
}
/* AJAX CALLBACK FOR AJAX LOGIN */
/*
<div id="logerin">
  <div class="logbox">
	<div id="nonaj" data-jano="<?php echo wp_create_nonce("loginchecker"); ?>"></div>
	<div class="lfields">
		<h3 class="">Login Here</h3>
		<div id="eresponse" class=""></div>
		<input id="wpr_unmli" type="text" name="wpr_usernm" placeholder="username" />
		<input id="wpr_ppassli" type="password" name="wpr_pass" placeholder="password" />
		<div class="loginspace">
		<p id="fgotmepas" class="frgtpass">forgot<br/>password?</p>
		<p id="entersite" class="lbtn"><span>login</span></p>
		</div>
		<div class="soclogin"></div>
	</div>
	<div class="linbrow"></div>
  </div>
</div>
*/
add_action('wp_ajax_logruser', 'wpramplify_logruser_callback');
add_action('wp_ajax_nopriv_logruser', 'wpramplify_logruser_callback');


function wpramplify_logruser_callback(){
global $current_user;
get_currentuserinfo();
global $wpdb;
global $user_ID;  

if (!$current_user->ID ) {
	check_ajax_referer( 'loginchecker', 'loginsecure' );
    if($_POST['wpr_usernm']){
        $username = $wpdb->escape($_POST['wpr_usernm']);
        $password = $wpdb->escape($_POST['wpr_pass']);
		
		$login_data = array();
		$login_data['user_login'] = $username;
		$login_data['user_password'] = $password;
		$login_data['remember'] = true;
		$user_verify = wp_signon( $login_data, false ); //true if SSL!!!!!
		$notgood = 1;
		if(is_wp_error($user_verify)){
			$notgood = 1;		
		$username = str_replace( '&', '&amp;', stripslashes( $username ) );
		$user = get_user_by( 'email', $username );
			if ( isset( $user, $user->user_login, $user->user_status ) && 0 == (int) $user->user_status ){
				$username = $user->user_login;
				$login_data['user_login'] = $username;
				$user_verify = wp_signon( $login_data, false );
				if(is_wp_error($user_verify)){
					$notgood = 1;
				}
				else{$notgood = 0;}		
			}
		}
		else {$notgood = 0;}
		if ($notgood) 
		{
			echo json_encode(array("results"=>$user_verify->get_error_message() . "<br/>")); 
			die();
		 }
		 else{	
			wp_set_current_user($user_verify->ID);
			wp_set_auth_cookie($user_verify->ID);
			do_action('wp_login', $user_verify->user_login );
			echo json_encode(array("results"=>"good", "user"=> $user_verify->user_login, "tourl"=>"someurl")); 
			die();
		  }
  
        die();  
  
    } else {  
        echo json_encode(array("results"=>"Something went wrong, please notify the Administrator.")); 
		die();
	}
}else {  
        echo json_encode(array("results"=>"You are already logged in, you shouldn't be here.")); 
		die();
	}
	/* [ID] => [user_login] => [user_pass] =>[user_nicename] => [user_email] => [user_url] =>[user_registered] =>[user_activation_key] =>[user_status] =>  [display_name] =>*/
}


/*
$args = array(
	'authors'      => '',
	'child_of'     => 0,
	'date_format'  => get_option('date_format'),
	'depth'        => 0,
	'echo'         => 1,
	'exclude'      => '',
	'include'      => '',
	'link_after'   => '',
	'link_before'  => '',
	'post_type'    => 'page',
	'post_status'  => 'publish',
	'show_date'    => '',
	'sort_column'  => 'menu_order, post_title',
        'sort_order'   => '',
	'title_li'     => __('Pages'), 
	'walker'       => ''
);
wp_list_pages( $args );

global $menu, $submenu;


	public static function get_roles() {
		global $wp_roles;
		if ( !isset($wp_roles) ) {
			$wp_roles = new WP_Roles();
		}
		//TODO: Do something about Super Admin
		return $wp_roles;
	}
*/


/*
function wpr_update_meta_array

This method is provided as a quick way of creating/updating and removing post and user meta array values. This method only deals with one-dimensional arrays.

@param string $metkey required. The meta key in question.
@param array $metvals required. The array of values you want to add or remove from the meta array.
@param string $type optional. Will this be updating the "post" meta value or the "user" meta value. Accepted values are "post" or "user".  DEFAULT is "post".
@param string/integer $postid optional/required. If the $type is "post", then $postid is required.  You must provide a post id for the post to be updated.  Will return an error if this is not provided.  DEFAULT is empty string.
@param string $neworgo optional. Accepted values are "new" or "remove".  "new" indicates that you are updating or creating the meta value array, "remove" indicates that you wish to remove meta array elements.
@param array $exclude optional. If you wish to pass values to the method that cannot be updated or removed, then provide them as an array in this parameter.  This is useful if you are using this method dynamically with user input and want to prevent certain values from being manipulated, created or removed.
@param bool $unique optional. Do you want to prevent duplicate entries into the meta value array?  This is only applicable to $neworgo = "new".  Default is true.
@param string/integer $user optional. If not provided, the current logged in user's id will be used for the update_user_meta method. Default is 0, indicating that the current user id is to be used.

RETURNS string on failure, array on success.  Success array will contain array({RETURNED VALUE FROM UPDATE META METHOD}. {NOTIFICATION OF ANY IN THE METVALS ARRAY THAT WERE NOT UPDATED}, {FINAL ARRAY THAT IS BEING UPDATED FOR THE META KEY})
*/
function wpr_update_meta_array($metkey, $metvals, $type = "post", $postid = "", $neworgo = "new", $exclude = array(), $unique = true, $user = 0){
	if($user === 0){$current_user = wp_get_current_user(); $user = $current_user->ID;}else{$user = (int)$user;}
	if(!is_array($metvals)){$metvals = (array)$metvals;}
	if(empty($metvals)){return "You did not pass any data.";}
	if(array_keys($metvals) !== range(0, count($metvals) - 1)){return "This method is not for associative arrays";}
	//if(count($metvals) == count($metvals, COUNT_RECURSIVE)){return "This method is not equipped to handle multi-dimensional arrays.";}
	$tempvals = $metvals;
	rsort( $tempvals ); if(isset( $tempvals[0] ) && is_array( $tempvals[0] )){return "This method is not equipped to handle multi-dimensional arrays.";}
	$tempvals = "";
	if($type == "post"){if(!$postid){return "You did not supply a post id";}$therenow = get_post_meta($postid, $metkey, true);}
	elseif($type == "user"){$therenow = get_user_meta($user, $metkey, true);}
	
	if($neworgo == "new"){
		$leftovers = "";
		if(!$therenow){$therenow = array();}
		$newarr = array();
		
		foreach($metvals as $metval){
			if($unique){
				if(in_array($metval, $therenow)){$leftovers .= "Value " . $metval . " already exists.<br/>"; continue;}
			}
			if(!empty($exclude)){
				if(in_array($metval, $exclude)){$leftovers .= "Value " . $metval . " is excluded.<br/>"; continue;}
			}
			$newarr[] = $metval;
		}
		if(empty($newarr)){return $leftovers . "<br/>All values attempted already exist or are excluded.";}
		$therenow = array_merge($therenow, $newarr);
		
		if($type == "post"){$upped = update_post_meta($postid, $metkey, $therenow);}
		elseif($type = "user"){$upped = update_user_meta($user, $metkey, $therenow);}
		if($upped === false){return "Update failed for some reason.";}
		return array($upped, $leftovers, $therenow);
	}
	elseif($neworgo == "remove"){
		$leftovers = "";
		if(!$therenow || empty($therenow)){return "This meta key is empty.";}
		$newarr = array();		
		foreach($metvals as $metval){
			if(!empty($exclude)){
				if(in_array($metval, $exclude)){$leftovers .= "Value " . $metval . " is excluded.<br/>"; continue;}
			}
			if(!in_array($metval, $therenow)){$leftovers .= "Value " . $metval . " does not exist.<br/>"; continue;}
			$newarr[] = $metval;
		}
		$finarr = array();
		foreach($therenow as $existingval){
				if(in_array($existingval, $newarr)){continue;}
				$finarr[] = $existingval;
		}
		
		if(empty($finarr)){$finarr = ""; $leftovers = "You have emptied this meta entry.";}
		if($type == "post"){$upped = update_post_meta($postid, $metkey, $finarr);}
		elseif($type = "user"){$upped = update_user_meta($user, $metkey, $finarr);}
		if($upped === false){return "Update failed for some reason, the values to be removed may not exist.";}
		return array($upped, $leftovers, $finarr);
	}
}

/*
function wpr_update_meta_assoc

NOTE: This method can be combined with the method above by sorting by index/key for both, whether numeric or associative.  It can also be adjusted to search recursively for update/remove key-values from subarrays in passed multidimensional arrays.  These methods were created for the benefit of a project and as quick snippets/example/tutorial code for worldpressrevolution so it may not be as robust as it could be.  If anyone would like to improve upon these methods, combine them into one and make the code available in the comments of this snippet, please do so.

This method is provided as a quick way of creating/updating and removing post and user meta associative array values.  This method will return an error if the values sent are ordered integer indexed and not associative. Associatie arrays are provided as an array of key value pairs.  This method only deals with one-dimensional arrays.

@param string $metkey required. The meta key in question.
@param array $metvals required. The associative array of values you want to add or remove from the meta associative array.
@param string $type optional. Will this be updating the "post" meta value or the "user" meta value. Accepted values are "post" or "user".  DEFAULT is "post".
@param string/integer $postid optional/required. If the $type is "post", then $postid is required.  You must provide a post id for the post to be updated.  Will return an error if this is not provided.  DEFAULT is empty string.
@param string $neworgo optional. Accepted values are "new" or "remove".  "new" indicates that you are updating or creating the meta value array, "remove" indicates that you wish to remove meta array elements.
@param array $excludekeys optional. If you wish to pass values to the method that cannot be updated or removed, then provide them as an associative array in this parameter.  This is useful if you are using this method dynamically with user input and want to prevent certain values from being manipulated, created or removed.
@param bool $unique optional. Do you want to prevent duplicate entries into the meta value array?  This is only applicable to $neworgo = "new".  Default is true.
@param string/integer $user optional. If not provided, the current logged in user's id will be used for the update_user_meta method. Default is 0, indicating that the current user id is to be used.

RETURNS string on failure, array on success.  Success array will contain array({RETURNED VALUE FROM UPDATE META METHOD}. {NOTIFICATION OF ANY IN THE METVALS ARRAY THAT WERE NOT UPDATED}, {FINAL ARRAY THAT IS BEING UPDATED FOR THE META KEY})
*/
function wpr_update_meta_assoc($metkey, $metvals, $type = "post", $postid = "", $neworgo = "new", $excludekeys = array(), $unique = true, $user = 0){
	if($user === 0){$current_user = wp_get_current_user(); $user = $current_user->ID;}else{$user = (int)$user;}
	if(!is_array($metvals)){return "You must pass an array to this method.";}
	if(empty($metvals)){return "You did not pass any data.";}
	if(array_keys($metvals) === range(0, count($metvals) - 1)){return "This method is only used for associative arrays";}
	$tempvals = $metvals;
	rsort( $tempvals ); if(isset( $tempvals[0] ) && is_array( $tempvals[0] )){return "This method is not equipped to handle multi-dimensional arrays.";}
	$tempvals = "";
	if($type == "post"){if(!$postid){return "You did not supply a post id";}$therenow = get_post_meta($postid, $metkey, true);}
	elseif($type == "user"){$therenow = get_user_meta($user, $metkey, true);}
	
	if($neworgo == "new"){
		$leftovers = "";
		if(!$therenow){$therenow = array();}
		$newarr = array();
		
		foreach($metvals as $key => $metval){
			if($unique){
				if(array_key_exists($key, $therenow)){
					if($therenow[$key] == $metval){$leftovers .= "Key " . $key . " with value " . $metval . " already exists.<br/>"; continue;}
				}
			}
			if(!empty($excludekeys)){
				if(array_key_exists($key, $excludekeys)){
					if($excludekeys[$key] == $metval){$leftovers .= "Key " . $key . " with value " . $metval . " is excluded.<br/>"; continue;}
				}
			}
			$newarr[$key] = $metval;
		}
		if(empty($newarr)){return $leftovers . "<br/>All values attempted already exist or are excluded.";}
		$therenow = array_merge($therenow, $newarr);
		
		if($type == "post"){$upped = update_post_meta($postid, $metkey, $therenow);}
		elseif($type = "user"){$upped = update_user_meta($user, $metkey, $therenow);}
		if($upped === false){return "Update failed for some reason.";}
		return array($upped, $leftovers, $therenow);
	}
	elseif($neworgo == "remove"){
		$leftovers = "";
		if(!$therenow || empty($therenow)){return "This meta value does not exist.";}		
		$newarr = array();
		foreach($metvals as $key => $metval){
				if(!array_key_exists($key, $therenow)){$leftovers .= "Key " . $key . " does not exist.<br/>"; continue;}
				if(array_key_exists($key, $therenow)){
					if($therenow[$key] !== $metval){$leftovers .= "Key " . $key . " with value " . $metval . " does not exist.<br/>"; continue;}
				}
			if(!empty($excludekeys)){
				if(array_key_exists($key, $excludekeys)){
					if($excludekeys[$key] == $metval){$leftovers .= "Key " . $key . " with value " . $metval . " is excluded.<br/>"; continue;}
				}
			}
			$newarr[$key] = $metval;
		}
		$finarr = array();
		foreach($therenow as $key => $existingval){
				if(array_key_exists($key, $newarr)){if($newarr[$key] == $existingval){continue;}}
				$finarr[$key] = $existingval;
		}
		
		if(empty($finarr)){$finarr = ""; $leftovers = "You have emptied this meta entry.";}
		if($type == "post"){$upped = update_post_meta($postid, $metkey, $finarr);}
		elseif($type = "user"){$upped = update_user_meta($user, $metkey, $finarr);}
		if($upped === false){return "Update failed for some reason, the values to be removed may not exist.";}
		return array($upped, $leftovers, $finarr);
	}

}

/*
$qstar = array('name' => 'Questions','plural' => 'Questions','public' => true,'navs' => true,'queryable' => true,'showui' => true,'cap' => 'post','hierarch' => true,'rewrite' => array('slug' => 'question', 'with_front' => true),'queryvar' => true,'excludesearch' => false,'menupos' => 5,'supports' => array('title', 'editor', 'revisions', 'excerpt', 'thumbnail', 'custom-fields'));

$aqstar = array(  array('id' => 'question_type','name' => 'Question Type','plural' => 'Question Types','hierarch' => true,'rewrite' => array( 'slug' => 'question-type' ),'label' => 'Question Type','archive' => true,'queryvar' => true,'linkpost' => true,'columnid' => 'qstccol','columnname' => 'Q Type'));

$artlst = new MakeCustType('garden_question', $qstar, true, $aqstar, 'questn16.png', 'questn32.png');
*/
?>