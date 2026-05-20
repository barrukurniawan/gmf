<?php
namespace App\Models;

use CodeIgniter\Model;

class EmployeeEducationModel extends Model {

    protected $table = 'employee_education';

    protected $primaryKey = 'education_id';

    protected $allowedFields = ['education_id','user_id','degree','institution','major','graduate_year','created_at','updated_at'];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

}
