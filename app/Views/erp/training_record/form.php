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
$prepared_by_signature = '';
$approved_by_signature = '';
foreach($signatures as $sig) {
    if(($sig['signature_type'] ?? '') === 'prepared_by') {
        $prepared_by_signature = !empty($sig['file_path']) ? base_url().'/'.$sig['file_path'] : ($sig['signature_data'] ?? '');
    }
    if(($sig['signature_type'] ?? '') === 'approved_by') {
        $approved_by_signature = !empty($sig['file_path']) ? base_url().'/'.$sig['file_path'] : ($sig['signature_data'] ?? '');
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
</style>

<div class="row">
  <div class="col-sm-12">
    <?php $attributes = array('name' => 'training_record_form', 'id' => 'training_record_form', 'autocomplete' => 'off', 'class' => 'm-b-1', 'enctype' => 'multipart/form-data'); ?>
    <?php $hidden = array('user_id' => $employee['user_id']); ?>
    <?= form_open('erp/training-record-save', $attributes, $hidden); ?>

    <input type="hidden" name="type" value="save_record" />
    <input type="hidden" name="send_email_signatories" id="send_email_signatories" value="0" />

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
                <th style="width:13%">Course Title</th>
                <th style="width:15%">Course Objective</th>
                <th style="width:11%">Date Completed</th>
                <th style="width:11%">Test Result</th>
                <th style="width:10%">Total Hours</th>
                <th style="width:12%">Institution</th>
                <th style="width:12%">Location</th>
                <th style="width:12%">Instructor</th>
                <th style="width:8%"></th>
              </tr>
            </thead>
            <tbody>
              <?php if(!empty($training_history)): ?>
                <?php foreach($training_history as $th): ?>
                <tr>
                  <td><input type="text" class="form-control" name="course_title[]" value="<?= $th['course_title']; ?>" placeholder="Course Title"></td>
                  <td><input type="text" class="form-control" name="course_objective[]" value="<?= $th['course_objective']; ?>" placeholder="Objective"></td>
                  <td><input type="text" class="form-control date" name="date_completed[]" data-date-format="dd-mm-yyyy" value="<?= !empty($th['date_completed']) && strtotime($th['date_completed']) ? date('d-m-Y', strtotime($th['date_completed'])) : ''; ?>" placeholder="Date"></td>
                  <td><input type="text" class="form-control" name="test_result[]" value="<?= $th['test_result']; ?>" placeholder="Test Result"></td>
                  <td><input type="text" class="form-control" name="total_hours[]" value="<?= $th['total_hours']; ?>" placeholder="Hours"></td>
                  <td><input type="text" class="form-control" name="institution[]" value="<?= $th['institution'] ?? ''; ?>" placeholder="Institution"></td>
                  <td><input type="text" class="form-control" name="location[]" value="<?= $th['location']; ?>" placeholder="Location"></td>
                  <td><input type="text" class="form-control" name="instructor_name[]" value="<?= $th['instructor_name']; ?>" placeholder="Instructor"></td>
                  <td class="text-center"><i class="feather icon-trash-2 btn-remove-row"></i></td>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
              <tr>
                <td><input type="text" class="form-control" name="course_title[]" placeholder="Course Title"></td>
                <td><input type="text" class="form-control" name="course_objective[]" placeholder="Objective"></td>
                <td><input type="text" class="form-control date" name="date_completed[]" data-date-format="dd-mm-yyyy" placeholder="Date"></td>
                <td><input type="text" class="form-control" name="test_result[]" placeholder="Test Result"></td>
                <td><input type="text" class="form-control" name="total_hours[]" placeholder="Hours"></td>
                <td><input type="text" class="form-control" name="institution[]" placeholder="Institution"></td>
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

    <!-- G. Signature - Prepared By -->
    <div class="card section-card">
      <div class="card-header">
        <h5>G. Signature - Prepared By</h5>
      </div>
      <div class="card-body">
        <div class="sig-tabs">
          <div class="sig-tab active" data-type="prepared_by" data-target="draw_sig_prepared_by">Free Draw</div>
          <div class="sig-tab" data-type="prepared_by" data-target="upload_sig_prepared_by">Upload File</div>
          <?php if(!empty($prepared_by_signature)): ?>
          <div class="sig-tab" data-type="prepared_by" data-target="preview_sig_prepared_by">Current Signature</div>
          <?php endif; ?>
        </div>
        <div id="draw_sig_prepared_by" class="sig-content sig-content-prepared_by active" data-tab="draw_sig">
          <div class="signature-canvas-wrapper">
            <canvas id="signature_canvas_prepared_by" width="400" height="150" style="cursor:crosshair; background:#fff;"></canvas>
          </div>
          <div class="mt-2">
            <button type="button" class="btn btn-sm btn-secondary" onclick="clearSig('prepared_by')">Clear</button>
            <small class="text-muted ml-2">Draw your signature in the box above</small>
          </div>
        </div>
        <div id="upload_sig_prepared_by" class="sig-content sig-content-prepared_by" data-tab="upload_sig">
          <input type="file" class="form-control-file" name="prepared_by_signature_file" id="prepared_by_signature_file" accept="image/jpeg,image/jpg,image/png">
          <small class="text-muted">Allowed formats: JPG, JPEG, PNG</small>
          <div id="upload_preview_container_prepared_by" style="display:none; margin-top:10px;">
            <img id="upload_preview_img_prepared_by" class="signature-preview-img" style="display:block;" />
          </div>
        </div>
        <?php if(!empty($prepared_by_signature)): ?>
        <div id="preview_sig_prepared_by" class="sig-content sig-content-prepared_by" data-tab="preview_sig">
          <div class="mb-2">
            <img src="<?= $prepared_by_signature; ?>" alt="Current Prepared By Signature" style="max-width:100%; max-height:120px; border:1px solid #ddd;">
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="delete_signature_prepared_by" id="delete_signature_prepared_by" value="1">
            <label class="form-check-label" for="delete_signature_prepared_by">Delete current signature</label>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- H. Signature - Approved By -->
    <div class="card section-card">
      <div class="card-header">
        <h5>H. Signature - Approved By</h5>
      </div>
      <div class="card-body">
        <div class="sig-tabs">
          <div class="sig-tab active" data-type="approved_by" data-target="draw_sig_approved_by">Free Draw</div>
          <div class="sig-tab" data-type="approved_by" data-target="upload_sig_approved_by">Upload File</div>
          <?php if(!empty($approved_by_signature)): ?>
          <div class="sig-tab" data-type="approved_by" data-target="preview_sig_approved_by">Current Signature</div>
          <?php endif; ?>
        </div>
        <div id="draw_sig_approved_by" class="sig-content sig-content-approved_by active" data-tab="draw_sig">
          <div class="signature-canvas-wrapper">
            <canvas id="signature_canvas_approved_by" width="400" height="150" style="cursor:crosshair; background:#fff;"></canvas>
          </div>
          <div class="mt-2">
            <button type="button" class="btn btn-sm btn-secondary" onclick="clearSig('approved_by')">Clear</button>
            <small class="text-muted ml-2">Draw your signature in the box above</small>
          </div>
        </div>
        <div id="upload_sig_approved_by" class="sig-content sig-content-approved_by" data-tab="upload_sig">
          <input type="file" class="form-control-file" name="approved_by_signature_file" id="approved_by_signature_file" accept="image/jpeg,image/jpg,image/png">
          <small class="text-muted">Allowed formats: JPG, JPEG, PNG</small>
          <div id="upload_preview_container_approved_by" style="display:none; margin-top:10px;">
            <img id="upload_preview_img_approved_by" class="signature-preview-img" style="display:block;" />
          </div>
        </div>
        <?php if(!empty($approved_by_signature)): ?>
        <div id="preview_sig_approved_by" class="sig-content sig-content-approved_by" data-tab="preview_sig">
          <div class="mb-2">
            <img src="<?= $approved_by_signature; ?>" alt="Current Approved By Signature" style="max-width:100%; max-height:120px; border:1px solid #ddd;">
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="delete_signature_approved_by" id="delete_signature_approved_by" value="1">
            <label class="form-check-label" for="delete_signature_approved_by">Delete current signature</label>
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
        <button type="button" class="btn btn-primary ladda-button" data-style="expand-right" id="save_record_btn" data-toggle="modal" data-target="#email_notification_modal">
          <i class="feather icon-save"></i> Save Training Record
        </button>
      </div>
    </div>

    <?= form_close(); ?>

    <!-- Email Notification Modal -->
    <div class="modal fade" id="email_notification_modal" tabindex="-1" role="dialog" aria-labelledby="email_notification_modalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="email_notification_modalLabel">Send Notification Email?</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <p>Apakah anda ingin mengirim email ke Assign Signatories untuk segera tanda tangan?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" id="btn_send_email_yes">
              <i class="feather icon-mail"></i> Ya, Kirim & Save
            </button>
            <button type="button" class="btn btn-secondary" id="btn_send_email_no">
              <i class="feather icon-save"></i> Tidak, Save Saja
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
  "use strict";

  // --- Signature Drawing ---
  window.hasDrawn_prepared_by = false;
  window.hasDrawn_approved_by = false;

  function initCanvas(type) {
    var canvas = document.getElementById('signature_canvas_' + type);
    if(!canvas) return;
    var ctx = canvas.getContext('2d');
    var isDrawing = false;

    function getPos(e) {
      var rect = canvas.getBoundingClientRect();
      var clientX = e.touches ? e.touches[0].clientX : e.clientX;
      var clientY = e.touches ? e.touches[0].clientY : e.clientY;
      return {
        x: (clientX - rect.left) * (canvas.width / rect.width),
        y: (clientY - rect.top) * (canvas.height / rect.height)
      };
    }

    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    ctx.strokeStyle = '#000000';

    canvas.addEventListener('mousedown', function(e) {
      isDrawing = true;
      window['hasDrawn_' + type] = true;
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
      window['hasDrawn_' + type] = true;
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
  }

  initCanvas('prepared_by');
  initCanvas('approved_by');

  window.clearSig = function(type) {
    var canvas = document.getElementById('signature_canvas_' + type);
    if(canvas) {
      var ctx = canvas.getContext('2d');
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      window['hasDrawn_' + type] = false;
    }
  };

  // --- Signature Tabs ---
  $('.sig-tab').on('click', function() {
    var type = $(this).data('type');
    $('.sig-tab[data-type="'+type+'"]').removeClass('active');
    $(this).addClass('active');
    $('.sig-content-' + type).removeClass('active');
    var target = $(this).data('target');
    if(target) $('#'+target).addClass('active');
  });

  // --- Upload Preview ---
  $('#prepared_by_signature_file').on('change', function() {
    var file = this.files[0];
    if(file) {
      var url = URL.createObjectURL(file);
      $('#upload_preview_img_prepared_by').attr('src', url).show();
      $('#upload_preview_container_prepared_by').show();
    }
  });

  $('#approved_by_signature_file').on('change', function() {
    var file = this.files[0];
    if(file) {
      var url = URL.createObjectURL(file);
      $('#upload_preview_img_approved_by').attr('src', url).show();
      $('#upload_preview_container_approved_by').show();
    }
  });

  // --- Modal Confirmation for Email ---
  $('#btn_send_email_yes').on('click', function() {
    $('#send_email_signatories').val('1');
    $('#email_notification_modal').modal('hide');
    submitTrainingRecordForm();
  });

  $('#btn_send_email_no').on('click', function() {
    $('#send_email_signatories').val('0');
    $('#email_notification_modal').modal('hide');
    submitTrainingRecordForm();
  });

  function submitTrainingRecordForm() {
    var form = document.getElementById('training_record_form');
    var $btn = $('#save_record_btn');
    $btn.prop('disabled', true).html('<i class="feather icon-loader"></i> Saving...');

    var fd = new FormData(form);

    // Helper to convert dataURL to Blob
    function dataURLToBlob(dataURL) {
      var parts = dataURL.split(',');
      var mime = parts[0].match(/:(.*?);/)[1];
      var binary = atob(parts[1]);
      var array = [];
      for (var i = 0; i < binary.length; i++) {
        array.push(binary.charCodeAt(i));
      }
      return new Blob([new Uint8Array(array)], { type: mime });
    }

    var sigTypes = ['prepared_by', 'approved_by'];
    var promises = [];

    sigTypes.forEach(function(type) {
      var isDelete = $('#delete_signature_' + type).is(':checked');
      var activeTab = $('.sig-content-' + type + '.active').attr('data-tab');

      if (!isDelete && activeTab === 'draw_sig') {
        var canvas = document.getElementById('signature_canvas_' + type);
        var hasDrawn = window['hasDrawn_' + type];
        if (canvas && hasDrawn) {
          if (typeof canvas.toBlob === 'function') {
            var p = new Promise(function(resolve) {
              canvas.toBlob(function(blob) {
                fd.append(type + '_signature_file', blob, 'signature.png');
                resolve();
              }, 'image/png');
            });
            promises.push(p);
          } else {
            fd.append(type + '_signature_file', dataURLToBlob(canvas.toDataURL('image/png')), 'signature.png');
          }
        }
      }
    });

    Promise.all(promises).then(function() {
      $.ajax({
        url: $(form).attr('action'),
        type: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        success: function(response) {
          var data = response;
          if(typeof data === 'string') {
            try { data = JSON.parse(data); } catch (e) { data = { result: 'Saved successfully!' }; }
          }
          if(data && data.error) {
            toastr.error(data.error);
            $btn.prop('disabled', false).html('<i class="feather icon-save"></i> Save Training Record');
          } else {
            toastr.success((data && data.result) ? data.result : 'Saved successfully!');
            setTimeout(function() { window.location.href = '<?= site_url('erp/training-record'); ?>'; }, 1000);
          }
        },
        error: function(xhr) {
          toastr.error('Error saving record. Please try again.');
          $btn.prop('disabled', false).html('<i class="feather icon-save"></i> Save Training Record');
        }
      });
    });
  }

});
</script>
