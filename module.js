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

	YUI({ filter: 'raw' }).use('datatable-sort', 'datatable-column-widths', 'datatablepaginator', 'paginatorview', function (Y) {
	    var table = new Y.DataTable({
	    	columns: recordingsbn.columns,
	        data: recordingsbn.data
	    }).render('#recordingsbn_yui_table');
	});
	
}

M.mod_recordingsbn.gallery_datatable_init = function(Y) {

    recordingsbn.columns[3].formatter = fmtDate;
    for(var i = 0; i < recordingsbn.data.length; i++){
        recordingsbn.data[i].date = new Date(recordingsbn.data[i].date);
    }
    
    YUI({ combine:false, filter: 'raw' 
        }).use( 'datatable-scroll', 
                'datatable-sort', 
                'datatable-column-widths', 
                'cssfonts', 
                'cssbutton', 
                'querystring-parse', 
                'datatablepaginator', 
                'paginatorview', function (Y) {
        var table = new Y.DataTable({
            columns: recordingsbn.columns,
            data: recordingsbn.data,
            paginator: new Y.PaginatorView({
                model: new Y.PaginatorModel({
                    itemsPerPage: 10 
                }),
                container: '#recordingsbn_yui_paginator'
            }),
     
            paginationSource:  'client'  // client-side pagination

        }).render('#recordingsbn_yui_table');
    });
    
    function fmtDate(o){
        return Y.DataType.Date.format(o.value,{format:"%a %h %d, %Y %H:%M:%S %Z"});
    }
    
}
