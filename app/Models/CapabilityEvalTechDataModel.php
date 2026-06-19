<?php
namespace App\Models;

use CodeIgniter\Model;

class CapabilityEvalTechDataModel extends Model {

    protected $table = 'ci_capability_eval_tech_data';

    protected $primaryKey = 'id';

    protected $allowedFields = [
        'eval_id', 'requirement', 'available', 'reference'
    ];

    protected $useTimestamps = false;
}
