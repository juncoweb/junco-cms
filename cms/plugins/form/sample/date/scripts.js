
/* --- Test ----------------------------- */
var Test = function() {
	JsFelem.load('#content');
	//
	FeDate(null, {
		'inject': document.querySelector('#date-test'),
		'setDrop': false,
		'onSelect': function(date) {
			alert(date);
		}
	});
	
};
