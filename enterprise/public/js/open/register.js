$(function(){
	$("input:checkbox").click(function(){
		$(".checked").toggleClass("show");
		if($(".checked").attr("class") == "checked"){
			$("#checkbox").show();
		} else {
			$("#checkboxSure").hide();
			$("#checkbox").show();
		}
	})
})
