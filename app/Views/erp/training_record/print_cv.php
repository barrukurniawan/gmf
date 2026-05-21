<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Training Program Manual - <?= $employee['first_name'].' '.$employee['last_name']; ?></title>
  <style>
    * { box-sizing: border-box; }
    body {
      margin: 0; padding: 0;
      font-family: Arial, Helvetica, sans-serif;
      background-color: #525659;
      display: flex; justify-content: center;
    }
    .page-a4 {
      width: 210mm; min-height: 297mm;
      background-color: white;
      box-shadow: 0 0 10px rgba(0,0,0,0.2);
      margin: 20px auto;
      padding: 8mm 8mm;
      display: flex; flex-direction: column;
    }
    h1.main-title {
      font-family: "Arial Black", Arial, sans-serif;
      font-size: 20px; margin: 0;
      text-transform: uppercase; letter-spacing: -0.5px;
    }
    .sub-title {
      text-align: center; font-size: 14px;
      font-weight: bold; margin-bottom: 5px;
    }
    .top-header {
      display: flex; justify-content: space-between;
      align-items: center; margin-bottom: 8px;
    }
    .top-header img { height: 65px; }
    hr.thick-line {
      border: none; border-top: 4px solid black;
      margin: 0 0 15px 0;
    }
    table.main-table {
      width: 100%; border-collapse: collapse;
      border: 1px solid black; font-size: 12px;
    }
    table.main-table > tbody > tr > td { border: 1px solid black; }
    .company-logo-cell { width: 20%; text-align: center; padding: 8px; }
    .company-logo-cell img { height: 45px; }
    .company-name-cell { width: 50%; text-align: center; font-weight: bold; font-size: 11px; padding: 5px; }
    .company-contact-cell { width: 30%; font-size: 9.5px; padding: 5px; }
    .contact-table { width: 100%; border-collapse: collapse; border: none; }
    .contact-table td { padding: 1px 0; border: none !important; vertical-align: top; }
    .section-title { font-size: 14px; font-weight: bold; padding: 4px 8px; background-color: white; }
    .center-title { text-align: center; font-size: 15px; font-weight: bold; padding: 5px; text-transform: uppercase; }
    .personal-layout { width: 100%; border-collapse: collapse; border: none; }
    .personal-fields { width: 70%; padding: 8px; vertical-align: top; }
    .personal-photo { width: 30%; border-left: 1px solid black; text-align: center; vertical-align: middle; font-size: 14px; }
    .field-table { width: 100%; border-collapse: collapse; border: none; line-height: 2; font-size: 12px; }
    .field-table td { border: none !important; padding: 0; vertical-align: top; }
    .field-label { width: 150px; }
    .field-colon { width: 15px; }
    .edu-wrapper { padding: 15px; }
    .edu-table { width: 90%; margin: 0 auto; border-collapse: collapse; text-align: center; border: 1px solid black; }
    .edu-table th { border: 1px solid black; font-weight: normal; padding: 5px; }
    .edu-table td { border: 1px solid black; padding: 12px; height: 25px; }
    .train-table { width: 100%; border-collapse: collapse; text-align: center; border: none; }
    .train-table th { border-right: 1px solid black; border-bottom: 1px solid black; font-weight: normal; padding: 6px 4px; font-size: 12px; vertical-align: middle; }
    .train-table th:last-child { border-right: none; }
    .train-table td { border-right: 1px solid black; height: 25px; padding: 4px; }
    .train-table td:last-child { border-right: none; }
    .license-header-table { width: 100%; border-collapse: collapse; text-align: center; border: none; }
    .license-header-table td { width: 25%; border-right: 1px solid black; border-bottom: 1px solid black; padding: 3px; }
    .license-header-table td:last-child { border-right: none; }
    .license-body-table { width: 100%; border-collapse: collapse; border: none; }
    .license-body-table td.col-box { width: 25%; border-right: 1px solid black; padding: 5px 8px; vertical-align: top; }
    .license-body-table td.col-box:last-child { border-right: none; padding: 0; }
    .lic-field-table { width: 100%; border-collapse: collapse; border: none; line-height: 2; font-size: 12px; }
    .lic-field-table td { border: none !important; padding: 0; vertical-align: top; }
    .lic-label { width: 50px; }
    .lic-colon { width: 10px; }
    .checkbox-table { width: 100%; height: 100%; border-collapse: collapse; border: none; }
    .checkbox-table td { border: none !important; padding: 8px 3px; text-align: center; font-size: 11px; white-space: nowrap; }
    .checkbox-table tr:first-child td { border-bottom: 1px solid black !important; }
    .chk-box { font-size: 18px; margin-right: 3px; vertical-align: -1.1px; line-height: 1; }
    .sig-table { width: 100%; border-collapse: collapse; text-align: center; border: none; }
    .sig-table td { width: 33.33%; border-right: 1px solid black !important; padding: 5px; vertical-align: top; height: 80px; }
    .sig-table td:last-child { border-right: none !important; }
    .sig-line-text { margin-top: 50px; }
    .footer-no { margin-top: 5px; font-size: 13px; }
    @page {
      size: A4;
      margin: 0;
    }
    @media print {
      body { background: none; }
      .page-a4 { margin: 0; box-shadow: none; padding: 5mm 5mm; border: none; }
      /* Wrapper div around main-table gets box-decoration-break: clone
         so its border repeats at every page break:
         - page 1 ends with a bottom border
         - page 2 starts with a top border */
      .main-table-wrapper {
        -webkit-box-decoration-break: clone;
        box-decoration-break: clone;
        border: 1px solid black;
      }
      /* Remove outer table border (wrapper provides it) */
      table.main-table {
        border: none;
      }
    }
  </style>
