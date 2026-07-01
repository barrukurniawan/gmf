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
                                <div style="height: 100px; display: flex; align-items: center; justify-content: center;">
                                    <?php if(!empty($signatures['prepared_by_1'])): ?>
                                        <?php if($signatures['prepared_by_1']['signature_data']): ?>
                                            <img src="<?= $signatures['prepared_by_1']['signature_data'] ?>" class="signature-img">
                                        <?php elseif($signatures['prepared_by_1']['file_path']): ?>
                                            <img src="<?= base_url($signatures['prepared_by_1']['file_path']) ?>" class="signature-img">
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                Name: <?= $prep1_user ? $prep1_user['first_name'].' '.$prep1_user['last_name'] : '' ?><br>
                                Function: <br>
                                Date: <?= !empty($signatures['prepared_by_1']) ? date('d-M-Y', strtotime($signatures['prepared_by_1']['updated_at'])) : '' ?>
                            </td>
                            <td style="width: 33.33%; vertical-align: top; padding: 5px;">
                                Prepared By<br>
                                <div style="height: 100px; display: flex; align-items: center; justify-content: center;">
                                    <?php if(!empty($signatures['prepared_by_2'])): ?>
                                        <?php if($signatures['prepared_by_2']['signature_data']): ?>
                                            <img src="<?= $signatures['prepared_by_2']['signature_data'] ?>" class="signature-img">
                                        <?php elseif($signatures['prepared_by_2']['file_path']): ?>
                                            <img src="<?= base_url($signatures['prepared_by_2']['file_path']) ?>" class="signature-img">
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                Name: <?= $prep2_user ? $prep2_user['first_name'].' '.$prep2_user['last_name'] : '' ?><br>
                                Function: <br>
                                Date: <?= !empty($signatures['prepared_by_2']) ? date('d-M-Y', strtotime($signatures['prepared_by_2']['updated_at'])) : '' ?>
                            </td>
                            <td style="width: 33.33%; vertical-align: top; padding: 5px;">
                                Approved By<br>
                                <div style="height: 100px; display: flex; align-items: center; justify-content: center;">
                                    <?php if(!empty($signatures['approved_by'])): ?>
                                        <?php if($signatures['approved_by']['signature_data']): ?>
                                            <img src="<?= $signatures['approved_by']['signature_data'] ?>" class="signature-img">
                                        <?php elseif($signatures['approved_by']['file_path']): ?>
                                            <img src="<?= base_url($signatures['approved_by']['file_path']) ?>" class="signature-img">
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                Name: <?= $appv_user ? $appv_user['first_name'].' '.$appv_user['last_name'] : '' ?><br>
                                Function: <br>
                                Date: <?= !empty($signatures['approved_by']) ? date('d-M-Y', strtotime($signatures['approved_by']['updated_at'])) : '' ?>
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
                            <td>: 
                                <?php 
                                echo (isset($tools[0]['part_number'])) ? $tools[0]['part_number'] : ''; 
                                ?>
                            </td>
                            <td rowspan="2">Proposed Scope of Work</td>
                            <td rowspan="2">: <?= nl2br($header['scope_of_work']) ?></td>
                        </tr>
                        <tr>
                            <td>Type</td>
                            <td>: 
                                <?php 
                                echo (isset($tools[0]['type'])) ? $tools[0]['type'] : ''; 
                                ?>
                            </td>
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
                            <th style="width:15%;">Experience</th>
                            <th style="width:20%;">Training</th>
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
                                <div style="height: 100px; display: flex; align-items: center; justify-content: center;">
                                    <?php if(!empty($signatures['prepared_by_1'])): ?>
                                        <?php if($signatures['prepared_by_1']['signature_data']): ?>
                                            <img src="<?= $signatures['prepared_by_1']['signature_data'] ?>" class="signature-img">
                                        <?php elseif($signatures['prepared_by_1']['file_path']): ?>
                                            <img src="<?= base_url($signatures['prepared_by_1']['file_path']) ?>" class="signature-img">
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                Name: <?= $prep1_user ? $prep1_user['first_name'].' '.$prep1_user['last_name'] : '' ?><br>
                                Function: <br>
                                Date: <?= !empty($signatures['prepared_by_1']) ? date('d-M-Y', strtotime($signatures['prepared_by_1']['updated_at'])) : '' ?>
                            </td>
                            <td style="width: 33.33%; vertical-align: top; padding: 5px;">
                                Prepared By<br>
                                <div style="height: 100px; display: flex; align-items: center; justify-content: center;">
                                    <?php if(!empty($signatures['prepared_by_2'])): ?>
                                        <?php if($signatures['prepared_by_2']['signature_data']): ?>
                                            <img src="<?= $signatures['prepared_by_2']['signature_data'] ?>" class="signature-img">
                                        <?php elseif($signatures['prepared_by_2']['file_path']): ?>
                                            <img src="<?= base_url($signatures['prepared_by_2']['file_path']) ?>" class="signature-img">
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                Name: <?= $prep2_user ? $prep2_user['first_name'].' '.$prep2_user['last_name'] : '' ?><br>
                                Function: <br>
                                Date: <?= !empty($signatures['prepared_by_2']) ? date('d-M-Y', strtotime($signatures['prepared_by_2']['updated_at'])) : '' ?>
                            </td>
                            <td style="width: 33.33%; vertical-align: top; padding: 5px;">
                                Approved By<br>
                                <div style="height: 100px; display: flex; align-items: center; justify-content: center;">
                                    <?php if(!empty($signatures['approved_by'])): ?>
                                        <?php if($signatures['approved_by']['signature_data']): ?>
                                            <img src="<?= $signatures['approved_by']['signature_data'] ?>" class="signature-img">
                                        <?php elseif($signatures['approved_by']['file_path']): ?>
                                            <img src="<?= base_url($signatures['approved_by']['file_path']) ?>" class="signature-img">
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                Name: <?= $appv_user ? $appv_user['first_name'].' '.$appv_user['last_name'] : '' ?><br>
                                Function: <br>
                                Date: <?= !empty($signatures['approved_by']) ? date('d-M-Y', strtotime($signatures['approved_by']['updated_at'])) : '' ?>
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
</body>
</html>
