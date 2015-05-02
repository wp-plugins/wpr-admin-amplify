<?php
if(!class_exists('GeneratePostMeta')){
class GeneratePostMeta{

	protected $pagesadded_met;
/*===
Constructor.
HOOK: apply_filters('wpr_postmeta_argsedit', $margs);//$margs = args passed to add_meta_box.
This hook is not really needed because $margs is editable upon creation, however may be limited after UI is created so
hook exists for that purpose.
===*/
	public function __construct($fields, $id, $title, $postnm, $type, $context='normal', $priority='high', $margs=array()){		
	$this->pagesadded_met = array(
		'id' => $id,
		'title' => $title,
		'postnm' => $postnm,
		'page' => $type,
		'context' => $context,
		'priority' => $priority,
		'fields' => $fields,
	);
		$this->pagesadded_met['xargs'] = apply_filters('wpr_postmeta_argsedit', $margs);
		add_action('admin_menu',  array( $this, 'add_paga_metabox'));
		add_action('save_post',  array( $this, 'save_pag_metabox'));
	}
/*===
Actually adds the meta box.
===*/
	public function add_paga_metabox(){
		global $post;
		add_meta_box($this->pagesadded_met['id'], $this->pagesadded_met['title'],  array( $this, "init_paga_metabox"), $this->pagesadded_met['page'], $this->pagesadded_met['context'], $this->pagesadded_met['priority'], $this->pagesadded_met['xargs']);
	}
/*===
Actually adds the meta box.
HOOK: apply_filters('wpr_postmeta_myownthing', true, $this->pagesadded_met, $post, $xargs);//true/false/string, Param=Class Data, $post=global post, $xargs=params passed by add_meta_box
return true to continue script, return false to stop execution, return string to echo string and stop execution.
HOOK: do_action('wpr_postmeta_myfield', $post_ob_name, $value, $metabox, $post, $this->pagesadded_met, $xargs);
$post_ob_name=Name used in name attribute of field;$value=the passed params for this field;$metabox=current saved value of field;$post=global post$Param=class data;$xargs=additional arguments passed to add_meta_box(usually empty)
This hook is the default of the "type" switch method.  If you create your own type, you would call this filter and return the html for your field type after checking that $value["type"] matches your custom type.
HOOK: do_action('wpr_postmeta_afterfield', $post_ob_name, $value, $metabox, $post, $this->pagesadded_met, $xargs); $post_ob_name=Name used in name attribute of field;$value=the passed params for this field;$metabox=current saved value of field;$post=global post;$Param=class data;$xargs=additional arguments passed to add_meta_box(usually empty)
This hook is called after the switch "type" method and does it's thing after the field in question is created, but is still within the loop of field creation, so it will be called after every field.
HOOK: do_action('wpr_postmeta_afterfields', $post, $this->pagesadded_met, $xargs); 
This is called after all fields have been created and is outside of the loop that generates the fields, so it will be called once, after all fields are generated.
=============
//datepicker, timepicker, radio (Yes|value,Yes|value,No|value), select (Yes|value,Yes|value,No|value), text (* "value" => "decimal" *), textarea, textwys, separator(id=separter), file, multilist, advancedlist (* "sections" => "Event,Description,Start,End" *), sidebars, check ("no|yes|no" second val is checked third is not checked)
===*/
/*
TODO
=========
User (select 1 or more WP users, api returns the selected user objects)
Google Maps (interactive map, api returns lat,lng,address data)
Date Picker (jquery date picker, options for format, api returns string)
Color Picker (WP color swatch picker)
Tab (Group fields into tabs)
Message (Render custom messages into the fields)
Repeater (ability to create repeatable blocks of fields!)
Flexible Content (ability to create flexible blocks of fields!)
Gallery (Add, edit and order multiple images in 1 simple field)
*/
	public function init_paga_metabox($xargs){
		global $post, $wp_registered_sidebars;
		$tocontinue = apply_filters('wpr_postmeta_myownthing', true, $this->pagesadded_met, $post, $xargs);
		if($tocontinue === false){return;}
		if(is_string($tocontinue)){echo $tocontinue; return;}
		$postarr = $this->pagesadded_met['postnm'];
		$ti = 0;
		foreach ($this->pagesadded_met['fields'] as $value) {
	
			$adcustmet = get_post_meta($post->ID, $value['id'], true);
			if(!empty($adcustmet) || $adcustmet == "0"){$metabox = $adcustmet;}elseif(isset($value['std'])){$metabox = $value['std'];}else{$metabox = "";}
			$post_ob_name = $postarr . '[' . $value['id'] . ']';
			?>		
			<?php
			switch ($value['type']) {
				/*Radio syntax for array of value should be {default|value,choice 1|value,choice 2|value, ..., choice n|value} PLEASE NOTE: default is not only first set, but must be included again in position you choose*/
				case 'radio':
				$radioarr = explode(",", $value['std']);
				$defval = $radioarr[0];
				if($metabox == $value['std'] || $metabox == "" || $metabox == undefined || $metabox == null || $metabox == $radioarr[0]){				
					$metabox = $defval;			
				}
				?>
				<div class="metabox" style="display:block;width:100%;padding:10px;">
					<div class="radio" style="float:left;width:70%;">
						<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
						<p class="description"><?php echo $value['desc']; ?></p>
					</div>
					<div style="float:left;width:75%;">
						<?php for($tu=1;$tu<count($radioarr);$tu++){ ?>
						<div style="float:left;<?php  if($tu!= 1){echo 'padding-left:10px;'; }?>">
						<?php 
						$radtwo = explode("|", $radioarr[$tu]);
						echo $radtwo[0]; ?>
						<input type="radio" id="<?php echo $value['id']; ?>" class="met_<?php echo $value['type']; ?>" name="<?php echo $postarr . '[' . $value['id'] . ']'; ?>" value="<?php echo $radtwo[1] ?>" <?php if ($metabox == $radtwo[1]) echo "checked=1";?> /> 
						</div>
						<?php } ?>
					</div>
					<br class="clear" />
				</div>
				<?php
				break;
				
				case 'select':
				$selarr = explode(",", $value['std']);
				if($metabox == $value['std'] || $metabox == "" || $metabox == undefined || $metabox == null){
					//$selval = explode("|",$selarr[0]);
					$metabox = $selarr[0];	//$selval[1]	
				}
				?>
				<div class="metabox" style="display:block;width:100%;padding:10px;">
					<div class="selector" style="float:left;width:70%;">
						<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
						<p class="description"><?php echo $value['desc']; ?></p>
					</div>
					<div style="float:left;width:75%;">
					<select id="<?php echo $value['id']; ?>" class="met_<?php echo $value['type']; ?> metselect" name="<?php echo $postarr . '[' . $value['id'] . ']'; ?>">
						<?php for($tu=1;$tu<count($selarr);$tu++){					
						$radtwo = explode("|", $selarr[$tu]); ?>					
						<option value="<?php echo $radtwo[1] ?>" <?php if ($metabox == $radtwo[1]) echo 'selected="selected"';?> <?php if($radtwo[1] == "Default"){ echo "disabled";} ?>> <?php echo $radtwo[0]; ?></option>
						<?php } ?>
						</select>
					</div>
					<br class="clear" />
				</div>
				<?php
				break;
				
				case 'multicheck':
				$radioarr = explode(",", $value['std']);
				$defval = $radioarr[0];
				if($metabox == $value['std'] || $metabox == "" || $metabox == undefined || $metabox == null || $metabox == $radioarr[0]){				
					$metabox = explode("&", $defval);		
				}
				?>
				<div class="metabox" style="display:block;width:100%;padding:10px;">
					<div class="radio" style="float:left;width:70%;">
						<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
						<p class="description"><?php echo $value['desc']; ?></p>
					</div>
					<div style="float:left;width:75%;">
						<?php for($tu=1;$tu<count($radioarr);$tu++){ ?>
						<div style="float:left;<?php  if($tu!= 1){echo 'padding-left:10px;'; }?> text-align:center;">
						<?php 
						$radtwo = explode("|", $radioarr[$tu]);
						echo $radtwo[0]; ?>
						<input style="margin-top:5px;" type="checkbox" class="met_<?php echo $value['type']; ?>" name="<?php echo $postarr . '[' . $value['id'] . ']'; ?>[]" value="<?php echo $radtwo[1] ?>" <?php if(in_array($radtwo[1], $metabox)){echo "checked=1";} ?> /> 
						</div>
						<?php } ?>
					</div>
					<br class="clear" />
				</div>
				<?php
				break;
				
				case 'multiselect':
				$selarr = explode(",", $value['std']);
				$defval = $selarr[0];
				if($metabox == $value['std'] || $metabox == "" || $metabox == undefined || $metabox == null || $metabox == $selarr[0]){				
					$metabox = explode("&", $defval);		
				}
				?>
				<div class="metabox" style="display:block;width:100%;padding:10px;">
					<div class="selector" style="float:left;width:70%;">
						<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
						<p class="description"><?php echo $value['desc']; ?></p>
					</div>
					<div style="float:left;width:75%;">
					<select id="<?php echo $value['id']; ?>" class="met_<?php echo $value['type']; ?> metselect" name="<?php echo $postarr . '[' . $value['id'] . ']'; ?>[]" multiple>
						<?php for($tu=1;$tu<count($selarr);$tu++){					
						$radtwo = explode("|", $selarr[$tu]); ?>					
						<option value="<?php echo $radtwo[1] ?>" <?php if(in_array($radtwo[1], $metabox)){echo 'selected="selected"';} ?> > <?php echo $radtwo[0]; ?></option>
						<?php } ?>
						</select>
					</div>
					<br class="clear" />
				</div>
				<?php
				break;
				
				case 'sidebars':
				if($metabox == $value['std'] || $metabox == "" || $metabox == undefined || $metabox == null){
					$metabox = "";			
				}
				?>
				<div class="metabox" style="display:block;width:100%;padding:10px;">
					<div class="selector" style="float:left;width:70%;">
						<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
						<p class="description"><?php echo $value['desc']; ?></p>
					</div>
					<div style="float:left;width:75%;">
					<select id="<?php echo $value['id']; ?>" class="met_<?php echo $value['type']; ?> metselect" name="<?php echo $postarr . '[' . $value['id'] . ']'; ?>">
					<option value="none" >Default</option>
						<?php foreach($wp_registered_sidebars as $tu){ ?>					
						<option value="<?php echo $tu['id'] ?>" <?php if ($metabox == $tu['id']) echo 'selected="selected"';?>> <?php echo $tu['name']; ?></option>
						<?php } ?>
						</select>
					</div>
					<br class="clear" />
				</div>
				<?php
				break;
				
				case 'text':	
				?>
				<div class="metabox" style="display:block;width:100%;padding:10px;">
					<div class="text" style="float:left;width:70%;">
						<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
						<p class="description"><?php echo $value['desc']; ?></p>
					</div>
					<div style="float:left;width:75%;">
						<input id="<?php echo $value['id']; ?>" class="met_<?php echo $value['type']; ?>" type="text" size="120" name="<?php echo $postarr . '[' . $value['id'] . ']'; ?>" value="<?php echo $metabox; ?>" <?php if($value['decimal'] && $value['decimal'] == 'true'){ ?>onkeyup="if(/\D/g.test(this.value)){this.value = this.value.replace(/\D/g,'');}" <?php } ?> placeholder="<?php echo isset($field["placeholder"])?$field["placeholder"]:""; ?>"/>
					</div>
					<br class="clear" />
				</div>
				<?php
				break;
				
				case 'noneditable':
				?>
				<div class="metabox" style="display:block;width:100%;padding:10px;">
					<div class="text" style="float:left;width:70%;">
						<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
						<p class="description"><?php echo $value['desc']; ?></p>
					</div>
					<div style="float:left;width:90%;">
						<p id="<?php echo $value['id']; ?>" class="<?php echo $value['id']; ?>" ><?php echo $metabox; ?></p>
					</div>
					<br class="clear" />
				</div>
				<?php
				break;
				
				case 'check':	
				?>
				<?php 
					$dars = explode("|", $value['std']); $def = ($dars[0] == $dars[1])? $dars[1]: $dars[2]; 
					if($metabox == $value['std']){$metabox = $def;}
					$ischk = ($metabox == $dars[1])? "checked": "";
				?>
				<div class="metabox" style="display:block;width:100%;padding:10px;">
					<div class="text" style="float:left;width:70%;">
						<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
						<p class="description"><?php echo $value['desc']; ?></p>
					</div>
					<div style="float:left;width:75%;">
						<input class="checkersbox" type="checkbox" value="<?php echo $metabox; ?>" data-on="<?php echo $dars[1]; ?>" data-off="<?php echo $dars[2]; ?>" <?php echo $ischk; ?> >
						<input id="<?php echo $value['id']; ?>" class="met_<?php echo $value['type']; ?> checkshadow" type="hidden" name="<?php echo $postarr . '[' . $value['id'] . ']'; ?>" value="<?php echo $metabox; ?>" />
					</div>
					<br class="clear" />
				</div>
				<?php
				break;
				
				case 'multilist':	
				?>
				<div class="metabox" style="display:block;width:100%;padding:10px;">
					<div class="text" style="float:left;width:70%;">
						<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
						<p class="description"><?php echo $value['desc']; ?></p>
					</div>
					<br class="clear" />
					<div class="lister-wrapper">
					<div class="reminder-container">
					
					<div id="<?php echo $value['id']; ?>" class="multlistdata" data-classnm="met_<?php echo $value['type']; ?> multilister" data-inputname="<?php echo $postarr . '[' . $value['id'] . ']'; ?>[]"></div>
						<div class="input-form">
						<input type="text" class="textholder" placeholder="Add .." value="" style="background:white !important;" />
						<div class="doitdinow" >Add</div>
						</div>
						<div class="sortrlist"><ul class="reminders">
						<?php
							if($metabox){
							if(!is_array($metabox)){$metabox = (array)$metabox;}
							$cm = count($metabox);
							$ci = 0;
							foreach($metabox as $amet){
							?>
								<li class="met_<?php echo $value['type']; ?> multilister"><div class="nothis">X</div><input name="<?php echo $postarr . '[' . $value['id'] . ']'; ?>[]" class="nlist" type="text" value="<?php echo $amet; ?>" /></li>
							<?php
								$ci++; }
							}
						?>
						</ul></div>
						<button class="clear-all">Delete All</button>
					</div></div><!--end lister wrapper-->
				</div>
				<?php
				break;
				
				case 'advancedlist':	
				?>
				<div class="metabox" style="display:block;width:100%;padding:10px;">
					<div class="text" style="float:left;width:70%;">
						<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
						<p class="description"><?php echo $value['desc']; ?></p>
					</div>
					<br class="clear" />
					<div class="lister-wrapper">
					<div class="reminder-container advanced">
					
					<div id="<?php echo $value['id']; ?>" class="multlistdata" data-classnm="met_<?php echo $value['type']; ?> multilister" data-inputname="<?php echo $postarr . '[' . $value['id'] . ']'; ?>" data-sects="<?php echo $value['sections']; ?>"></div>
						<div class="input-form">
						<?php
							$stadar = explode(",", $value['sections']);
							$das=0;
							$ww = count($stadar);
							foreach($stadar as $onestad){
							
						?>
						<div class="txteses" style="width:<?php echo (int)(100/$ww)-1; ?>% !important;float:left;<?php if($das+1 != $ww) echo 'margin-right: 5px;'; ?>" >
						<p><?php echo $onestad; ?></p>
						<input type="text" class="textholder tx-<?php echo $das; ?>" data-subname="<?php echo $onestad; ?>" placeholder="Add .." value="" style="background:white !important;" />
						</div>
						<?php
							}
						?>
						<div class="advancednow" >Add</div>
						</div>
						<div class="advancedlist"><ul class="reminders advanced">
						<?php
							if($metabox){
							if(!is_array($metabox)){$metabox = (array)$metabox;}
							$cm = count($metabox);
							$ci = 0;
							foreach($metabox as $amet){
							?>
								<li class="met_<?php echo $value['type']; ?> multilister"><div class="nothis">X</div><div style="clear:both"></div>
							<?php
								$ww = count($stadar);
								$hc = 0;
								foreach($stadar as $raa){
								$mval = isset($amet[$raa])?$amet[$raa]:"";
							?>
								<input class="nlist nl-<?php echo $hc; ?>" type="text" value="<?php echo $mval; ?>" style="width:<?php echo (100/$ww)-1; ?>% !important;float:left;<?php if($hc+1 != $ww) echo 'margin-right: 5px;'; ?>" name="<?php echo $postarr . '[' . $value['id'] . ']'; ?>[mldex_<?php echo $ci; ?>][<?php echo $raa; ?>]" />
							<?php
								$hc++;
								}
							?>								
								</li>
							<?php
								$ci++; }
							}
						?>
						</ul></div>
						<button class="clear-all">Delete All</button>
					</div></div><!--end lister wrapper-->
				</div>
				<?php
				break;
				
				case 'textarea':
				?>
				<div class="metabox" style="display:block;width:100%;padding:10px;">
					<div class="text" style="float:left;width:70%;">
						<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
						<p class="description"><?php echo $value['desc']; ?></p>
					</div>
					<div style="float:left;width:90%;">
						<textarea id="<?php echo $value['id']; ?>" class="<?php echo $value['id']; ?>" type="text" name="<?php echo $postarr . '[' . $value['id'] . ']'; ?>" placeholder="<?php echo isset($field["placeholder"])?$field["placeholder"]:""; ?>"><?php echo $metabox; ?></textarea>
					</div>
					<br class="clear" />
				</div>
				<?php
				break;
				
				case 'textwys':
				?>
				<div class="metabox" style="display:block;width:100%;padding:10px;">
					<div class="text" style="float:left;width:70%;">
						<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
						<p class="description"><?php echo $value['desc']; ?></p>
					</div>
					</div>
				<div style="clear:both;padding-bottom: 10px;"></div>
				<?php
				
				wp_editor($metabox, 'thetxt' . $ti, array( 'textarea_name' => $postarr . '[' . $value['id'] . ']', 'media_buttons' => false, 'tinymce' => array( 'theme_advanced_buttons1' => 'formatselect,forecolor,|,bold,italic,underline,|,bullist,numlist,blockquote,|,justifyleft,justifycenter,justifyright,justifyfull,|,link,unlink,|,spellchecker,wp_fullscreen,wp_adv' ) ) ); 
				$ti++;
				?>
					<br class="clear" />
				<?php
				break;
				
				case 'datepicker':	
				?>
				<div class="metabox" style="display:block;width:100%;padding:10px;">
					<div class="text" style="float:left;width:70%;">
						<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
						<p class="description"><?php echo $value['desc']; ?></p>
					</div>
					<div style="float:left;width:75%;">
	
						<input id="<?php echo $value['id']; ?>" class="datetype" type="text" size="120" name="<?php echo $postarr . '[' . $value['id'] . ']'; ?>" value="<?php echo $metabox; ?>" />
					</div>			
					<br class="clear" />
				</div>
				<?php
				break;
				
				case 'timepicker':	
				?>
				<div class="metabox" style="display:block;width:100%;padding:10px;">
					<div class="text" style="float:left;width:70%;">
						<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
						<p class="description"><?php echo $value['desc']; ?></p>
					</div>
					<div style="float:left;width:75%;">
	
						<input id="<?php echo $value['id']; ?>" class="timetype" type="text" size="120" name="<?php echo $postarr . '[' . $value['id'] . ']'; ?>" value="<?php echo $metabox; ?>" />
					</div>			
					<br class="clear" />
				</div>
				<?php
				break;
			
				case 'separator':	
				?>
				<div class="metanewplace" style="display:block;width:100%;">
					<div class="putinmid">
					<h3 class="hndle" title="<?php echo $value['desc']; ?>"><?php echo $value['name']; ?></h3>
					</div>
					<br class="clear" />
				</div>
				<?php
				break;
				
				case 'file':	
				?>
				<div class="metabox" style="display:block;width:100%;padding:10px;">
					<div class="text" style="float:left;width:70%;">
						<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
						<p class="description"><?php echo $value['desc']; ?></p>
					</div>
					<div style="float:left;width:75%;">
						<input id="<?php echo $value['id']; ?>" class="ad-upload" type="text" size="120" name="<?php echo $postarr . '[' . $value['id'] . ']'; ?>" value="<?php echo $metabox; ?>" />
					</div>
					<br class="clear" />
				</div>
				<?php
				break;
				
				case 'calltoaction': 
				/*"id" => '{id of button}',"attrdata" => 'data-test="testdata" ...',"action" => {ajax action method call},"title" => button title,"otherfields" => {id list of fields with values (.val()) to pass comma separated,"type" => 'calltoaction'*/
				?>
				<div class="metabox" style="display:block;width:100%;padding:10px;">
					<div class="text" style="float:left;width:70%;">
						<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
						<p class="description"><?php echo $value['desc']; ?></p>
					</div>
					<div style="float:left;width: 200px;background: #0074A2;color:white;cursor:pointer;text-align:center;display:inline-block;padding: 3px 5px;" id="<?php echo $value['id']; ?>" class="adactionbut" <?php echo $value['attrdata']; ?> data-pid="<?php echo $post->ID; ?>">
						<?php echo $value['title']; ?>
					</div>
					<br class="clear" />
				</div>
				<script>
				jQuery(document).ready(function(){
					<?php
						$jaxcontinue = apply_filters('wpr_postmeta_action_field', true, $value, $post, $xargs, $this->pagesadded_met);
						if(is_string($jaxcontinue)){echo $jaxcontinue; $jaxcontinue = false;}
						if($jaxcontinue !== false){
					?>
					jQuery("#<?php echo $value['id']; ?>").click(function(e){
						var tdata = jQuery(this).data();
						var adat = {
							action: "<?php echo $value['action']; ?>",
							thspostid: "<?php echo $post->ID; ?>",
						};
						var bdat = {};
						tdata = jQuery.extend({}, tdata, adat);
						var sp = "<?php echo $value['otherfields']; ?>".split(",");
						for(var tsp=0; tsp<sp.length; tsp++){
							var ofld = sp[tsp].trim();
							var pdd = ofld;
							var vlu = jQuery("#" + ofld).val();
							bdat[pdd] = vlu;
						}
						tdata = jQuery.extend({}, tdata, bdat);
						// = {}; for(var i in tdata){if(tdata.hasOwnProperty(i)){ndat = jQuery.extend({}, adat, {i : tdata});}}

						jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", tdata, function(response) {
							var data = jQuery.parseJSON(response);
							alert(data.results);
						});
					});
					<?php } ?>
				});
				</script>
				<?php
				break;
				
				default:
				do_action('wpr_postmeta_myfield', $post_ob_name, $value, $metabox, $post, $this->pagesadded_met, $xargs);
			}
			do_action('wpr_postmeta_afterfield', $post_ob_name, $value, $metabox, $post, $this->pagesadded_met, $xargs); 
		}
		do_action('wpr_postmeta_afterfields', $post, $this->pagesadded_met, $xargs); 
	}
