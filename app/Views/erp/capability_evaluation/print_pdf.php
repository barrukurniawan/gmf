<?php
$session = \Config\Services::session();
$usession = $session->get('sup_username');
$logged_in_user_id = $usession['sup_user_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Capability Evaluation PDF</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 11px; 
            margin: 0; 
            padding: 0; 
            background: #525659; 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
        }
        .page { 
            width: 210mm; 
            min-height: 297mm; 
            background: white; 
            margin: 20px auto; 
            padding: 10mm; 
            box-sizing: border-box; 
            box-shadow: 0 0 10px rgba(0,0,0,0.2); 
            page-break-after: always;
        }
        
        .main-table { 
            width: 100%; 
            border-collapse: collapse; 
            border: 1.5px solid #000; 
        }
        .main-table > tbody > tr > td { 
            border: 1.5px solid #000; 
            padding: 4px; 
        }
        
        .inner-table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        .inner-table th, .inner-table td { 
            border: 1px solid #000; 
            padding: 4px; 
        }
        /* Magic CSS to remove outer borders of nested inner tables */
        .inner-table tr:first-child th, .inner-table tr:first-child td { border-top: none; }
        .inner-table tr:last-child th, .inner-table tr:last-child td { border-bottom: none; }
        .inner-table tr th:first-child, .inner-table tr td:first-child { border-left: none; }
        .inner-table tr th:last-child, .inner-table tr td:last-child { border-right: none; }

        .signature-img { 
            max-height: 50px; 
            display: block; 
            margin: 0 auto; 
        }
        
        @media print {
            .no-print { display: none; }
            body { background: none; display: block; }
            .page { margin: 0; padding: 10mm; box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align:center; padding: 10px; background:#f0f0f0; margin-bottom: 20px; width: 100%;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">Print PDF</button>
    </div>

    <?php if($header['form_type'] == 'maintenance'): ?>
    <!-- MAINTENANCE PAGE -->
    <div class="page">
        <table class="main-table">
            <tr>
                <td style="padding: 0;">
                    <table class="inner-table">
                        <tr>
                            <td style="width: 20%; text-align: center; padding: 8px;">
                                <img src="https://gis.globalmaintenance.co.id/public/uploads/logo/other/GMF%20Logo%202021.png" alt="GMF Logo" style="height:45px;">
                            </td>
                            <td style="width: 50%; text-align: center; font-weight: bold; font-size: 11px; padding: 5px;">
                                <div style="margin-bottom:4px;">PT Global Maintenance Facility</div>
                                <div style="margin-bottom:4px;">Approved Maintenance Organization</div>
                                <div>DGCA No: 145D-376</div>
                            </td>
                            <td style="width: 30%; font-size: 9.5px; padding: 5px;">
                                <table style="width: 100%; border-collapse: collapse; border: none;">
                                    <tr><td style="border: none !important; padding: 1px 0; width: 35px; vertical-align: top;">Phone</td><td style="border: none !important; padding: 1px 0; vertical-align: top;">: +62 21 809 2019</td></tr>
                                    <tr><td style="border: none !important; padding: 1px 0; vertical-align: top;">Email</td><td style="border: none !important; padding: 1px 0; vertical-align: top;">: info@globalmaintenance.co.id</td></tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="text-align: center; font-weight: bold; padding: 5px;">
                    SELF EVALUATION FOR AIRCRAFT MAINTENANCE
                </td>
            </tr>
            <!-- PART A -->
            <tr>
                <td style="font-weight: bold; padding: 4px;">
                    PART A – GENERAL INFORMATION
                </td>
            </tr>
            <tr>
                <td style="padding: 0;">
                    <table class="inner-table">
                        <tr>
                            <td style="width: 20%;">Capability No</td>
                            <td style="width: 30%;">: <?= $header['capability_no'] ?></td>
                            <td style="width: 20%;">ATA Chapter</td>
                            <td style="width: 30%;">: <?= $header['ata_chapter'] ?></td>
                        </tr>
                        <tr>
                            <td>Date</td>
                            <td>: <?= $header['evaluation_date'] ?></td>
                            <td>Rating</td>
                            <td>: <?= $header['rating'] ?></td>
                        </tr>
                        <tr>
                            <td>Type of Aircraft</td>
                            <td>: <?= $header['type_of_aircraft'] ?></td>
                            <td rowspan="2">Proposed Scope of Work</td>
                            <td rowspan="2">: <?= nl2br($header['scope_of_work']) ?></td>
                        </tr>
                        <tr>
                            <td>Manufacture</td>
                            <td>: <?= $header['manufacture'] ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <!-- PART B -->
            <tr>
                <td style="font-weight: bold; padding: 4px;">
                    PART B – TECHNICAL DATA REVIEW
                </td>
            </tr>
            <tr>
                <td style="padding: 0;">
                    <table class="inner-table">
                        <tr>
                            <th style="width:40%;">Requirement</th>
                            <th style="width:30%; text-align:center;">Available</th>
                            <th style="width:30%;">Reference</th>
                        </tr>
                        <?php foreach($tech_data as $td): ?>
                        <tr>
                            <td><?= $td['requirement'] ?></td>
                            <td style="text-align:center;">
                                <span style="font-family: monospace;"><?= $td['available'] == 'Yes' ? '☑' : '☐' ?></span> Yes &nbsp;&nbsp;
                                <span style="font-family: monospace;"><?= $td['available'] == 'No' ? '☑' : '☐' ?></span> No &nbsp;&nbsp;
                                <span style="font-family: monospace;"><?= $td['available'] == 'N/A' ? '☑' : '☐' ?></span> N/A
                            </td>
                            <td><?= $td['reference'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </td>
            </tr>
            <!-- PART C -->
            <tr>
                <td style="font-weight: bold; padding: 4px;">
                    PART C – FACILITY ASSESSMENT
                </td>
            </tr>
            <tr>
                <td style="padding: 0;">
                    <table class="inner-table">
                        <tr>
                            <th style="width:40%;">Requirement</th>
                            <th style="width:30%; text-align:center;">Available</th>
                            <th style="width:30%;">Remarks</th>
                        </tr>
                        <?php foreach($facilities as $fac): ?>
                        <tr>
                            <td><?= $fac['requirement'] ?></td>
                            <td style="text-align:center;">
                                <span style="font-family: monospace;"><?= $fac['available'] == 'Yes' ? '☑' : '☐' ?></span> Yes &nbsp;&nbsp;
                                <span style="font-family: monospace;"><?= $fac['available'] == 'No' ? '☑' : '☐' ?></span> No
                            </td>
                            <td><?= $fac['remarks'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </td>
            </tr>
            <!-- PART D -->
            <tr>
                <td style="font-weight: bold; padding: 4px;">
                    PART D – TOOLS, TEST EQUIPMENT, AND SPECIAL TOOLS
                </td>
            </tr>
            <tr>
                <td style="padding: 0;">
                    <table class="inner-table">
                        <tr>
                            <th style="width:30%;">Descriptions</th>
                            <th style="width:25%;">Part Number</th>
                            <th style="width:20%;">Type</th>
                            <th style="width:25%;">Remarks</th>
                        </tr>
                        <?php foreach($tools as $t): ?>
                        <tr>
                            <td><?= $t['description'] ?></td>
                            <td><?= $t['part_number'] ?></td>
                            <td><?= $t['type'] ?></td>
                            <td><?= $t['remarks'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(count($tools) < 3): for($i=0; $i<(3-count($tools)); $i++): ?>
                        <tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
                        <?php endfor; endif; ?>
                    </table>
                </td>
            </tr>
            <!-- PART E -->
            <tr>
                <td style="font-weight: bold; padding: 4px;">
                    PART E – PERSONNEL COMPETENCY
                </td>
            </tr>
            <tr>
                <td style="padding: 0;">
                    <table class="inner-table">
                        <tr>
                            <th style="width:25%;">Name</th>
                            <th style="width:20%;">Position</th>
                            <th style="width:15%;">Year of Experience</th>
                            <th style="width:20%;">Rating</th>
                            <th style="width:20%;">AMEL No.</th>
                        </tr>
                        <?php foreach($personnel as $p): ?>
                        <tr>
                            <td><?= $p['name'] ?></td>
                            <td><?= $p['position'] ?></td>
                            <td><?= $p['year_of_experience'] ?></td>
                            <td><?= $p['rating'] ?></td>
                            <td><?= $p['amel_no'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(count($personnel) < 3): for($i=0; $i<(3-count($personnel)); $i++): ?>
                        <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>
                        <?php endfor; endif; ?>
                    </table>
                </td>
            </tr>
        </table>
        <div style="font-size: 10px; margin-top:5px;">
            Form No : QA/004A/26, Rev.0, June 2026
        </div>
    </div>

    <!-- MAINTENANCE PAGE 2 -->
    <div class="page">
        <table class="main-table">
            <tr>
                <td style="padding: 0;">
                    <table class="inner-table">
                        <tr>
                            <td style="width: 20%; text-align: center; padding: 8px;">
                                <img src="https://gis.globalmaintenance.co.id/public/uploads/logo/other/GMF%20Logo%202021.png" alt="GMF Logo" style="height:45px;">
                            </td>
                            <td style="width: 50%; text-align: center; font-weight: bold; font-size: 11px; padding: 5px;">
                                <div style="margin-bottom:4px;">PT Global Maintenance Facility</div>
                                <div style="margin-bottom:4px;">Approved Maintenance</div>
                                <div>Organization</div>
                            </td>
                            <td style="width: 30%; font-size: 9.5px; padding: 5px;">
                                <table style="width: 100%; border-collapse: collapse; border: none;">
                                    <tr><td style="border: none !important; padding: 1px 0; width: 40px; vertical-align: top;">Phone</td><td style="border: none !important; padding: 1px 0; vertical-align: top;">: +62 21 809 1993</td></tr>
                                    <tr><td style="border: none !important; padding: 1px 0; vertical-align: top;">Email</td><td style="border: none !important; padding: 1px 0; vertical-align: top;">: info@globalmaintenance.co.id</td></tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="text-align: center; font-weight: bold; padding: 5px;">
                    SELF EVALUATION FOR AIRCRAFT MAINTENANCE
                </td>
            </tr>
            <!-- PART F -->
            <tr>
                <td style="font-weight: bold; padding: 4px;">
                    PART F – CAPABILITY SELF EVALUATION APPROVAL
                </td>
            </tr>
            <tr>
                <td style="padding: 0;">
                    <table class="inner-table">
                        <tr>
                            <th style="width:70%; text-align:center;">Item</th>
                            <th style="width:30%; text-align:center;">Status</th>
                        </tr>
                        <?php foreach($approvals_data as $ad): ?>
                        <tr>
                            <td><?= $ad['item'] ?></td>
                            <td style="text-align:center;"><?= $ad['status'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </td>
            </tr>
            <!-- PART G -->
            <tr>
                <td style="font-weight: bold; padding: 4px;">
                    PART G – VERIFICATION
                </td>
            </tr>
            <tr>
                <td style="padding: 10px; text-align: center;">
                    Based on the capability self-evaluation conducted in accordance with the approved AMO procedures and applicable CASR Part 145 requirements, it has been determined that the organization possesses the necessary facilities, equipment, tooling, technical data, maintenance processes, and qualified personnel to perform the proposed scope of work.
                </td>
            </tr>
            <tr>
                <td style="padding: 0;">
                    <table class="inner-table" style="text-align: center;">
                        <tr>
                            <td style="width: 33.33%; vertical-align: top; padding: 5px;">
                                Prepared By<br>
                                <div style="height: 100px; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                                    <?php if(!empty($signatures['prepared_by_1'])): ?>
                                        <?php if($signatures['prepared_by_1']['signature_data']): ?>
                                            <img src="<?= $signatures['prepared_by_1']['signature_data'] ?>" class="signature-img" style="max-height:60px;">
                                        <?php elseif($signatures['prepared_by_1']['file_path']): ?>
                                            <img src="<?= base_url($signatures['prepared_by_1']['file_path']) ?>" class="signature-img" style="max-height:60px;">
                                        <?php endif; ?>
                                        <?php if($header['prepared_by_1_id'] == $logged_in_user_id): ?>
                                            <div class="no-print" style="margin-top: 5px;">
                                                <button type="button" class="btn-sign btn-sign-danger" onclick="deleteSignature('prepared_by_1', '<?= $header['prepared_by_1_id'] ?>')">Remove</button>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if($header['prepared_by_1_id'] == $logged_in_user_id): ?>
                                            <div class="no-print" style="margin-top: 5px;">
                                                <button type="button" class="btn-sign" onclick="openSignModal('prepared_by_1', '<?= $header['prepared_by_1_id'] ?>')">Sign</button>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <?= $prep1_user ? $prep1_user['first_name'].' '.$prep1_user['last_name'] : '' ?><br>
                                <?= $prep1_user ? $prep1_user['designation_name'] : '' ?><br>
                                <?= isset($signatures['prepared_by_1']) ? date('d/m/Y', strtotime($signatures['prepared_by_1']['updated_at'])) : ($prep1_user ? date('d/m/Y', strtotime($header['evaluation_date'])) : '') ?>
                            </td>
                            <td style="width: 33.33%; vertical-align: top; padding: 5px;">
                                Checked By<br>
                                <div style="height: 100px; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                                    <?php if(!empty($signatures['prepared_by_2'])): ?>
                                        <?php if($signatures['prepared_by_2']['signature_data']): ?>
                                            <img src="<?= $signatures['prepared_by_2']['signature_data'] ?>" class="signature-img" style="max-height:60px;">
                                        <?php elseif($signatures['prepared_by_2']['file_path']): ?>
                                            <img src="<?= base_url($signatures['prepared_by_2']['file_path']) ?>" class="signature-img" style="max-height:60px;">
                                        <?php endif; ?>
                                        <?php if($header['prepared_by_2_id'] == $logged_in_user_id): ?>
                                            <div class="no-print" style="margin-top: 5px;">
                                                <button type="button" class="btn-sign btn-sign-danger" onclick="deleteSignature('prepared_by_2', '<?= $header['prepared_by_2_id'] ?>')">Remove</button>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if($header['prepared_by_2_id'] == $logged_in_user_id): ?>
                                            <div class="no-print" style="margin-top: 5px;">
                                                <button type="button" class="btn-sign" onclick="openSignModal('prepared_by_2', '<?= $header['prepared_by_2_id'] ?>')">Sign</button>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <?= $prep2_user ? $prep2_user['first_name'].' '.$prep2_user['last_name'] : '' ?><br>
                                <?= $prep2_user ? $prep2_user['designation_name'] : '' ?><br>
                                <?= isset($signatures['prepared_by_2']) ? date('d/m/Y', strtotime($signatures['prepared_by_2']['updated_at'])) : ($prep2_user ? date('d/m/Y', strtotime($header['evaluation_date'])) : '') ?>
                            </td>
                            <td style="width: 33.33%; vertical-align: top; padding: 5px;">
                                Approved By<br>
                                <div style="height: 100px; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                                    <?php if(!empty($signatures['approved_by'])): ?>
                                        <?php if($signatures['approved_by']['signature_data']): ?>
                                            <img src="<?= $signatures['approved_by']['signature_data'] ?>" class="signature-img" style="max-height:60px;">
                                        <?php elseif($signatures['approved_by']['file_path']): ?>
                                            <img src="<?= base_url($signatures['approved_by']['file_path']) ?>" class="signature-img" style="max-height:60px;">
                                        <?php endif; ?>
                                        <?php if($header['approved_by_id'] == $logged_in_user_id): ?>
                                            <div class="no-print" style="margin-top: 5px;">
                                                <button type="button" class="btn-sign btn-sign-danger" onclick="deleteSignature('approved_by', '<?= $header['approved_by_id'] ?>')">Remove</button>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if($header['approved_by_id'] == $logged_in_user_id): ?>
                                            <div class="no-print" style="margin-top: 5px;">
                                                <button type="button" class="btn-sign" onclick="openSignModal('approved_by', '<?= $header['approved_by_id'] ?>')">Sign</button>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <?= $appv_user ? $appv_user['first_name'].' '.$appv_user['last_name'] : '' ?><br>
                                <?= $appv_user ? $appv_user['designation_name'] : '' ?><br>
                                <?= isset($signatures['approved_by']) ? date('d/m/Y', strtotime($signatures['approved_by']['updated_at'])) : ($appv_user ? date('d/m/Y', strtotime($header['evaluation_date'])) : '') ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <!-- DECISION -->
            <tr>
                <td style="padding: 10px;">
                    <div style="font-weight: bold; margin-bottom: 10px;">EVALUATION DECISION</div>
                    <div>
                        <span style="font-family: monospace; font-size:14px;"><?= $header['decision_status'] == 'approved' ? '☑' : '☐' ?></span> APPROVED – Recommended for inclusion in the Capability List
                    </div>
                    <div style="margin-top: 10px;">
                        <span style="font-family: monospace; font-size:14px;"><?= $header['decision_status'] == 'not_approved' ? '☑' : '☐' ?></span> NOT APPROVED – Not recommended for inclusion in the Capability List
                    </div>
                    
                    <div style="margin-top: 20px;">
                        Remarks (if any):
                        <div style="border-bottom: 1px solid #000; height: 20px; margin-top:5px; padding-left:5px;">
                            <?= $header['decision_remarks'] ?>
                        </div>
                        <div style="border-bottom: 1px solid #000; height: 20px; margin-top:10px;"></div>
                        <div style="border-bottom: 1px solid #000; height: 20px; margin-top:10px;"></div>
                    </div>
                </td>
            </tr>
        </table>
        <div style="font-size: 10px; margin-top:5px;">
            Form No : QA/004B/26, Rev.0, June 2026
        </div>
    </div>
    <?php endif; ?>

    <?php if($header['form_type'] == 'component'): ?>
    <!-- COMPONENT PAGE -->
    <!-- COMPONENT PAGE 1 -->
    <div class="page">
        <table class="main-table">
            <tr>
                <td style="padding: 0;">
                    <table class="inner-table">
                        <tr>
                            <td style="width: 20%; text-align: center; padding: 8px;">
                                <img src="https://gis.globalmaintenance.co.id/public/uploads/logo/other/GMF%20Logo%202021.png" alt="GMF Logo" style="height:45px;">
                            </td>
                            <td style="width: 50%; text-align: center; font-weight: bold; font-size: 11px; padding: 5px;">
                                <div style="margin-bottom:4px;">PT Global Maintenance Facility</div>
                                <div style="margin-bottom:4px;">Approved Maintenance</div>
                                <div>Organization</div>
                            </td>
                            <td style="width: 30%; font-size: 9.5px; padding: 5px;">
                                <table style="width: 100%; border-collapse: collapse; border: none;">
                                    <tr><td style="border: none !important; padding: 1px 0; width: 40px; vertical-align: top;">Phone</td><td style="border: none !important; padding: 1px 0; vertical-align: top;">: +62 21 809 1993</td></tr>
                                    <tr><td style="border: none !important; padding: 1px 0; vertical-align: top;">Email</td><td style="border: none !important; padding: 1px 0; vertical-align: top;">: info@globalmaintenance.co.id</td></tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="text-align: center; font-weight: bold; padding: 5px;">
                    SELF EVALUATION FOR AIRCRAFT COMPONENT
                </td>
            </tr>
            <!-- PART A -->
            <tr>
                <td style="font-weight: bold; padding: 4px;">
                    PART A – GENERAL INFORMATION
                </td>
            </tr>
            <tr>
                <td style="padding: 0;">
                    <table class="inner-table">
                        <tr>
                            <td style="width: 20%;">Capability No</td>
                            <td style="width: 30%;">: <?= $header['capability_no'] ?></td>
                            <td style="width: 25%;">Manufacturer</td>
                            <td style="width: 25%;">: <?= $header['manufacture'] ?></td>
                        </tr>
                        <tr>
                            <td>Date</td>
                            <td>: <?= $header['evaluation_date'] ?></td>
                            <td>ATA Chapter</td>
                            <td>: <?= $header['ata_chapter'] ?></td>
                        </tr>
                        <tr>
                            <td>Component Name</td>
                            <td>: <?= $header['type_of_aircraft'] ?></td>
                            <td>Rating</td>
                            <td>: <?= $header['rating'] ?></td>
                        </tr>
                        <tr>
                            <td>Part Number</td>
                            <td>: <?= $header['part_number'] ?></td>
                            <td rowspan="2">Proposed Scope of Work</td>
                            <td rowspan="2">: <?= nl2br($header['scope_of_work']) ?></td>
                        </tr>
                        <tr>
                            <td>Type</td>
                            <td>: <?= $header['component_type'] ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <!-- PART B -->
            <tr>
                <td style="font-weight: bold; padding: 4px;">
                    PART B – TECHNICAL DATA REVIEW
                </td>
            </tr>
            <tr>
                <td style="padding: 0;">
                    <table class="inner-table">
                        <tr>
                            <th style="width:40%;">Requirement</th>
                            <th style="width:30%; text-align:center;">Available</th>
                            <th style="width:30%;">Reference</th>
                        </tr>
                        <?php foreach($tech_data as $td): ?>
                        <tr>
                            <td><?= $td['requirement'] ?></td>
                            <td style="text-align:center;">
                                <span style="font-family: monospace;"><?= $td['available'] == 'Yes' ? '☑' : '☐' ?></span> Yes &nbsp;&nbsp;
                                <span style="font-family: monospace;"><?= $td['available'] == 'No' ? '☑' : '☐' ?></span> No &nbsp;&nbsp;
                                <span style="font-family: monospace;"><?= $td['available'] == 'N/A' ? '☑' : '☐' ?></span> N/A
                            </td>
                            <td><?= $td['reference'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </td>
            </tr>
            <!-- PART C -->
            <tr>
                <td style="font-weight: bold; padding: 4px;">
                    PART C – FACILITY ASSESSMENT
                </td>
            </tr>
            <tr>
                <td style="padding: 0;">
                    <table class="inner-table">
                        <tr>
                            <th style="width:40%;">Requirement</th>
                            <th style="width:30%; text-align:center;">Available</th>
                            <th style="width:30%;">Remarks</th>
                        </tr>
                        <?php foreach($facilities as $fac): ?>
                        <tr>
                            <td><?= $fac['requirement'] ?></td>
                            <td style="text-align:center;">
                                <span style="font-family: monospace;"><?= $fac['available'] == 'Yes' ? '☑' : '☐' ?></span> Yes &nbsp;&nbsp;
                                <span style="font-family: monospace;"><?= $fac['available'] == 'No' ? '☑' : '☐' ?></span> No
                            </td>
                            <td><?= $fac['remarks'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </td>
            </tr>
            <!-- PART D -->
            <tr>
                <td style="font-weight: bold; padding: 4px;">
                    PART D – TOOLS & TEST EQUIPMENT
                </td>
            </tr>
            <tr>
                <td style="padding: 0;">
                    <table class="inner-table">
                        <tr>
                            <th style="width:30%;">Descriptions</th>
                            <th style="width:25%;">Part Number</th>
                            <th style="width:20%;">Type</th>
                            <th style="width:25%;">Remarks</th>
                        </tr>
                        <?php foreach($tools as $t): ?>
                        <tr>
                            <td><?= $t['description'] ?></td>
                            <td><?= $t['part_number'] ?></td>
                            <td><?= $t['type'] ?></td>
                            <td><?= $t['remarks'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(count($tools) < 3): for($i=0; $i<(3-count($tools)); $i++): ?>
                        <tr><td>&nbsp;</td><td></td><td></td><td></td></tr>
                        <?php endfor; endif; ?>
                    </table>
                </td>
            </tr>
            <!-- PART E -->
            <tr>
                <td style="font-weight: bold; padding: 4px;">
                    PART E – PERSONNEL COMPETENCY
                </td>
            </tr>
            <tr>
                <td style="padding: 0;">
                    <table class="inner-table">
                        <tr>
                            <th style="width:25%;">Name</th>
                            <th style="width:20%;">Position</th>
                            <th style="width:15%;">EXPERIENCE</th>
                            <th style="width:20%;">TRAINING</th>
                            <th style="width:20%;">COMA No.</th>
                        </tr>
                        <?php foreach($personnel as $p): ?>
                        <tr>
                            <td><?= $p['name'] ?></td>
                            <td><?= $p['position'] ?></td>
                            <td><?= $p['year_of_experience'] ?></td>
                            <td><?= $p['rating'] ?></td>
                            <td><?= $p['amel_no'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(count($personnel) < 3): for($i=0; $i<(3-count($personnel)); $i++): ?>
                        <tr><td>&nbsp;</td><td></td><td></td><td></td><td></td></tr>
                        <?php endfor; endif; ?>
                    </table>
                </td>
            </tr>
        </table>
        <div style="font-size: 10px; margin-top:5px;">
            Form No : QA/003A/26, Rev.0, June 2026
        </div>
    </div>

    <!-- COMPONENT PAGE 2 -->
    <div class="page">
        <table class="main-table">
            <tr>
                <td style="padding: 0;">
                    <table class="inner-table">
                        <tr>
                            <td style="width: 20%; text-align: center; padding: 8px;">
                                <img src="https://gis.globalmaintenance.co.id/public/uploads/logo/other/GMF%20Logo%202021.png" alt="GMF Logo" style="height:45px;">
                            </td>
                            <td style="width: 50%; text-align: center; font-weight: bold; font-size: 11px; padding: 5px;">
                                <div style="margin-bottom:4px;">PT Global Maintenance Facility</div>
                                <div style="margin-bottom:4px;">Approved Maintenance</div>
                                <div>Organization</div>
                            </td>
                            <td style="width: 30%; font-size: 9.5px; padding: 5px;">
                                <table style="width: 100%; border-collapse: collapse; border: none;">
                                    <tr><td style="border: none !important; padding: 1px 0; width: 40px; vertical-align: top;">Phone</td><td style="border: none !important; padding: 1px 0; vertical-align: top;">: +62 21 809 1993</td></tr>
                                    <tr><td style="border: none !important; padding: 1px 0; vertical-align: top;">Email</td><td style="border: none !important; padding: 1px 0; vertical-align: top;">: info@globalmaintenance.co.id</td></tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="text-align: center; font-weight: bold; padding: 5px;">
                    SELF EVALUATION FOR AIRCRAFT COMPONENT
                </td>
            </tr>
            <!-- PART F -->
            <tr>
                <td style="font-weight: bold; padding: 4px;">
                    PART F – CAPABILITY SELF EVALUATION APPROVAL
                </td>
            </tr>
            <tr>
                <td style="padding: 0;">
                    <table class="inner-table">
                        <tr>
                            <th style="width:70%; text-align:center;">Item</th>
                            <th style="width:30%; text-align:center;">Status</th>
                        </tr>
                        <?php foreach($approvals_data as $ad): ?>
                        <tr>
                            <td><?= $ad['item'] ?></td>
                            <td style="text-align:center;"><?= $ad['status'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </td>
            </tr>
            <!-- PART G -->
            <tr>
                <td style="font-weight: bold; padding: 4px;">
                    PART G – VERIFICATION
                </td>
            </tr>
            <tr>
                <td style="padding: 10px; text-align: center;">
                    Based on the capability self-evaluation conducted in accordance with the approved AMO procedures and applicable CASR Part 145 requirements, it has been determined that the organization possesses the necessary facilities, equipment, tooling, technical data, maintenance processes, and qualified personnel to perform the proposed scope of work.
                </td>
            </tr>
            <tr>
                <td style="padding: 0;">
                    <table class="inner-table" style="text-align: center;">
                        <tr>
                            <td style="width: 33.33%; vertical-align: top; padding: 5px;">
                                Prepared By<br>
                                <div style="height: 100px; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                                    <?php if(!empty($signatures['prepared_by_1'])): ?>
                                        <?php if($signatures['prepared_by_1']['signature_data']): ?>
                                            <img src="<?= $signatures['prepared_by_1']['signature_data'] ?>" class="signature-img" style="max-height:60px;">
                                        <?php elseif($signatures['prepared_by_1']['file_path']): ?>
                                            <img src="<?= base_url($signatures['prepared_by_1']['file_path']) ?>" class="signature-img" style="max-height:60px;">
                                        <?php endif; ?>
                                        <?php if($header['prepared_by_1_id'] == $logged_in_user_id): ?>
                                            <div class="no-print" style="margin-top: 5px;">
                                                <button type="button" class="btn-sign btn-sign-danger" onclick="deleteSignature('prepared_by_1', '<?= $header['prepared_by_1_id'] ?>')">Remove</button>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if($header['prepared_by_1_id'] == $logged_in_user_id): ?>
                                            <div class="no-print" style="margin-top: 5px;">
                                                <button type="button" class="btn-sign" onclick="openSignModal('prepared_by_1', '<?= $header['prepared_by_1_id'] ?>')">Sign</button>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <?= $prep1_user ? $prep1_user['first_name'].' '.$prep1_user['last_name'] : '' ?><br>
                                <?= $prep1_user ? $prep1_user['designation_name'] : '' ?><br>
                                <?= isset($signatures['prepared_by_1']) ? date('d/m/Y', strtotime($signatures['prepared_by_1']['updated_at'])) : ($prep1_user ? date('d/m/Y', strtotime($header['evaluation_date'])) : '') ?>
                            </td>
                            <td style="width: 33.33%; vertical-align: top; padding: 5px;">
                                Checked By<br>
                                <div style="height: 100px; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                                    <?php if(!empty($signatures['prepared_by_2'])): ?>
                                        <?php if($signatures['prepared_by_2']['signature_data']): ?>
                                            <img src="<?= $signatures['prepared_by_2']['signature_data'] ?>" class="signature-img" style="max-height:60px;">
                                        <?php elseif($signatures['prepared_by_2']['file_path']): ?>
                                            <img src="<?= base_url($signatures['prepared_by_2']['file_path']) ?>" class="signature-img" style="max-height:60px;">
                                        <?php endif; ?>
                                        <?php if($header['prepared_by_2_id'] == $logged_in_user_id): ?>
                                            <div class="no-print" style="margin-top: 5px;">
                                                <button type="button" class="btn-sign btn-sign-danger" onclick="deleteSignature('prepared_by_2', '<?= $header['prepared_by_2_id'] ?>')">Remove</button>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if($header['prepared_by_2_id'] == $logged_in_user_id): ?>
                                            <div class="no-print" style="margin-top: 5px;">
                                                <button type="button" class="btn-sign" onclick="openSignModal('prepared_by_2', '<?= $header['prepared_by_2_id'] ?>')">Sign</button>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <?= $prep2_user ? $prep2_user['first_name'].' '.$prep2_user['last_name'] : '' ?><br>
                                <?= $prep2_user ? $prep2_user['designation_name'] : '' ?><br>
                                <?= isset($signatures['prepared_by_2']) ? date('d/m/Y', strtotime($signatures['prepared_by_2']['updated_at'])) : ($prep2_user ? date('d/m/Y', strtotime($header['evaluation_date'])) : '') ?>
                            </td>
                            <td style="width: 33.33%; vertical-align: top; padding: 5px;">
                                Approved By<br>
                                <div style="height: 100px; display: flex; align-items: center; justify-content: center; flex-direction: column;">
                                    <?php if(!empty($signatures['approved_by'])): ?>
                                        <?php if($signatures['approved_by']['signature_data']): ?>
                                            <img src="<?= $signatures['approved_by']['signature_data'] ?>" class="signature-img" style="max-height:60px;">
                                        <?php elseif($signatures['approved_by']['file_path']): ?>
                                            <img src="<?= base_url($signatures['approved_by']['file_path']) ?>" class="signature-img" style="max-height:60px;">
                                        <?php endif; ?>
                                        <?php if($header['approved_by_id'] == $logged_in_user_id): ?>
                                            <div class="no-print" style="margin-top: 5px;">
                                                <button type="button" class="btn-sign btn-sign-danger" onclick="deleteSignature('approved_by', '<?= $header['approved_by_id'] ?>')">Remove</button>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if($header['approved_by_id'] == $logged_in_user_id): ?>
                                            <div class="no-print" style="margin-top: 5px;">
                                                <button type="button" class="btn-sign" onclick="openSignModal('approved_by', '<?= $header['approved_by_id'] ?>')">Sign</button>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <?= $appv_user ? $appv_user['first_name'].' '.$appv_user['last_name'] : '' ?><br>
                                <?= $appv_user ? $appv_user['designation_name'] : '' ?><br>
                                <?= isset($signatures['approved_by']) ? date('d/m/Y', strtotime($signatures['approved_by']['updated_at'])) : ($appv_user ? date('d/m/Y', strtotime($header['evaluation_date'])) : '') ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <!-- DECISION -->
            <tr>
                <td style="padding: 10px;">
                    <div style="font-weight: bold; margin-bottom: 10px;">EVALUATION DECISION</div>
                    <div>
                        <span style="font-family: monospace; font-size:14px;"><?= $header['decision_status'] == 'approved' ? '☑' : '☐' ?></span> APPROVED – Recommended for inclusion in the Capability List
                    </div>
                    <div style="margin-top: 10px;">
                        <span style="font-family: monospace; font-size:14px;"><?= $header['decision_status'] == 'not_approved' ? '☑' : '☐' ?></span> NOT APPROVED – Not recommended for inclusion in the Capability List
                    </div>
                    
                    <div style="margin-top: 20px;">
                        Remarks (if any):
                        <div style="border-bottom: 1px solid #000; height: 20px; margin-top:5px; padding-left:5px;">
                            <?= $header['decision_remarks'] ?>
                        </div>
                        <div style="border-bottom: 1px solid #000; height: 20px; margin-top:10px;"></div>
                        <div style="border-bottom: 1px solid #000; height: 20px; margin-top:10px;"></div>
                    </div>
                </td>
            </tr>
        </table>
        <div style="font-size: 10px; margin-top:5px;">
            Form No : QA/003B/26, Rev.0, June 2026
        </div>
    </div>
    <?php endif; ?>

    <!-- Reusable Sign Modal -->
    <div id="signModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
        <div style="background:#fff; border-radius:8px; padding:20px; width:460px; max-width:95%; box-shadow:0 4px 20px rgba(0,0,0,0.3);">
            <h3 style="margin-top:0; font-size:16px;" id="signModalTitle">Sign</h3>
            <input type="hidden" id="modal_signature_type" value="">
            <input type="hidden" id="modal_user_id" value="">
            <input type="hidden" id="modal_eval_id" value="<?= $header['id'] ?>">

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
            var normalizedType = (type || '').toString().trim().toLowerCase();
            document.getElementById('modal_signature_type').value = normalizedType;
            document.getElementById('modal_user_id').value = userId;
            var label = normalizedType.replace(/_/g, ' ').replace(/\b\w/g, function(l){ return l.toUpperCase(); });
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
            var type = (document.getElementById('modal_signature_type').value || '').trim().toLowerCase();
            var userId = document.getElementById('modal_user_id').value;
            var evalId = document.getElementById('modal_eval_id').value;
            if(!['prepared_by_1','prepared_by_2','approved_by'].includes(type)) {
                alert('Invalid signature type.');
                return;
            }
            var formData = new FormData();
            formData.append(csrfName, csrfHash);
            formData.append('user_id', userId);
            formData.append('eval_id', evalId);
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
            fetch('<?= site_url("erp/capability-evaluation/sign-record"); ?>', {
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
            var evalId = document.getElementById('modal_eval_id').value;
            var formData = new FormData();
            formData.append(csrfName, csrfHash);
            formData.append('user_id', userId);
            formData.append('eval_id', evalId);
            formData.append('signature_type', type);
            formData.append('delete_signature', '1');

            fetch('<?= site_url("erp/capability-evaluation/sign-record"); ?>', {
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
