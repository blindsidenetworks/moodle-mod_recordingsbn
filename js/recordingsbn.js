function recordingsbn_actionCall(action, recordingid, courseid) {
	
	action = (typeof action == 'undefined') ? 'publish' : action;
	
	if (action == 'publish' || action == 'unpublish' || (action == 'delete' && confirm("Are you sure to delete this recording?"))) {
		if (action == 'publish' || action == 'unpublish') {
			
			var el_a = document.getElementById('actionbar-publish-a-'+ recordingid);
			if (el_a) {
				var el_img = document.getElementById('actionbar-publish-img-'+ recordingid);
				if (el_a.title == view_hint_actionbar_hide ) {
					el_a.title = view_hint_actionbar_show;
					el_img.src = 'pix/show.gif';

				} else {
					el_a.title = view_hint_actionbar_hide;
					el_img.src = 'pix/hide.gif';

				}

			}
			
		} else {
			// Deletes the line in the dataTable
			//var row = $(document.getElementById('actionbar-publish-img-'+ recordingid)).closest("tr").get(0);
			//oTable.fnDeleteRow(oTable.fnGetPosition(row));

		}
		console.debug(wwwroot + '/mod/bigbluebuttonbn/bbb-broker.php?action=' + action + '&recordingid=' + recordingid + '&cid=' + courseid);
		/*
		$.ajax({
		    url	: wwwroot + '/mod/bigbluebuttonbn/bbb-broker.php?action=' + action + '&recordingid=' + recordingid + '&cid=' + courseid,
		    dataType : 'xml'
		});
		*/
	}
}


/*
M.mod_recordingsbn.test = function(Y) {
	 
    // example to submit a form field on change
    Y.on('change', function(e) {
        Y.one('#mform1').submit();
    }, '#id_fieldname' );
};
*/