/*===
Actually saves the meta box data when the create or update post button is clicked.
HOOK: apply_filters('wpr_postmeta_saving_call', true, $_POST[$this->pagesadded_met['postnm']], $this->pagesadded_met, $post_id);
true/false (true to continue with code execution, false to exit;$_POST=post data passed;$Param=class data;$post_id=post id
This is here for you to do your own thing and exit out of the plugin behavior.
HOOK: apply_filters('wpr_postmeta_saving_one', true, $post_id, $dpname, $dpval, $_POST[$this->pagesadded_met['postnm']], $this->pagesadded_met);
true/false (true to continue with code execution, false to exit;$post_id= post id;$dpname=name of this particular field;$dpval=value of this particular field;$_POST=post data passed;$Param=class data;$post_id=post id
This filter executes in a loop that saves each posted value from $_POST individually.  You can check for certain $_POST params and the name/value pair is located in teh $dpname = $dpval params respectively.  You return true to continue with script execution (which will save the dpval in the dpname post meta field for post_id) or you can return false to exit code execution for this particular post value.
===*/
	public function save_pag_metabox($post_id){
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){return $post_id;}
		$tocontinue = apply_filters('wpr_postmeta_saving_call', true, $_POST[$this->pagesadded_met['postnm']], $this->pagesadded_met, $post_id);
		if($tocontinue === false){return;}
		if(isset($_POST[$this->pagesadded_met['postnm']])){	
			foreach($_POST[$this->pagesadded_met['postnm']] as $dpname => $dpval){
					$donext = apply_filters('wpr_postmeta_saving_one', true, $post_id, $dpname, $dpval, $_POST[$this->pagesadded_met['postnm']], $this->pagesadded_met);
					if($donext === false){continue;}
					update_post_meta($post_id, $dpname, $dpval);
			}
		}
	}
}
}
/*
//datepicker, timepicker, radio (Yes|value,Yes|value,No|value), select (Yes|value,Yes|value,No|value), text (* "value" => "decimal" *), textarea, textwys, separator(id=separter), file, multilist, advancedlist (* "sections" => "Event,Description,Start,End" *), sidebars, check ("no|yes|no" second val is checked third is not checked)

//high, core, default low - normal advanced side
//$this->pagesadded_met['xargs'] = apply_filters('wpr_postmeta_argsedit', $margs);
do_action('wpr_postmeta_afterfield', $post_ob_name, $value, $metabox, $post, $this->pagesadded_met, $xargs);  
do_action('wpr_postmeta_afterfields', $post_ob_name, $post, $this->pagesadded_met, $xargs); 
//$tocontinue = apply_filters('wpr_postmeta_myownthing', true, $this->pagesadded_met, $post, $xargs);
//do_action('wpr_postmeta_myfield', $post_ob_name, $value, $metabox, $post, $this->pagesadded_met, $xargs);
//$tocontinue = apply_filters('wpr_postmeta_saving_call', true, $_POST[$this->pagesadded_met['postnm']], $this->pagesadded_met, $post_id);
//$donext = apply_filters('wpr_postmeta_saving_one', true, $post_id, $dpname, $dpval, $_POST[$this->pagesadded_met['postnm']], $this->pagesadded_met);
*/
?>