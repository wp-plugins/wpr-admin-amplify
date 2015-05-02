<?php
/*
http://codepen.io/GreenSock/pen/dPZLEp
This function hooks into postmeta.php to set a custom field/meta area.
The code is all contained in "one" custom field type and extended from the default postmeta class.
The fields are added as associative arrays so that all data can be saved directly to one post meta field as a serialized object.
*/
function createFormmakerFields($post_ob_name, $value, $metabox, $post, $meta_data, $xargs){
	if($meta_data['postnm'] != "wpr_made_form_data" && $meta_data['id'] != "allformmakerdat"){return;}
		wp_enqueue_media();
		global $wprformtypes;
		$mtet = get_post_meta($post->ID, 'wpr_made_form_data', true);
		//print_r($mtet);
		?>
		
		<div style="width:100%;" id="wprmformmakeropts">
		<div id="wprformplacer">
			<div class="dragplace">
				<div class="wheredropped">
					<div class="fgridelem"></div>
					<div class="fgridelem"></div>
					<div class="fgridelem"></div>
				</div>
				<div class="wherefrom">
					<div class="ffieldelem forigin"></div>
					<div class="ffieldelem forigin"></div>
					<div class="ffieldelem forigin"></div>
				</div>
			</div>
		</div>
		<div id="wprformcontroller">
			
		</div>
			<script>
			jQuery(document).ready(function(){
				jQuery("#allformmakerdat h3.hndle").removeClass("hndle");
				jQuery("#allformmakerdat .handlediv").removeClass("handlediv");	
				
				jQuery(".wherefrom").find(".ffieldelem").draggable({
					helper: "clone",
					snap: ".fgridelem",
					cursor: "move",
					//appendTo: ".fgridelem"
					/*revert : function(event, ui){
						jQuery(this).data("uiDraggable").originalPosition = {top : 0,left : 0};
						return true;//!event;
					},*/
					start: function(event, ui){jQuery(ui.helper).width(20);},
					drag: function(event, ui){jQuery(ui.helper).width(20);},
					stop: function(event, ui){jQuery(this).attr("style","");}//jQuery(ui.helper).clone(true);
				});
				jQuery(".fgridelem").droppable({
					accept: ".ffieldelem",
					over: function(event, ui) {
						jQuery(this).append(ui.draggable);//.clone();
					},
					drop: function(event, ui){
						if(jQuery(ui.draggable).hasClass("forigin")){
							var celm = ui.draggable.clone();
							jQuery(".wherefrom").append(celm);
							jQuery(ui.draggable).removeClass("forigin");
							jQuery(celm).draggable({
								helper: "clone",
								snap: ".fgridelem",
								cursor: "move",
								start: function(event, ui){jQuery(ui.helper).width(20);},
								drag: function(event, ui){jQuery(ui.helper).width(20);},
								stop: function(event, ui){jQuery(this).attr("style","");}//jQuery(ui.helper).clone(true);
							});
						}
					}
				});
				/*
				destroyer{
					$(this).draggable('destroy');
					$(this).remove();
				}
				*/
				/*var sanzdit = {action: 'dosanz', tosanitize: fieldid};
				jQuery.post(fromphp.jaxfile, sanzdit, function(data) {
					if(data.sanitized){
						var sectionid = data.sanitized;
						jQuery("#mfieldid").val("");
						jQuery('#metamakersetfields .errmsg').text("");
						jQuery.post(fromphp.jaxfile, {action: "formjax", ftype: fieldtype, fid: sectionid}, function(hmel){
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
				}, "json");*/
			});
			
			</script>
		</div>		
		<?php
}
add_action('wpr_postmeta_myfield', 'createFormmakerFields', 10, 6);

add_action("wp_ajax_formjax", "formFieldJax");
function formFieldJax(){
	echo doFormFields($_POST['fid'], array("id"=>$_POST['fid'], "type"=>$_POST["ftype"]));
	die();
}
function doFormFields($k, $field){/*id, type*/
	$returnhmel = "";
	switch($field["type"]){
		case "radio":
			$returnhmel .= '';
		break;
		case "select":
			$returnhmel .= '';
		break;
		case "multicheck":
			$returnhmel .= '';
		break;
		case "multiselect":
			$returnhmel .= '';
		break;
		case "check":
			$returnhmel .= '';
		break;
		case "text": 
			$returnhmel .= '';
		break;
		case "textarea":
			$returnhmel .= '';
		break;
		case "datetime":
			$returnhmel .= '';
		break;
		case "html":
			$returnhmel .= '';
		break;
		case "sectionbreak":
			$returnhmel .= '';
		break;
		default:
			$returnhmel .= apply_filters("wpr_custom_made_form", "", $field);
	}
	return $returnhmel;
}
?>