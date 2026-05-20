<?php
namespace App\Models;

use CodeIgniter\Model;

class TrainingSignatureModel extends Model {

    protected $table = 'employee_training_signatures';

    protected $primaryKey = 'signature_id';

    protected $allowedFields = ['signature_id','user_id','signature_type','signature_data','file_path','created_at','updated_at'];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

}
