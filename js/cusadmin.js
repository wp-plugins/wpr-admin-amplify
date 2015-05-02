jQuery(document).ready(function(jQuery){ 

if(jQuery.fn.datepicker){
	jQuery( "input.datetype" ).datepicker({ dateFormat: 'yy/mm/dd', numberOfMonths: 1 }); jQuery( "#ui-datepicker-div" ).hide();
}if(jQuery.fn.timepicker){
	jQuery( "input.timetype" ).timepicker();
}

jQuery("#gen_rec_opts h3.hndle").each(function(e){
	jQuery(this).attr("class", "hndlle");
});
			jQuery('.ad-upload').live('click', function() {
				uploadInput = this;
				// Media Library params
				var frame = wp.media({
					title : 'Pick a file to use here.',
					multiple : false,
					//library : { type : 'image'},
					button : { text : 'Insert' },
					//filters: 'all'
				});
				// Runs on select
				frame.on('select',function() {
					// Get the attachment details
					attachment = frame.state().get('selection').first().toJSON();
					// Set image URL
					jQuery(uploadInput).val( attachment['url'] );
					// Image sublayer
					//if(jQuery(uploadInput).is('input[name="image"]')) {
						//jQuery(uploadInput).prev().attr('src', attachment['url']);
					//}
				});
				// Open ML
				frame.open();
			});
		//} else {

			// Bind upload button to show media uploader
			//jQuery('.ad-upload').live('click', function() {
			//	uploadInput = this;
			//	tb_show('Upload or select a new image to insert into LayerSlider WP', 'media-upload.php?type=image&amp;TB_iframe=true&width=650&height=400');
			//	return false;
			//});		
		//}		
	// Bind an event to image url insert
		/*window.send_to_editor = function(html) {

			// Get the image URL
			var img = jQuery('img',html).attr('src');
			// Set image URL
			jQuery(uploadInput).val( img );

			// Remove thickbox window
			tb_remove();

			// Image sublayer
			//if(jQuery(uploadInput).is('input[name="image"]')) {
			//	jQuery(uploadInput).prev().attr('src', img);
			//}
		};*/
	var ajaxurl = fromphp.jaxfile;
	jQuery('input:submit.ordnum').click(function(event){
		event.preventDefault();
		var postid = jQuery(this).parents('.fakeform').children('.deid').val();
		var orderr = jQuery(this).parents('.fakeform').children('.layoutor').val();
		var loadgif = jQuery(this).parents('.rapper').children('.progress');
		loadgif.css("visibility","visible");
		//var yourel = plug_drect.thefile + "/TheVideoPage/quicksave.php";
		var dito = {
				action: 'up_datepost',
				deid: postid,
				ordr: orderr
			};
		jQuery.post(ajaxurl, dito,
		function(data) {
			loadgif.css("visibility","hidden");
			if(data.com == "true"){alert("This button is broken, ask Aryan why!");}
			
			}, 
			"json"
		);
		
	});
/*=================================== LISTER =================================*/

if(jQuery('.sortrlist').doesExist()){
	jQuery(".sortrlist ul").sortable();
	jQuery(".doitdinow").on("click", function(e){
	  var sibin = jQuery(this).siblings(".textholder");		
	  var addedval = sibin.val();
	  sibin.val("");
	  if(addedval && addedval != ""){
		var topplace = jQuery(this).closest(".reminder-container");
		var datdiv = topplace.find(".multlistdata");
		topplace.find("ul.reminders").append('<li class="' + datdiv.data("classnm") + '"><div class="nothis">X</div><input name="' + datdiv.data("inputname") + '" class="nlist" type="text" value="' + addedval + '" /></li>');
		//jQuery(this).closest(".metabox").find("input.textholder").val("");
	  }
	});	
	jQuery(".clear-all").on("click", function(e){
		e.preventDefault();
		jQuery(this).closest(".reminder-container").find("ul.reminders li").each(function(e, el){
			jQuery(this).slideUp("fast",function(){jQuery(this).remove();});
		});
		var mettop = jQuery(this).parents(".metabox");
	});
	jQuery("ul.reminders").on("click", "li .nothis", function(e){
		var upperdiv = jQuery(this).closest('ul.reminders');
		jQuery(this).closest("li").remove();
	});
}
if(jQuery('.advancedlist').doesExist()){
	jQuery(".advancedlist ul").sortable();
	jQuery(".advancednow").on("click", function(e){
		var arr = [];
		var hc = jQuery(this).closest('.advancedlist ul.advanced li').length;
		var topplace = jQuery(this).closest(".reminder-container");
		var datdiv = topplace.find(".multlistdata");
		hc += 1;
		$wwdd = jQuery(this).siblings(".txteses").css('width');
		jQuery(this).siblings(".txteses").children(".textholder").each(function(e, el){
			var snm = jQuery(this).data("subname");
			arr.push({"name" : snm, "value" : jQuery(this).val()});
			jQuery(this).val("");
		});
		
		var mudex = topplace.find("ul.reminders li").length;
		if(arr.length > 0){
		var valstets = '';
		var arlen = arr.length;
		var mlen = Math.floor(100/arlen )-1;
		for(var t=0; t<arlen; t++){ 
			valstets += '<input class="nlist nl-' + hc + '" type="text" value="' + arr[t]["value"] + '" style="width:' + mlen + '% !important;float:left;';
			if(t != arlen-1){valstets += 'margin-right: 5px;';}
			valstets += '" name="' + datdiv.data("inputname") + '[mldex_' + mudex + '][' + arr[t]["name"] + ']"/>';
			hc++;
		}
		topplace.find("ul.reminders").append('<li class="' + datdiv.data("classnm") + '"><div class="nothis">X</div><div style="clear:both"></div>' + valstets + '</li>');
		}
	});	
	jQuery(".clear-all").on("click", function(e){
		e.preventDefault();
		jQuery(this).closest(".reminder-container").find("ul.reminders li").each(function(e, el){
			jQuery(this).slideUp("fast",function(){jQuery(this).remove();});
		});
		var mettop = jQuery(this).closest(".metabox");
		mettop.find("input.textholder").val("");
	});
	jQuery("ul").on("click", "li .nothis", function(e){
		var upperdiv = jQuery(this).closest('ul.reminders');
		jQuery(this).closest("li").remove();
		changeadv(upperdiv);		
	});
	jQuery(".advancedlist ul").on( "sortupdate", function( event, ui ) { changeadv(ui.item);} ); 
}
/*========================================= END LISTER =======================================*/
	jQuery(".checkersbox").change(function(){
		$tval = jQuery(this).val();
		$onval = jQuery(this).data("on");
		$offval = jQuery(this).data("off");
		if($tval == $onval){
			jQuery(this).val($offval);
			jQuery(this).siblings(".checkshadow").val($offval);
		}else{
			jQuery(this).val($onval);
			jQuery(this).siblings(".checkshadow").val($onval);
		}
	});

});//end doc ready
jQuery.fn.doesExist = function(){
        return jQuery(this).length > 0;
 };
function changeadv(elm){
	var lemel = elm.closest(".metabox");
	lemel.find("ul.advanced li").each(function(i, elm){							
		jQuery(this).find("input").each(function(it, elmt){
			var cname = jQuery(this).attr("name");
			cname = cname.replace(/mldex_[^\]]+\]/, "mldex_" + i + "]");
			jQuery(this).attr("name", cname);
		});
	});
}

function isValidID(s){
    var validName = /^[a-z][0-9A-Z_]*$/i;
    return validName.test(s);
}