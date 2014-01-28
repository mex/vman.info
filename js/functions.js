$(document).ready(function(){
	$('#id').focus(function(){if($(this).val() == $(this).attr('defaultValue')){$(this).val('');}});
	$('#id').blur(function(){if($(this).val() == ''){$(this).val($(this).attr('defaultValue'));}});
	$('.stats').hover(function(){
		$('#stats'+$(this).attr('id')).show();
	},function(){
		$('#stats'+$(this).attr('id')).hide();
	});
});
function goto(id) {
	parent.location.href = '/search/'+$('#'+id).val();
}