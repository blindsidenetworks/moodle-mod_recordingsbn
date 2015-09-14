/**
 * @namespace
 */
M.mod_recordingsbn = M.mod_recordingsbn || {};

/**
 * This function is initialized from PHP
 *
 * @param {Object}
 *            Y YUI instance
 */
M.mod_recordingsbn.datatable_init = function(Y) {
    for(var i = 0; i < recordingsbn.data.length; i++){
        recordingsbn.data[i].date = new Date(recordingsbn.data[i].date);
    }

	YUI().use('datatable', 'datatable-sort', 'datatable-paginator', 'datatype-number', function (Y) {
        var table = new Y.DataTable({
            width:  "900px",
            columns: recordingsbn.columns,
            data: recordingsbn.data,
	        rowsPerPage: 10,
            paginatorLocation: ['header', 'footer']
	    }).render('#recordingsbn_yui_table');
	});
}
