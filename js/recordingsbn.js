function actionCall(action, recordingid, courseid) {
	action = (typeof action == 'undefined') ? 'publish' : action;
	
	if (action == 'publish' || action == 'unpublish' || (action == 'delete' && confirm("Are you sure to delete this recording?"))) {
		if (action == 'publish' || action == 'unpublish') {
			
			var el_a = document.getElementById('actionbar-publish-a-'+ recordingid);
			if (el_a) {
				var el_img = document.getElementById('actionbar-publish-img-'+ recordingid);
				if (el_a.title == view_recording_list_actionbar_hide ) {
					el_a.title = view_recording_list_actionbar_show;
					el_img.src = 'pix/show.gif';

				} else {
					el_a.title = view_recording_list_actionbar_hide;
					el_img.src = 'pix/hide.gif';

				}

			}
			
		} else {
			// Deletes the line in the dataTable
			var row = $(document.getElementById('actionbar-publish-img-'+ recordingid)).closest("tr").get(0);
			oTable.fnDeleteRow(oTable.fnGetPosition(row));

		}
		$.ajax({
		    url	: M.cfg.wwwroot + '/mod/bigbluebuttonbn/bbb-broker.php?action=' + action + '&recordingid=' + recordingid + '&cid=' + courseid,
		    dataType : 'xml'
		});
		
	}
}

$.fn.dataTableExt.oApi.fnReloadAjax = function(oSettings, sNewSource, fnCallback, bStandingRedraw) {

	if (typeof sNewSource != 'undefined' && sNewSource != null) {
		oSettings.sAjaxSource = sNewSource;
	}
	this.oApi._fnProcessingDisplay(oSettings, true);
	var that = this;
	var iStart = oSettings._iDisplayStart;

	oSettings.fnServerData(oSettings.sAjaxSource, null, function(json) {
		/* Clear the old information from the table */
		that.oApi._fnClearTable(oSettings);

		/* Got the data - add it to the table */
		for ( var i = 0; i < json.aaData.length; i++) {
			that.oApi._fnAddData(oSettings, json[oSettings.sAjaxDataProp][i]);
		}

		oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
		that.fnDraw(that);

		if (typeof bStandingRedraw != 'undefined' && bStandingRedraw === true) {
			oSettings._iDisplayStart = iStart;
			that.fnDraw(false);
		}

		that.oApi._fnProcessingDisplay(oSettings, false);

		/* Callback user function - for event handlers etc */
		if (typeof fnCallback == 'function' && fnCallback != null) {
			fnCallback(oSettings);
		}
	}, oSettings);
}

var oTable;

$(document).ready(function(){
    oTable = $('#recordingsbn').dataTable( {
        "aoColumns": [
            {"sTitle": view_recording_list_recording, "sWidth": "150px"},
            {"sTitle": view_recording_list_course, "sWidth": "150px"},
            {"sTitle": view_recording_list_activity, "sWidth": "150px"},
            {"sTitle": view_recording_list_description, "sWidth": "150px"},
            {"sTitle": view_recording_list_date, "sWidth": "200px", "sClass": "right"},
            {"sTitle": view_recording_list_actionbar, "sWidth": "50px", "sClass": "right", "bVisible" : false}
            ],
	    
        "oTableTools": {
            "sRowSelect": "multi",
            "aButtons": [ "select_all", "select_none" ]
            },
                
        "sAjaxSource": M.cfg.wwwroot + "/mod/bigbluebuttonbn/ajax.php?cid=" + courseid + "&mid=" + meetingid,
        "bFilter": false,
        "bPaginate": false,
        "bInfo": false,
        "fnInitComplete": function () {
            oTable.fnReloadAjax();
        }
    });
            
    if (ismoderator == 'true' )
        oTable.fnSetColumnVis( 5, true );			
             
    setInterval(function() {
        oTable.fnReloadAjax();
    }, 300000);

});
