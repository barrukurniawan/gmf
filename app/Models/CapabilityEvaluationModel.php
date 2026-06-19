<?php
namespace App\Models;

use CodeIgniter\Model;

class CapabilityEvaluationModel extends Model {

    protected $table = 'ci_capability_evaluations';

    protected $primaryKey = 'id';

    protected $allowedFields = [
        'capability_no', 'evaluation_date', 'type_of_aircraft', 'manufacture', 
        'ata_chapter', 'rating', 'scope_of_work', 'form_type', 
        'decision_status', 'decision_remarks', 
        'prepared_by_1_id', 'prepared_by_2_id', 'approved_by_id',
        'created_by', 'created_at', 'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

}
