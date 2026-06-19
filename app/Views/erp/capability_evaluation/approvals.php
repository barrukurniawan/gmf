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
                                    <?php foreach($my_roles as $role): 
                                        $sig_type = '';
                                        if($role == 'Prepared By (1)') $sig_type = 'prepared_by_1';
                                        if($role == 'Prepared By (2)') $sig_type = 'prepared_by_2';
                                        if($role == 'Approved By') $sig_type = 'approved_by';
                                    ?>
                                    <button class="btn btn-sm btn-primary mb-1" onclick="openSignatureModal(<?= $r['id'] ?>, '<?= $sig_type ?>', '<?= $role ?>')">Sign <?= $role ?></button><br>
                                    <?php endforeach; ?>
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

<!-- Signature Modal -->
<div class="modal fade" id="signatureModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sigModalTitle">Sign Document</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="signatureForm" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= csrf_hash(); ?>">
                <input type="hidden" name="eval_id" id="sig_eval_id">
                <input type="hidden" name="signature_type" id="sig_type">
                
                <div class="modal-body">
                    <div class="form-group">
                        <label>Upload Signature Image (JPG/PNG)</label>
                        <input type="file" class="form-control" name="sig_file" accept="image/*">
                    </div>
                    <div class="text-center my-3">OR</div>
                    <div class="form-group">
                        <label>Draw Signature</label>
                        <div style="border: 1px solid #ccc; background:#fff;">
                            <canvas id="sigCanvas" width="400" height="150" style="touch-action: none;"></canvas>
                        </div>
                        <input type="hidden" name="sig_data" id="sig_data">
                        <button type="button" class="btn btn-sm btn-light mt-2" onclick="clearCanvas()">Clear Signature</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" onclick="deleteSignature()">Delete Existing Signature</button>
                    <button type="button" class="btn btn-primary" onclick="saveSignature()">Save Signature</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
let signaturePad;

$(document).ready(function() {
    var canvas = document.getElementById('sigCanvas');
    signaturePad = new SignaturePad(canvas, { backgroundColor: 'rgb(255, 255, 255)' });
});

function openSignatureModal(eval_id, sig_type, role_name) {
    $('#sig_eval_id').val(eval_id);
    $('#sig_type').val(sig_type);
    $('#sigModalTitle').text('Sign as ' + role_name);
    signaturePad.clear();
    $('#sig_data').val('');
    $('#signatureModal').modal('show');
}

function clearCanvas() {
    signaturePad.clear();
}

function saveSignature() {
    if(!signaturePad.isEmpty()) {
        $('#sig_data').val(signaturePad.toDataURL('image/png'));
    } else {
        $('#sig_data').val('');
    }

    let formData = new FormData($('#signatureForm')[0]);
    $.ajax({
        url: '<?= site_url("erp/capability-evaluation/sign-record") ?>',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(resp) {
            if(resp.error) { toastr.error(resp.error); }
            else { 
                toastr.success(resp.result); 
                $('#signatureModal').modal('hide'); 
                location.reload();
            }
        }
    });
}

function deleteSignature() {
    if(!confirm('Are you sure you want to delete your signature for this role?')) return;
    
    let formData = new FormData($('#signatureForm')[0]);
    formData.append('delete_signature', '1');
    $.ajax({
        url: '<?= site_url("erp/capability-evaluation/sign-record") ?>',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(resp) {
            if(resp.error) { toastr.error(resp.error); }
            else { 
                toastr.success(resp.result); 
                $('#signatureModal').modal('hide'); 
            }
        }
    });
}
</script>
