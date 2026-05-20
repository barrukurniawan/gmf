<?php
namespace App\Models;

use CodeIgniter\Model;

class TrainingRecordHeaderModel extends Model {

    protected $table = 'employee_training_record_header';

    protected $primaryKey = 'training_record_id';

    protected $allowedFields = ['training_record_id','user_id','place_of_birth','address','prepared_by_id','approved_by_id','created_at','updated_at'];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

}
