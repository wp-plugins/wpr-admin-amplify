<?php
if(!class_exists('GenerateSettingsPages')){
class GenerateSettingsPages{
	public $pagemaker_data;
/*===
Constructor, initializes the settings
===*/
	public function __construct($settings, $pagetitle, $menutitle, $menuorsub='submenu', $parentmenu = 'options-general.php', $heading = 'WPR Settings Page', $slug='', $icon='default', $caps='manage_options', $pos='', $priority = 10){
		if($pos){$pos = (string)$pos . ".25";}else{$pos = null;}
		$this->pagemaker_data = array(
			'settings' => $settings,
			'pagetitle' => $pagetitle,
			'menutitle' => $menutitle,
			'menuorsub' => $menuorsub,
			'capability' => $caps?$caps:'manage_options',
			'parentmenu' => $parentmenu,
			'icon' => $icon,
			'position' => $pos,
			'heading' => $heading,
			'slug' => $slug
		);
		if(empty($this->pagemaker_data['slug'])){$this->pagemaker_data['slug'] = sanitize_title_for_query($this->pagemaker_data['menutitle']);}
		$this->pagemaker_data = apply_filters('wpr_pagemaker_paramsadjust', $this->pagemaker_data);
		add_action('admin_menu', array($this, 'wpr_add_admin_menu'), $priority);
		add_action('admin_init',  array($this, 'wpr_settings_init'));
	}
/*===
Creates the menu page, hooked from constructor
===*/
	function wpr_add_admin_menu(){
		$a = $this->pagemaker_data['pagetitle'];
		$b = $this->pagemaker_data['menutitle'];
		$c = $this->pagemaker_data['capability'];
		$d = $this->pagemaker_data['slug'];
		$e = $this->pagemaker_data['icon'];
		$f = $this->pagemaker_data['position'];
		$g = $this->pagemaker_data['menuorsub'];
		$h = $this->pagemaker_data['parentmenu'];
		if($g == "menu"){			
			$page = add_menu_page($a, $b, $c, $d, array($this, 'wpr_options_page'), $e, $f);
			$page = add_submenu_page($d, $a, $b, $c, $d);
		}elseif($g == "submenu"){
			$page = add_submenu_page($h, $a, $b, $c, $d, array($this, 'wpr_options_page'), $e, $f);
		}else{return false;}
		add_action( 'load-' . $page, array($this, 'load_settings_js'));
	}
/*===
Helper functions
===*/
	public function present_vars(){
		return $this->pagemaker_data;
	}	
	public function randString($length=5, $charset='abcdefghijklmnopqrstuvwxyz23456789'){
		$str = '';//ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789
		$count = strlen($charset) - 1;
		while($length--){$str .= $charset[mt_rand(0, $count)];}
		return $str;
	}
/*===
Allows hooking in order to load styles and scripts if needed.
HOOK: do_action('wpr_pagemaker_scriptshere', $this->pagemaker_data);//Param=all data passed to class
called when $this page is loaded, can be used to enqueue scripts/styles
===*/
	public function load_settings_js(){
		do_action('wpr_pagemaker_scriptshere', $this->pagemaker_data);
	}
/*===
Registers settings, adds section, adds fields. Hook callback from contructor
HOOK: apply_filters('wpr_pagemaker_settingsinit', $this->pagemaker_data);//Param all class settings
Do what you want here. Return false to stop execution, return true to continue execution, return string to echo something and stop execution.
===*/
	public function wpr_settings_init(){
		$contin = apply_filters('wpr_pagemaker_settingsinit', true, $this->pagemaker_data);
		if($contin == false){return;}
		
		foreach($this->pagemaker_data['settings'] as $name => $value){
		  $optname = $value['optionname'];
		  if(false === get_option($optname)){add_option($optname, '');}	
		  register_setting($optname, $optname, array($this, 'wpr_validate_fields'));
		  add_settings_section('wpr_' . $optname . '_section', $value['name'], array($this, 'wpr_section_callback'), $optname);
		  $dex = 1;
		  if(empty($value['fields'])){$value['fields'] = array();}
		  foreach($value['fields'] as &$settings){//text, check, radio, textarea, select, separator
			if(!$settings['id']){$settings['id'] = 'pagemaker_' . $settings['type'] . '_' . $dex;}		  		
			$nsettings = $settings;
			$thisid =  $optname . '[' . $settings['id'] . ']';
			add_settings_field($settings['id'] , $settings['label'], array($this, 'wpr_' . $settings["type"] . '_field_render'), $optname, 'wpr_' . $optname . '_section', array('name'=>$optname, 'tagid'=> $thisid, 'id'=> $settings['id'], 'label'=> $settings['label'], 'defval'=> $settings['default_value'], 'settings'=>$nsettings));
			$dex++;
		  }
		}
	}
/*===
Methods for field type rendering.  These are called dynamically in 'add_settings_field' by way of 'wpr_{FIELD TYPE}_field_render'
$args are passed in from add_settings_field method by way of its args parameter
HOOK: apply_filters('wpr_pagemaker_{FIELD TYPE}_field', $html, $args);//$html=html that will be outputted, $args=all arguments passed to this field
===*/	
	public function wpr_text_field_render($args){ 	
		$options = get_option($args['name']);
		if(!isset($options[$args["id"]]) && !isset($args['defval'])){
				$val = "";
		}elseif(!isset($options[$args["id"]]) && isset($args['defval'])){
			$val = $args['defval'];
		}else{
			$val = $options[$args["id"]];
		}
		$html = '';
		$html .= '<p>' . $args['settings']['description'] . '</p>';
		$html .= '<input type="text" id="' . $args["tagid"] . '" name="' . $args["tagid"] . '" value="' . $val . '">';	
		echo apply_filters('wpr_pagemaker_text_field', $html, $args);
	}	
	public function wpr_check_field_render($args){ 
		$options = get_option($args['name']);
		if(!isset($options[$args["id"]]) && !isset($args['defval'])){
				$val = 0;
		}elseif(!isset($options[$args["id"]]) && isset($args['defval'])){
			$val = 0;
		}else{
			$val = $options[$args["id"]];
		}
		$html = '';
		$html .= '<p>' . $args['settings']['description'] . '</p>';
		$html .= '<input type="checkbox" id="' . $args["tagid"] . '" name="' . $args["tagid"] . '" value="1" ' . checked($val, 1, false) . '/>';
		echo apply_filters('wpr_pagemaker_check_field', $html, $args);
	}	
	public function wpr_radio_field_render($args){ 	
		$options = get_option($args['name']);
		if(!isset($options[$args["id"]]) && !isset($args['defval'])){
				$val = "";
		}elseif(!isset($options[$args["id"]]) && isset($args['defval'])){
			$val = $args['defval'];
		}else{
			$val = $options[$args["id"]];
		}
		$html = '';
		$html .= '<p>' . $args['settings']['description'] . '</p>';
		$defvals = $args['settings']['morethanone'];
		$d = 0;
		foreach($defvals as $aval){
			$html .= '<input type="radio" id="' . $args["tagid"] . '" name="' . $args["tagid"] . '" value="' . $aval["value"] . '" ' . checked($val, $aval['value'], false) . '/>';
			$html .= '<label style="margin-right:5px;" for="' . $args["tagid"] . '"> '  . $aval["label"] . '</label>';
			$d++;
		}
		echo apply_filters('wpr_pagemaker_radio_field', $html, $args);
	}	
	public function wpr_textarea_field_render($args){ 	
		$options = get_option($args['name']);
		if(!isset($options[$args["id"]]) && !isset($args['defval'])){
				$val = "";
		}elseif(!isset($options[$args["id"]]) && isset($args['defval'])){
			$val = $args['defval'];
		}else{
			$val = $options[$args["id"]];
		}
		$hmtl = '';
		$html .= '<p>' . $args['settings']['description'] . '</p>';
		$html .= '<textarea id="' . $args["tagid"] . '" name="' . $args["tagid"] . '">' . $val . '</textarea>';
		echo apply_filters('wpr_pagemaker_textarea_field', $html, $args);
	}	
	public function wpr_select_field_render($args){ 	
		$options = get_option($args['name']);
		if(!isset($options[$args["id"]]) && !isset($args['defval'])){
				$val = "";
		}elseif(!isset($options[$args["id"]]) && isset($args['defval'])){
			$val = $args['defval'];
		}else{
			$val = $options[$args["id"]];
		}
		$hmtl = '';
		$html .= '<p>' . $args['settings']['description'] . '</p>';
		$html .= '<select id="' . $args["tagid"] . '" name="' . $args["tagid"] . '">';
		$defvals = $args['settings']['morethanone'];
		$d = 0;
		foreach($defvals as $aval){
			$html .= '<option value="' . $aval["value"] . '" ';
			$html .= selected($val, $aval['value'], false);
			$html .= '>' . $aval["label"] . '</option>';
		}
		$html .= '</select>';
		echo apply_filters('wpr_pagemaker_select_field', $html, $args);
	}
	public function wpr_separator_field_render($args){ 	
		$html = '<hr/>';		
		echo apply_filters('wpr_pagemaker_separator_field', $html, $args);
	}
	public function wpr_html_field_render($args){ 	
		$html = $args['defval'];		
		echo apply_filters('wpr_pagemaker_html_field', $html, $args);
	}
	public function wpr_custom_field_render($args){ 			
		echo apply_filters('wpr_pagemaker_custom_field', $html, $args);
	}
/*===
$_POST validation after form submission.
HOOK: apply_filters('wpr_pagemaker_validate_fields', $output, $input);//$output=final output after validation; $input=input post data to be validated
===*/
	public function wpr_validate_fields($input){
		$output = array();
		foreach($input as $key => $value){
			if(isset($input[$key])){
				if(strpos($key,'textarea') !== false || strpos($key,'text') !== false){$output[$key] = sanitize_text_field($value);}
				else{$output[$key] = strip_tags(stripslashes($value));}
			}
		}
		return apply_filters('wpr_pagemaker_validate_fields', $output, $input);
	}
/*===
Section callback before form fields are displayed.
HOOK: apply_filters('wpr_pagemaker_sectiondisplay', $html, $args, $this->pagemaker_data);
//$html=html to be outputted; $args=data sent to method (sent by add_settings_section); $LastParam=all class data
===*/
	public function wpr_section_callback($args){
		$html = '';
		foreach($this->pagemaker_data['settings'] as $name => $value){
			if($value['name'] == $args['title']){
				$desc = $value['description']?$value['description']:"";
				$html .= '<p class="pagemaker_top_descript">' . $desc . '</p><br/>';
				break;
			}
		}
		echo apply_filters('wpr_pagemaker_sectiondisplay', $html, $args, $this->pagemaker_data);
	}
/*===
Display of options with/without tabs based on user input.
HOOK: apply_filters('wpr_pagemaker_pagecall_continue', true, $this->pagemaker_data);//true/false/string, Param=Class Data
return true to continue script, return false to stop execution, return string to echo string and stop execution.
HOOK: apply_filters('wpr_pagemaker_section_open', $html, $this->pagemaker_data);$html=All title and heading stuff for page, $LastParam=Class data
HOOK: do_action('wpr_pagemaker_pagecallback_end', $this->pagemaker_data, $activetab);//Param=Class data, Param=active tab name
Do stuff after page is created
===*/
	public function wpr_options_page(){
		if(!current_user_can('manage_options')){wp_die( __( 'You do not have sufficient permissions to access this page.' ) );}
		$contin = apply_filters('wpr_pagemaker_pagecall_continue', true, $this->pagemaker_data);
		if($contin == false){return;}
		if(is_string($contin)){echo $contin; return;}
		reset($this->pagemaker_data['settings']);
		$first_key = key($this->pagemaker_data['settings']);
		$activetab = $active_tab = isset($_GET['tab'])?$_GET['tab']:$first_key;		
		$num = count($this->pagemaker_data['settings']);
		
		$html = '';
		$html .= '<h2 class="pagemakertitle">' . $this->pagemaker_data['pagetitle'] . '</h2>';
		$html .= '<p class="pagemakerdesc">' . $this->pagemaker_data['heading'] . '</p>';
		$html = apply_filters('wpr_pagemaker_section_open', $html, $this->pagemaker_data);
		?>
		<div class="wrap">
		<?php screen_icon();
			echo $html;
			settings_errors();
			if($num > 1){				
				?>
				<h2 class="nav-tab-wrapper">
				<?php 
				foreach($this->pagemaker_data['settings'] as $name => $value){ ?>
					<a href="?page=<?php echo $this->pagemaker_data['slug']; ?>&tab=<?php echo $name; ?>" class="nav-tab <?php echo $active_tab == $name ? 'nav-tab-active' : ''; ?>">
					<?php echo $value['tab_name']?$value['tab_name']:$value['name']; ?>
					</a>
					<?php } ?>
				</h2>
				<?php
			}
			 /*options.php admin.php?page=<?php echo $this->pagemaker_data['slug']; ?>&tab=<?php echo $active_tab; ?>&pagemaker_update=true*/ 
			?> <form method="post" action="options.php"> <input type="hidden" /><?php
			foreach($this->pagemaker_data['settings'] as $name => $value){
				if($num >1){
					if($active_tab == $name){
						settings_fields($value['optionname']);
						do_settings_sections($value['optionname']);
					}
				}else{
					settings_fields($value['optionname']);
					do_settings_sections($value['optionname']);
				}
			}
			
			submit_button();
		?>
		</form>
		</div>
		<?php	
		do_action('wpr_pagemaker_pagecallback_end', $this->pagemaker_data, $activetab);
	}
}
}
/*Dashboard:'index.php', Posts: 'edit.php', Media:'upload.php', Links: 'link-manager.php', Pages: 'edit.php?post_type=page', Comments: 'edit-comments.php', Custom Post Types: 'edit.php?post_type=your_post_type', Appearance: 'themes.php', lugins: 'plugins.php', Users:'users.php', Tools: 'tools.php', Settings: 'options-general.php',  Settings in the Network Admin: 'settings.php'*/
 /*2 Dashboard 4 Separator 5 Posts 10 Media 15 Links 20 Pages 25 Comments 59 Separator 60 Appearance 65 Plugins 70 Users 75 Tools 80 Settings 99 Separator*/
 /*
do_action('wpr_pagemaker_scriptshere', $this->pagemaker_data);
$contin = apply_filters('wpr_pagemaker_settingsinit', true, $this->pagemaker_data);// false or string returned, will not complete page callback
$html = apply_filters('wpr_pagemaker_text_field', $html, $args);
echo apply_filters('wpr_pagemaker_check_field', $html, $args);
echo apply_filters('wpr_pagemaker_radio_field', $html, $args);
echo apply_filters('wpr_pagemaker_textarea_field', $html, $args);
echo apply_filters('wpr_pagemaker_select_field', $html, $args);
echo apply_filters('wpr_pagemaker_separator_field', $html, $args);
return apply_filters('wpr_pagemaker_validate_fields', $output, $input);
echo apply_filters('wpr_pagemaker_sectiondisplay', $html, $args, $this->pagemaker_data);
$html = apply_filters('wpr_pagemaker_section_open', $html, $this->pagemaker_data);
$contin = apply_filters('wpr_pagemaker_pagecall_continue', true, $this->pagemaker_data);// false or string returned, will not complete page callback
do_action('wpr_pagemaker_pagecallback_end', $this->pagemaker_data, $activetab);

//$shrtslug = substr($this->pagemaker_data['slug'], 0, 7);
//$shrtnm = substr(sanitize_title($name), 0, 7);
//$randstring = $this->randString(mt_rand(3, 7));
//if(!$this->pagemaker_data['settings'][$name]['optionname']){
//$optname = $shrtslug . "_" . $shrtnm . "_" . $randstring;
//$this->pagemaker_data['settings'][$name]['optionname'] = $optname;
	$settings = array(
		'section1' => array(
			'name' => '',
			'descripton' => '',
			'optionname' => '',
			'fields' => array(
				array(
					'id' => '',
					'type' => '',
					'label' => '',
					'description' => '',
					'default_value' => '',
					'morethanone' => array(
						array(
							'label'=> '',
							'value' = ''
						)
					),
					'grouped' => ''
				),
				array(
					...
				)
			)
		),
		'section2' => array(
			
		)
	)
 */

?>