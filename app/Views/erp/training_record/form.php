<?php
use App\Models\UsersModel;
use App\Models\SystemModel;

$SystemModel = new SystemModel();
$UsersModel = new UsersModel();

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$xin_system = erp_company_settings();
$xin_com_system = erp_company_settings();

$employee = $employee ?? [];
$staff_detail = $staff_detail ?? [];
$header = $header ?? [];
$education = $education ?? [];
$training_history = $training_history ?? [];
$licenses = $licenses ?? [];
$certificates = $certificates ?? [];
$signatures = $signatures ?? [];
$approved_by_signature = '';
foreach($signatures as $sig) {
    if(($sig['signature_type'] ?? '') === 'approved_by') {
        $approved_by_signature = !empty($sig['file_path']) ? base_url().'/'.$sig['file_path'] : ($sig['signature_data'] ?? '');
        break;
    }
}
?>
<style>
.table-dynamic td { vertical-align: middle; }
.table-dynamic .form-control { font-size: 12px; padding: 4px 8px; }
.btn-remove-row { color: #dc3545; cursor: pointer; }
.btn-add-row { cursor: pointer; }
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
#signature_canvas { cursor: crosshair; background: #fff; }
</style>

<div class="row">
  <div class="col-sm-12">
    <?php $attributes = array('name' => 'training_record_form', 'id' => 'training_record_form', 'autocomplete' => 'off', 'class' => 'm-b-1'); ?>
    <?php $hidden = array('user_id' => $employee['user_id']); ?>
    <?= form_open('erp/training-record-save', $attributes, $hidden); ?>

    <input type="hidden" name="type" value="save_record" />
    <input type="hidden" name="approved_by_sig" id="approved_by_sig" value="" />

    <!-- Employee Info Banner -->
    <div class="card">
      <div class="card-body">
        <div class="row align-items-center">
          <div class="col-md-2 text-center">
            <img src="<?= base_url().'/public/uploads/users/'.$employee['profile_photo']; ?>" alt="" class="img-fluid wid-80 rounded">
          </div>
          <div class="col-md-10">
            <h5><?= $employee['first_name'].' '.$employee['last_name']; ?></h5>
            <p class="mb-0 text-muted">
              <?= $designation['designation_name'] ?? '-'; ?> |
              Emp ID: <?= $staff_detail['employee_id'] ?? '-'; ?> |
              Phone: <?= $employee['contact_number'] ?? '-'; ?> |
              Email: <?= $employee['email']; ?>
            </p>
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
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Place of Birth</label>
              <input type="text" class="form-control" name="place_of_birth" value="<?= $header['place_of_birth'] ?? ''; ?>" placeholder="Place of Birth">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Phone Number</label>
              <input type="text" class="form-control" value="<?= $employee['contact_number'] ?? ''; ?>" placeholder="Phone Number" readonly>
              <small class="text-muted">(from employee data)</small>
            </div>
          </div>
          <div class="col-md-12">
            <div class="form-group">
              <label>Address</label>
              <textarea class="form-control" name="address" rows="2" placeholder="Address"><?= $header['address'] ?? ''; ?></textarea>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- B. Education -->
    <div class="card section-card">
      <div class="card-header">
        <h5>B. Education</h5>
        <div class="card-header-right">
          <button type="button" class="btn btn-sm btn-primary btn-add-row" id="add_education_row">
            <i class="feather icon-plus"></i> Add Row
          </button>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-dynamic" id="education_table">
            <thead>
              <tr>
                <th style="width:22%">Degree</th>
                <th style="width:28%">Institution</th>
                <th style="width:28%">Major</th>
                <th style="width:15%">Graduate Year</th>
                <th style="width:7%"></th>
              </tr>
            </thead>
            <tbody>
              <?php if(!empty($education)): ?>
                <?php foreach($education as $edu): ?>
                <tr>
                  <td><input type="text" class="form-control" name="degree[]" value="<?= $edu['degree']; ?>" placeholder="Degree"></td>
                  <td><input type="text" class="form-control" name="institution[]" value="<?= $edu['institution']; ?>" placeholder="Institution"></td>
                  <td><input type="text" class="form-control" name="major[]" value="<?= $edu['major']; ?>" placeholder="Major"></td>
                  <td><input type="text" class="form-control" name="graduate_year[]" value="<?= $edu['graduate_year']; ?>" placeholder="Year"></td>
                  <td class="text-center"><i class="feather icon-trash-2 btn-remove-row"></i></td>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
              <tr>
                <td><input type="text" class="form-control" name="degree[]" placeholder="Degree"></td>
                <td><input type="text" class="form-control" name="institution[]" placeholder="Institution"></td>
                <td><input type="text" class="form-control" name="major[]" placeholder="Major"></td>
                <td><input type="text" class="form-control" name="graduate_year[]" placeholder="Year"></td>
                <td class="text-center"><i class="feather icon-trash-2 btn-remove-row"></i></td>
              </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- C. Training -->
    <div class="card section-card">
      <div class="card-header">
        <h5>C. Training</h5>
        <div class="card-header-right">
          <button type="button" class="btn btn-sm btn-primary btn-add-row" id="add_training_row">
            <i class="feather icon-plus"></i> Add Row
          </button>
        </div>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-dynamic" id="training_table">
            <thead>
              <tr>
                <th style="width:14%">Course Title</th>
                <th style="width:16%">Course Objective</th>
                <th style="width:12%">Date Completed</th>
                <th style="width:12%">Test Result</th>
                <th style="width:10%">Total Hours</th>
                <th style="width:14%">Location</th>
                <th style="width:14%">Instructor</th>
                <th style="width:8%"></th>
              </tr>
            </thead>
            <tbody>
              <?php if(!empty($training_history)): ?>
                <?php foreach($training_history as $th): ?>
                <tr>
                  <td><input type="text" class="form-control" name="course_title[]" value="<?= $th['course_title']; ?>" placeholder="Course Title"></td>
                  <td><input type="text" class="form-control" name="course_objective[]" value="<?= $th['course_objective']; ?>" placeholder="Objective"></td>
                  <td><input type="text" class="form-control date" name="date_completed[]" value="<?= $th['date_completed']; ?>" placeholder="Date"></td>
                  <td><input type="text" class="form-control" name="test_result[]" value="<?= $th['test_result']; ?>" placeholder="Test Result"></td>
                  <td><input type="text" class="form-control" name="total_hours[]" value="<?= $th['total_hours']; ?>" placeholder="Hours"></td>
                  <td><input type="text" class="form-control" name="location[]" value="<?= $th['location']; ?>" placeholder="Location"></td>
                  <td><input type="text" class="form-control" name="instructor_name[]" value="<?= $th['instructor_name']; ?>" placeholder="Instructor"></td>
                  <td class="text-center"><i class="feather icon-trash-2 btn-remove-row"></i></td>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
              <tr>
                <td><input type="text" class="form-control" name="course_title[]" placeholder="Course Title"></td>
                <td><input type="text" class="form-control" name="course_objective[]" placeholder="Objective"></td>
                <td><input type="text" class="form-control date" name="date_completed[]" placeholder="Date"></td>
                <td><input type="text" class="form-control" name="test_result[]" placeholder="Test Result"></td>
                <td><input type="text" class="form-control" name="total_hours[]" placeholder="Hours"></td>
                <td><input type="text" class="form-control" name="location[]" placeholder="Location"></td>
                <td><input type="text" class="form-control" name="instructor_name[]" placeholder="Instructor"></td>
                <td class="text-center"><i class="feather icon-trash-2 btn-remove-row"></i></td>
              </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- D. Licenses & Certificates -->
    <div class="card section-card">
      <div class="card-header">
        <h5>D. Licenses & Certificates</h5>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-dynamic" id="license_table">
            <thead>
              <tr>
                <th style="width:18%">License Type</th>
                <th style="width:22%">License No</th>
                <th style="width:18%">Expired Date</th>
                <th style="width:30%">Rating</th>
                <th style="width:12%"></th>
              </tr>
            </thead>
            <tbody>
              <?php if(!empty($licenses)): ?>
                <?php foreach($licenses as $lic): ?>
                <tr>
                  <td>
                    <select class="form-control" name="license_type[]">
                      <option value="">Select Type</option>
                      <option value="AME" <?= ($lic['license_type']=='AME')?'selected':''; ?>>AME License</option>
                      <option value="COMA" <?= ($lic['license_type']=='COMA')?'selected':''; ?>>COMA</option>
                      <option value="COC" <?= ($lic['license_type']=='COC')?'selected':''; ?>>C of C</option>
                    </select>
                  </td>
                  <td><input type="text" class="form-control" name="license_no[]" value="<?= $lic['license_no']; ?>" placeholder="License No"></td>
                  <td><input type="text" class="form-control date" name="expired_date[]" value="<?= $lic['expired_date']; ?>" placeholder="Expired Date"></td>
                  <td><input type="text" class="form-control" name="rating[]" value="<?= $lic['rating']; ?>" placeholder="Rating"></td>
                  <td class="text-center"><i class="feather icon-trash-2 btn-remove-row"></i></td>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
              <tr>
                <td>
                  <select class="form-control" name="license_type[]">
                    <option value="">Select Type</option>
                    <option value="AME">AME License</option>
                    <option value="COMA">COMA</option>
                    <option value="COC">C of C</option>
                  </select>
                </td>
                <td><input type="text" class="form-control" name="license_no[]" placeholder="License No"></td>
                <td><input type="text" class="form-control date" name="expired_date[]" placeholder="Expired Date"></td>
                <td><input type="text" class="form-control" name="rating[]" placeholder="Rating"></td>
                <td class="text-center"><i class="feather icon-trash-2 btn-remove-row"></i></td>
              </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        <div class="text-left mt-2">
          <button type="button" class="btn btn-sm btn-primary" id="add_license_row">
            <i class="feather icon-plus"></i> Add License
          </button>
        </div>
      </div>
    </div>

    <!-- E. Basic Certificates -->
    <div class="card section-card">
      <div class="card-header">
        <h5>E. Basic Certificate</h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <h6>Module A</h6>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" name="a1" value="1" id="cert_a1" <?= (isset($certificates['a1']) && $certificates['a1']) ? 'checked' : ''; ?>>
              <label class="form-check-label" for="cert_a1">A1</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" name="a2" value="1" id="cert_a2" <?= (isset($certificates['a2']) && $certificates['a2']) ? 'checked' : ''; ?>>
              <label class="form-check-label" for="cert_a2">A2</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" name="a3" value="1" id="cert_a3" <?= (isset($certificates['a3']) && $certificates['a3']) ? 'checked' : ''; ?>>
              <label class="form-check-label" for="cert_a3">A3</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" name="a4" value="1" id="cert_a4" <?= (isset($certificates['a4']) && $certificates['a4']) ? 'checked' : ''; ?>>
              <label class="form-check-label" for="cert_a4">A4</label>
            </div>
          </div>
          <div class="col-md-6">
            <h6>Module C</h6>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" name="c1" value="1" id="cert_c1" <?= (isset($certificates['c1']) && $certificates['c1']) ? 'checked' : ''; ?>>
              <label class="form-check-label" for="cert_c1">C1</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" name="c2" value="1" id="cert_c2" <?= (isset($certificates['c2']) && $certificates['c2']) ? 'checked' : ''; ?>>
              <label class="form-check-label" for="cert_c2">C2</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="checkbox" name="c4" value="1" id="cert_c4" <?= (isset($certificates['c4']) && $certificates['c4']) ? 'checked' : ''; ?>>
              <label class="form-check-label" for="cert_c4">C4</label>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- F. Assign Signatories -->
    <div class="card section-card">
      <div class="card-header">
        <h5>F. Assign Signatories</h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label>Prepared By</label>
              <select class="form-control select2" name="prepared_by_id" id="prepared_by_id">
                <option value="">-- Select Staff --</option>
                <?php foreach($all_staff ?? [] as $s): ?>
                <option value="<?= $s['user_id']; ?>" <?= (isset($header['prepared_by_id']) && $header['prepared_by_id'] == $s['user_id']) ? 'selected' : ''; ?>>
                  <?= $s['first_name'].' '.$s['last_name']; ?>
                </option>
                <?php endforeach; ?>
              </select>
              <small class="text-muted">This person will be able to sign the "Prepared By" column.</small>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Approved By</label>
              <select class="form-control select2" name="approved_by_id" id="approved_by_id">
                <option value="">-- Select Staff --</option>
                <?php foreach($all_staff ?? [] as $s): ?>
                <option value="<?= $s['user_id']; ?>" <?= (isset($header['approved_by_id']) && $header['approved_by_id'] == $s['user_id']) ? 'selected' : ''; ?>>
                  <?= $s['first_name'].' '.$s['last_name']; ?>
                </option>
                <?php endforeach; ?>
              </select>
              <small class="text-muted">This person will be able to sign the "Approved By" column.</small>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- G. Signature - Approved By -->
    <div class="card section-card">
      <div class="card-header">
        <h5>G. Signature - Approved By</h5>
      </div>
      <div class="card-body">
        <div class="sig-tabs">
          <div class="sig-tab active" data-target="draw_sig">Free Draw</div>
          <div class="sig-tab" data-target="upload_sig">Upload File</div>
          <?php if(!empty($approved_by_signature)): ?>
          <div class="sig-tab" data-target="preview_sig">Current Signature</div>
          <?php endif; ?>
        </div>
        <div id="draw_sig" class="sig-content active">
          <div class="signature-canvas-wrapper">
            <canvas id="signature_canvas" width="400" height="150"></canvas>
          </div>
          <div class="mt-2">
            <button type="button" class="btn btn-sm btn-secondary" id="clear_sig">Clear</button>
            <small class="text-muted ml-2">Draw your signature in the box above</small>
          </div>
        </div>
        <div id="upload_sig" class="sig-content">
          <input type="file" class="form-control-file" name="approved_by_signature_file" id="approved_by_signature_file" accept="image/jpeg,image/jpg,image/png">
          <small class="text-muted">Allowed formats: JPG, JPEG, PNG</small>
          <div id="upload_preview_container" style="display:none; margin-top:10px;">
            <img id="upload_preview_img" class="signature-preview-img" style="display:block;" />
          </div>
        </div>
        <?php if(!empty($approved_by_signature)): ?>
        <div id="preview_sig" class="sig-content">
          <div class="mb-2">
            <img src="<?= $approved_by_signature; ?>" alt="Current Approved By Signature" style="max-width:100%; max-height:120px; border:1px solid #ddd;">
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="delete_signature" id="delete_signature" value="1">
            <label class="form-check-label" for="delete_signature">Delete current signature</label>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Submit -->
    <div class="card">
      <div class="card-footer text-right">
        <a href="<?= site_url('erp/training-record'); ?>" class="btn btn-light">
          <?= lang('Main.xin_close'); ?>
        </a>
        <button type="submit" class="btn btn-primary ladda-button" data-style="expand-right">
          <i class="feather icon-save"></i> Save Training Record
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
  var canvas = document.getElementById('signature_canvas');
  var ctx = canvas ? canvas.getContext('2d') : null;
  var isDrawing = false;
  var hasDrawn = false;

  function getPos(e) {
    var rect = canvas.getBoundingClientRect();
    var clientX = e.touches ? e.touches[0].clientX : e.clientX;
    var clientY = e.touches ? e.touches[0].clientY : e.clientY;
    return {
      x: (clientX - rect.left) * (canvas.width / rect.width),
      y: (clientY - rect.top) * (canvas.height / rect.height)
    };
  }

  if(canvas && ctx) {
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    ctx.strokeStyle = '#000000';

    canvas.addEventListener('mousedown', function(e) {
      isDrawing = true;
      hasDrawn = true;
      var p = getPos(e);
      ctx.beginPath();
      ctx.moveTo(p.x, p.y);
    });
    canvas.addEventListener('mousemove', function(e) {
      if(!isDrawing) return;
      var p = getPos(e);
      ctx.lineTo(p.x, p.y);
      ctx.stroke();
    });
    canvas.addEventListener('mouseup', function() { isDrawing = false; });
    canvas.addEventListener('mouseout', function() { isDrawing = false; });
    canvas.addEventListener('touchstart', function(e) {
      e.preventDefault();
      isDrawing = true;
      hasDrawn = true;
      var p = getPos(e);
      ctx.beginPath();
      ctx.moveTo(p.x, p.y);
    }, { passive: false });
    canvas.addEventListener('touchmove', function(e) {
      e.preventDefault();
      if(!isDrawing) return;
      var p = getPos(e);
      ctx.lineTo(p.x, p.y);
      ctx.stroke();
    }, { passive: false });
    canvas.addEventListener('touchend', function() { isDrawing = false; });

    $('#clear_sig').on('click', function() {
      if(ctx) ctx.clearRect(0, 0, canvas.width, canvas.height);
      hasDrawn = false;
      $('#approved_by_sig').val('');
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
  $('#approved_by_signature_file').on('change', function() {
    var file = this.files[0];
    if(file) {
      var url = URL.createObjectURL(file);
      $('#upload_preview_img').attr('src', url).show();
      $('#upload_preview_container').show();
    }
  });

  // --- Save canvas to hidden input before submit ---
  $('#training_record_form').on('submit', function(e) {
    // If draw tab is active, save canvas
    if($('#draw_sig').hasClass('active') && canvas && hasDrawn) {
      var dataURL = canvas.toDataURL('image/png');
      $('#approved_by_sig').val(dataURL);
    } else if($('#draw_sig').hasClass('active')) {
      $('#approved_by_sig').val('');
    }
  });

  // --- Signature preview on load ---
  <?php if(!empty($approved_by_signature)): ?>
  // keep signature base if needed
  <?php endif; ?>

});
</script>
