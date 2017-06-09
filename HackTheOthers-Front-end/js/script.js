(function($, window){
	//  remove owner solution
	var $remove_trigger = $('.fa-times-circle-o');

	$remove_trigger.on('click', function(e) {
		if (confirm("Are You sure?")) {
			$(this).closest('.solution').remove();
		}
	});

	// filter profiles
	var $filter_dropdown = $('.filter-dropdown'),
		$filter = $('.isotope-menu'),
		$container = $('.all-profiles');

	// Trigger item lists filter when link clicked
	$filter.find('a').click(function() {
		var $this = $(this),
			selector = $this.data('filter'),
			select_value = $this.data('value');
		
		$filter_dropdown.find('.dropdown-toggle .choose').text(select_value);
		// $filter.find('a').removeClass('active');
		// $(this).addClass('active');
		$filter_dropdown.removeClass('open');
		
		if ($container.length > 0) {
			$container.isotope({
				filter			 : selector,
				animationOptions : {
					animationDuration : 400,
					queue : false
				}
			});
		}
		

		return false;
	});

	//  choose language and level for new task
	var $choose_lng = $('.choose-language'),
		$choose_level = $('.choose-level'),
		$result_lng = $('.result-language'),
		$result_level = $('.result-level'),
		$remove_btn = $('.close');

	$result_lng.hide();
	$result_level.hide();

	$choose_lng.on('click', 'a', function() {
		var $this = $(this),
			$value = $this.data('value');

		$result_lng.find('.value').text($value);
		$result_lng.show();
	});

	$choose_level.on('click', 'a', function() {
		var $this = $(this),
			$value = $this.data('value');

		$result_level.find('.value').text($value);
		$result_level.show();
	});

	$remove_btn.on('click', function() {
		$(this).closest('.alert').hide();
	});

})(jQuery, window);