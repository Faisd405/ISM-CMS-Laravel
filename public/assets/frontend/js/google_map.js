var map;
	function initMap() {
	var myLatlng = new google.maps.LatLng(-6.238865, 106.831741);

	map = new google.maps.Map(document.getElementById('map'), {
		center: myLatlng,
		zoom: 14,
		gestureHandling: 'greedy',
		scrollwheel: false,
	});

	var marker = new google.maps.Marker({
		position: myLatlng,
		map: map,
		title: 'IISIA (The Indonesian Iron and Steel Industry Association)',
	});
}