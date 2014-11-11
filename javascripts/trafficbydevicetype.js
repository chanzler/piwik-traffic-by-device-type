/*!
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */

var settings = $.extend( {
	rowHeight			: 25,
});
var history = [];

/**
 * jQueryUI widget for Live visitors widget
 */
$(function() {
    var refreshTrafficByDeviceTypeWidget = function (element, refreshAfterXSecs) {
        // if the widget has been removed from the DOM, abort
        if ($(element).parent().length == 0) {
            return;
        }
        var lastMinutes = $(element).find('.dynameter').attr('data-last-minutes') || 30;

        var ajaxRequest = new ajaxHelper();
        ajaxRequest.addParams({
            module: 'API',
            method: 'TrafficByDeviceType.getTrafficByDeviceType',
            format: 'json',
            lastMinutes: lastMinutes
        }, 'get');
        ajaxRequest.setFormat('json');
        ajaxRequest.setCallback(function (data) {
        	data.sort(function(a, b){
        	    return b.percentage - a.percentage;
        	});
        	$.each( data, function( index, value ){
              	var pc = value['percentage'];
        		pc = pc > 100 ? 100 : pc;
        		$('#TrafficByDeviceTypeChart').find("div[id="+value['id']+"]").children('.percent').html(pc+'%');
        		var ww = $('#TrafficByDeviceTypeChart').find("div[id="+value['id']+"]").width();
        		var len = parseInt(ww, 10) * parseInt(pc, 10) / 100;
        		$('#TrafficByDeviceTypeChart').find("div[id="+value['id']+"]").children('.bar').animate({ 'width' : len+'px' }, 1500);
        		$('#TrafficByDeviceTypeChart').find("div[id="+value['id']+"]").attr("index", index);

        	});
			//animation
			var vertical_offset = 0; // Beginning distance of rows from the table body in pixels
			for ( index = 0; index < data.length; index++) {
				$("#TrafficByDeviceTypeChart").find("div[index="+index+"]").stop().delay(1 * index).animate({ top: vertical_offset}, 1000, 'swing').appendTo("#TrafficByDeviceTypeChart");
				vertical_offset += settings['rowHeight'];
			}
			for ( index = data.length; index <= 20; index++) {
				$("#TrafficByDeviceTypeChart").find("div[index="+index+"]").hide();
			}
            // schedule another request
            setTimeout(function () { refreshTrafficByDeviceTypeWidget(element, refreshAfterXSecs); }, refreshAfterXSecs * 1000);
        });
        ajaxRequest.send(true);
    };

    var exports = require("piwik/TrafficByDeviceType");
    exports.initSimpleRealtimeTrafficByDeviceTypeWidget = function (refreshInterval) {
        var ajaxRequest = new ajaxHelper();
        ajaxRequest.addParams({
            module: 'API',
            method: 'TrafficByDeviceType.getTrafficByDeviceType',
            format: 'json',
            lastMinutes: 30
        }, 'get');
        ajaxRequest.setFormat('json');
        ajaxRequest.setCallback(function (data) {
        	data.sort(function(a, b){
        	    return b.percentage - a.percentage;
        	});
            $('#TrafficByDeviceTypeChart').each(function() {
                // Set table height and width
    			$("#TrafficByDeviceTypeChart").height((data.length*settings['rowHeight']));

    			for (j=0; j<data.length; j++){
                	$("#TrafficByDeviceTypeChart").find("div[index="+j+"]").css({ top: (j*settings['rowHeight']) }).appendTo("#TrafficByDeviceTypeChart");
                }
            });
        	$.each( data, function( index, value ){
               	var pc = value['percentage'];
        		pc = pc > 100 ? 100 : pc;
        		$('#TrafficByDeviceTypeChart').find("div[index="+index+"]").attr("id", value['id']);
        		$('#TrafficByDeviceTypeChart').find("div[index="+index+"]").children('.percent').html(pc+'%');
        		$('#TrafficByDeviceTypeChart').find("div[index="+index+"]").children('.title').text(value['name']);
        		var ww = $('#TrafficByDeviceTypeChart').find("div[index="+index+"]").width();
        		var len = parseInt(ww, 10) * parseInt(pc, 10) / 100;
        		$('#TrafficByDeviceTypeChart').find("div[index="+index+"]").children('.bar').animate({ 'width' : len+'px' }, 1500);
        	});
            $('#TrafficByDeviceTypeChart').each(function() {
    			var $this = $(this),
                   refreshAfterXSecs = refreshInterval;
                setTimeout(function() { refreshTrafficByDeviceTypeWidget($this, refreshAfterXSecs ); }, refreshAfterXSecs * 1000);
            });
        });
        ajaxRequest.send(true);
     };
});

