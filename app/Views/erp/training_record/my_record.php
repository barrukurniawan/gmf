<?php
use App\Models\UsersModel;
use App\Models\SystemModel;

$SystemModel = new SystemModel();
$UsersModel = new UsersModel();

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$xin_system = erp_company_settings();

$employee = $employee ?? [];
$staff_detail = $staff_detail ?? [];
$header = $header ?? [];
$education = $education ?? [];
$training_history = $training_history ?? [];
$licenses = $licenses ?? [];
$certificates = $certificates ?? [];
$employee_signature = $employee_signature ?? [];
$emp_sig_src = !empty($employee_signature['file_path']) ? base_url().'/'.$employee_signature['file_path'] : ($employee_signature['signature_data'] ?? '');
?>
<style>
.section-card { margin-bottom: 20px; }
.section-card .card-header { padding: 10px 15px; }
.section-card .card-header h5 { font-size: 15px; margin: 0; }
.form-group label { font-size: 13px; }
.sig-tabs { display: flex; gap: 10px; margin-bottom: 10px; }
.sig-tab { padding: 5px 12px; cursor: pointer; border: 1px solid #ccc; border-radius: 4px; background: #f8f9fa; }
.sig-tab.active { background: #0062cc; color: #fff; border-color: #0062cc; }
.sig-content { display: none; }
.sig-content.active { display: block; }
.signature-preview-img { max-width: 100%; max-height: 120px; border: 1px solid #ddd; display: none; margin-top: 5px; }
.signature-canvas-wrapper { position: relative; border: 1px solid #ddd; display: inline-block; }
#employee_signature_canvas { cursor: crosshair; background: #fff; }
.data-display-table { width: 100%; font-size: 13px; }
.data-display-table td { padding: 6px 10px; vertical-align: top; }
.data-display-table .label-cell { width: 150px; font-weight: 600; color: #555; }
.display-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.display-table th, .display-table td { border: 1px solid #dee2e6; padding: 6px 8px; text-align: center; }
.display-table th { background: #f8f9fa; font-weight: 600; }
.chk-box { font-size: 16px; margin-right: 2px; vertical-align: text-bottom; }
</style>

<div class="row">
  <div class="col-sm-12">
    <?php $attributes = array('name' => 'my_training_record_form', 'id' => 'my_training_record_form', 'autocomplete' => 'off', 'class' => 'm-b-1'); ?>
    <?php $hidden = array('user_id' => $employee['user_id']); ?>
    <?= form_open('erp/training-record-save-signature', $attributes, $hidden); ?>

    <input type="hidden" name="type" value="save_employee_sig" />
    <input type="hidden" name="employee_sig" id="employee_sig" value="" />

    <!-- Employee Info Banner -->
    <div class="card">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-md-2 text-center">
            <img src="<?= base_url().'/public/uploads/users/'.$employee['profile_photo']; ?>" alt="" class="img-fluid wid-80 rounded">
          </div>
          <div class="col-md-8">
            <h5><?= $employee['first_name'].' '.$employee['last_name']; ?></h5>
            <p class="mb-0 text-muted">
              <?= $designation['designation_name'] ?? '-'; ?> |
              Emp ID: <?= $staff_detail['employee_id'] ?? '-'; ?> |
              Phone: <?= $employee['contact_number'] ?? '-'; ?>
            </p>
          </div>
          <div class="col-md-2 text-right">
            <a href="<?= site_url('erp/training-record-print/'.uencode($employee['user_id'])); ?>" class="btn btn-info" target="_blank">
              <i class="feather icon-printer"></i> Print CV
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- A. Personal -->
    <div class="card section-card">
      <div class="card-header">
        <h5>A. Personal</h5>
      </div>
      <div class="card-body">
        <table class="data-display-table">
          <tr><td class="label-cell">Place of Birth</td><td>: <?= $header['place_of_birth'] ?? '-'; ?></td></tr>
          <tr><td class="label-cell">Address</td><td>: <?= $header['address'] ?? '-'; ?></td></tr>
          <tr><td class="label-cell">Phone Number</td><td>: <?= $employee['contact_number'] ?? '-'; ?></td></tr>
          <tr><td class="label-cell">Email</td><td>: <?= $employee['email'] ?? '-'; ?></td></tr>
        </table>
      </div>
    </div>

    <!-- B. Education -->
    <div class="card section-card">
      <div class="card-header">
        <h5>B. Education</h5>
      </div>
      <div class="card-body">
        <?php if(!empty($education)): ?>
        <div class="table-responsive">
          <table class="display-table">
            <thead>
              <tr><th>Degree</th><th>Institution</th><th>Major</th><th>Graduate Year</th></tr>
            </thead>
            <tbody>
              <?php foreach($education as $edu): ?>
              <tr><td><?= $edu['degree']; ?></td><td><?= $edu['institution']; ?></td><td><?= $edu['major']; ?></td><td><?= $edu['graduate_year']; ?></td></tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php else: ?>
        <p class="text-muted mb-0">No education data.</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- C. Training History -->
    <div class="card section-card">
      <div class="card-header">
        <h5>C. Training</h5>
      </div>
      <div class="card-body">
        <?php if(!empty($training_history)): ?>
        <div class="table-responsive">
          <table class="display-table">
            <thead>
              <tr><th>Course Title</th><th>Objective</th><th>Date Completed</th><th>Result</th><th>Hours</th><th>Location</th><th>Instructor</th></tr>
            </thead>
            <tbody>
              <?php foreach($training_history as $th): ?>
              <tr><td><?= $th['course_title']; ?></td><td><?= $th['course_objective']; ?></td><td><?= $th['date_completed']; ?></td><td><?= $th['test_result']; ?></td><td><?= $th['total_hours']; ?></td><td><?= $th['location']; ?></td><td><?= $th['instructor_name']; ?></td></tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php else: ?>
        <p class="text-muted mb-0">No training data.</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- D. Licenses & Certificates -->
    <div class="card section-card">
      <div class="card-header">
        <h5>D. Licenses & Certificates</h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <h6>AME License</h6>
            <?php
            $ame = array_values(array_filter($licenses, function($l) { return $l['license_type']=='AME'; }));
            $ame = $ame[0] ?? [];
            ?>
            <table class="data-display-table">
              <tr><td class="label-cell">No</td><td>: <?= $ame['license_no'] ?? '-'; ?></td></tr>
              <tr><td class="label-cell">Expired</td><td>: <?= $ame['expired_date'] ?? '-'; ?></td></tr>
              <tr><td class="label-cell">Rating</td><td>: <?= $ame['rating'] ?? '-'; ?></td></tr>
            </table>
          </div>
          <div class="col-md-6">
            <h6>COMA</h6>
            <?php
            $coma = array_values(array_filter($licenses, function($l) { return $l['license_type']=='COMA'; }));
            $coma = $coma[0] ?? [];
            ?>
            <table class="data-display-table">
              <tr><td class="label-cell">No</td><td>: <?= $coma['license_no'] ?? '-'; ?></td></tr>
              <tr><td class="label-cell">Expired</td><td>: <?= $coma['expired_date'] ?? '-'; ?></td></tr>
              <tr><td class="label-cell">Rating</td><td>: <?= $coma['rating'] ?? '-'; ?></td></tr>
            </table>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="col-md-6">
            <h6>C of C</h6>
            <?php
            $coc = array_values(array_filter($licenses, function($l) { return $l['license_type']=='COC'; }));
            $coc = $coc[0] ?? [];
            ?>
            <table class="data-display-table">
              <tr><td class="label-cell">No</td><td>: <?= $coc['license_no'] ?? '-'; ?></td></tr>
              <tr><td class="label-cell">Expired</td><td>: <?= $coc['expired_date'] ?? '-'; ?></td></tr>
              <tr><td class="label-cell">Rating</td><td>: <?= $coc['rating'] ?? '-'; ?></td></tr>
            </table>
          </div>
          <div class="col-md-6">
            <h6>Basic Certificate</h6>
            <p>
              <span class="chk-box"><?= (!empty($certificates['a1'])) ? '&#9745;' : '&#9744;'; ?></span> A1 &nbsp;&nbsp;
              <span class="chk-box"><?= (!empty($certificates['a2'])) ? '&#9745;' : '&#9744;'; ?></span> A2 &nbsp;&nbsp;
              <span class="chk-box"><?= (!empty($certificates['a3'])) ? '&#9745;' : '&#9744;'; ?></span> A3 &nbsp;&nbsp;
              <span class="chk-box"><?= (!empty($certificates['a4'])) ? '&#9745;' : '&#9744;'; ?></span> A4
              <br>
              <span class="chk-box"><?= (!empty($certificates['c1'])) ? '&#9745;' : '&#9744;'; ?></span> C1 &nbsp;&nbsp;
              <span class="chk-box"><?= (!empty($certificates['c2'])) ? '&#9745;' : '&#9744;'; ?></span> C2 &nbsp;&nbsp;
              <span class="chk-box"><?= (!empty($certificates['c4'])) ? '&#9745;' : '&#9744;'; ?></span> C4
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- E. Employee Signature -->
    <div class="card section-card">
      <div class="card-header">
        <h5>E. Employee Signature</h5>
      </div>
      <div class="card-body">
        <div class="sig-tabs">
          <div class="sig-tab active" data-target="draw_emp_sig">Free Draw</div>
          <div class="sig-tab" data-target="upload_emp_sig">Upload File</div>
          <?php if(!empty($emp_sig_src)): ?>
          <div class="sig-tab" data-target="preview_emp_sig">Current Signature</div>
          <?php endif; ?>
        </div>
        <div id="draw_emp_sig" class="sig-content active">
          <div class="signature-canvas-wrapper">
            <canvas id="employee_signature_canvas" width="400" height="150"></canvas>
          </div>
          <div class="mt-2">
            <button type="button" class="btn btn-sm btn-secondary" id="clear_emp_sig">Clear</button>
            <small class="text-muted ml-2">Draw your signature in the box above</small>
          </div>
        </div>
        <div id="upload_emp_sig" class="sig-content">
          <input type="file" class="form-control-file" name="employee_signature_file" id="employee_signature_file" accept="image/jpeg,image/jpg,image/png">
          <small class="text-muted">Allowed formats: JPG, JPEG, PNG (max 2MB)</small>
          <div id="upload_emp_preview_container" style="display:none; margin-top:10px;">
            <img id="upload_emp_preview_img" class="signature-preview-img" style="display:block;" />
          </div>
        </div>
        <?php if(!empty($emp_sig_src)): ?>
        <div id="preview_emp_sig" class="sig-content">
          <div class="mb-2">
            <img src="<?= $emp_sig_src; ?>" alt="Current Employee Signature" style="max-width:100%; max-height:120px; border:1px solid #ddd;">
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="delete_employee_signature" id="delete_employee_signature" value="1">
            <label class="form-check-label" for="delete_employee_signature">Delete current signature</label>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Submit -->
    <div class="card">
      <div class="card-footer text-right">
        <a href="<?= site_url('erp/desk'); ?>" class="btn btn-light">
          <i class="feather icon-arrow-left"></i> Back to Dashboard
        </a>
        <button type="submit" class="btn btn-primary ladda-button" data-style="expand-right" id="save_sig_btn">
          <i class="feather icon-save"></i> Save Signature
        </button>
      </div>
    </div>

    <?= form_close(); ?>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
  "use strict";

  // --- Signature Drawing ---
  var empCanvas = document.getElementById('employee_signature_canvas');
  var empCtx = empCanvas ? empCanvas.getContext('2d') : null;
  var isEmpDrawing = false;
  var hasEmpDrawn = false;

  function getEmpPos(e) {
    var rect = empCanvas.getBoundingClientRect();
    var clientX = e.touches ? e.touches[0].clientX : e.clientX;
    var clientY = e.touches ? e.touches[0].clientY : e.clientY;
    return {
      x: (clientX - rect.left) * (empCanvas.width / rect.width),
      y: (clientY - rect.top) * (empCanvas.height / rect.height)
    };
  }

  if(empCanvas && empCtx) {
    empCtx.lineWidth = 2;
    empCtx.lineCap = 'round';
    empCtx.strokeStyle = '#000000';

    empCanvas.addEventListener('mousedown', function(e) {
      isEmpDrawing = true;
      hasEmpDrawn = true;
      var p = getEmpPos(e);
      empCtx.beginPath();
      empCtx.moveTo(p.x, p.y);
    });
    empCanvas.addEventListener('mousemove', function(e) {
      if(!isEmpDrawing) return;
      var p = getEmpPos(e);
      empCtx.lineTo(p.x, p.y);
      empCtx.stroke();
    });
    empCanvas.addEventListener('mouseup', function() { isEmpDrawing = false; });
    empCanvas.addEventListener('mouseout', function() { isEmpDrawing = false; });
    empCanvas.addEventListener('touchstart', function(e) {
      e.preventDefault();
      isEmpDrawing = true;
      hasEmpDrawn = true;
      var p = getEmpPos(e);
      empCtx.beginPath();
      empCtx.moveTo(p.x, p.y);
    }, { passive: false });
    empCanvas.addEventListener('touchmove', function(e) {
      e.preventDefault();
      if(!isEmpDrawing) return;
      var p = getEmpPos(e);
      empCtx.lineTo(p.x, p.y);
      empCtx.stroke();
    }, { passive: false });
    empCanvas.addEventListener('touchend', function() { isEmpDrawing = false; });

    $('#clear_emp_sig').on('click', function() {
      if(empCtx) empCtx.clearRect(0, 0, empCanvas.width, empCanvas.height);
      hasEmpDrawn = false;
      $('#employee_sig').val('');
    });
  }

  // --- Signature Tabs ---
  $('.sig-tab').on('click', function() {
    $('.sig-tab').removeClass('active');
    $(this).addClass('active');
    $('.sig-content').removeClass('active');
    var target = $(this).data('target');
    if(target) $('#'+target).addClass('active');
  });

  // --- Upload Preview ---
  $('#employee_signature_file').on('change', function() {
    var file = this.files[0];
    if(file) {
      var url = URL.createObjectURL(file);
      $('#upload_emp_preview_img').attr('src', url).show();
      $('#upload_emp_preview_container').show();
    }
  });

  // --- Save canvas to hidden input before submit ---
  $('#my_training_record_form').on('submit', function(e) {
    e.preventDefault();

    var isDelete = $('#delete_employee_signature').is(':checked');

    if(!isDelete) {
      var activeTab = $('.sig-content.active').attr('id');

      // Check if draw tab is active
      if(activeTab === 'draw_emp_sig') {
        if(!hasEmpDrawn) {
          toastr.warning('Please draw your signature first.');
          return;
        }
        var dataURL = empCanvas.toDataURL('image/png');
        $('#employee_sig').val(dataURL);
      }
      // Check if upload tab is active
      else if(activeTab === 'upload_emp_sig') {
        var fileInput = $('#employee_signature_file')[0];
        if(!fileInput || !fileInput.files || !fileInput.files[0]) {
          toastr.warning('Please select a signature file to upload.');
          return;
        }
      }
      // Preview tab - no new signature provided
      else if(activeTab === 'preview_emp_sig') {
        toastr.warning('Check "Delete current signature" to remove it, or switch to Draw/Upload tab to provide a new signature.');
        return;
      }
    }

    var $btn = $('#save_sig_btn');
    $btn.prop('disabled', true).html('<i class="feather icon-loader"></i> Saving...');

    var formData = new FormData(this);

    $.ajax({
      url: $(this).attr('action'),
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: function(response) {
        if(response.error) {
          toastr.error(response.error);
          $btn.prop('disabled', false).html('<i class="feather icon-save"></i> Save Signature');
        } else {
          toastr.success(response.result || 'Signature saved successfully!');
          setTimeout(function() {
            location.reload();
          }, 1000);
        }
      },
      error: function(xhr, status, error) {
        toastr.error('Error saving signature. Please try again.');
        $btn.prop('disabled', false).html('<i class="feather icon-save"></i> Save Signature');
      }
    });
  });
});
</script>