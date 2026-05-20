<?php
use App\Models\UsersModel;
use App\Models\SystemModel;
use App\Models\TrainingRecordHeaderModel;

$SystemModel = new SystemModel();
$UsersModel = new UsersModel();

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$xin_system = erp_company_settings();
?>
<div class="card user-profile-list">
  <div class="card-header">
    <h5>
      Training Record
    </h5>
  </div>
  <div class="card-body">
    <div class="box-datatable table-responsive">
      <table class="datatables-demo table table-striped table-bordered" id="xin_table">
        <thead>
          <tr>
            <th>Employee Name</th>
            <th>Employee ID</th>
            <th>Designation</th>
            <th>Status</th>
            <th>Email</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($employees as $emp): ?>
          <tr>
            <td>
              <img src="<?= base_url().'/public/uploads/users/'.$emp['profile_photo'];?>" alt="" class="img-fluid wid-40 mr-2">
              <?= $emp['first_name'].' '.$emp['last_name']; ?>
            </td>
            <td><?= $emp['employee_id'] ?? '-'; ?></td>
            <td><?= $emp['designation_name'] ?? '-'; ?></td>
            <td>
              <?php if($emp['has_record']): ?>
                <span class="badge badge-light-success">Filled</span>
              <?php else: ?>
                <span class="badge badge-light-danger">Not Filled</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if($emp['has_record']): ?>
              <button class="btn btn-sm btn-info text-white" onclick="sendReminder(<?= $emp['user_id']; ?>)">
                <i class="feather icon-mail"></i> Send Reminder
              </button>
              <?php else: ?>
              <span class="text-muted">-</span>
              <?php endif; ?>
            </td>
            <td>
              <a href="<?= site_url('erp/training-record-form/'.uencode($emp['user_id'])); ?>" class="btn btn-sm btn-primary">
                <i class="feather icon-edit"></i> <?= $emp['has_record'] ? 'Edit' : 'Fill CV'; ?>
              </a>
              <?php if($emp['has_record']): ?>
              <a href="<?= site_url('erp/training-record-print/'.uencode($emp['user_id'])); ?>" class="btn btn-sm btn-info" target="_blank">
                <i class="feather icon-printer"></i> Print
              </a>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
function sendReminder(userId) {
  if(!confirm('Are you sure you want to send a reminder email to this employee?')) return;
  
  var formData = new FormData();
  formData.append('user_id', userId);
  formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

  var btn = event.currentTarget;
  var originalText = btn.innerHTML;
  btn.innerHTML = '<i class="feather icon-loader"></i> Sending...';
  btn.disabled = true;

  fetch('<?= site_url("erp/training-record-email"); ?>', {
    method: 'POST',
    body: formData
  })
  .then(function(r){ return r.json(); })
  .then(function(data){
    btn.innerHTML = originalText;
    btn.disabled = false;
    if(data.error) {
      toastr.error(data.error);
    } else {
      toastr.success(data.result);
    }
  })
  .catch(function(err){
    btn.innerHTML = originalText;
    btn.disabled = false;
    toastr.error('Error: ' + err);
  });
}
</script>
