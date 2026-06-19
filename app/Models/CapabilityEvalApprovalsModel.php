<?php
namespace App\Models;

use CodeIgniter\Model;

class CapabilityEvalApprovalsModel extends Model {

    protected $table = 'ci_capability_eval_approvals';

    protected $primaryKey = 'id';

    protected $allowedFields = [
        'eval_id', 'item', 'status'
    ];

    protected $useTimestamps = false;
}
