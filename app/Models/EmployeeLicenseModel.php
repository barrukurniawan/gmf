<?php
namespace App\Models;

use CodeIgniter\Model;

class EmployeeLicenseModel extends Model {

    protected $table = 'employee_licenses';

    protected $primaryKey = 'license_id';

    protected $allowedFields = ['license_id','user_id','license_type','license_no','expired_date','rating','created_at','updated_at'];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

}
