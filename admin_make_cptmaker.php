<?php
/*
This function hooks into postmeta.php to set a custom field.
The code is all contained in "one" custom field type and extended from the default postmeta class.
The fields are added as associative arrays so that all data can be saved directly to one post meta field as a serialized object.
*/
function createCPTmakerFields($post_ob_name, $value, $metabox, $post, $meta_data, $xargs){
	if($meta_data['postnm'] != "wpr_made_cpt_data" && $meta_data['id'] != "allcptmakerdat"){return;}
		wp_enqueue_media();
		global $caparray;
		global $wp_post_types;		
		$mtet = get_post_meta($post->ID, 'wpr_made_cpt_data', true);
		?>
		
		<div style="width:100%;" id="wprpagemakeropts">
		<h4>Please note that the title above is required and this page will not publish until one is provided.  The title is the CPT title that will be displayed in the custom meta box area. If you are only using this to add taxonomies to a pre-existing post type, a title will still be required but it will do nothing and display nowhere.</h4>
			
			<?php $canhide = ""; if($post->post_name){$canhide = 'style="display:none;"';}?>
			
			<?php /*================ GENERAL LABELING ====================*/ ?>
			<div id="cptmakegen" class="clicktog">
			<div class="daclicker">General Options</div>
			<div class="togglethis" <?php echo $canhide; ?>>
						
			<div class="cptmakereq">
			<div class="pmp">Post Type existing</div>
			<div class="pip"><select name="wpr_made_cpt_data[typeidexisting]" >
			<option value=""></option>
			<?php
			if(!isset($mtet["posttype"])){$mtet["posttype"] = "";}
			foreach($wp_post_types as $type=>$obj){
				if($type == "revision"){continue;}
				if(!empty($type)){
					$menselct = $mtet["posttype"] == $type?"selected":"";
					echo '<option value="' . $type . '" ' . $menselct . '>' . $obj->label . '</option>';
				}
			}
			?>
			</select></div>
			</div>
			
			<h4>or Add New</h4>
			
			<div class="cptmakereq">
			<div class="pmp">
			Post Type ID (once set, you do NOT want to change this)
			</div>
			<div class="pip">
			<span class="rd">*</span> <input type="text" name="wpr_made_cpt_data[typeid]" onkeyup="varvalidate(jQuery(this), 'slug');" value="<?php echo $mtet["typeid"]; ?>" />
			</div>
			</div>
			
			<div class="cptmakereq">
			<div class="pmp">
			Post Type Singular Name
			</div>
			<div class="pip">
			<span class="rd">*</span> <input type="text" name="wpr_made_cpt_data[general_single]" value="<?php echo $mtet["general_single"]; ?>"/>
			</div>
			</div>
			
			<div class="cptmakereq">
			<div class="pmp">
			Post Type Plural Name
			</div>
			<div class="pip">
			<span class="rd">*</span> <input type="text" name="wpr_made_cpt_data[general_plural]" value="<?php echo $mtet["general_plural"]; ?>"/>
			</div>
			</div>
			
			<div class="cptmakereq">
			<div class="pmp">
			Post Type Supports (optional. default is all, choosing all or leaving blank will have the same effect)
			</div>
			<div class="pip">
			<select name="wpr_made_cpt_data[supports][]" style="min-width: 200px;" multiple>
			<option value=""></option>
			<?php
			 $supps = array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'page-attributes', 'post-formats');
			 foreach($supps as $supper){
				$slctd = "";
				if(in_array($supper, $mtet["supports"])){$slctd="selected";}
				echo '<option value="' . $supper . '" ' . $slctd . '>' . ucfirst($supper) . '</option>';
			 }
			?>
			</select>
			</div>
			</div>
			
			</div>
			</div>
			
			<?php /*================ ADVANCED OPTIONS ====================*/ ?>
			<div id="cptmakeradvanced" class="clicktog">
			<div class="daclicker">Advanced Options</div>
			<div class="togglethis" style="display:none;">
			<span style="text-align:center;display:block;">see <a href="http://codex.wordpress.org/Function_Reference/register_post_type" target="_blank">reference</a> for more information.</span>
			
			<div class="cptmakereq">
			<div class="pmp">Icon Url (16X16px image)</div>
			<div class="pip"><input type="text" class="ad-upload" name="wpr_made_cpt_data[menu_icon]" value="<?php echo isset($mtet["menu_icon"])?$mtet["menu_icon"]:""; ?>"/></div>
			</div>
			
			<div class="cptmakereq">
			<div class="pmp">Menu Position</div>
			<div class="pip"><input type="text" name="wpr_made_cpt_data[menu_position]" value="<?php echo isset($mtet["menu_position"])?$mtet["menu_position"]:""; ?>" onkeyup="if(/\D/g.test(this.value)){this.value = this.value.replace(/\D/g,'');}" /></div>
			</div>
			
			<div class="cptmakereq">
			<div class="pmp">Description</div>
			<div class="pip"><textarea name="wpr_made_cpt_data[description]" style="min-width:200px;"><?php echo isset($mtet["description"])?$mtet["description"]:""; ?></textarea></div>
			</div>
			
			<div class="cptmakereq">
			<div class="pmp">Capability Type (default is post)</div>
			<div class="pip">
			<?php if(!isset($mtet["capability_type"])){$mtet["capability_type"] = "";} ?>
			<input type="radio" name="wpr_made_cpt_data[capability_type]" value="post" <?php echo $mtet["capability_type"] == "post"?"checked":""; ?> /><span class="nin">Post</span>
			<input type="radio" name="wpr_made_cpt_data[capability_type]" value="page" <?php echo $mtet["capability_type"] == "page"?"checked":""; ?> /><span class="nin">Page</span>
			</div>
			</div>
			
			<?php 
			$advopts = array("hierarchical"=> "Hierarchical", "public"=>"Generally Public", "exclude_from_search"=>"Exclude From Search", "publicly_queryable"=>"Publicly Queryable", "show_ui"=>"Show UI", "show_in_nav_menus"=>"Show in Nav Menus (whether selectable in admin menus page)", "show_in_menu"=>"Show in Admin Menu (show_in_menu... hook to define parent)", "show_in_admin_bar"=>"Show in Admin Bar", "has_archive"=>"Have an Archive Page (hook to define custom)", "rewrite"=>"Allow Rewrites (hook to define custom)", "query_var"=>"Allow This Query Variable (hook to define custom)", "can_export"=>"Exportable");
			
			foreach($advopts as $ko => $aopt){
				if(!isset($mtet[$ko])){$mtet[$ko] = "";}
				$optsetval = $mtet[$ko] == "1"?"checked":"";
				echo '<div class="cptmakereq"><div class="pmp">' .$aopt . '</div>
				<div class="pip"><input type="radio" name="wpr_made_cpt_data[' . $ko . ']" value="1" ' . $optsetval . ' /><span class="nin">Yes</span><input type="radio" name="wpr_made_cpt_data[' . $ko . ']" value="0" ' . $optsetval . ' /><span class="nin">No</span></div></div>';
			}
			?>
			
			<div class="cptmakereq"><h4>Excluded, Must Use Hook</h4>
			<h5>In order to adjust these values, you must hook into filter (wpr_custtypes_newpostarray):</h5>
			eg.<br/><pre style="white-space:normal;">add_filter("wpr_custtypes_newpostarray", "myFunc", 10, 2);<br/>function myFunct($modifiedarray, $originalarray){... return $modifiedarray;}</pre>where <i>$modifiedarray</i> is the array constructed with all the defaults filling in any values that were omited and the <i>$originalarray</i> is the array of key/value pairs that was sent to the constructor (based on the information you have set above).
			<ul class="musthookdese"><li>show_in_menu (to use string)</li><li>has_archive (to use string)</li><li>rewrite (to use string)</li><li>query_var (to use string)</li><li>capability_type (to use string or array)</li><li>capabilities</li><li>map_meta_cap</li><li>register_meta_box_cb</li><li>taxonomies</li><li>permalink_epmask</li><li>_builtin</li><li>_edit_link</li></ul>
			</div>
			
			</div>
			</div>
			
			<?php /*================ ADVANCED LABELING ====================*/ ?>
			<div id="cptmakeradvancedl" class="clicktog">
			<div class="daclicker">Advanced Labeling</div>
			<div class="togglethis" style="display:none;">
			
			<?php 
			$advlabels = array("menu_name"=>"Menu Name", "name_admin_bar"=>"Admin Bar Name", "all_items"=>"All Items", "add_new"=>"Add New", "add_new_item"=>"Add New Item", "edit_item"=>"Edit Item", "new_item"=>"New Item", "view_item"=>"View Item", "search_items"=>"Search Items", "not_found"=>"Not Found", "not_found_in_trash"=>"Not Found in Trash", "parent_item_colon"=>"Parent Item Colon");
			
			foreach($advlabels as $lk => $lpiece){
				$lblsetval = isset($mtet[$lk])?$mtet[$lk]:"";
				echo '<div class="cptmakereq"><div class="pmp">' . $lpiece . '</div><div class="pip"><input type="text" name="wpr_made_cpt_data[' . $lk . ']" value="' . $lblsetval . '"/></div></div>';
			}
			?>
			
			</div>
			</div>
			
			<?php 
			?>
			<hr/>
			<div id="cptmaketax">
			<p class="errmsg" style="color:red;"></p>
			<div class="sectheader"><h3>You May Create Custom Taxonomies for this Post Type</h3></div>			
			
			<div class="cptmakereq">
			<div class="pmp">Taxonomy ID</div>
			<div class="pip"><span class="rd">*</span> <input type="text" id="taxid" onkeyup="varvalidate(jQuery(this), 'slug');" /></div>
			</div>
			
			<div class="cptmakereq">
			<div class="pmp">Taxonomy Singular Name</div>
			<div class="pip"><span class="rd">*</span> <input type="text" id="taxsing"  /></div>
			</div>
			
			<div class="cptmakereq">
			<div class="pmp">Taxonomy Plural Name</div>
			<div class="pip"><span class="rd">*</span> <input type="text" id="taxplur" /></div>
			</div>
			
			<div class="cptmakereq">
			<div class="pmp">Hierarchical</div>
			<div class="pip"><span class="rd">*</span> 
			<input type="radio" class="taxhierarch" value="1" /><span class="nin">Yes</span>
			<input type="radio" class="taxhierarch" value="0" /><span class="nin">No</span>
			</div>
			</div>
			
			</div>
			<div id="makepagetab" class="amplifybutt fullbutt">Add A Taxonomy</div>
			<div id="taxsections">	
						
			<?php 
			/*======================================= BEGIN SECTIONS =====================================*/
			$advtax = array("public"=>"Generally Public", "show_ui"=>"Show UI", "show_in_nav_menus"=>"Show in Navigation Menus", "show_tagcloud"=>"Show Tag Cloud", "show_admin_column"=>"Show Admin Column", "query_var"=>"Allow This Query Variable (must hook to use string)", "rewrite"=>"Allow Rewrites for this Taxonomy (must hook to user array)", "sort"=>"Remember Order in Which Terms Are Added");			
			$advtaxlabs = array("menu_name"=>"Menu Name", "all_items"=>"All Items", "edit_item"=>"Edit Item", "view_item"=>"View Item", "update_item"=>"Update Item", "add_new_item"=>"Add New Item", "new_item_name"=>"New Item Name", "parent_item"=>"Parent Item", "parent_item_colon"=>"Parent Item Colon", "search_items"=>"Search Items", "popular_items"=>"Popular Items", "separate_items_with_commas"=>"Separate Items With Commas", "add_or_remove_items"=>"Add/Remove Items", "choose_from_most_used"=>"Choose From Most Used", "not_found"=>"Not Found");
			$taxonomies = $mtet["taxonomies"];
			if(empty($taxonomies)){$taxonomies = array();}
			foreach($taxonomies as $k => $tax){
			?>
			<div class="taxsection clicktog canbedie">
			
			<div class="daclicker sectionsclick">Taxonomy <?php echo $k; ?></div>
			
			<div class="togglethis autohide">
			<input type="hidden" name="wpr_made_cpt_data[taxonomies][<?php echo $k; ?>][id]" value="<?php echo $tax["id"];  ?>" >
			
			<div class="cptmakereq">
			<div class="pmp">Taxonomy Single Label</div>
			<div class="pip">
			<input type="text" name="wpr_made_cpt_data[taxonomies][<?php echo $k; ?>][general_single]" value="<?php echo $tax["general_single"];  ?>" />
			</div>
			</div>
			
			<div class="cptmakereq">
			<div class="pmp">Taxonomy Plural Label</div>
			<div class="pip">
			<input type="text" name="wpr_made_cpt_data[taxonomies][<?php echo $k; ?>][general_plural]" value="<?php echo $tax["general_plural"];  ?>" />
			</div>
			</div>
			
			<div class="cptmakereq">
			<div class="pmp">Hierarchical</div>
			<div class="pip">
			<?php if(!isset($tax["hierarchical"])){$tax["hierarchical"] = "";} ?>
			<input type="radio" name="wpr_made_cpt_data[taxonomies][<?php echo $k; ?>][hierarchical]" value="1" <?php echo $tax["hierarchical"] == "1"?"checked":""; ?> /><span class="nin">Yes</span>
			<input type="radio" name="wpr_made_cpt_data[taxonomies][<?php echo $k; ?>][hierarchical]" value="0" <?php echo $tax["hierarchical"] == "0"?"checked":""; ?> /><span class="nin">No</span>
			</div>
			</div>
			
			<div class="taxmakeradvanced clicktog taxeach">
			<div class="daclicker onefclick">Advanced Options</div>
			<div class="togglethis" style="display:none;">
			<span style="text-align:center;display:block;">see <a href="http://codex.wordpress.org/Function_Reference/register_taxonomy" target="_blank">reference</a> for more information.</span>
			<?php
			foreach($advtax as $tko => $atopt){
				if(!isset($tax[$tko])){$tax[$tko] = "";}
				$optsetval = $tax[$tko] == "1"?"checked":"";
				echo '<div class="cptmakereq"><div class="pmp">' .$atopt . '</div>
				<div class="pip"><input type="radio" name="wpr_made_cpt_data[taxonomies][' . $k . '][' . $tko . ']" value="1" ' . $optsetval . ' /><span class="nin">Yes</span><input type="radio" name="wpr_made_cpt_data[taxonomies][' . $k . '][' . $tko . ']" value="0" ' . $optsetval . ' /><span class="nin">No</span></div></div>';
			}
			?>
			</div>
			</div>
			
			<div class="taxmakeradvancedl clicktog taxeach">
			<div class="daclicker onefclick">Advanced Labeling</div>
			<div class="togglethis" style="display:none;">
			<?php
			foreach($advtaxlabs as $ltk => $ltpiece){
				$lblsetval = isset($tax[$ltk])?$tax[$ltk]:"";
				echo '<div class="cptmakereq"><div class="pmp">' . $ltpiece . '</div><div class="pip"><input type="text" name="wpr_made_cpt_data[taxonomies][' . $k . '][' . $ltk . ']" value="' . $lblsetval . '"/></div></div>';
			}
			?>
			</div>
			</div>		
			
			</div><!--END Toggle -->			
			</div><!--END taxsection -->
			<?php 
			} 
			?>
			<?php /*============================ END SECTIONS ===============================*/ ?>
			
			<div id="wprtrashbin">DOP HERE TO REMOVE</div>
			<div class="cptmakereq"><h4>Excluded, Must Use Hook</h4>
			<h5 style="margin:0px;">In order to adjust these values, you must hook into filter (wpr_custtypes_taxarray):</h5>
			eg.<pre style="white-space:normal;margin-top:0px;">add_filter("wpr_custtypes_taxarray", "myFunc", 10, 2);<br/>function myFunct($modifiedarray, $originalarray){... return $modifiedarray;}</pre>where <i>$modifiedarray</i> is the array constructed with all the defaults filling in any values that were omited and the <i>$originalarray</i> is the array of key/value pairs that was sent to the constructor (based on the information you have set above).
			<ul class="musthookdese"><li>capabilities</li><li>meta_box_cb</li><li>update_count_callback<li>_builtin</li></ul>
			</div>
			</div>
			
			<script>
			jQuery(document).ready(function(){
				jQuery("#allcptmakerdat h3.hndle").removeClass("hndle");
				jQuery("#allcptmakerdat .handlediv").removeClass("handlediv");	
				wprsortpage();
				jQuery("body").on("click", ".daclicker", function(e){
					if(jQuery(this).hasClass("noClick")){return;}
					jQuery(this).closest(".clicktog").find(".togglethis").first().slideToggle();
				});
				jQuery("#wprtrashbin").droppable({
					accept: ".canbedie",
					drop: function(event, ui){	
						var obj = jQuery(ui.draggable);
						obj.remove();
						//if(obj.hasClass("taxsection"){}											
					}
				});
				jQuery("#makepagetab").click(function(e){
					var taxid = jQuery("#taxid").val();
					var taxsing = jQuery("#taxsing").val();
					var taxplur = jQuery("#taxplur").val();
					var taxhierarch = jQuery("input.taxhierarch:checked").val();
					if((taxid == "" || taxid == null) || (taxsing == "" || taxsing == null) || (taxplur == "" || taxplur == null) || (taxhierarch == "" || taxhierarch == null)){
						jQuery('#cptmaketax .errmsg').text("You must fill in all required fields.");
						return false;
					}
					var sanzdit = {action: 'dosanz', tosanitize: taxid};
					jQuery.post(fromphp.jaxfile, sanzdit, function(data) {
						if(data.sanitized){
							var sectionid = data.sanitized;
							var psects = '<div class="taxsection clicktog canbedie">';
							psects += '<div class="daclicker sectionsclick">Taxonomy ' + sectionid + '</div>';
							psects += '<div class="togglethis autohide">';
							
							psects += '<input type="hidden" name="wpr_made_cpt_data[taxonomies][' + sectionid + '][id]" value="' + sectionid +  '" >';
							
							psects += '<div class="cptmakereq"><div class="pmp">Taxonomy Single Label</div><div class="pip"><input type="text" name="wpr_made_cpt_data[taxonomies][' + sectionid + '][general_single]" value="' + taxsing + '"></div></div>';
							
							psects += '<div class="cptmakereq"><div class="pmp">Taxonomy Plural Label</div><div class="pip"><input type="text" name="wpr_made_cpt_data[taxonomies][' + sectionid + '][general_plural]" value="' + taxplur + '"></div></div>';
							
							psects += '<div class="cptmakereq">';
							psects += '<div class="pmp">Hierarchical</div>';
							psects += '<div class="pip">';
							psects += '<input type="radio" name="wpr_made_cpt_data[taxonomies][' + sectionid + '][hierarchical]" value="1" ';
							if(taxhierarch == "1"){psects += 'checked';}
							psects += '/><span class="nin">Yes</span>';
							psects += '<input type="radio" name="wpr_made_cpt_data[taxonomies][' + sectionid + '][hierarchical]" value="0" ';
							if(taxhierarch == "0"){psects += 'checked';}
							psects += '/><span class="nin">No</span>';
							psects += '</div></div>';
							
							psects += '<div class="taxmakeradvanced clicktog taxeach">';
							psects += '<div class="daclicker onefclick">Advanced Options</div>';
							psects += '<div class="togglethis" style="display:none;">';
							psects += '<span style="text-align:center;display:block;">see <a href="http://codex.wordpress.org/Function_Reference/register_taxonomy" target="_blank">reference</a> for more information.</span>';
							<?php
							foreach($advtax as $tko => $atopt){
								echo 'psects += \'<div class="cptmakereq"><div class="pmp">' . $atopt . '</div><div class="pip"><input type="radio" name="wpr_made_cpt_data[taxonomies][\' + sectionid + \'][' . $tko . ']" value="1"/><span class="nin">Yes</span><input type="radio" name="wpr_made_cpt_data[taxonomies][\' + sectionid + \'][' . $tko . ']" value="0"/><span class="nin">No</span></div></div>\';';
							}
							?>
							psects += '</div></div>';
							
							psects += '<div class="taxmakeradvancedl clicktog taxeach">';
							psects += '<div class="daclicker onefclick">Advanced Labeling</div>';
							psects += '<div class="togglethis" style="display:none;">';
							<?php
							foreach($advtaxlabs as $ltk => $ltpiece){
								echo 'psects += \'<div class="cptmakereq"><div class="pmp">' . $ltpiece . '</div><div class="pip"><input type="text" name="wpr_made_cpt_data[taxonomies][\' + sectionid + \'][' . $ltk . ']" value=""/></div></div>\';';
							}
							?>
							psects += '</div></div>';
							
							psects += '</div></div>';
							
							jQuery("#taxid").val("");
							jQuery("#taxsing").val("");
							jQuery("#taxplur").val("");
							jQuery("input.taxhierarch").prop('checked', false);
							jQuery('#cptmaketax .errmsg').text("");
							//jQuery("#taxsections").append(psects);
							jQuery(psects).insertBefore("#wprtrashbin");
							wprsortpage();
						}
					}, "json");
				});
			});
			function wprsortpage(){
				jQuery("#taxsections").sortable({
					items: ".taxsection",
					handle: ".daclicker",
					delay: 150,
					start: function(event, ui){jQuery(ui.item).find(".sectionsclik").addClass("noClick");},
					stop: function(event, ui){setTimeout(function(){jQuery(ui.item).find(".sectionsclik").removeClass("noClick");}, 350);}
				});
			}
			</script>
		</div>		
		<?php
}
add_action('wpr_postmeta_myfield', 'createCPTmakerFields', 10, 6);
?>