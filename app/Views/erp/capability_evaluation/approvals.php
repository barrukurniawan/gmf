<?php
use App\Models\UsersModel;
$UsersModel = new UsersModel();
?>

<div class="row">
    <div class="col-sm-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5><?= $title ?></h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="xin_table">
                        <thead>
                            <tr>
                                <th>Capability No</th>
                                <th>Form Type</th>
                                <th>Date</th>
                                <th>Target Action</th>
                                <th>Sign</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($records as $r): 
                                $my_roles = [];
                                $session = \Config\Services::session();
                                $uid = $session->get('sup_username')['sup_user_id'];
                                if($r['prepared_by_1_id'] == $uid) $my_roles[] = 'Prepared By (1)';
                                if($r['prepared_by_2_id'] == $uid) $my_roles[] = 'Prepared By (2)';
                                if($r['approved_by_id'] == $uid) $my_roles[] = 'Approved By';
                            ?>
                            <tr>
                                <td><?= $r['capability_no'] ?></td>
                                <td><?= ucfirst($r['form_type']) ?></td>
                                <td><?= $r['evaluation_date'] ?></td>
                                <td><?= implode(', ', $my_roles) ?></td>
                                <td>
                                    <a href="<?= site_url('erp/capability-evaluation/print-pdf/' . $r['id']); ?>" class="btn btn-sm btn-info" target="_blank">View / Sign PDF</a>
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
