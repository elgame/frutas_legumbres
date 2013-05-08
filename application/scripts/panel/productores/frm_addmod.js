$(function(){
	$("#dtipo").on("change", function(){
		var vthis = $(this);
		if (vthis.val() == "f") {
			$(".control-group.req_field").addClass("error");
			$(".control-group.req_field input").attr("required", "true");
		}else{
			$(".control-group.req_field").removeClass("error");
			$(".control-group.req_field input").removeAttr("required");
		}
	});

});


