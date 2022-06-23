(function($) {
	'use strict';

	$(document).on('hivepress:init', function(event, container) {

		// Rating
		container.find(hivepress.getSelector('rating')).each(function() {
			var field = $(this);

			field.raty({
				starType: 'i',
				starOff: 'fas fa-star',
				starHalf: 'fas fa-star-half active',
				starOn: 'fas fa-star active',
				hints: ['', '', '', '', ''],
				noRatedMsg: '',
				readOnly: typeof field.data('name') === 'undefined',
				scoreName: field.data('name'),
				score: field.data('value'),
			});
		});
	});
})(jQuery);
