function wppvaddvote(postId) {
	jQuery.ajax({
		type: 'POST',
		url: wppvajax.ajaxurl,
		data: {
			action: 'wppv_count_vote',
			postid: postId
		},
		success:function(data, textStatus, XMLHttpRequest){
			var linkid = '#wppv-' + postId;
			jQuery(linkid).html('');
			jQuery(linkid).append(data);
		},
		error: function(MLHttpRequest, textStatus, errorThrown){
			alert(errorThrown);
		}
	});
}
jQuery(document).ready(function() {
	jQuery(".wp_vote_icon").mouseover(function () {
		jQuery('#votetext').text(wppv_text.pv_hover);
	});
	jQuery(".wp_vote_icon").mouseout(function () {
		jQuery('#votetext').text(wppv_text.pv_label);
	});
	jQuery(".wp_voted_icon").mouseover(function () {
		jQuery('#onlyreg').text(wppv_text.pv_refusal);
	});
	jQuery(".wp_voted_icon").mouseout(function () {
		jQuery('#onlyreg').text(wppv_text.pv_label);
	});
});