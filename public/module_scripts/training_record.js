(function($) {
  "use strict";

  $(document).ready(function() {
    // --- List Page: DataTable ---
    if ($('#xin_table').length > 0) {
      $('#xin_table').DataTable({
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
          }
        },
        "fnDrawCallback": function(settings) {
          $('[data-toggle="tooltip"]').tooltip();
        }
      });
    }

    // --- Form Page: Dynamic rows + AJAX submit ---
    if ($('#training_record_form').length > 0) {
      // Init datepickers
      $('.date').bootstrapMaterialDatePicker({
        weekStart: 0,
        time: false,
        clearButton: false,
        format: 'YYYY-MM-DD',
        lang: 'en'
      });

      // Add education row
      $('#add_education_row').on('click', function(){
        var row = '<tr>' +
          '<td><input type="text" class="form-control" name="degree[]" placeholder="Degree"></td>' +
          '<td><input type="text" class="form-control" name="institution[]" placeholder="Institution"></td>' +
          '<td><input type="text" class="form-control" name="major[]" placeholder="Major"></td>' +
          '<td><input type="text" class="form-control" name="graduate_year[]" placeholder="Year"></td>' +
          '<td class="text-center"><i class="feather icon-trash-2 btn-remove-row"></i></td>' +
        '</tr>';
        $('#education_table tbody').append(row);
      });

      // Add training row
      $('#add_training_row').on('click', function(){
        var row = '<tr>' +
          '<td><input type="text" class="form-control" name="course_title[]" placeholder="Course Title"></td>' +
          '<td><input type="text" class="form-control" name="course_objective[]" placeholder="Objective"></td>' +
          '<td><input type="text" class="form-control date" name="date_completed[]" placeholder="Date"></td>' +
          '<td><input type="text" class="form-control" name="test_result[]" placeholder="Test Result"></td>' +
          '<td><input type="text" class="form-control" name="total_hours[]" placeholder="Hours"></td>' +
          '<td><input type="text" class="form-control" name="training_institution[]" placeholder="Institution"></td>' +
          '<td><input type="text" class="form-control" name="location[]" placeholder="Location"></td>' +
          '<td><input type="text" class="form-control" name="instructor_name[]" placeholder="Instructor"></td>' +
          '<td class="text-center"><i class="feather icon-trash-2 btn-remove-row"></i></td>' +
        '</tr>';
        $('#training_table tbody').append(row);
        $('#training_table tbody tr:last .date').bootstrapMaterialDatePicker({
          weekStart: 0, time: false, clearButton: false,
          format: 'YYYY-MM-DD', lang: 'en'
        });
      });

      // Add license row
      $('#add_license_row').on('click', function(){
        var row = '<tr>' +
          '<td><select class="form-control" name="license_type[]"><option value="">Select Type</option><option value="AME">AME License</option><option value="COMA">COMA</option><option value="COC">C of C</option></select></td>' +
          '<td><input type="text" class="form-control" name="license_no[]" placeholder="License No"></td>' +
          '<td><input type="text" class="form-control date" name="expired_date[]" placeholder="Expired Date"></td>' +
          '<td><input type="text" class="form-control" name="rating[]" placeholder="Rating"></td>' +
          '<td class="text-center"><i class="feather icon-trash-2 btn-remove-row"></i></td>' +
        '</tr>';
        $('#license_table tbody').append(row);
        $('#license_table tbody tr:last .date').bootstrapMaterialDatePicker({
          weekStart: 0, time: false, clearButton: false,
          format: 'YYYY-MM-DD', lang: 'en'
        });
      });

      // Remove row
      $(document).on('click', '.btn-remove-row', function(){
        var tbody = $(this).closest('tbody');
        if(tbody.find('tr').length > 1){
          $(this).closest('tr').remove();
        } else {
          toastr.warning('At least one row is required.');
        }
      });

      // Form submit via AJAX
      Ladda.bind('button[type=submit]');

      function isCanvasBlank(canvas) {
        if (!canvas) return true;
        var blank = document.createElement('canvas');
        blank.width = canvas.width;
        blank.height = canvas.height;
        return canvas.toDataURL() === blank.toDataURL();
      }

      function dataURLToBlob(dataURL) {
        var parts = dataURL.split(',');
        var mime = parts[0].match(/:(.*?);/)[1];
        var binary = atob(parts[1]);
        var array = [];
        for (var i = 0; i < binary.length; i++) {
          array.push(binary.charCodeAt(i));
        }
        return new Blob([new Uint8Array(array)], {type: mime});
      }

      $("#training_record_form").submit(function(e){
        e.preventDefault();
        var form = this;
        var fd = new FormData(form);
        var canvas = document.getElementById('signature_canvas');

        if (fd.has('approved_by_sig')) {
          fd.delete('approved_by_sig');
        }

        var processSubmission = function() {
          var prepId = $('#prepared_by_id').val();
          var appvId = $('#approved_by_id').val();

          var executeSubmit = function(sendValue) {
            fd.append('send_email_signatories', sendValue ? '1' : '0');
            submitTrainingRecordAjax(fd, form);
          };

          if (prepId || appvId) {
            Swal.fire({
              title: 'Send Notification Email?',
              text: "Apakah anda ingin mengirim email ke Assign Signatories untuk segera tanda tangan?",
              icon: 'question',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Ya, Kirim & Save',
              cancelButtonText: 'Tidak, Save Saja'
            }).then((result) => {
              if (result.isConfirmed) {
                executeSubmit(true);
              } else if (result.dismiss === Swal.DismissReason.cancel) {
                executeSubmit(false);
              } else {
                Ladda.stopAll();
              }
            });
          } else {
            executeSubmit(false);
          }
        };

        var submitAfterCanvas = function() {
          processSubmission();
        };

        if ($('#draw_sig').hasClass('active') && canvas && !isCanvasBlank(canvas)) {
          if (typeof canvas.toBlob === 'function') {
            canvas.toBlob(function(blob) {
              var dataURL = canvas.toDataURL('image/png');
              fd.append('approved_by_signature_file', blob, 'signature.png');
              fd.append('approved_by_sig', dataURL);
              submitAfterCanvas();
            }, 'image/png');
          } else {
            var dataURL = canvas.toDataURL('image/png');
            var blob = dataURLToBlob(dataURL);
            fd.append('approved_by_signature_file', blob, 'signature.png');
            fd.append('approved_by_sig', dataURL);
            submitAfterCanvas();
          }
        } else {
          submitAfterCanvas();
        }
      });

      function submitTrainingRecordAjax(fd, form) {
        var obj = $(form), action = obj.attr('name');
        var redirectUrl = main_url + 'training-record';
        fd.append("is_ajax", 1);
        fd.append("type", 'save_record');
        fd.append("form", action);
        
        $.ajax({
          url: form.action,
          type: "POST",
          data:  fd,
          contentType: false,
          cache: false,
          processData: false,
          success: function(JSON) {
            if (JSON.error != '') {
              toastr.error(JSON.error);
              $('input[name="csrf_token"]').val(JSON.csrf_hash);
              Ladda.stopAll();
            } else {
              toastr.success(JSON.result);
              $('input[name="csrf_token"]').val(JSON.csrf_hash);
              Ladda.stopAll();
              setTimeout(function(){
                window.location.href = redirectUrl;
              }, 1500);
            }
          },
          error: function() {
            toastr.error('An error occurred.');
            $('input[name="csrf_token"]').val($('input[name="csrf_token"]').val());
            Ladda.stopAll();
          }
        });
      }
    }
  });
})(jQuery);
