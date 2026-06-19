<?php
namespace App\Models;

use CodeIgniter\Model;

class CapabilityEvalFacilitiesModel extends Model {

    protected $table = 'ci_capability_eval_facilities';

    protected $primaryKey = 'id';

    protected $allowedFields = [
        'eval_id', 'requirement', 'available', 'remarks'
    ];

    protected $useTimestamps = false;
}
