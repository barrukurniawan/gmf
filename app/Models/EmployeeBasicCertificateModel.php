<?php
namespace App\Models;

use CodeIgniter\Model;

class EmployeeBasicCertificateModel extends Model {

    protected $table = 'employee_basic_certificates';

    protected $primaryKey = 'certificate_id';

    protected $allowedFields = ['certificate_id','user_id','a1','a2','a3','a4','c1','c2','c4','created_at','updated_at'];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

}
