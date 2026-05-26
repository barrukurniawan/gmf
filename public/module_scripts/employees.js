$(document).ready(function() {
   var xin_table = $('#xin_table').dataTable({
        "bDestroy": true,
		"ajax": {
            url : main_url+"employees/employees_list",
            type : 'GET'
        },
		"language": {
            "lengthMenu": dt_lengthMenu,
            "zeroRecords": dt_zeroRecords,
            "info": dt_info,
            "infoEmpty": dt_infoEmpty,
            "infoFiltered": dt_infoFiltered,
			"search": dt_search,
			"paginate": {
				"first": dt_first,
				"previous": dt_previous,
				"next": dt_next,
				"last": dt_last
			},
        },
		"fnDrawCallback": function(settings){
		$('[data-toggle="tooltip"]').tooltip();          
		}
    });
	jQuery("#department_id").change(function(){
		jQuery.get(main_url+"employees/is_designation/"+jQuery(this).val(), function(data, status){
			jQuery('#designation_ajax').html(data);
		});
	});
	/* Delete data */
	$("#delete_record").submit(function(e){
	/*Form Submit*/
	e.preventDefault();
		var obj = $(this), action = obj.attr('name');
		$.ajax({
			type: "POST",
			url: e.target.action,
			data: obj.serialize()+"&is_ajax=2&type=delete_record&form="+action,
			cache: false,
			success: function (JSON) {
				if (JSON.error != '') {
					toastr.error(JSON.error);
					$('input[name="csrf_token"]').val(JSON.csrf_hash);
					Ladda.stopAll();
				} else {
					$('.delete-modal').modal('toggle');
					xin_table.api().ajax.reload(function(){ 
						toastr.success(JSON.result);
					}, true);		
					$('input[name="csrf_token"]').val(JSON.csrf_hash);	
					Ladda.stopAll();				
				}
			}
		});
	});
	
	// edit
	$('.edit-modal-data').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget);
		var user_id = button.data('field_id');
		var modal = $(this);
	$.ajax({
		url : main_url+"users/read",
		type: "GET",
		data: 'jd=1&data=user&user_id='+user_id,
		success: function (response) {
			if(response) {
				$("#ajax_modal").html(response);
			}
		}
		});
	});
	
	$('.view-modal-data').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget);
		var user_id = button.data('field_id');
		var modal = $(this);
	$.ajax({
		url :  main_url+"users/read",
		type: "GET",
		data: 'jd=1&type=view_user&user_id='+user_id,
		success: function (response) {
			if(response) {
				$("#ajax_view_modal").html(response);
			}
		}
		});
	});
	
	$('.custom-file-input').on('change', function() {
		var $input = $(this);
		var fileName = $input.val().split('\\').pop();
		var $label = $input.siblings('.custom-file-label');
		var maxSizeMb = $input.data('max-size');
		if (fileName) {
			if (maxSizeMb) {
				var maxSizeBytes = maxSizeMb * 1024 * 1024;
				var file = this.files[0];
				if (file && file.size > maxSizeBytes) {
					toastr.error('File terlalu besar. Maksimal ukuran file: ' + maxSizeMb + ' MB');
					$input.val('');
					$label.removeClass('selected').html($label.data('original'));
					return;
				}
			}
			$label.addClass('selected').html('<i class="fas fa-check-circle text-success mr-1"></i>' + fileName);
		} else {
			$label.removeClass('selected').html($label.data('original'));
		}
	});

	$('.custom-file-label').each(function() {
		$(this).data('original', $(this).html());
	});

	/* Email auto-suggestion dengan domain perusahaan */
	var companyDomain = '@globalmaintenance.co.id';
	$('input[name="email"]').on('input', function() {
		var val = $(this).val();
		var atPos = val.indexOf('@');
		if (atPos !== -1) {
			var beforeAt = val.substring(0, atPos);
			var afterAt = val.substring(atPos);
			if (companyDomain.startsWith(afterAt) && afterAt.length >= 2) {
				$('#email-datalist').html('<option value="' + beforeAt + companyDomain + '">');
			} else {
				$('#email-datalist').html('');
			}
		} else {
			$('#email-datalist').html('');
		}
	});

	/* Hourly Rate auto-calculate dari Basic Salary (40 jam/minggu × 4 minggu = 160 jam/bulan) */
	$('input[name="basic_salary"]').on('input', function() {
		var basicSalary = parseFloat($(this).val().replace(/[^0-9.]/g, ''));
		if (!isNaN(basicSalary) && basicSalary > 0) {
			var hourlyRate = Math.round(basicSalary / 160);
			$('input[name="hourly_rate"]').val(hourlyRate);
		}
	});

	/* Add data */ /*Form Submit*/
	$("#xin-form").submit(function(e){
		var fd = new FormData(this);
		var obj = $(this), action = obj.attr('name');
		fd.append("is_ajax", 1);
		fd.append("type", 'add_record');
		fd.append("form", action);
		e.preventDefault();		
		$.ajax({
			url: e.target.action,
			type: "POST",
			data:  fd,
			contentType: false,
			cache: false,
			processData:false,
			success: function(JSON)
			{
				if (JSON.error != '') {
					toastr.error(JSON.error);
					$('input[name="csrf_token"]').val(JSON.csrf_hash);
					Ladda.stopAll();
				} else {
					xin_table.api().ajax.reload(function(){ 
						toastr.success(JSON.result);
					}, true);
					$('input[name="csrf_token"]').val(JSON.csrf_hash);
					$('#xin-form')[0].reset(); // To reset form fields
					$('.custom-file-label').removeClass('selected').each(function() {
						$(this).html($(this).data('original'));
					});
					$('.add-form').removeClass('show');
					Ladda.stopAll();
				}
			},
			error: function() 
			{
				toastr.error(JSON.error);
				$('input[name="csrf_token"]').val(JSON.csrf_hash);
					Ladda.stopAll();
			} 	        
	   });
	});
});
$( document ).on( "click", ".delete", function() {
	$('input[name=_token]').val($(this).data('record-id'));
	$('#delete_record').attr('action',main_url+'employees/delete_staff');
});