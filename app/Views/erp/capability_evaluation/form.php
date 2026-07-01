<?php
$h = $header;
?>
<div class="row">
    <div class="col-sm-12">
        <form action="<?= site_url('erp/capability-evaluation/save') ?>" method="post" id="eval_form">
            <input type="hidden" name="csrf_token" value="<?= csrf_hash(); ?>">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="form_type" value="<?= $type ?>">

            <div class="card mb-4">
                <div class="card-header">
                    <h5>PART A - GENERAL INFORMATION</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Capability No</label>
                            <input type="text" class="form-control" name="capability_no" value="<?= $h['capability_no'] ?? '' ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Date</label>
                            <input type="date" class="form-control" name="evaluation_date" value="<?= $h['evaluation_date'] ?? '' ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label><?= $type == 'component' ? 'Component Name' : 'Type of Aircraft' ?></label>
                            <input type="text" class="form-control" name="type_of_aircraft" value="<?= $h['type_of_aircraft'] ?? '' ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Manufacture</label>
                            <input type="text" class="form-control" name="manufacture" value="<?= $h['manufacture'] ?? '' ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>ATA Chapter</label>
                            <input type="text" class="form-control" name="ata_chapter" value="<?= $h['ata_chapter'] ?? '' ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Rating</label>
                            <input type="text" class="form-control" name="rating" value="<?= $h['rating'] ?? '' ?>">
                        </div>
                        <?php if($type == 'component'): ?>
                        <div class="col-md-6 mb-3">
                            <label>Part Number</label>
                            <input type="text" class="form-control" name="part_number" value="<?= $h['part_number'] ?? '' ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Type</label>
                            <input type="text" class="form-control" name="component_type" value="<?= $h['component_type'] ?? '' ?>">
                        </div>
                        <?php endif; ?>
                        <div class="col-md-12 mb-3">
                            <label>Proposed Scope of Work</label>
                            <textarea class="form-control" name="scope_of_work" rows="3"><?= $h['scope_of_work'] ?? '' ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PART B -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>PART B - TECHNICAL DATA REVIEW</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Requirement</th>
                                    <th>Available (Yes/No/NA)</th>
                                    <th>Reference</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $default_reqs = $type == 'component' 
                                    ? ['Latest CMM', 'IPC', 'Service Bulletin', 'Airworthiness Directive', 'Standard Practices']
                                    : ['Latest AMM', 'IPC', 'Service Bulletin', 'Airworthiness Directive', 'Standard Practices'];
                                $td_idx = 0;
                                foreach($default_reqs as $req): 
                                    $t_avail = ''; $t_ref = '';
                                    if(isset($tech_data[$td_idx])) {
                                        $t_avail = $tech_data[$td_idx]['available'];
                                        $t_ref = $tech_data[$td_idx]['reference'];
                                    }
                                ?>
                                <tr>
                                    <td><input type="hidden" name="td_requirement[]" value="<?= $req ?>"><?= $req ?></td>
                                    <td>
                                        <select name="td_available[]" class="form-control">
                                            <option value="">-Select-</option>
                                            <option value="Yes" <?= $t_avail=='Yes'?'selected':'' ?>>Yes</option>
                                            <option value="No" <?= $t_avail=='No'?'selected':'' ?>>No</option>
                                            <option value="N/A" <?= $t_avail=='N/A'?'selected':'' ?>>N/A</option>
                                        </select>
                                    </td>
                                    <td><input type="text" class="form-control" name="td_reference[]" value="<?= $t_ref ?>"></td>
                                </tr>
                                <?php $td_idx++; endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- PART C -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>PART C - FACILITY ASSESSMENT</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Requirement</th>
                                    <th>Available (Yes/No)</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $fac_reqs = $type == 'component'
                                    ? ['Dedicated Shop Area', 'Environmental Control', 'Lighting', 'Storage Area', 'Quarantine Area']
                                    : ['Dedicated Hangar Area', 'Environmental Control', 'Lighting', 'Storage Area', 'Quarantine Area'];
                                $fac_idx = 0;
                                foreach($fac_reqs as $req): 
                                    $f_avail = ''; $f_rem = '';
                                    if(isset($facilities[$fac_idx])) {
                                        $f_avail = $facilities[$fac_idx]['available'];
                                        $f_rem = $facilities[$fac_idx]['remarks'];
                                    }
                                ?>
                                <tr>
                                    <td><input type="hidden" name="fac_requirement[]" value="<?= $req ?>"><?= $req ?></td>
                                    <td>
                                        <select name="fac_available[]" class="form-control">
                                            <option value="">-Select-</option>
                                            <option value="Yes" <?= $f_avail=='Yes'?'selected':'' ?>>Yes</option>
                                            <option value="No" <?= $f_avail=='No'?'selected':'' ?>>No</option>
                                        </select>
                                    </td>
                                    <td><input type="text" class="form-control" name="fac_remarks[]" value="<?= $f_rem ?>"></td>
                                </tr>
                                <?php $fac_idx++; endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- PART D -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between">
                    <h5>PART D - TOOLS, TEST EQUIPMENT, AND SPECIAL TOOLS</h5>
                    <button type="button" class="btn btn-sm btn-info" onclick="addToolRow()">Add Row</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="toolsTable">
                            <thead>
                                <tr>
                                    <th>Descriptions</th>
                                    <th>Part Number</th>
                                    <th>Type</th>
                                    <th>Remarks</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($tools)): foreach($tools as $t): ?>
                                <tr>
                                    <td><input type="text" class="form-control" name="tool_description[]" value="<?= $t['description'] ?>"></td>
                                    <td><input type="text" class="form-control" name="tool_part_number[]" value="<?= $t['part_number'] ?>"></td>
                                    <td><input type="text" class="form-control" name="tool_type[]" value="<?= $t['type'] ?>"></td>
                                    <td><input type="text" class="form-control" name="tool_remarks[]" value="<?= $t['remarks'] ?>"></td>
                                    <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">X</button></td>
                                </tr>
                                <?php endforeach; else: ?>
                                <tr>
                                    <td><input type="text" class="form-control" name="tool_description[]"></td>
                                    <td><input type="text" class="form-control" name="tool_part_number[]"></td>
                                    <td><input type="text" class="form-control" name="tool_type[]"></td>
                                    <td><input type="text" class="form-control" name="tool_remarks[]"></td>
                                    <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">X</button></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- PART E -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between">
                    <h5>PART E - PERSONNEL COMPETENCY</h5>
                    <button type="button" class="btn btn-sm btn-info" onclick="addPersRow()">Add Row</button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="persTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th><?= $type == 'component' ? 'EXPERIENCE' : 'Year of Exp' ?></th>
                                    <th><?= $type == 'component' ? 'TRAINING' : 'Rating' ?></th>
                                    <th><?= $type == 'component' ? 'COMA No.' : 'AMEL No' ?></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($personnel)): foreach($personnel as $p): ?>
                                <tr>
                                    <td><input type="text" class="form-control" name="pers_name[]" value="<?= $p['name'] ?>"></td>
                                    <td><input type="text" class="form-control" name="pers_position[]" value="<?= $p['position'] ?>"></td>
                                    <td><input type="text" class="form-control" name="pers_year[]" value="<?= $p['year_of_experience'] ?>"></td>
                                    <td><input type="text" class="form-control" name="pers_rating[]" value="<?= $p['rating'] ?>"></td>
                                    <td><input type="text" class="form-control" name="pers_amel[]" value="<?= $p['amel_no'] ?>"></td>
                                    <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">X</button></td>
                                </tr>
                                <?php endforeach; else: ?>
                                <tr>
                                    <td><input type="text" class="form-control" name="pers_name[]"></td>
                                    <td><input type="text" class="form-control" name="pers_position[]"></td>
                                    <td><input type="text" class="form-control" name="pers_year[]"></td>
                                    <td><input type="text" class="form-control" name="pers_rating[]"></td>
                                    <td><input type="text" class="form-control" name="pers_amel[]"></td>
                                    <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">X</button></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <!-- PART F -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>PART F - CAPABILITY SELF EVALUATION APPROVAL</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $app_items = ['1. Technical Data', '2. Required Tools and Special Tools', '3. Test Equipment', '4. Qualified Personnel', '5. Housing and Facility'];
                                $app_idx = 0;
                                foreach($app_items as $req): 
                                    $a_stat = '';
                                    if(isset($approvals_data[$app_idx])) {
                                        $a_stat = $approvals_data[$app_idx]['status'];
                                    }
                                ?>
                                <tr>
                                    <td><input type="hidden" name="app_item[]" value="<?= $req ?>"><?= $req ?></td>
                                    <td><input type="text" class="form-control" name="app_status[]" value="<?= $a_stat ?>"></td>
                                </tr>
                                <?php $app_idx++; endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- PART G & DECISION & SIGNATURE ASSIGNMENT -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>PART G - VERIFICATION & EVALUATION DECISION</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Based on the capability self-evaluation conducted in accordance with the approved AMO procedures and applicable CASR Part 145 requirements, it has been determined that the organization possesses the necessary facilities, equipment, tooling, technical data, maintenance processes, and qualified personnel to perform the proposed scope of work.</p>
                    
                    <div class="row mt-4">
                        <div class="col-md-4 mb-3">
                            <label><strong>Assign: Prepared By (1)</strong></label>
                            <select name="prepared_by_1_id" class="form-control" data-plugin="select_hrm">
                                <option value="">-Select Staff-</option>
                                <?php foreach($all_staff as $staff): ?>
                                <option value="<?= $staff['user_id'] ?>" <?= ($h['prepared_by_1_id'] ?? '') == $staff['user_id'] ? 'selected' : '' ?>><?= $staff['first_name'] . ' ' . $staff['last_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label><strong>Assign: Prepared By (2)</strong></label>
                            <select name="prepared_by_2_id" class="form-control" data-plugin="select_hrm">
                                <option value="">-Select Staff-</option>
                                <?php foreach($all_staff as $staff): ?>
                                <option value="<?= $staff['user_id'] ?>" <?= ($h['prepared_by_2_id'] ?? '') == $staff['user_id'] ? 'selected' : '' ?>><?= $staff['first_name'] . ' ' . $staff['last_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label><strong>Assign: Approved By</strong></label>
                            <select name="approved_by_id" class="form-control" data-plugin="select_hrm">
                                <option value="">-Select Staff-</option>
                                <?php foreach($all_staff as $staff): ?>
                                <option value="<?= $staff['user_id'] ?>" <?= ($h['approved_by_id'] ?? '') == $staff['user_id'] ? 'selected' : '' ?>><?= $staff['first_name'] . ' ' . $staff['last_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label><strong>EVALUATION DECISION</strong></label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="decision_status" id="d1" value="approved" <?= ($h['decision_status'] ?? '') == 'approved' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="d1">APPROVED - Recommended for inclusion in the Capability List</label>
                                </div>
                                <br>
                                <div class="form-check form-check-inline mt-2">
                                    <input class="form-check-input" type="radio" name="decision_status" id="d2" value="not_approved" <?= ($h['decision_status'] ?? '') == 'not_approved' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="d2">NOT APPROVED - Not recommended for inclusion in the Capability List</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Remarks (if any):</label>
                            <textarea class="form-control" name="decision_remarks" rows="3"><?= $h['decision_remarks'] ?? '' ?></textarea>
                        </div>
                    </div>

                </div>
            </div>
            <div class="card-footer text-right">
                <button type="button" class="btn btn-primary" onclick="saveForm()"><i class="feather icon-save"></i> Save Record</button>
            </div>
            </div>

        </form>
    </div>
</div>

<script>
function addToolRow() {
    let tr = '<tr>' +
        '<td><input type="text" class="form-control" name="tool_description[]"></td>' +
        '<td><input type="text" class="form-control" name="tool_part_number[]"></td>' +
        '<td><input type="text" class="form-control" name="tool_type[]"></td>' +
        '<td><input type="text" class="form-control" name="tool_remarks[]"></td>' +
        '<td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">X</button></td>' +
        '</tr>';
    $('#toolsTable tbody').append(tr);
}

function addPersRow() {
    let tr = '<tr>' +
        '<td><input type="text" class="form-control" name="pers_name[]"></td>' +
        '<td><input type="text" class="form-control" name="pers_position[]"></td>' +
        '<td><input type="text" class="form-control" name="pers_year[]"></td>' +
        '<td><input type="text" class="form-control" name="pers_rating[]"></td>' +
        '<td><input type="text" class="form-control" name="pers_amel[]"></td>' +
        '<td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">X</button></td>' +
        '</tr>';
    $('#persTable tbody').append(tr);
}

function removeRow(btn) {
    $(btn).closest('tr').remove();
}

function saveForm() {
    let formData = new FormData($('#eval_form')[0]);
    $.ajax({
        url: $('#eval_form').attr('action'),
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(resp) {
            if(resp.error) {
                toastr.error(resp.error);
            } else {
                toastr.success(resp.result);
                setTimeout(function(){
                    window.location.href = "<?= site_url('erp/capability-evaluation/'.$type) ?>";
                }, 1500);
            }
        },
        error: function() {
            toastr.error('Error saving form');
        }
    });
}
</script>
