<?php
namespace App\Models;

use CodeIgniter\Model;

class EmployeeSignatureModel extends Model {

    protected $table = 'employee_signatures';

    protected $primaryKey = 'signature_id';

    protected $allowedFields = ['signature_id','user_id','signature_data','file_path','created_at','updated_at'];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

}