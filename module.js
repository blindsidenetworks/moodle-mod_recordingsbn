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
var bigbluebuttonbn_dataSource;
var bigbluebuttonbn_ping_interval_id;

M.mod_recordingsbn.datatable_init = function(Y) {
    var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    for(var i = 0; i < recordingsbn.data.length; i++){
        var date = new Date(recordingsbn.data[i].date);
        recordingsbn.data[i].date = date.toLocaleDateString(bigbluebuttonbn.locale, options);
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
