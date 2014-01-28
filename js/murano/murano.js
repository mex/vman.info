var murano = {};

murano = function ()
{
	var pub = {};
	var self = {};

	pub.init = function ()
	{
		$('#dataTable').tablesorter();
		$('#dataTable2').tablesorter({
			headers: {
				1: {sorter: false}
			}
		});
		$('#dataTable3').tablesorter();
        
        
		$('#gallery a').lightBox ();
		
		$('.bargraph').visualize({ 
			type: 'bar',
			width: '650',
			height: '200px',
			colors: ['#111','#333','#555','#777','#999','#bbb','#ccc','#eee'],
			appendTitle: false
		});
		
		$('#search').find ('input').live ('click' , function () { $(this).val ('') });
		$("select, input:checkbox, input:radio, input:file").uniform();
	}
	
	return pub;
	
}();