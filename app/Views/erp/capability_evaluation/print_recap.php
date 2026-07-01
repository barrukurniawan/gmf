<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Recap Capability Evaluation</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            font-size: 8px; /* Shrink font to fit in portrait */
            margin: 0; 
            padding: 0; 
            background: #525659; 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
        }
        .page { 
            width: 210mm; /* Portrait A4 (Vertical) */
            min-height: 297mm; 
            background: white; 
            margin: 20px auto; 
            padding: 8mm; 
            box-sizing: border-box; 
            box-shadow: 0 0 10px rgba(0,0,0,0.2); 
            page-break-after: always;
            display: flex;
            flex-direction: column;
        }
        .content-wrapper {
            border: 2px solid #000;
            padding: 10px;
            flex: 1; /* Take up all available space in the page */
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
        }
        
        .main-table { 
            width: 100%; 
            border-collapse: collapse; 
            border: 1.5px solid #000; 
        }
        .main-table > tbody > tr > td { 
            border: 1.5px solid #000; 
            padding: 3px; 
        }
        
        .inner-table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        .inner-table th, .inner-table td { 
            border: 1px solid #000; 
            padding: 3px; 
        }
        
        /* Data table styles */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
            vertical-align: top;
            font-size: 8px; /* Shrink data font for portrait */
        }
        .data-table th {
            text-align: center;
            font-weight: bold;
            background-color: #f9f9f9;
            text-transform: uppercase;
        }
        .data-table .center {
            text-align: center;
        }

        .signature-section {
            margin-top: auto; 
            margin-bottom: 20px; /* Push slightly up from the bottom border */
            width: 100%;
            padding-top: 20px;
        }
        .signature-table {
            width: 100%;
            text-align: center; /* Center the text */
            border-collapse: collapse;
            font-size: 9px; 
        }
        .signature-table td {
            width: 33.33%;
            vertical-align: bottom; 
            padding: 5px 5px;
        }
        
        @media print {
            .no-print { display: none; }
            body { background: none; display: block; }
            .page { 
                margin: 0; 
                padding: 8mm; 
                box-shadow: none; 
                width: 100%; 
                height: 100vh;
                page-break-after: avoid; 
            }
            .content-wrapper {
                height: 100%; /* Ensure border spans the whole printable height inside padding */
            }
            @page { size: portrait; margin: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align:center; padding: 10px; background:#f0f0f0; margin-bottom: 20px; width: 100%;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">Print PDF</button>
    </div>

    <div class="page">
        <div class="content-wrapper">
            <table class="main-table" style="margin-bottom: 10px;">
                <tr>
                    <td style="padding: 0;">
                        <table class="inner-table" style="border: none;">
                            <tr>
                                <td style="width: 20%; text-align: center; padding: 4px; border-top:none; border-bottom:none; border-left:none;">
                                    <img src="https://gis.globalmaintenance.co.id/public/uploads/logo/other/GMF%20Logo%202021.png" alt="GMF Logo" style="height:35px;">
                                </td>
                                <td style="width: 50%; text-align: center; font-weight: bold; font-size: 11px; padding: 4px; border-top:none; border-bottom:none;">
                                    <div style="margin-bottom:2px;">PT Global Maintenance Facility</div>
                                    <div style="margin-bottom:2px;">Approved Maintenance Organization</div>
                                    <div>DGCA No: 145D-376</div>
                                </td>
                                <td style="width: 30%; font-size: 8px; padding: 4px; border-top:none; border-bottom:none; border-right:none;">
                                    <table style="width: 100%; border-collapse: collapse; border: none;">
                                        <tr><td style="border: none !important; padding: 1px 0; width: 30px; vertical-align: top;">Phone</td><td style="border: none !important; padding: 1px 0; vertical-align: top;">: +62 21 809 2019</td></tr>
                                        <tr><td style="border: none !important; padding: 1px 0; vertical-align: top;">Email</td><td style="border: none !important; padding: 1px 0; vertical-align: top;">: info@globalmaintenance.co.id</td></tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            
            <div style="text-align: center; font-weight: bold; font-size: 12px; margin: 10px 0;">
                RECAPITULATE AIRCRAFT <?= strtoupper($type) ?> CAPABILITY EVALUATION
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 3%;">NO</th>
                        <th style="width: 10%;">CAPABILITY NO.</th>
                        <th style="width: 12%;">COMPONENT NAME</th>
                        <th style="width: 10%;">PART NUMBER</th>
                        <th style="width: 8%;">TYPE</th>
                        <th style="width: 10%;">MANUFACTURE</th>
                        <th style="width: 5%;">ATA</th>
                        <th style="width: 32%;">SCOPE OF WORK</th>
                        <th style="width: 10%;">STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    foreach($records as $r): 
                    ?>
                    <tr>
                        <td class="center"><?= $no++ ?></td>
                        <td><?= $r['capability_no'] ?></td>
                        <td><?= strtoupper($r['type_of_aircraft']) ?></td>
                        <td><?= strtoupper($r['part_number']) ?></td>
                        <td><?= strtoupper($r['tool_type']) ?></td>
                        <td><?= strtoupper($r['manufacture']) ?></td>
                        <td class="center"><?= $r['ata_chapter'] ?: '-' ?></td>
                        <td><?= strtoupper(str_replace(["\r\n", "\r", "\n"], ", ", $r['scope_of_work'] ?: '-')) ?></td>
                        <td class="center"><?= $r['decision_status'] ? strtoupper($r['decision_status']) : '-' ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($records)): ?>
                    <tr>
                        <td colspan="9" class="center">No Data Available</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="signature-section">
                <table class="signature-table">
                    <tr>
                        <td>
                            <div>Prepared By</div>
                            <div style="height: 50px;"></div>
                            <div><?= $prep['name'] ?></div>
                            <div><?= $prep['designation'] ?></div>
                            <div><?= date('d/m/Y') ?></div>
                        </td>
                        <td>
                            <div>Checked By</div>
                            <div style="height: 50px;"></div>
                            <div><?= $check['name'] ?></div>
                            <div><?= $check['designation'] ?></div>
                            <div><?= date('d/m/Y') ?></div>
                        </td>
                        <td>
                            <div>Approved By</div>
                            <div style="height: 50px;"></div>
                            <div><?= $appv['name'] ?></div>
                            <div><?= $appv['designation'] ?></div>
                            <div><?= date('d/m/Y') ?></div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
