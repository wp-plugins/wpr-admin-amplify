<?php
/*
This function hooks into postmeta.php to set a custom field.
The code is all contained in "one" custom field type and extended from the default postmeta class.
The fields are added as associative arrays so that all data can be saved directly to one post meta field as a serialized object.
*/
function createPagemakerFields($post_ob_name, $value, $metabox, $post, $meta_data, $xargs){
	if($meta_data['postnm'] != "wpr_made_page_data" && $meta_data['id'] != "allpagemakerdat"){return;}
		wp_enqueue_media();
		global $caparray, $pagefieldtypes;
		global $menu;/*$wp_roles;*/
		$mtet = get_post_meta($post->ID, 'wpr_made_page_data', true);
		?>
		
		<div style="width:100%;" id="wprpagemakeropts">
		<h4>Please note that the title above is required and this page will not publish until one is provided.  The title is the menu title that will be displayed in the wp admin menu.</h4>
			
			<?php $canhide = ""; if($post->post_name){$canhide = 'style="display:none;"';}?>
			
			<div id="pagemakegen" class="clicktog">
			<div class="daclicker">General Options</div>
			<div class="togglethis" <?php echo $canhide; ?>>
			
			<div class="pagemakereq">
			<div class="pmp">Will this be a top level menu or a submenu </div>
			<div class="pip"><span class="rd">*</span> 
			<?php if(!isset($mtet["menuorsub"])){$mtet["menuorsub"] = "";} ?>
			<input type="radio" name="wpr_made_page_data[menuorsub]" value="menu" <?php echo $mtet["menuorsub"] == "menu"?"checked":""; ?> /><span class="nin">Menu</span>
			<input type="radio" name="wpr_made_page_data[menuorsub]" value="submenu" <?php echo $mtet["menuorsub"] == "submenu"?"checked":""; ?> /><span class="nin">Submenu</span>
			</div>
			</div>
			
			<div class="pagemakereq">
			<div class="pmp">Parent Menu slug</div>
			<div class="pip"><select name="wpr_made_page_data[parentmenu]" >
			<?php
			if(!isset($mtet["parentmenu"])){$mtet["parentmenu"] = "";}
			foreach($menu as $mitem){
				if(!empty($mitem[0])){
					$menselct = $mtet["parentmenu"] == $mitem[2]?"selected":"";
					echo '<option value="' . $mitem[2] . '" ' . $menselct . '>' . $mitem[0] . '</option>';
				}
			}
			?>
			</select></div>
			</div>
			
			<div class="pagemakereq">
			<div class="pmp">Heading for your page.</div>
			<div class="pip"><input type="text" name="wpr_made_page_data[heading]" value="<?php echo isset($mtet["heading"])?$mtet["heading"]:""; ?>"/></div>
			</div>

			<div class="pagemakereq">
			<div class="pmp">Icon Url (16X16px image, not available for submenu pages)</div>
			<div class="pip"><input type="text" class="ad-upload" name="wpr_made_page_data[iconurl]" value="<?php echo isset($mtet["iconurl"])?$mtet["iconurl"]:""; ?>"/></div>
			</div>
			
			</div>
			</div>
			
			<div id="mainadvanced" class="clicktog">
			<div class="daclicker">Advanced Options <small>(all optional)</small></div>
			<div class="togglethis" <?php echo $canhide; ?>>
			
			<div class="pagemakereq">
			<div class="pmp">Capabilities, see <a href="http://codex.wordpress.org/Roles_and_Capabilities" target="_blank">list</a>.  By default this is set to manage_options.</div>
			<div class="pip"><select name="wpr_made_page_data[capability]">
			<?php
			$isselct = '';
			if(!isset($mtet["capability"])){$mtet["capability"] = "manage_options";}
			foreach($caparray as $acap){
					$isselct = $mtet["capability"] == $acap?"selected":"";
					echo '<option value="' . $acap . '" ' . $isselct . '>' . $acap . '</option>';
			}
			?>
			</select></div>
			</div>
			
			<div class="pagemakereq">
			<div class="pmp">Menu Position. see <a href="http://codex.wordpress.org/Function_Reference/add_menu_page#Parameters" target="_blank">menu_position</a></div>
			<div class="pip"><input type="text" name="wpr_made_page_data[position]" onkeyup="if(/\D/g.test(this.value)){this.value = this.value.replace(/\D/g,'');} if(this.value.length>3){this.value = this.value.slice(0, 3);}" value="<?php echo isset($mtet["position"])?$mtet["position"]:""; ?>"/></div>
			</div>
			
			<div class="pagemakereq">
			<div class="pmp">Priority, you can set the priority for the admin_menu action hook that calls the add_***_page method.</div>
			<div class="pip"><input type="text" name="wpr_made_page_data[priority]" onkeyup="if(/\D/g.test(this.value)){this.value = this.value.replace(/\D/g,'');} if(this.value.length>3){this.value = this.value.slice(0, 3);}" value="<?php echo isset($mtet["priority"])?$mtet["priority"]:""; ?>"/></div>
			</div>
			
			</div>
			</div>
			<?php 
			?>
			<hr/>
			<div id="pagemakesetfields">
			<p class="errmsg" style="color:red;"></p>
			<div class="sectheader"><h3>Create a Tab<br/>(if you only create one, then it will not be displayed as a tab).</h3></div>
			
			<div class="pagemakereq">
			<div class="pmp">Name of the Section (displayed inside of section)</div>
			<div class="pip"><span class="rd">*</span> <input type="text" id="sectname" /></div>
			</div>
			
			<div class="pagemakereq">
			<div class="pmp">Name of the Tab (displayed inside of tab, try to keep short. Required even if you want no tabs and will only create one section.)</div>
			<div class="pip"><span class="rd">*</span> <input type="text" id="tabname" /></div>
			</div>
			
			<div class="pagemakereq">
			<div class="pmp">Section Description (optional)</div>
			<div class="pip"><textarea class="sectdesc" id="sectdesc"></textarea></div>
			</div>
			
			<div class="pagemakereq">
			<div class="pmp">Option Name (identifier for the database, this is what you will call when you use get_option() for this section...)</div>
			<div class="pip"><span class="rd">*</span> <input type="text" id="optname" onkeyup="varvalidate(jQuery(this), 'slug');" /></div>
			</div>
			
			</div>
			<div id="makepagetab" class="amplifybutt fullbutt">Add A Tab</div>
			<div id="pagesections">			
			<?php 
			/*======================================= BEGIN SECTIONS =====================================*/			
			$sections = $mtet["sections"];
			if(empty($sections)){$sections = array();}
			foreach($sections as $k => $section){
			?>
			<div class="pagesection clicktog canbedie">
			
			<div class="daclicker sectionsclick">Section: <?php echo $section["optionname"]; ?> Tab: <?php echo $section["tab_name"]; ?></div>
			
			<div class="togglethis autohide">
			<p style="text-align:center;"><b>USE: </b> get_option("<?php echo $section["optionname"];?>");</p>
			<input type="hidden" name="wpr_made_page_data[sections][<?php echo $k; ?>][name]" value="<?php echo $section["name"];  ?>">
			<input type="hidden" name="wpr_made_page_data[sections][<?php echo $k; ?>][tab_name]" value="<?php echo $section["tab_name"];  ?>">
			<input type="hidden" name="wpr_made_page_data[sections][<?php echo $k; ?>][description]" value="<?php echo $section["description"];  ?>">
			<input type="hidden" name="wpr_made_page_data[sections][<?php echo $k; ?>][optionname]" value="<?php echo $section["optionname"];  ?>">
			
			<div class="pagemakereq">
			<div class="pmp">Create a field.</div>			
			<div class="pip">
			<select class="fselct">
			<?php
				foreach($pagefieldtypes as $pft){
					echo '<option value="' . $pft . '">' . $pft . '</option>';
				}
			?>
			</select>			
			<div class="fieldchoose amplifybutt" onclick="addSelctFields('<?php echo $k; ?>', jQuery(this).parents('.pagesection'));">Add Field</div>
			</div>
			</div>
							
			<div class="pagesectfields">
				<?php 
				if(empty($section["fields"])){$section["fields"] = array();}
				foreach($section["fields"] as $fk => $field){ 
				$cdex = str_replace("wprdex_", "", $fk);
				?>
				<div class="onefield clicktog canbedie">
				
				<div class="daclicker onefclick" >Field <span class="droodle"><?php echo $cdex;  ?></span> Type <?php echo $field["type"]  ?></div>
				
				<div class="togglethis autohide">
				<p style="text-align:center;"><b>USE: </b> $opt = get_option("<?php echo $section["optionname"];?>"); echo $opt["<?php echo $field["id"]; ?>"];</p>
				<input type="hidden" name= "wpr_made_page_data[sections][<?php echo $k; ?>][fields][<?php echo $fk; ?>][type]" value="<?php echo $field["type"]  ?>" />
				
				<div class="pagemakereq">
				<div class="pmp">Field ID (You will access this ID as a key with get_option(<?php echo $k; ?>). Once set you do not want to change.</div>
				<div class="pip">
				<input type="text" name= "wpr_made_page_data[sections][<?php echo $k; ?>][fields][<?php echo $fk; ?>][id]" onkeyup="varvalidate(jQuery(this), 'slug');" value="<?php echo $field["id"]; ?>"/>
				</div>
				</div>
				
				<div class="pagemakereq">
				<div class="pmp">Field Label (will display to the left of the field)</div>
				<div class="pip">
				<input type="text" name= "wpr_made_page_data[sections][<?php echo $k; ?>][fields][<?php echo $fk; ?>][label]" value="<?php echo $field["label"]; ?>" />
				</div>
				</div>
				
				<div class="pagemakereq">
				<div class="pmp">Default Value for this field</div>
				<div class="pip">
				<?php if($field["type"] == "html"){ ?>
				<textarea name= "wpr_made_page_data[sections][<?php echo $k; ?>][fields][<?php echo $fk; ?>][default_value]"><?php echo $field["default_value"]; ?></textarea>
				<?php }else{ ?>
				<input type="text" name= "wpr_made_page_data[sections][<?php echo $k; ?>][fields][<?php echo $fk; ?>][default_value]" value="<?php echo $field["default_value"]; ?>" />
				<?php } ?>
				</div>
				</div>
				
				<div class="pagemakereq">
				<div class="pmp">Field Descripton</div>
				<div class="pip"><input type="text" style="width: 100%;" name= "wpr_made_page_data[sections][<?php echo $k; ?>][fields][<?php echo $fk;  ?>][description]" value="<?php echo $field["description"]; ?>" />
				</div>
				</div>
				
				<?php /*name= "wpr_made_page_data[sections][<?php echo $k;  ?>][fields][<?php echo $fk;  ?>][grouped]"*/ ?>
				
				<?php 
				if($field["type"] == "select" ||$field["type"] == "radio" ||$field["type"] == "multiselect" || $field["type"] == "multicheck"){ 
				?>
					<div class="feldholder">
					<div class="amplifybutt multichoose" onclick="addMultiValue('<?php echo $k; ?>', '<?php echo $cdex; ?>', jQuery(this).parents('.feldholder'));">Add a Value</div>
					
					<?php if(empty($field["morethanone"])){ ?>
					
					<div class="multvals canbedie" style="padding-top:15px;">					
					Name: <input type="text" name= "wpr_made_page_data[sections][<?php echo $k; ?>][fields][<?php echo $fk; ?>][morethanone][wprmdex_0][label]" value=""/>					
					Value: <input type="text" name= "wpr_made_page_data[sections][<?php echo $k; ?>][fields][<?php echo $fk;?>][morethanone][wprmdex_0][value]" value=""/>				
					</div>
					
					<?php 
					} else{
					foreach($field["morethanone"] as $mk => $mv){ 
					?>
					
					<div class="multvals canbedie" style="padding-top:15px;">					
					Name: <input type="text" name= "wpr_made_page_data[sections][<?php echo $k; ?>][fields][<?php echo $fk; ?>][morethanone][<?php echo $mk; ?>][label]" value="<?php echo $mv["label"]; ?>"/>					
					Value: <input type="text" name= "wpr_made_page_data[sections][<?php echo $k; ?>][fields][<?php echo $fk;?>][morethanone][<?php echo $mk; ?>][value]" value="<?php echo $mv["value"]; ?>"/>				
					</div>
					
					<?php } } ?>
					
					</div>
					
				<?php } ?>
				</div>
				</div>
				<?php } ?>
			</div>
			
			
			</div><!--END Toggle -->
			
			</div><!--END Pagesection -->
			<?php 
			} 
			?>
			<?php /*============================ END SECTIONS ===============================*/ ?>
			
			<div id="wprtrashbin">DOP HERE TO REMOVE</div>
			</div>
			
			<script>
			jQuery(document).ready(function(){
				jQuery("#allpagemakerdat h3.hndle").removeClass("hndle");
				jQuery("#allpagemakerdat .handlediv").removeClass("handlediv");	
				wprsortpage();
				jQuery("body").on("click", ".daclicker", function(e){
					if(jQuery(this).hasClass("noClick")){return;}
					jQuery(this).closest(".clicktog").find(".togglethis").first().slideToggle();
				});
				jQuery("#wprtrashbin").droppable({
					accept: ".canbedie",
					drop: function(event, ui){	
						var obj = jQuery(ui.draggable);
						var needdofunc = false;
						var par;
						if(obj.hasClass("onefield")){needdofunc = true; par = obj.closest(".pagesectfields")}
						if(obj.hasClass("multvals")){needdofunc = true; par = obj.closest(".onefield")}
						obj.remove();	
						if(needdofunc){wpradjustfield("is", jQuery(par));}
					}
				});
				jQuery("#makepagetab").click(function(e){
					var sectname = jQuery("#sectname").val();
					var tabname = jQuery("#tabname").val();
					var sectdesc = jQuery("#sectdesc").val();
					var optname = jQuery("#optname").val();
					if((sectname == "" || sectname == null) || (tabname == "" || tabname == null) || (optname == "" || optname == null)){
						jQuery('#pagemakesetfields .errmsg').text("You must fill in all required fields.");
						return false;
					}
					var sanzdit = {action: 'dosanz', tosanitize: tabname+"|"+optname};
					jQuery.post(fromphp.jaxfile, sanzdit, function(data) {
						if(data.sanitized){
							var sectionid = data.sanitized[0];
							optname = data.sanitized[1];
							var psects = '<div class="pagesection clicktog canbedie"><div class="daclicker sectionsclick" >Section: ' + sectionid + ' Tab: ' + tabname + '</div><div class="togglethis autohide"><input type="hidden" name="wpr_made_page_data[sections][' + sectionid + '][name]" value="' + sectname + '"><input type="hidden" name="wpr_made_page_data[sections][' + sectionid + '][tab_name]" value="' + tabname + '"><input type="hidden" name="wpr_made_page_data[sections][' + sectionid + '][description]" value="' + sectdesc + '"><input type="hidden" name="wpr_made_page_data[sections][' + sectionid + '][optionname]" value="' + optname + '"><div class="pagemakereq"><div class="pmp">Create a field.</div><div class="pip"><select class="fselct"><?php
								foreach($pagefieldtypes as $pft){
									echo '<option value="' . $pft . '">' . $pft . '</option>';
								}
							?></select><div class="fieldchoose amplifybutt" onclick="addSelctFields(\'' + sectionid + '\', jQuery(this).parents(\'.pagesection\'));">Add Field</div></div></div><div class="pagesectfields"></div></div></div>';
							jQuery("#sectname").val("");
							jQuery("#tabname").val("");
							jQuery("#sectdesc").val("");
							jQuery("#optname").val("");
							jQuery('#pagemakesetfields .errmsg').text("");
							//jQuery("#pagesections").append(psects);
							jQuery(psects).insertBefore("#wprtrashbin");
							wprsortpage();
						}
					}, "json");
				});
			});
			function addSelctFields(sectionid, elm){
				var cdex = elm.find(".onefield").length;
				var tipper = elm.find(".fselct").val();
				
				var pfelds = '<div class="onefield clicktog canbedie">';
				pfelds += '<div class="daclicker onefclick">Field <span class="droodle">' + cdex + '</span> Type ' + tipper + '</div>';
				pfelds += '<div class="togglethis autohide">';
				pfelds += '<input type="hidden" name= "wpr_made_page_data[sections][' + sectionid + '][fields][wprdex_' + cdex + '][type]" value="' + tipper + '" /><div class="pagemakereq"><div class="pmp">Field ID (You will access this ID as a key with get_option(' + sectionid + '). Once set you do not want to change.</div><div class="pip"><input type="text" name= "wpr_made_page_data[sections][' + sectionid + '][fields][wprdex_' + cdex + '][id]" onkeyup="varvalidate(jQuery(this), \'slug\');" value="';
				if(tipper == "separator"){pfelds += "separator";}
				pfelds += '"/></div></div><div class="pagemakereq"><div class="pmp">Field Label (will display to the left of the field)</div><div class="pip"><input type="text" name= "wpr_made_page_data[sections][' + sectionid + '][fields][wprdex_' + cdex + '][label]" /></div></div><div class="pagemakereq"><div class="pmp">Default Value for this field</div><div class="pip">';
				
				if(tipper == "html"){
				pfelds += '<textarea name= "wpr_made_page_data[sections][' + sectionid + '][fields][wprdex_' + cdex + '][default_value]"></textarea>';
				}else{
				pfelds += '<input type="text" name= "wpr_made_page_data[sections][' + sectionid + '][fields][wprdex_' + cdex + '][default_value]" />';
				}
				pfelds += '</div></div><div class="pagemakereq"><div class="pmp">Field Descripton</div><div class="pip"><input type="text" style="width: 100%;" name= "wpr_made_page_data[sections][' + sectionid + '][fields][wprdex_' + cdex + '][description]" /></div></div>';
				//name= "wpr_made_page_data[sections][' + sectionid + '][fields][' + cdex + '][grouped]"
				if(tipper == "select" || tipper == "radio" || tipper == "multiselect" || tipper == "multicheck"){
					pfelds += '<div class="feldholder">';
					pfelds += '<div class="amplifybutt multichoose" onclick="addMultiValue(\'' + sectionid + '\', \'' + cdex + '\', jQuery(this).parents(\'.feldholder\'));">Add a Value</div>';
					pfelds += '<div class="multvals canbedie" style="padding-top:15px;">';
					pfelds += 'Name: <input type="text" name= "wpr_made_page_data[sections][' + sectionid + '][fields][wprdex_' + cdex + '][morethanone][wprmdex_0][label]" value=""/>';
					pfelds += 'Value: <input type="text" name= "wpr_made_page_data[sections][' + sectionid + '][fields][wprdex_' + cdex + '][morethanone][wprmdex_0][value]" value=""/>';					
					pfelds += '</div></div>';
				}
				pfelds += '</div></div>';
				elm.find(".pagesectfields").append(pfelds);
				wprsortpage();
			}
			function addMultiValue(sectionid, cdex, elm){
				var ndex = elm.find(".multvals canbedie").length;
				var pfelds = "";
				pfelds += '<div class="multvals canbedie" style="padding-top:15px;" data-namepart="wpr_made_page_data[sections][' + sectionid + '][fields][wprdex_' + cdex + '][morethanone]">';
				pfelds += 'Name: <input type="text" name= "wpr_made_page_data[sections][' + sectionid + '][fields][wprdex_' + cdex + '][morethanone][wprmdex_' + ndex + '][label]" value="" />';
				pfelds += 'Value: <input type="text" name= "wpr_made_page_data[sections][' + sectionid + '][fields][wprdex_' + cdex + '][morethanone][wprmdex_' + ndex + '][value]" value="" />';					
				pfelds += '</div>';
				var jayob = jQuery(pfelds);
				elm.append(jayob);
				wprsortpage();
				wpradjustmultis("under", jayob);
			}
			function wprsortpage(){
				jQuery("#pagesections").sortable({
					items: ".pagesection",
					handle: ".daclicker",
					delay: 150,
					start: function(event, ui){jQuery(ui.item).find(".sectionsclik").addClass("noClick");},
					stop: function(event, ui){setTimeout(function(){jQuery(ui.item).find(".sectionsclik").removeClass("noClick");}, 350);}
				});
				jQuery(".pagesectfields").sortable({
					items: ".onefield",
					handle: ".daclicker",
					delay: 150,
					start: function(event, ui){jQuery(ui.item).find(".onefclick").addClass("noClick");},
					stop: wpradjustfield
				});
				jQuery(".onefield").sortable({
					items: ".multvals",
					//handle: ".daclicker",
					delay: 150,
					stop: wpradjustmultis
				});
			}
			function wpradjustfield(event, ui){
				var topplace;
				if(event === "is"){topplace = ui;}else if(event === "under"){topplace = ui.closest(".pagesectfields");}else{topplace = jQuery(ui.item).closest(".pagesectfields");}
				topplace.find(".onefield").each(function(i, elm){
					jQuery(this).find("input").each(function(it, elmt){
						var cname = jQuery(this).attr("name");
						cname = cname.replace(/wprdex_[^\]]+\]/, "wprdex_" + i + "]");
						jQuery(this).attr("name", cname);
					});
					jQuery(this).find(".droodle").text(i);
				});
				setTimeout(function(){jQuery(ui.item).find(".onefclick").removeClass("noClick");}, 350);
			}
			function wpradjustmultis(event, ui){
				var topplace;
				if(event === "is"){topplace = ui;}else if(event === "under"){topplace = ui.closest(".onefield");}else{topplace = jQuery(ui.item).closest(".onefield");}
				topplace.find(".multvals").each(function(i, elm){							
					jQuery(this).find("input").each(function(it, elmt){
						var cname = jQuery(this).attr("name");
						cname = cname.replace(/wprmdex_[^\]]+\]/, "wprmdex_" + i + "]");
						jQuery(this).attr("name", cname);
					});
				});
			}
			function doAToggle(trig, par, down){
				if(trig.hasClass("noClick")){return;}
				trig.parents("."+par).find("." + down).slideToggle();
			}
			</script>
		</div>		
		<?php
}
add_action('wpr_postmeta_myfield', 'createPagemakerFields', 10, 6);
?>