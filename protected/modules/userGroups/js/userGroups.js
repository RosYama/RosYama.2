/**
 * @author Nicola Puddu
 */

/**
 * sends an ajax request to a specific page
 */
function loadPage(me, where) {
	var indirizzo = $(me).attr("href");
	if (where == undefined) {
		where = '#userGroups-container';
	}
	$.ajax({url:indirizzo ,success:function(data){
		$(where).replaceWith(data);
	}});
	return false;
}

/**
 * display the user or group access permission settings
 * @param what it may be user or group
 * @param id user or group id
 */
function getPermission(baseurl, what, id) {
	if (id != '') {
		$.ajax({url: baseurl + '/userGroups/admin/accessList?what=' + what + '&id=' + id,success:function(data){
			var div = what == 1 ? 'user' : 'group';
			$('#' + div + '-detail').slideUp('slow', function(){
				$('#' + div + '-detail').html(data).slideDown();
			});
		}});
	}
}