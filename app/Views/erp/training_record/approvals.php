<?php
use App\Models\UsersModel;
use App\Models\SystemModel;

$SystemModel = new SystemModel();
$UsersModel = new UsersModel();

$session = \Config\Services::session();
$usession = $session->get('sup_username');
$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
$xin_system = erp_company_settings();
?>

<div class="row">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <h5>Record Approvals</h5>
        <span class="text-muted">Training records assigned to you for signing</span>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-bordered table-striped" id="approvals_table">
            <thead>
              <tr>
                <th>#</th>
                <th>Employee Name</th>
                <th>Employee ID</th>
                <th>Designation</th>
                <th>Your Role</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if(!empty($approvals)): ?>
                <?php foreach($approvals as $i => $row): ?>
                <tr>
                  <td><?= $i + 1; ?></td>
                  <td><?= $row['employee_name']; ?></td>
                  <td><?= $row['employee_id_no']; ?></td>
                  <td><?= $row['designation_name']; ?></td>
                  <td>
                    <?php foreach($row['role'] as $r): ?>
                    <span class="badge badge-<?= ($r == 'Prepared By') ? 'info' : 'success'; ?>"><?= $r; ?></span>
                    <?php endforeach; ?>
                  </td>
                  <td>
                    <a href="<?= site_url('erp/training-record-print/' . $row['encoded_user_id']); ?>" class="btn btn-sm btn-primary" target="_blank">
                      <i class="feather icon-eye"></i> View / Sign
                    </a>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php else: ?>
              <tr>
                <td colspan="6" class="text-center text-muted">No records assigned to you for signing.</td>
              </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  $('#approvals_table').DataTable({
    "order": [[ 0, "asc" ]],
    "pageLength": 25
  });
});
</script>
