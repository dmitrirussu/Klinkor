jQuery(function(){
	var $alert = $('.alert');

	$alert.first().removeClass("alert-warning").addClass("alert-danger");
	$alert.alert();

	$alert.bind('closed.bs.alert', function(){

		$(this).next().removeClass("alert-warning").addClass("alert-danger");

	});
});
