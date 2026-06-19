<?php
use App\Models\UsersModel;
$UsersModel = new UsersModel();
?>

<div class="row">
    <div class="col-sm-12">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><?= $title ?></h5>
                <a href="<?= site_url('erp/capability-evaluation/form/'.$type) ?>" class="btn btn-primary btn-sm">
                    <i class="feather icon-plus"></i> Add New Record
                </a>
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
