<?php
namespace App\Models;

use CodeIgniter\Model;

class CapabilityEvalPersonnelModel extends Model {

    protected $table = 'ci_capability_eval_personnel';

    protected $primaryKey = 'id';

    protected $allowedFields = [
        'eval_id', 'name', 'position', 'year_of_experience', 'rating', 'amel_no'
    ];

    protected $useTimestamps = false;
}
