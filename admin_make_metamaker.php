<?php
/*
This function hooks into postmeta.php to set a custom field/meta area.
The code is all contained in "one" custom field type and extended from the default postmeta class.
The fields are added as associative arrays so that all data can be saved directly to one post meta field as a serialized object.
*/
function createMetamakerFields($post_ob_name, $value, $metabox, $post, $meta_data, $xargs){
	if($meta_data['postnm'] != "wpr_made_meta_data" && $meta_data['id'] != "allmetamakerdat"){return;}
		wp_enqueue_media();
		global $wp_post_types, $metafieldtypes;//$caparray, 
		//$fields, $id, $title, $postnm, $type(posttype), $context='normal', $priority='high', $margs=array()
		/*
		$wprPagemaker = array(array("name" => __('Create your page', 'wpr'), "desc" => "", "id" => "", "std" => "", "type" => "wprpagemaker"));
		$makepageMet = new GeneratePostMeta($wprPagemaker, "allpagemakerdat", "WPR Page Maker", "wpr_made_page_data", 'wprmakepages');
		
		datepicker, timepicker, radio (Yes|value,Yes|value,No|value), select (Yes|value,Yes|value,No|value), text (* "value" => "decimal" *), textarea, textwys, separator(id=separter), file, multilist, advancedlist (* "sections" => "Event,Description,Start,End" *), sidebars, check ("no|yes|no" second val is checked third is not checked
		
		*/
		$mtet = get_post_meta($post->ID, 'wpr_made_meta_data', true);
		//print_r($mtet["sections"]);

		?>
		
		<div style="width:100%;" id="wprmetamakeropts">
		<h4>Please note that the title above is required and this page will not publish until one is provided.  The title is the Meta Field title that will be displayed above the meta box.</h4>
			
			<?php $canhide = ""; if($post->post_name){$canhide = 'style="display:none;"';}?>
			
			<div id="metamakegen" class="clicktog">
			<div class="daclicker">General Options</div>
			<div class="togglethis" <?php echo $canhide; ?>>
						
			<div class="metamakereq">
			<div class="pmp">Post Type</div>
			<div class="pip"><span class="rd">*</span> <select name="wpr_made_meta_data[posttype]" >
			<?php
			if(!isset($mtet["posttype"])){$mtet["posttype"] = "post";}
			foreach($wp_post_types as $type=>$obj){
				if($type == "revision"){continue;}
				if(!empty($type)){
					$menselct = $mtet["posttype"] == $type?"selected":"";
					echo '<option value="' . $type . '" ' . $menselct . '>' . $obj->label . '</option>';
				}
			}
			?>
			</select>
			</div>
			</div>
			
			<?php //$fields, $id, $title, $postnm, $type, $context='normal', $priority='high', $margs=array() ?>
			
			</div>
			</div>
			
			
			<div id="cptmakeradvanced" class="clicktog">
			<div class="daclicker">Advanced Options</div>
			<div class="togglethis" <?php echo $canhide; ?>>
			
			<div class="metamakereq">
			<div class="pmp">Context (location of of the field).</div>
			<div class="pip"><select name="wpr_made_meta_data[context]" >			
			<option value="normal" <?php $defiver = $mtet["context"] == "normal"?"selected":""; echo $defiver;?>>Normal</option>
			<option value="advanced" <?php $defiver = $mtet["context"] == "advanced"?"selected":""; if(empty($mtet["context"])){$defiver = "selected";} echo $defiver;?>>Advanced</option>
			<option value="side" <?php $defiver = $mtet["context"] == "side"?"selected":""; echo $defiver;?>>Side</option>
			</select></div>
			</div>
			
			<div class="metamakereq">
			<div class="pmp">Priority (see <a href="http://codex.wordpress.org/Function_Reference/add_meta_box" target="_blank">add_meta_box</a>)</div>
			<div class="pip"><select name="wpr_made_meta_data[priority]" >			
			<option value="high" <?php $defiver = $mtet["priority"] == "high"?"selected":""; echo $defiver;?>>High</option>
			<option value="core" <?php $defiver = $mtet["priority"] == "core"?"selected":""; echo $defiver;?>>Core</option>
			<option value="default" <?php $defiver = $mtet["priority"] == "default"?"selected":""; if(empty($mtet["priority"])){$defiver = "selected";} echo $defiver;?>>Default</option>
			<option value="low" <?php $defiver = $mtet["priority"] == "low"?"selected":""; echo $defiver;?>>Low</option>
			</select></div>
			</div>
			
			<div class="metamakereq">
			<div class="pmp">Advanced Arguments (comma separated list of key:value, eg. secondary_default:some value,organization:Trusty Co )</div>
			<div class="pip"><input type="text" name="wpr_made_meta_data[advancedargs]" value="<?php echo isset($mtet["advancedargs"])?$mtet["advancedargs"]:""; ?>"/></div>
			</div>
			
			</div>
			</div>
			
			<?php 
			?>
			<hr/>
			<div id="metamakersetfields">
			<p class="errmsg" style="color:red;"></p>
			<div class="sectheader"><h3>Create a Custom Meta Field</h3></div>			
			
			<div class="metamakereq">
			<div class="pmp">Field ID (identifier for the database, this is what you will call when you use get_post_meta() for a post of this post type...)</div>
			<div class="pip"><span class="rd">*</span> <input type="text" id="mfieldid" onkeyup="varvalidate(jQuery(this), 'slug');" /></div>
			</div>
			
			<div class="metamakereq">
			<div class="pmp">Field Type see Amplify Information page for details</div>
			<div class="pip"><span class="rd">*</span> 
			<select id="mfieldtpe" class="fselct">
			<?php
				foreach($metafieldtypes as $mtep => $meng){
					$defsectl = "";
					if($mtep == "text"){$defsectl = "selected";}
					echo '<option value="' . $mtep . '" ' . $defsectl . '>' . $meng . '</option>';
				}
			?>
			</select>
			</div>
			</div>
			
			</div>
			<div id="makemetafield" class="amplifybutt fullbutt">Add A Field</div>
			<div id="metasections">			
			<?php 
			/*======================================= BEGIN SECTIONS =====================================*/			
			$fields = $mtet["fields"];
			if(empty($fields)){$fields = array();}
			foreach($fields as $k => $field){
			?>
			<div class="metasection clicktog canbedie">
			
			<div class="daclicker sectionsclick">Meta Field <?php echo $k; ?> Type <?php echo $field["type"]; ?></div>
			
			<div class="togglethis autohide">
			<?php echo doMetaFields($k, $field); ?>
			</div><!--END Toggle -->
			
			</div><!--END metasection -->
			<?php 
			} 
			?>
			<?php /*============================ END SECTIONS ===============================*/ ?>
			
			<div id="wprtrashbin">DOP HERE TO REMOVE</div>
			</div>
			
			<script>
			jQuery(document).ready(function(){
				jQuery("#allmetamakerdat h3.hndle").removeClass("hndle");
				jQuery("#allmetamakerdat .handlediv").removeClass("handlediv");	
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
						if(obj.hasClass("onefield")){needdofunc = true; par = obj.closest(".metasection")}
						obj.remove();	
						if(needdofunc){wpradjustfield("is", jQuery(par));}
					}
				});
				jQuery("#makemetafield").click(function(e){
					var fieldid = jQuery("#mfieldid").val();
					var fieldtype = jQuery("#mfieldtpe").val();
					if((fieldid == "" || fieldid == null) || (fieldtype == "" || fieldtype == null)){
						jQuery('#metamakersetfields .errmsg').text("You must fill in all required fields.");
						return false;
					}
					var sanzdit = {action: 'dosanz', tosanitize: fieldid};
					jQuery.post(fromphp.jaxfile, sanzdit, function(data) {
						if(data.sanitized){
							var sectionid = data.sanitized;
							jQuery("#mfieldid").val("");
							jQuery('#metamakersetfields .errmsg').text("");
							jQuery.post(fromphp.jaxfile, {action: "metjax", ftype: fieldtype, fid: sectionid}, function(hmel){
								var psects = "";
								psects += '<div class="metasection clicktog canbedie"><div class="daclicker sectionsclick">Meta Field ' + sectionid + ' Type '  + fieldtype + '</div>';			
								psects += '<div class="togglethis autohide">';
								psects += hmel;
								psects += '</div><!--END Toggle -->'			
								psects += '</div><!--END metasection -->';
								jQuery(psects).insertBefore("#wprtrashbin");
								wprsortpage();
							}, "html");
						}
					}, "json");
				});
				jQuery(document).on("click", ".optionadder", function(e){
					var curfield = jQuery(this).closest(".metasection");
					var optnamer = curfield.find(".optadrname").val();
					var optvaler = curfield.find(".optadrval").val();
					if((optnamer == "" || optnamer == null) || (optvaler == "" || optvaler == null)){
						jQuery('#saywhenwrong').text("You must fill in all required fields.");
						return false;
					}
					var returnhmel = '';
					returnhmel += '<div class="onefield clicktog canbedie">';
					returnhmel += '<div class="daclicker onefclick">Option <span class="droodle">' + optnamer + '</span></div>';
					returnhmel += '<div class="togglethis autohide">';
					returnhmel += '<div class="metamakereq">';
					returnhmel += '<div class="pmp">Option Label</div>';
					returnhmel += '<div class="pip">';
					returnhmel += '<input class="hereliesnam" type="text" value="';
					returnhmel += optnamer;
					returnhmel += '"/>';
					returnhmel += '</div><div style="clear:both;"></div>';
					returnhmel += '<div class="pmp">Option Value</div>';
					returnhmel += '<div class="pip">';
					returnhmel += '<input class="hereliesval" type="text" value="';
					returnhmel += optvaler;
					returnhmel += '"/>';
					returnhmel += '</div>';
					returnhmel += '</div></div></div>';
					curfield.find(".optadrname").val("");
					curfield.find(".optadrval").val("");
					jQuery('#saywhenwrong').text("");
					curfield.find(".placeoptions").append(returnhmel);
					wpradjustfield("under", jQuery(this));
				});
				jQuery(document).on("keyup", ".hereliesnam, .hereliesval", function(e){
					wpradjustfield("under", jQuery(this));
				});
				jQuery(document).on("keyup", ".checkdef, .checkchckdval, .checkunval", function(e){
					var toper = jQuery(this).closest(".metasection");
					var cdef = toper.find(".checkdef").val();
					var ccheck = toper.find(".checkchckdval").val();
					var cuncheck = toper.find(".checkunval").val();
					var fincheck = cdef + "|" + ccheck + "|" + cuncheck;
					toper.find(".chckstad").val(fincheck);
				});
			});
			function wprsortpage(){
				jQuery("#metasections").sortable({
					items: ".metasection",
					handle: ".daclicker",
					delay: 150,
					start: function(event, ui){jQuery(ui.item).find(".sectionsclik").addClass("noClick");},
					stop: function(event, ui){setTimeout(function(){jQuery(ui.item).find(".sectionsclik").removeClass("noClick");}, 350);}
				});
				jQuery(".metasection").sortable({
					items: ".onefield",
					handle: ".daclicker",
					delay: 150,
					start: function(event, ui){jQuery(ui.item).find(".onefclick").addClass("noClick");},
					stop: wpradjustfield
				});
			}
			function wpradjustfield(event, ui){
				var topplace;
				if(event === "is"){topplace = ui;}else if(event === "under"){topplace = ui.closest(".metasection");}else{topplace = jQuery(ui.item).closest(".metasection");}
				var compval = "";
				var niput = topplace.find(".herestand");
				var defv = topplace.find(".defvaljav").val();
				defv = defv.replace(/&+$/, '');
				defv = defv.replace(/ +$/, '');
				compval +=  defv;
				topplace.find(".onefield").each(function(i, elm){
						var hrn = jQuery(elm).find(".hereliesnam");
						var hrv = jQuery(elm).find(".hereliesval");
						if(hrn && hrv && hrn.val() != undefined && hrv.val() != undefined){
							compval += "," + hrn.val() + "|" + hrv.val();
						}
				});
				niput.val(compval);
				setTimeout(function(){topplace.find(".onefclick").removeClass("noClick");}, 350);
			}
			</script>
		</div>		
		<?php
}
add_action('wpr_postmeta_myfield', 'createMetamakerFields', 10, 6);

