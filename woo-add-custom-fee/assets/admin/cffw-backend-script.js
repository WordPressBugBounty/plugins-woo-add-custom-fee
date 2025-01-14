jQuery(document).ready(function($) {
	var type = $("select[name='wacf_type']").val();
	
	wcaf_condition_text( type );

	$("select[name='wacf_type']").on('change', function () {
		wcaf_condition_text( $(this).val() );
	})
});

function wcaf_condition_text( value ) {
	if( value == 'percentage' ) {
		jQuery("label[for='wacf_fee_charges']").text("Custom Fee percentage");
	} else {
		jQuery("label[for='wacf_fee_charges']").text("Custom Fee charges");
	}

	function toggleRequiredField(checkboxSelector, inputSelector) {
	    jQuery(inputSelector).prop('required', jQuery(checkboxSelector).is(':checked'));
	}

	jQuery(document).on('change', '#wacf_enable_min', function() {
	    toggleRequiredField('#wacf_enable_min', '#wacf_minimum');
	});

	jQuery(document).on('change', '#wacf_enable_max', function() {
	    toggleRequiredField('#wacf_enable_max', '#wacf_maximum');
	});

	// Trigger the initial state
	jQuery('#wacf_enable_min').change();
	jQuery('#wacf_enable_max').change();

}