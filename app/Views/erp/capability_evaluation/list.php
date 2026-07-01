<?php
use App\Models\UsersModel;
$UsersModel = new UsersModel();
?>

<div class="row">
    <div class="col-sm-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><?= $title ?></h5>
                <div>
                    <button type="button" class="btn btn-info btn-sm mr-2" data-toggle="modal" data-target="#printRecapModal">
                        <i class="feather icon-download"></i> Download Recap
                    </button>
                    <a href="<?= site_url('erp/capability-evaluation/form/'.$type) ?>" class="btn btn-primary btn-sm">
                        <i class="feather icon-plus"></i> Add New Record
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="xin_table">
                        <thead>
                            <tr>
                                <th>Capability No</th>
                                <th>Date</th>
                                <th><?= $type == 'maintenance' ? 'Type of Aircraft' : 'Item' ?></th>
                                <?php if($type == 'component'): ?>
                                <th>Prepared By 1</th>
                                <th>Prepared By 2</th>
                                <th>Approved By</th>
                                <th>Status</th>
                                <?php endif; ?>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($records as $r): ?>
                            <tr>
                                <td><?= $r['capability_no'] ?></td>
                                <td><?= $r['evaluation_date'] ?></td>
                                <td><?= $r['type_of_aircraft'] ?></td>
                                <?php if($type == 'component'): ?>
                                <td>
                                    <?php 
                                        if($r['prepared_by_1_id']) {
                                            $u = $UsersModel->find($r['prepared_by_1_id']);
                                            echo $u ? $u['first_name'] . ' ' . $u['last_name'] : '-';
                                        } else { echo '-'; }
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                        if($r['prepared_by_2_id']) {
                                            $u = $UsersModel->find($r['prepared_by_2_id']);
                                            echo $u ? $u['first_name'] . ' ' . $u['last_name'] : '-';
                                        } else { echo '-'; }
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                        if($r['approved_by_id']) {
                                            $u = $UsersModel->find($r['approved_by_id']);
                                            echo $u ? $u['first_name'] . ' ' . $u['last_name'] : '-';
                                        } else { echo '-'; }
                                    ?>
                                </td>
                                <td>
                                    <?php if($r['decision_status'] == 'approved'): ?>
                                        <span class="badge badge-light-success">Approved</span>
                                    <?php elseif($r['decision_status'] == 'not_approved'): ?>
                                        <span class="badge badge-light-danger">Not Approved</span>
                                    <?php else: ?>
                                        <span class="badge badge-light-warning">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <?php endif; ?>
                                <td>
                                    <a href="<?= site_url('erp/capability-evaluation/form/'.$type.'/'.$r['id']) ?>" class="btn btn-sm btn-icon btn-light-primary" title="Edit"><i class="feather icon-edit"></i></a>
                                    <a href="<?= site_url('erp/capability-evaluation/print-pdf/'.$r['id']) ?>" class="btn btn-sm btn-icon btn-light-info" target="_blank" title="Print PDF"><i class="feather icon-printer"></i></a>
                                    <a href="#!" class="btn btn-sm btn-icon btn-light-danger delete" data-id="<?= uencode($r['id']) ?>" data-type="<?= $type ?>" title="Delete"><i class="feather icon-trash-2"></i></a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="printRecapModal" tabindex="-1" role="dialog" aria-labelledby="printRecapModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="printRecapModalLabel">Print Recap Settings</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= site_url('erp/capability-evaluation/print-recap/'.$type) ?>" method="GET" target="_blank">
            <div class="modal-body">
                <div class="form-group">
                    <label>Prepared By</label>
                    <select name="prep_by" class="form-control" data-plugin="select_hrm">
                        <option value="">-- Select Employee --</option>
                        <?php foreach($all_staff as $staff): ?>
                        <option value="<?= $staff['user_id'] ?>"><?= $staff['first_name'] . ' ' . $staff['last_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Checked By</label>
                    <select name="check_by" class="form-control" data-plugin="select_hrm">
                        <option value="">-- Select Employee --</option>
                        <?php foreach($all_staff as $staff): ?>
                        <option value="<?= $staff['user_id'] ?>"><?= $staff['first_name'] . ' ' . $staff['last_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Approved By</label>
                    <select name="appv_by" class="form-control" data-plugin="select_hrm">
                        <option value="">-- Select Employee --</option>
                        <?php foreach($all_staff as $staff): ?>
                        <option value="<?= $staff['user_id'] ?>"><?= $staff['first_name'] . ' ' . $staff['last_name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary" onclick="$('#printRecapModal').modal('hide');">Print</button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- Hidden Delete Form -->
<form id="delete_record" name="delete_eval" action="<?= site_url('erp/capability-evaluation/delete_evaluation') ?>" method="post" style="display:none;">
    <input type="hidden" name="csrf_token" value="<?= csrf_hash() ?>">
    <input type="hidden" name="_token" value="" id="delete_token">
    <input type="hidden" name="type" value="delete_record">
</form>