add_action("wp_ajax_metjax", "metFieldJax");
function metFieldJax(){
	echo doMetaFields($_POST['fid'], array("id"=>$_POST['fid'], "type"=>$_POST["ftype"]));
	die();
}
function doMetaFields($k, $field){/*id, type*/
	$returnhmel = "";
		$returnhmel .= '<p  style="text-align:center;" class="metadef"><b>USE:</b> get_post_meta({POSTID}, "' . $k . '", true)</p>';
		$returnhmel .= '<input type="hidden" name="wpr_made_meta_data[fields][' . $k . '][id]" value="' . $field["id"] . '">';
		$returnhmel .= '<input type="hidden" name="wpr_made_meta_data[fields][' . $k . '][type]" value="' . $field["type"] . '">';
		
		$returnhmel .= '<div class="metamakereq">';
		$returnhmel .= '<div class="pmp">Name of Field</div>';
		$returnhmel .= '<div class="pip">';
		$returnhmel .= '<input type="text" name="wpr_made_meta_data[fields][' . $k . '][name]" value="';
		$returnhmel .= isset($field["name"])?$field["name"]:"";
		$returnhmel .= '"/>';
		$returnhmel .= '</div>';
		$returnhmel .= '</div>';
		
		$returnhmel .= '<div class="metamakereq">';
		$returnhmel .= '<div class="pmp">Description of Field</div>';
		$returnhmel .= '<div class="pip">';
		$returnhmel .= '<input type="text" name="wpr_made_meta_data[fields][' . $k . '][desc]" value="';
		$returnhmel .= isset($field["desc"])?$field["desc"]:"";
		$returnhmel .= '"/>';
		$returnhmel .= '</div>';
		$returnhmel .= '</div>';
		
		$returnhmel .= '<div id="saywhenwrong" style="clear:both;color:red;text-align:center;"></div>';
		 /*
		"name" => __('Order', 'vp'),
		"desc" => __("", 'vp'),
		"id" => "team_order",
		"std" => "",
		"type" => "text",
		"decimal" => "true"
		//datepicker, timepicker, radio (Yes|value,Yes|value,No|value), select (Yes|value,Yes|value,No|value), text (* "decimal" => "true" *), textarea, textwys, separator(id=separter), file, multilist, advancedlist (* "sections" => "Event,Description,Start,End" *), sidebars, check ("no|yes|no" second val is checked third is not checked)
		*/
		switch($field["type"]){
			case "radio":
			case "select":
				$returnhmel .= '<input class="herestand" type="hidden" name="wpr_made_meta_data[fields][' . $k . '][std]" value="';
				$returnhmel .= isset($field["std"])?$field["std"]:"";
				$returnhmel .= '"/>';
				
				$raytrace = explode(",", $field["std"]);
				if(empty($raytrace)){$raytrace = array();$defv = "";}
				else{$defv = $raytrace[0];}

				$returnhmel .= '<div class="metamakereq">';
				$returnhmel .= '<div class="pmp">Default Value (this should be identical to one of the option values below)</div>';
				$returnhmel .= '<div class="pip">';
				$returnhmel .= '<input type="text" class="defvaljav" value="';
				$returnhmel .= isset($defv)?$defv:"";
				$returnhmel .= '"/>';
				$returnhmel .= '</div>';
				$returnhmel .= '</div>';
				
				$returnhmel .= '<div class="placeoptions">';
				if(count($raytrace) > 1){$rayi = 0;foreach($raytrace as $tracer){ 
					if($rayi === 0){$rayi++; continue;}
					$traceparts = explode("|", $tracer);
					$returnhmel .= '<div class="onefield clicktog canbedie">';
					$returnhmel .= '<div class="daclicker onefclick">Option <span class="droodle">' . $traceparts[1] . '</span></div>';
					$returnhmel .= '<div class="togglethis autohide">';
					$returnhmel .= '<div class="metamakereq">';
					$returnhmel .= '<div class="pmp">Option Label</div>';
					$returnhmel .= '<div class="pip">';
					$returnhmel .= '<input class="hereliesnam" type="text" value="';
					$returnhmel .= isset($traceparts[0])?$traceparts[0]:"";
					$returnhmel .= '"/>';
					$returnhmel .= '</div><div style="clear:both;"></div>';
					$returnhmel .= '<div class="pmp">Option Value</div>';
					$returnhmel .= '<div class="pip">';
					$returnhmel .= '<input class="hereliesval" type="text" value="';
					$returnhmel .= isset($traceparts[1])?$traceparts[1]:"";
					$returnhmel .= '"/>';
					$returnhmel .= '</div>';
					$returnhmel .= '</div></div></div>';
					$rayi++;
				}}
				$returnhmel .= '</div>';
				
				$returnhmel .= '<hr/><div style="text-align:center;clear:both;">Create Options</div><hr/>';
				
				$returnhmel .= '<div class="metamakereq">';
				$returnhmel .= '<div class="pmp">Add Option Label</div>';
				$returnhmel .= '<div class="pip">';
				$returnhmel .= '<input class="optadrname" type="text" value=""/>';
				$returnhmel .= '</div>';
				$returnhmel .= '<div style="clear:both;"></div>';
				$returnhmel .= '<div class="pmp">Add Option Value</div>';
				$returnhmel .= '<div class="pip">';
				$returnhmel .= '<input class="optadrval" type="text" value=""/>';
				$returnhmel .= '</div>';
				$returnhmel .= '</div>';
				
				$returnhmel .= '<div class="optionadder amplifybutt multichoose">Add Option</div>';
			break;
			case "multicheck":
			case "multiselect":
				$returnhmel .= '<input class="herestand" type="hidden" name="wpr_made_meta_data[fields][' . $k . '][std]" value="';
				$returnhmel .= isset($field["std"])?$field["std"]:"";
				$returnhmel .= '"/>';
				
				$raytrace = explode(",", $field["std"]);
				if(empty($raytrace)){$raytrace = array();$defv = "";}
				else{$defv = $raytrace[0];}

				$returnhmel .= '<div class="metamakereq">';
				$returnhmel .= '<div class="pmp">Default Values (match values below. list separated by the & character.  No commas allowed.)</div>';
				$returnhmel .= '<div class="pip">';
				$returnhmel .= '<input type="text" class="defvaljav" value="';
				$returnhmel .= isset($defv)?$defv:"";
				$returnhmel .= '" onkeyup="if(/,/g.test(this.value)){this.value = this.value.replace(/,/g,\'&\');}"/>';
				$returnhmel .= '</div>';
				$returnhmel .= '</div>';
				
				$returnhmel .= '<div class="placeoptions">';
				if(count($raytrace) > 1){$rayi = 0;foreach($raytrace as $tracer){ 
					if($rayi === 0){$rayi++; continue;}
					$traceparts = explode("|", $tracer);
					$returnhmel .= '<div class="onefield clicktog canbedie">';
					$returnhmel .= '<div class="daclicker onefclick">Option <span class="droodle">' . $traceparts[1] . '</span></div>';
					$returnhmel .= '<div class="togglethis autohide">';
					$returnhmel .= '<div class="metamakereq">';
					$returnhmel .= '<div class="pmp">Option Label</div>';
					$returnhmel .= '<div class="pip">';
					$returnhmel .= '<input class="hereliesnam" type="text" value="';
					$returnhmel .= isset($traceparts[0])?$traceparts[0]:"";
					$returnhmel .= '"/>';
					$returnhmel .= '</div><div style="clear:both;"></div>';
					$returnhmel .= '<div class="pmp">Option Value</div>';
					$returnhmel .= '<div class="pip">';
					$returnhmel .= '<input class="hereliesval" type="text" value="';
					$returnhmel .= isset($traceparts[1])?$traceparts[1]:"";
					$returnhmel .= '"/>';
					$returnhmel .= '</div>';
					$returnhmel .= '</div></div></div>';
					$rayi++;
				}}
				$returnhmel .= '</div>';
				
				$returnhmel .= '<hr/><div style="text-align:center;clear:both;">Create Options</div><hr/>';
				
				$returnhmel .= '<div class="metamakereq">';
				$returnhmel .= '<div class="pmp">Add Option Label</div>';
				$returnhmel .= '<div class="pip">';
				$returnhmel .= '<input class="optadrname" type="text" value=""/>';
				$returnhmel .= '</div>';
				$returnhmel .= '<div style="clear:both;"></div>';
				$returnhmel .= '<div class="pmp">Add Option Value</div>';
				$returnhmel .= '<div class="pip">';
				$returnhmel .= '<input class="optadrval" type="text" value=""/>';
				$returnhmel .= '</div>';
				$returnhmel .= '</div>';
				
				$returnhmel .= '<div class="optionadder amplifybutt multichoose">Add Option</div>';
			break;
			case "check":
				$returnhmel .= '<input class="chckstad" type="hidden" name="wpr_made_meta_data[fields][' . $k . '][std]" value="';
				$returnhmel .= isset($field["std"])?$field["std"]:"";
				$returnhmel .= '"/>';
				
				$defchk = explode("|", $field["std"]);
				if(empty($defchk)){$defchck[0] = ""; $defchck[1] = "";}
				
				$returnhmel .= '<div style="clear:both;width: 100%;display:block;">This is a single checkbox, which represents a two val system.  Checked value represents the value when the checkbox is in the checked state and Unchecked value represents the value when in the unchecked state.  The default value dictates whether the checkbox will be checked or unchecked by default and the default value should be identical to either the checked or unchecked value.</div>';
				
				$returnhmel .= '<div class="metamakereq">';
				$returnhmel .= '<div class="pmp">Default Value</div>';
				$returnhmel .= '<div class="pip">';
				$returnhmel .= '<input class="checkdef" type="text" value="';
				$returnhmel .= isset($defchk[0])?$defchk[0]:"";
				$returnhmel .= '"/>';
				$returnhmel .= '</div>';				
				$returnhmel .= '</div>';
				
				$returnhmel .= '<div class="metamakereq">';
				$returnhmel .= '<div class="pmp">Checked Value</div>';
				$returnhmel .= '<div class="pip">';
				$returnhmel .= '<input class="checkchckdval" type="text" value="';
				$returnhmel .= isset($defchk[1])?$defchk[1]:"";
				$returnhmel .= '"/>';
				$returnhmel .= '</div>';				
				$returnhmel .= '</div>';
				
				$returnhmel .= '<div class="metamakereq">';
				$returnhmel .= '<div class="pmp">Unchecked Value</div>';
				$returnhmel .= '<div class="pip">';
				$returnhmel .= '<input class="checkunval" type="text" value="';
				$returnhmel .= isset($defchk[2])?$defchk[2]:"";
				$returnhmel .= '"/>';
				$returnhmel .= '</div>';
				$returnhmel .= '</div>';

			break;
			case "text": 
				$returnhmel .= '<div class="metamakereq">';
				$returnhmel .= '<div class="pmp">Default Value (if set will force empty values to default)</div>';
				$returnhmel .= '<div class="pip">';
				$returnhmel .= '<input type="text" name="wpr_made_meta_data[fields][' . $k . '][std]" value="';
				$returnhmel .= isset($field["std"])?$field["std"]:"";
				$returnhmel .= '"/>';
				$returnhmel .= '</div>';
				$returnhmel .= '</div>';
				
				$returnhmel .= '<div class="metamakereq">';
				$returnhmel .= '<div class="pmp">Placeholder</div>';
				$returnhmel .= '<div class="pip">';
				$returnhmel .= '<input type="text" name="wpr_made_meta_data[fields][' . $k . '][placeholder]" value="';
				$returnhmel .= isset($field["placeholder"])?$field["placeholder"]:"";
				$returnhmel .= '"/>';
				$returnhmel .= '</div>';
				$returnhmel .= '</div>';
		
				$returnhmel .= '<div class="metamakereq">';
				$returnhmel .= '<div class="pmp">Force Decimal (disallow text, this field will require a number)</div>';
				$returnhmel .= '<div class="pip">';
				if(!isset($field["decimal"])){$field["decimal"] = "";}
				$returnhmel .= '<input type="radio" name="wpr_made_meta_data[fields][' . $k . '][decimal]" value="true" ';
				$returnhmel .= $field["decimal"] == "true"?"checked":"";
				$returnhmel .= '/><span class="nin">Yes</span>';
				$returnhmel .= '<input type="radio" name="wpr_made_meta_data[fields][' . $k . '][decimal]" value="false" ';
				$returnhmel .= $field["decimal"] == "false"?"checked":"";
				if(empty($field["decimal"])){ $returnhmel .= "checked";}
				$returnhmel .= ' /><span class="nin">No</span>';
				$returnhmel .= '</div>';
				$returnhmel .= '</div>';
			break;
			case "advancedlist":
				$returnhmel .= '<div class="metamakereq">';
				$returnhmel .= '<div class="pmp">Comma separated list of the section titles for this advanced list</div>';
				$returnhmel .= '<div class="pip">';
				$returnhmel .= '<input type="text" name="wpr_made_meta_data[fields][' . $k . '][sections]" value="';
				$returnhmel .= isset($field["sections"])?$field["sections"]:"";
				$returnhmel .= '"/>';
				$returnhmel .= '</div>';
				$returnhmel .= '</div>';
			break;
			case "calltoaction":
				/*"id" => '{id of button}',"attrdata" => 'data-test="testdata" ...',"action" => {ajax action method call},"title" => button title,"otherfields" => {id list of fields with values (.val()) to pass comma separated,"type" => 'calltoaction'*/
				$returnhmel .= '<div class="metamakereq">';
				$returnhmel .= '<div class="pmp">Button Title</div>';
				$returnhmel .= '<div class="pip">';
				$returnhmel .= '<input type="text" name="wpr_made_meta_data[fields][' . $k . '][title]" value="';
				$returnhmel .= isset($field["title"])?$field["title"]:"";
				$returnhmel .= '"/>';
				$returnhmel .= '</div>';
				$returnhmel .= '</div>';
				
				$returnhmel .= '<div class="metamakereq">';
				$returnhmel .= '<div class="pmp">Data attributes (add data-?="blahblah" data-??="something", etc.) for use in your javascript</div>';
				$returnhmel .= '<div class="pip">';
				$returnhmel .= '<input type="text" name="wpr_made_meta_data[fields][' . $k . '][attrdata]" value="';
				$returnhmel .= isset($field["attrdata"])?$field["attrdata"]:"";
				$returnhmel .= '"/>';
				$returnhmel .= '</div>';
				$returnhmel .= '</div>';
				
				$returnhmel .= '<div class="metamakereq">';
				$returnhmel .= '<div class="pmp">Action callback (the wp ajax action callback) see <a href="http://codex.wordpress.org/Plugin_API/Action_Reference/wp_ajax_%28action%29" target="_blank">wp ajax</a></div>';
				$returnhmel .= '<div class="pip">';
				$returnhmel .= '<input type="text" name="wpr_made_meta_data[fields][' . $k . '>][action]" value="';
				$returnhmel .= isset($field["action"])?$field["action"]:"";
				$returnhmel .= '"/>';
				$returnhmel .= '</div>';
				$returnhmel .= '</div>';
				
				$returnhmel .= '<div class="metamakereq">';
				$returnhmel .= '<div class="pmp">Comma separated list of id\'s (from existing fields you\'ve created alongside this) that have a javascript .val() you wish to obtain.</div>';
				$returnhmel .= '<div class="pip">';
				$returnhmel .= '<input type="text" name="wpr_made_meta_data[fields][' . $k . '][otherfields]" value="';
				$returnhmel .= isset($field["otherfields"])?$field["otherfields"]:"";
				$returnhmel .= '"/>';
				$returnhmel .= '</div>';
				$returnhmel .= '</div>';
			break;
			case "textarea":
			case "datepicker":
			case "timepicker":
			case "file":
				$returnhmel .= '<div class="metamakereq">';
				$returnhmel .= '<div class="pmp">Default Value (if set will force empty values to default)</div>';
				$returnhmel .= '<div class="pip">';
				$returnhmel .= '<input type="text" name="wpr_made_meta_data[fields][' . $k . '][std]" value="';
				$returnhmel .= isset($field["std"])?$field["std"]:"";
				$returnhmel .= '"/>';
				$returnhmel .= '</div>';
				$returnhmel .= '</div>';
				
				$returnhmel .= '<div class="metamakereq">';
				$returnhmel .= '<div class="pmp">Placeholder</div>';
				$returnhmel .= '<div class="pip">';
				$returnhmel .= '<input type="text" name="wpr_made_meta_data[fields][' . $k . '][placeholder]" value="';
				$returnhmel .= isset($field["placeholder"])?$field["placeholder"]:"";
				$returnhmel .= '"/>';
				$returnhmel .= '</div>';
				$returnhmel .= '</div>';
			break;
			default:
				$returnhmel .= apply_filters("wpr_custom_made_meta", "", $field);
		}
		return $returnhmel;
}
?>