</head>
<body>
  <div class="page-a4">


    <div class="main-table-wrapper">
    <table class="main-table">
      <!-- Company Details Row -->
      <tr>
        <td class="company-logo-cell">
          <img src="https://gis.globalmaintenance.co.id/public/uploads/logo/other/GMF%20Logo%202021.png" alt="GMF Logo" style="height:45px;">
        </td>
        <td class="company-name-cell">
          <div style="margin-bottom:4px;">PT Global Maintenance Facility</div>
          <div style="margin-bottom:4px;">Approved Maintenance Organization</div>
          <div>DGCA No: 145D-376</div>
        </td>
        <td class="company-contact-cell">
          <table class="contact-table">
            <tr><td style="width:35px;">Phone</td><td>: +62 21 809 2019</td></tr>
            <tr><td>Email</td><td>: info@globalmaintenance.co.id</td></tr>
          </table>
        </td>
      </tr>

      <!-- Title Row -->
      <tr><td colspan="3" class="center-title">EMPLOYEE TRAINING RECORD</td></tr>

      <!-- A. Personal -->
      <tr><td colspan="3" class="section-title">A. Personal</td></tr>
      <tr>
        <td colspan="3" style="padding:0;">
          <table class="personal-layout">
            <tr>
              <td class="personal-fields">
                <table class="field-table">
                  <tr><td class="field-label">Name</td><td class="field-colon">:</td><td><?= $employee['first_name'].' '.$employee['last_name']; ?></td></tr>
                  <tr><td class="field-label">Job Position</td><td class="field-colon">:</td><td><?= $designation['designation_name'] ?? ''; ?></td></tr>
                  <tr><td class="field-label">Employee Number</td><td class="field-colon">:</td><td><?= $staff_detail['employee_id'] ?? ''; ?></td></tr>
                  <tr><td class="field-label">Place, Date of Birth</td><td class="field-colon">:</td>
                    <td><?= ($header['place_of_birth'] ?? '') . ', ' . ($staff_detail['date_of_birth'] ?? ''); ?></td></tr>
                  <tr><td class="field-label">Address</td><td class="field-colon">:</td><td><?= $header['address'] ?? ''; ?></td></tr>
                  <tr><td class="field-label">Phone Number</td><td class="field-colon">:</td><td><?= $employee['contact_number'] ?? ''; ?></td></tr>
                </table>
              </td>
              <td class="personal-photo">
                <img src="<?= base_url().'/public/uploads/users/'.$employee['profile_photo']; ?>" alt="Photo" style="height:150px;object-fit:cover;">
              </td>
            </tr>
          </table>
        </td>
      </tr>

      <!-- B. Education -->
      <tr><td colspan="3" class="section-title">B. Education</td></tr>
      <tr>
        <td colspan="3" class="edu-wrapper">
          <table class="edu-table">
            <tr><th style="width:25%;">Degree</th><th style="width:25%;">Institutions</th><th style="width:25%;">Major</th><th style="width:25%;">Graduate</th></tr>
            <?php if(!empty($education)): ?>
              <?php foreach($education as $edu): ?>
              <tr><td><?= $edu['degree']; ?></td><td><?= $edu['institution']; ?></td><td><?= $edu['major']; ?></td><td><?= $edu['graduate_year']; ?></td></tr>
              <?php endforeach; ?>
            <?php else: ?>
            <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
            <?php endif; ?>
          </table>
        </td>
      </tr>

      <!-- C. Training -->
      <tr><td colspan="3" class="section-title">C. Training</td></tr>
      <tr>
        <td colspan="3" style="padding:0;">
          <table class="train-table">
            <tr>
              <th style="width:15%;">Course Title</th>
              <th style="width:18%;">Course Objective</th>
              <th style="width:15%;">Date Completed</th>
              <th style="width:12%;">Test Resulted</th>
              <th style="width:10%;">Total Hours<br>of Training</th>
              <th style="width:12%;">Institution</th>
              <th style="width:13%;">Location<br>of<br>Training</th>
              <th style="width:15%;">Name of<br>Instructor</th>
            </tr>
            <?php if(!empty($training_history)): ?>
              <?php foreach($training_history as $th): ?>
              <tr>
                <td><?= $th['course_title']; ?></td>
                <td><?= $th['course_objective']; ?></td>
                <td><?= !empty($th['date_completed']) && strtotime($th['date_completed']) ? date('d-m-Y', strtotime($th['date_completed'])) : ''; ?></td>
                <td><?= $th['test_result']; ?></td>
                <td><?= $th['total_hours']; ?></td>
                <td><?= $th['institution']; ?></td>
                <td><?= $th['location']; ?></td>
                <td><?= $th['instructor_name']; ?></td>
              </tr>
              <?php endforeach; ?>
            <?php else: ?>
            <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
            <?php endif; ?>
          </table>
        </td>
      </tr>

      <!-- Licenses & Certificates -->
      <tr><td colspan="3" style="padding:0;">
        <table class="license-header-table">
          <tr><td>AME License</td><td>COMA</td><td>C of C</td><td>Basic Certificate</td></tr>
        </table>
        <table class="license-body-table">
          <tr>
            <?php
            $ame_list = array_values(array_filter($licenses, function($l) { return $l['license_type']=='AME'; }));
            $coma_list = array_values(array_filter($licenses, function($l) { return $l['license_type']=='COMA'; }));
            $coc_list = array_values(array_filter($licenses, function($l) { return $l['license_type']=='COC'; }));
            $ame = $ame_list[0] ?? [];
            $coma = $coma_list[0] ?? [];
            $coc = $coc_list[0] ?? [];
            ?>
            <td class="col-box">
              <table class="lic-field-table">
                <tr><td class="lic-label">No</td><td class="lic-colon">:</td><td><?= $ame['license_no'] ?? ''; ?></td></tr>
                <tr><td class="lic-label">Expired</td><td class="lic-colon">:</td><td><?= $ame['expired_date'] ?? ''; ?></td></tr>
                <tr><td class="lic-label">Rating</td><td class="lic-colon">:</td><td><?= $ame['rating'] ?? ''; ?></td></tr>
              </table>
            </td>
            <td class="col-box">
              <table class="lic-field-table">
                <tr><td class="lic-label">No</td><td class="lic-colon">:</td><td><?= $coma['license_no'] ?? ''; ?></td></tr>
                <tr><td class="lic-label">Expired</td><td class="lic-colon">:</td><td><?= $coma['expired_date'] ?? ''; ?></td></tr>
                <tr><td class="lic-label">Rating</td><td class="lic-colon">:</td><td><?= $coma['rating'] ?? ''; ?></td></tr>
              </table>
            </td>
            <td class="col-box">
              <table class="lic-field-table">
                <tr><td class="lic-label">No</td><td class="lic-colon">:</td><td><?= $coc['license_no'] ?? ''; ?></td></tr>
                <tr><td class="lic-label">Expired</td><td class="lic-colon">:</td><td><?= $coc['expired_date'] ?? ''; ?></td></tr>
                <tr><td class="lic-label">Rating</td><td class="lic-colon">:</td><td><?= $coc['rating'] ?? ''; ?></td></tr>
              </table>
            </td>
            <td class="col-box">
              <table class="checkbox-table">
                <tr><td>
                  <span class="chk-box"><?= (!empty($certificates['a1'])) ? '&#9745;' : '&#9744;'; ?></span>A1&nbsp;
                  <span class="chk-box"><?= (!empty($certificates['a2'])) ? '&#9745;' : '&#9744;'; ?></span>A2&nbsp;
                  <span class="chk-box"><?= (!empty($certificates['a3'])) ? '&#9745;' : '&#9744;'; ?></span>A3&nbsp;
                  <span class="chk-box"><?= (!empty($certificates['a4'])) ? '&#9745;' : '&#9744;'; ?></span>A4
                </td></tr>
                <tr><td>
                  <span class="chk-box"><?= (!empty($certificates['c1'])) ? '&#9745;' : '&#9744;'; ?></span>C1&nbsp;
                  <span class="chk-box"><?= (!empty($certificates['c2'])) ? '&#9745;' : '&#9744;'; ?></span>C2&nbsp;
                  <span class="chk-box"><?= (!empty($certificates['c4'])) ? '&#9745;' : '&#9744;'; ?></span>C4
                </td></tr>
              </table>
            </td>
          </tr>
        </table>
      </td></tr>

      <!-- Signatures -->
      <tr>
        <td colspan="3" style="padding:0;">
          <table class="sig-table">
            <tr>
              <td>
                <?php
                $prepared_by_sig = null;
                foreach($signatures ?? [] as $sig) {
                    if(($sig['signature_type'] ?? '') === 'prepared_by') {
                        $prepared_by_sig = $sig;
                        break;
                    }
                }
                $prepared_sig_src = null;
                if(!empty($prepared_by_sig['file_path'])) {
                    $prepared_sig_src = base_url() . '/' . $prepared_by_sig['file_path'];
                } else if(!empty($prepared_by_sig['signature_data'])) {
                    $prepared_sig_src = $prepared_by_sig['signature_data'];
                }
                $prepared_date = !empty($prepared_by_sig['created_at']) ? date('d-m-Y', strtotime($prepared_by_sig['created_at'])) : '';
                // Get assigned name
                $prepared_by_name = '';
                if(!empty($header['prepared_by_id'])) {
                    $pb = (new \App\Models\UsersModel())->where('user_id', $header['prepared_by_id'])->first();
                    $prepared_by_name = $pb ? $pb['first_name'].' '.$pb['last_name'] : '';
                }
                ?>
                <div style="font-size:11px; margin-bottom:4px;">Jakarta<?= $prepared_date ? ', '.$prepared_date : ''; ?></div>
                Prepared By
                <?php if($prepared_sig_src): ?>
                <div style="height:60px; display:flex; align-items:center; justify-content:center; margin-top:5px;">
                  <img src="<?= $prepared_sig_src; ?>" alt="Prepared By Signature" style="max-height:55px; max-width:100%; object-fit:contain;">
                </div>
                <div style="font-size:11px;">(<?= $prepared_by_name ?: '________________'; ?>)</div>
                <?php else: ?>
                <div class="sig-line-text">(<?= $prepared_by_name ?: '_________________________________'; ?>)</div>
                <?php endif; ?>
                <?php if(isset($logged_in_user_id) && !empty($header['prepared_by_id']) && $logged_in_user_id == $header['prepared_by_id']): ?>
                <div class="no-print" style="margin-top:5px;">
                  <?php if($prepared_sig_src): ?>
                  <button type="button" class="btn-sign" onclick="openSignModal('prepared_by', '<?= $employee['user_id']; ?>')">Edit</button>
                  <button type="button" class="btn-sign btn-sign-danger" onclick="deleteSignature('prepared_by', '<?= $employee['user_id']; ?>')">Remove</button>
                  <?php else: ?>
                  <button type="button" class="btn-sign" onclick="openSignModal('prepared_by', '<?= $employee['user_id']; ?>')">Sign</button>
                  <?php endif; ?>
                </div>
                <?php endif; ?>
              </td>
              <td>
                <?php
                $emp_sig_src = null;
                if(!empty($employee_signature['file_path'])) {
                    $emp_sig_src = base_url() . '/' . $employee_signature['file_path'];
                } else if(!empty($employee_signature['signature_data'])) {
                    $emp_sig_src = $employee_signature['signature_data'];
                }
                $employee_date = !empty($employee_signature['created_at']) ? date('d-m-Y', strtotime($employee_signature['created_at'])) : '';
                ?>
                <div style="font-size:11px; margin-bottom:4px;">Jakarta<?= $employee_date ? ', '.$employee_date : ''; ?></div>
                Employee Signed
                <?php if($emp_sig_src): ?>
                <div style="height:60px; display:flex; align-items:center; justify-content:center; margin-top:5px;">
                  <img src="<?= $emp_sig_src; ?>" alt="Employee Signature" style="max-height:55px; max-width:100%; object-fit:contain;">
                </div>
                <div style="font-size:11px;">(<?= $employee['first_name'].' '.$employee['last_name']; ?>)</div>
                <?php else: ?>
                <div class="sig-line-text">(<?= $employee['first_name'].' '.$employee['last_name']; ?>)</div>
                <?php endif; ?>
              </td>
              <td>
                <?php
                $approved_by_sig = null;
                foreach($signatures ?? [] as $sig) {
                    if(($sig['signature_type'] ?? '') === 'approved_by') {
                        $approved_by_sig = $sig;
                        break;
                    }
                }
                $approved_sig_src = null;
                if(!empty($approved_by_sig['file_path'])) {
                    $approved_sig_src = base_url() . '/' . $approved_by_sig['file_path'];
                } else if(!empty($approved_by_sig['signature_data'])) {
                    $approved_sig_src = $approved_by_sig['signature_data'];
                }
                $approved_date = !empty($approved_by_sig['created_at']) ? date('d-m-Y', strtotime($approved_by_sig['created_at'])) : '';
                // Get assigned name
                $approved_by_name = '';
                if(!empty($header['approved_by_id'])) {
                    $ab = (new \App\Models\UsersModel())->where('user_id', $header['approved_by_id'])->first();
                    $approved_by_name = $ab ? $ab['first_name'].' '.$ab['last_name'] : '';
                }
                ?>
                <div style="font-size:11px; margin-bottom:4px;">Jakarta<?= $approved_date ? ', '.$approved_date : ''; ?></div>
                Approved By
                <?php if($approved_sig_src): ?>
                <div style="height:60px; display:flex; align-items:center; justify-content:center; margin-top:5px;">
                  <img src="<?= $approved_sig_src; ?>" alt="Approved By Signature" style="max-height:55px; max-width:100%; object-fit:contain;">
                </div>
                <div style="font-size:11px;">(<?= $approved_by_name ?: '________________'; ?>)</div>
                <?php else: ?>
                <div class="sig-line-text">(<?= $approved_by_name ?: '_________________________________'; ?>)</div>
                <?php endif; ?>
                <?php if(isset($logged_in_user_id) && !empty($header['approved_by_id']) && $logged_in_user_id == $header['approved_by_id']): ?>
                <div class="no-print" style="margin-top:5px;">
                  <?php if($approved_sig_src): ?>
                  <button type="button" class="btn-sign" onclick="openSignModal('approved_by', '<?= $employee['user_id']; ?>')">Edit</button>
                  <button type="button" class="btn-sign btn-sign-danger" onclick="deleteSignature('approved_by', '<?= $employee['user_id']; ?>')">Remove</button>
                  <?php else: ?>
                  <button type="button" class="btn-sign" onclick="openSignModal('approved_by', '<?= $employee['user_id']; ?>')">Sign</button>
                  <?php endif; ?>
                </div>
                <?php endif; ?>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
    </div><!-- /.main-table-wrapper -->
    <div class="footer-no">Form No. : TPM/003/24</div>
  </div>

  <!-- Reusable Sign Modal -->
  <div id="signModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
    <div style="background:#fff; border-radius:8px; padding:20px; width:460px; max-width:95%; box-shadow:0 4px 20px rgba(0,0,0,0.3);">
      <h3 style="margin-top:0; font-size:16px;" id="signModalTitle">Sign</h3>
      <input type="hidden" id="modal_signature_type" value="">
      <input type="hidden" id="modal_user_id" value="">

      <div style="display:flex; gap:10px; margin-bottom:10px;">
        <button type="button" class="btn-sign" id="tabDraw" onclick="switchTab('draw')" style="font-weight:bold;">Free Draw</button>
        <button type="button" class="btn-sign" id="tabUpload" onclick="switchTab('upload')">Upload File</button>
      </div>

      <div id="drawPane">
        <div style="border:1px solid #ccc; display:inline-block; border-radius:4px; overflow:hidden;">
          <canvas id="modal_canvas" width="400" height="150" style="cursor:crosshair; background:#fff;"></canvas>
        </div>
        <div style="margin-top:8px;">
          <button type="button" class="btn-sign" onclick="clearCanvas()">Clear</button>
          <small style="color:#888; margin-left:8px;">Draw your signature above</small>
        </div>
      </div>

      <div id="uploadPane" style="display:none;">
        <input type="file" id="modal_sig_file" accept="image/jpeg,image/jpg,image/png" style="margin-bottom:8px;">
        <small style="color:#888;">Allowed: JPG, JPEG, PNG (max 2MB)</small>
        <div id="modal_upload_preview" style="display:none; margin-top:8px;">
          <img id="modal_preview_img" style="max-width:100%; max-height:120px; border:1px solid #ddd;">
        </div>
      </div>

      <div style="margin-top:15px; text-align:right;">
        <button type="button" class="btn-sign" onclick="closeSignModal()">Cancel</button>
        <button type="button" class="btn-sign" style="background:#0062cc; color:#fff; border-color:#0062cc;" onclick="submitSignature()">Save Signature</button>
      </div>
    </div>
  </div>

  <style>
    .no-print { display: block; }
    .btn-sign {
      padding: 4px 12px; font-size: 11px; cursor: pointer;
      border: 1px solid #ccc; border-radius: 4px; background: #f8f9fa; color: #333;
    }
    .btn-sign:hover { background: #e2e6ea; }
    .btn-sign-danger { color: #dc3545; border-color: #dc3545; }
    .btn-sign-danger:hover { background: #dc3545; color: #fff; }
    @media print {
      .no-print, #signModal { display: none !important; }
    }
  </style>

  <script>
  (function() {
    var csrfName = '<?= csrf_token(); ?>';
    var csrfHash = '<?= csrf_hash(); ?>';

    var canvas = document.getElementById('modal_canvas');
    var ctx = canvas ? canvas.getContext('2d') : null;
    var isDrawing = false, hasDrawn = false;

    function getPos(e) {
      var rect = canvas.getBoundingClientRect();
      var cx = e.touches ? e.touches[0].clientX : e.clientX;
      var cy = e.touches ? e.touches[0].clientY : e.clientY;
      return { x: (cx - rect.left) * (canvas.width / rect.width), y: (cy - rect.top) * (canvas.height / rect.height) };
    }

    if(canvas && ctx) {
      ctx.lineWidth = 2; ctx.lineCap = 'round'; ctx.strokeStyle = '#000';
      canvas.addEventListener('mousedown', function(e){ isDrawing=true; hasDrawn=true; var p=getPos(e); ctx.beginPath(); ctx.moveTo(p.x,p.y); });
      canvas.addEventListener('mousemove', function(e){ if(!isDrawing) return; var p=getPos(e); ctx.lineTo(p.x,p.y); ctx.stroke(); });
      canvas.addEventListener('mouseup', function(){ isDrawing=false; });
      canvas.addEventListener('mouseout', function(){ isDrawing=false; });
      canvas.addEventListener('touchstart', function(e){ e.preventDefault(); isDrawing=true; hasDrawn=true; var p=getPos(e); ctx.beginPath(); ctx.moveTo(p.x,p.y); }, {passive:false});
      canvas.addEventListener('touchmove', function(e){ e.preventDefault(); if(!isDrawing) return; var p=getPos(e); ctx.lineTo(p.x,p.y); ctx.stroke(); }, {passive:false});
      canvas.addEventListener('touchend', function(){ isDrawing=false; });
    }

    window.clearCanvas = function() {
      if(ctx) ctx.clearRect(0, 0, canvas.width, canvas.height);
      hasDrawn = false;
    };

    window.switchTab = function(tab) {
      document.getElementById('drawPane').style.display = (tab==='draw') ? 'block' : 'none';
      document.getElementById('uploadPane').style.display = (tab==='upload') ? 'block' : 'none';
      document.getElementById('tabDraw').style.fontWeight = (tab==='draw') ? 'bold' : 'normal';
      document.getElementById('tabUpload').style.fontWeight = (tab==='upload') ? 'bold' : 'normal';
    };

    window.openSignModal = function(type, userId) {
      document.getElementById('modal_signature_type').value = type;
      document.getElementById('modal_user_id').value = userId;
      var label = type.replace('_', ' ').replace(/\b\w/g, function(l){ return l.toUpperCase(); });
      document.getElementById('signModalTitle').textContent = 'Sign as ' + label;
      clearCanvas();
      document.getElementById('modal_sig_file').value = '';
      document.getElementById('modal_upload_preview').style.display = 'none';
      switchTab('draw');
      document.getElementById('signModal').style.display = 'flex';
    };

    window.closeSignModal = function() {
      document.getElementById('signModal').style.display = 'none';
    };

    // Upload preview
    document.getElementById('modal_sig_file').addEventListener('change', function() {
      var file = this.files[0];
      if(file) {
        var url = URL.createObjectURL(file);
        document.getElementById('modal_preview_img').src = url;
        document.getElementById('modal_upload_preview').style.display = 'block';
      }
    });

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

    window.submitSignature = function() {
      var type = document.getElementById('modal_signature_type').value;
      var userId = document.getElementById('modal_user_id').value;
      var formData = new FormData();
      formData.append(csrfName, csrfHash);
      formData.append('user_id', userId);
      formData.append('signature_type', type);

      var drawVisible = document.getElementById('drawPane').style.display !== 'none';
      if(drawVisible && hasDrawn) {
        if (typeof canvas.toBlob === 'function') {
          canvas.toBlob(function(blob) {
            formData.append('sig_file', blob, 'signature.png');
            submitSignatureRequest(formData);
          }, 'image/png');
          return;
        }
        formData.append('sig_file', dataURLToBlob(canvas.toDataURL('image/png')), 'signature.png');
      } else {
        var fileInput = document.getElementById('modal_sig_file');
        if(fileInput.files.length > 0) {
          formData.append('sig_file', fileInput.files[0]);
        } else {
          alert('Please draw a signature or upload a file.');
          return;
        }
      }

      submitSignatureRequest(formData);
    };

    function submitSignatureRequest(formData) {
      fetch('<?= site_url("erp/training-record-sign/"); ?>', {
        method: 'POST',
        body: formData
      })
      .then(function(r) {
        if (!r.ok) {
          return r.text().then(function(text) { throw new Error(text || r.statusText); });
        }
        return r.json();
      })
      .then(function(data){
        if(data.csrf_hash) csrfHash = data.csrf_hash;
        if(data.error) {
          alert(data.error);
        } else {
          alert(data.result || 'Saved!');
          closeSignModal();
          location.reload();
        }
      })
      .catch(function(err){ alert('Error: ' + err); });
    }

    window.deleteSignature = function(type, userId) {
      if(!confirm('Are you sure you want to remove this signature?')) return;
      var formData = new FormData();
      formData.append(csrfName, csrfHash);
      formData.append('user_id', userId);
      formData.append('signature_type', type);
      formData.append('delete_signature', '1');

      fetch('<?= site_url("erp/training-record-sign/"); ?>', {
        method: 'POST',
        body: formData
      })
      .then(function(r) {
        if (!r.ok) {
          return r.text().then(function(text) { throw new Error(text || r.statusText); });
        }
        return r.json();
      })
      .then(function(data){
        if(data.csrf_hash) csrfHash = data.csrf_hash;
        if(data.error) {
          alert(data.error);
        } else {
          alert(data.result || 'Removed!');
          location.reload();
        }
      })
      .catch(function(err){ alert('Error: ' + err); });
    };
  })();
  </script>
</body>
</html>
