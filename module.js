/**
 * @namespace
 */
M.mod_recordingsbn = M.mod_recordingsbn || {};

/**
 * This function is initialized from PHP
 *
 * @param {Object} Y YUI instance
 */

M.mod_recordingsbn.view_actionCall = function() {
	console.debug(wwwroot + '/mod/bigbluebuttonbn/bbb-broker.php?action=' + action + '&recordingid=' + recordingid + '&cid=' + courseid);
}
