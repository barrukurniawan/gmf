<?php
namespace App\Models;

use CodeIgniter\Model;

class CapabilitySignatureModel extends Model {

    protected $table = 'ci_capability_signatures';

    protected $primaryKey = 'signature_id';

    protected $allowedFields = [
        'eval_id', 'user_id', 'signature_type', 'signature_data', 'file_path',
        'created_at', 'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

}
