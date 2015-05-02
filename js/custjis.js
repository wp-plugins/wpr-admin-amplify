//jQuery.noConflict();
jQuery(document).ready(function(){	

});//end ready
function loguin(){
	var ajnon = jQuery('#nonaj').data("jano");
	var data = {
		action: 'logruser',
		loginsecure: ajnon,
		wpr_usernm: jQuery('#toptwhome #mbpunmli').val(),
		wpr_pass: jQuery('#toptwhome #mbppassli').val()
	};

	jQuery.post(fromphp.jaxfile, data, 
		function(response) {
			var data = jQuery.parseJSON(response);
			if(data.results == "good"){
				jQuery("#eresponse").html(data.user + " is now logged in.");
				location.reload();
			}
			else{jQuery("#eresponse").html(data.results);}
		}
	);
}

// if(jQuery.isFunction(jQuery.fn.marquee)){
jQuery.fn.doesExist = function(){
        return jQuery(this).length > 0;
 };
function progressBar(percent, $element, stuff) {
	var progressBarWidth = percent * $element.width() / 100;
	$element.find('div').animate({ width: progressBarWidth }, 500).html('<p class="percen">' + percent + "% " + stuff + "</=>");
}