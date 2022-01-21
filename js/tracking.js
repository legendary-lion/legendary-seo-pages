// vanilla javascript to call to the function to add an entry to the db, this should be set to 2000 by default
let time = 2000
setTimeout( function(){
	// alert('Waited for... ' + time / 1000 + ' seconds.')
	// call function from javascript
	
	var data = {
		'action': 'll_marketing_add_data',
		'ip_address': '<?php echo $ip_address;?>',
		'name': '<?php echo $traffic_source;?>',
		'url': '<?php echo $url;?>',

	};
	var ajax_url = "<?php echo admin_url('admin-ajax.php');?>";

	// We can also pass the url value separately from ajaxurl for front end AJAX implementations
	jQuery.post(ajax_url, data, function(response) {
		console.log(response);
	});

}, time);
