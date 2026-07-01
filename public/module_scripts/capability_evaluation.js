$(document).ready(function() {
    $(document).on("click", ".delete", function(e) {
        e.preventDefault();
        var record_id = $(this).attr('data-id');
        
        if (confirm("Are you sure you want to delete this record? This action cannot be undone.")) {
            // Set token in the hidden form (same pattern as other modules)
            $('#delete_token').val(record_id);
            var obj = $('#delete_record');
            
            $.ajax({
                type: "POST",
                url: obj.attr('action'),
                data: obj.serialize(),
                cache: false,
                success: function(JSON) {
                    if (JSON.error != '') {
                        toastr.error(JSON.error);
                        $('input[name="csrf_token"]').val(JSON.csrf_hash);
                    } else {
                        toastr.success(JSON.result);
                        $('input[name="csrf_token"]').val(JSON.csrf_hash);
                        setTimeout(function(){
                            window.location.reload();
                        }, 1000);
                    }
                }
            });
        }
    });
});
