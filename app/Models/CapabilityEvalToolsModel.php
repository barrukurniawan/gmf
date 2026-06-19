<?php
namespace App\Models;

use CodeIgniter\Model;

class CapabilityEvalToolsModel extends Model {

    protected $table = 'ci_capability_eval_tools';

    protected $primaryKey = 'id';

    protected $allowedFields = [
        'eval_id', 'description', 'part_number', 'type', 'remarks'
    ];

    protected $useTimestamps = false;
}
