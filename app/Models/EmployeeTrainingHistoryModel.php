<?php
namespace App\Models;

use CodeIgniter\Model;

class EmployeeTrainingHistoryModel extends Model {

    protected $table = 'employee_training_history';

    protected $primaryKey = 'training_history_id';

    protected $allowedFields = ['training_history_id','user_id','course_title','course_objective','date_completed','test_result','total_hours','institution','location','instructor_name','created_at','updated_at'];

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

}
