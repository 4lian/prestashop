
product_tabs['Shippingpro'] = new function(){

	$(".addSPCarrier").on('click', function() {
		var $div = $(this).closest('.SPGroup');
		$div.find('.availableSPCarriers option:selected').each( function() {
			$div.find('.selectedSPCarriers').append("<option value='"+$(this).val()+"'>"+$(this).text()+"</option>");
			$(this).remove();
		});
		// $div.find('.selectedSPCarriers option').prop('selected', true);
	   
		if ($div.find('.selectedSPCarriers').find("option").length == 0)
			$div.find('.no-selected-carries-alert').show();
		else
			$div.find('.no-selected-carries-alert').hide();
	});

	$(".removeSPCarrier").on('click', function() {
		var $div = $(this).closest('.SPGroup');
		$div.find('.selectedSPCarriers option:selected').each( function() {
			$div.find('.availableSPCarriers').append("<option value='"+$(this).val()+"'>"+$(this).text()+"</option>");
			$(this).remove();
		});
		// $div.find('.selectedSPCarriers option').prop('selected', true);

		if ($div.find('.selectedSPCarriers').find("option").length == 0)
			$div.find('.no-selected-carries-alert').show();
		else
			$div.find('.no-selected-carries-alert').hide();
	});

};

