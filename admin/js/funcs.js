var AjRunning = false,  ctime;
$(document).ready(function(){
	/*if($('.fancybox'))
	{
		$('.fancybox').fancybox({
			autoResize:  true,
			autoCenter:  true,
			afterShow: function(){			
				$('form').each(function(){
					$(this).validate();
				});
			}
		});
	}*/
	$(".forgot-password-link").click(function(){

		$(".login").hide();
		$(".forgot-password").show();
		$("#registered_email").focus();
	});
	$(".forgot-password-login").click(function(){

		$(".login").show();
		$(".forgot-password").hide();
		$("#email").focus();
	});
});

$(window).resize(function(){
	if($('.fancybox'))
	{
		$.fancybox.update()
	}
});

function confirmDelete(delUrl){
	if (confirm("Are you sure you want to delete?")){
		document.location = delUrl;
	}
}
function ValidateForgotPassword()
{
	var email = $.trim($("#registered_email").val());

	var Data = "&mode=SendForgotPasswordLink&email="+encodeURIComponent(email);
	$("#registered_email").addClass("loading-bg");
	$.ajax({
		type: "POST",
		url: "layouts/dbbyajax.php",
		cache: false,
		data: Data,
		success: function(response){

			var response = $.trim(response);
			var resArr = response.split("-::-");
			var resType = $.trim(resArr[0]);
			var resMsg = $.trim(resArr[1]);

			var color = "red";

			if(resType == "Success")
			{
				color = "green";
			}
			$("#forgot-pass-result").html(resMsg+"<br><br>").css("color",color);

			$("#registered_email").removeClass("loading-bg");
			return false;
		}
	});
	return false;
}