$(function(){
	var form = $('form '),
		error_msg = form.find('.error-msg'),
		success_msg = form.find('.success-msg');

	if (error_msg.data('error') !== '' ){
		error_msg.show();
	}

	if (success_msg.data('success') !== '' ){
		success_msg.show();
	}


	var filter_table = $('.filter-data');
	filter_table.find('.score').each(function(){
		var $this = $(this),
			image = $this.parent().find('.img img'),
			score = $this.data('score_color'),
			url = 'img/';

		if (score >= 2 && score < 3) {
			$this.addClass('red');
			image.attr('src', url + '1.png');
		} else if (score >= 3 && score < 4) {
			$this.addClass('orange');
			image.attr('src', url + '2.png');					
		} else if (score >= 4 && score < 5) {
			$this.addClass('blue');			
			image.attr('src', url + '3.png');
		} else if (score >= 5 && score < 6) {
			$this.addClass('purpul');		
			image.attr('src', url + '4.png');				
		} else if (score === 6) {
			$this.addClass('green');
			image.attr('src', url + '5.png');
				
		}
	});

	var setting_form = $('#makeSetting');
	setting_form.on('click', 'input[type=submit]', function(e){
		e.preventDefault();
		var students = $(this).data('students_array'),
			input_array = setting_form.find('.form-group.inline input'),
			msg = 'You created formula for final score: ';
		
		input_array.each(function(index){
			var $this = $(this),
				cat_title = $this.data('cat_title'),
				coef_value = $this.val();
			console.log(input_array.length, index)
			
			msg += cat_title + "*" + coef_value; 
			if (input_array.length - 1 !== index) {
				msg += ' + ';
			} 
			// console.log(msg);
		});
		
		$('.success-msg span').text(msg);
		$('.success-msg').show();

		setting_form.submit();
	})
});