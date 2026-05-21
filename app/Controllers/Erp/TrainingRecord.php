<?php
namespace App\Controllers\Erp;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\SystemModel;
use App\Models\UsersModel;
use App\Models\StaffdetailsModel;
use App\Models\DepartmentModel;
use App\Models\DesignationModel;
use App\Models\TrainingRecordHeaderModel;
use App\Models\EmployeeEducationModel;
use App\Models\EmployeeTrainingHistoryModel;
use App\Models\EmployeeLicenseModel;
use App\Models\EmployeeBasicCertificateModel;
use App\Models\TrainingSignatureModel;
use App\Models\EmployeeSignatureModel;

class TrainingRecord extends BaseController {

	public function index()
	{
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$StaffdetailsModel = new StaffdetailsModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		if(!$session->has('sup_username')){
			$session->setFlashdata('err_not_logged_in', lang('Dashboard.err_not_logged_in'));
			return redirect()->to(site_url('erp/login'));
		}
		if($user_info['user_type'] != 'company' && $user_info['user_type']!='staff'){
			$session->setFlashdata('unauthorized_module',lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}

		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = 'Training Record | '.$xin_system['application_name'];
		$data['path_url'] = 'training_record';
		$data['breadcrumbs'] = 'Training Record';

		// Get all employees
		if($user_info['user_type'] == 'company'){
			$company_id = $usession['sup_user_id'];
		} else {
			$company_id = $user_info['company_id'];
		}

		$employees = $UsersModel->where('company_id', $company_id)
			->where('user_type', 'staff')
			->orderBy('first_name', 'ASC')
			->findAll();

		$TrainingRecordHeaderModel = new TrainingRecordHeaderModel();
		$TrainingSignatureModel = new TrainingSignatureModel();
		$StaffdetailsModel = new StaffdetailsModel();
		$DesignationModel = new DesignationModel();
		$employee_list = [];
		foreach($employees as $emp){
			$header = $TrainingRecordHeaderModel->where('user_id', $emp['user_id'])->first();
			$emp['has_record'] = ($header) ? true : false;
			$emp['record_id'] = ($header) ? $header['training_record_id'] : 0;
			$staff_detail = $StaffdetailsModel->where('user_id', $emp['user_id'])->first();
			$emp['employee_id'] = $staff_detail['employee_id'] ?? '-';
			$designation = $DesignationModel->where('designation_id', $staff_detail['designation_id'] ?? 0)->first();
			$emp['designation_name'] = $designation['designation_name'] ?? '-';
			$employee_list[] = $emp;
		}
		$data['employees'] = $employee_list;

		$data['subview'] = view('erp/training_record/list', $data);
		return view('erp/layout/layout_main', $data);
	}

	public function form()
	{
		$request = \Config\Services::request();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$user_id_param = udecode($request->uri->getSegment(3));

		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$StaffdetailsModel = new StaffdetailsModel();
		$DepartmentModel = new DepartmentModel();
		$DesignationModel = new DesignationModel();
		$TrainingRecordHeaderModel = new TrainingRecordHeaderModel();
		$EmployeeEducationModel = new EmployeeEducationModel();
		$EmployeeTrainingHistoryModel = new EmployeeTrainingHistoryModel();
		$EmployeeLicenseModel = new EmployeeLicenseModel();
		$EmployeeBasicCertificateModel = new EmployeeBasicCertificateModel();

		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if(!$session->has('sup_username')){
			return redirect()->to(site_url('erp/login'));
		}

		$employee = $UsersModel->where('user_id', $user_id_param)->first();
		if(!$employee){
			$session->setFlashdata('error_record', 'Employee not found.');
			return redirect()->to(site_url('erp/training-record'));
		}

		$staff_detail = $StaffdetailsModel->where('user_id', $user_id_param)->first();
		$department = $DepartmentModel->where('department_id', $staff_detail['department_id'] ?? 0)->first();
		$designation = $DesignationModel->where('designation_id', $staff_detail['designation_id'] ?? 0)->first();

		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = 'Training Record - '.$employee['first_name'].' '.$employee['last_name'].' | '.$xin_system['application_name'];
		$data['path_url'] = 'training_record';
		$data['breadcrumbs'] = 'Training Record';

		$data['employee'] = $employee;
		$data['staff_detail'] = $staff_detail;
		$data['department'] = $department;
		$data['designation'] = $designation;

		$data['header'] = $TrainingRecordHeaderModel->where('user_id', $user_id_param)->first();
		$data['education'] = $EmployeeEducationModel->where('user_id', $user_id_param)->orderBy('education_id', 'ASC')->findAll();
		$data['training_history'] = $EmployeeTrainingHistoryModel->where('user_id', $user_id_param)->orderBy('training_history_id', 'ASC')->findAll();
		$data['licenses'] = $EmployeeLicenseModel->where('user_id', $user_id_param)->orderBy('license_id', 'ASC')->findAll();
		$data['certificates'] = $EmployeeBasicCertificateModel->where('user_id', $user_id_param)->first();
		$TrainingSignatureModel = new TrainingSignatureModel();
		$data['signatures'] = $TrainingSignatureModel->where('user_id', $user_id_param)->findAll();

		// Load staff list for Prepared By / Approved By dropdowns
		if($user_info['user_type'] == 'company'){
			$company_id = $usession['sup_user_id'];
		} else {
			$company_id = $user_info['company_id'];
		}
		$data['all_staff'] = $UsersModel->where('company_id', $company_id)
			->where('user_type', 'staff')
			->orderBy('first_name', 'ASC')
			->findAll();

		$data['subview'] = view('erp/training_record/form', $data);
		return view('erp/layout/layout_main', $data);
	}

	public function save()
	{
		if($this->request->getPost('type')=='save_record') {
			$Return = array('result'=>'', 'error'=>'', 'csrf_hash'=>'');
			$session = \Config\Services::session();
			$request = \Config\Services::request();
			$usession = $session->get('sup_username');
			$Return['csrf_hash'] = csrf_hash();

			$user_id = $this->request->getPost('user_id');
			$UsersModel = new UsersModel();
			$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

			$TrainingRecordHeaderModel = new TrainingRecordHeaderModel();
			$EmployeeEducationModel = new EmployeeEducationModel();
			$EmployeeTrainingHistoryModel = new EmployeeTrainingHistoryModel();
			$EmployeeLicenseModel = new EmployeeLicenseModel();
			$EmployeeBasicCertificateModel = new EmployeeBasicCertificateModel();

			// Validate user access
			if($user_info['user_type'] != 'company' && $user_info['user_type'] != 'staff'){
				$Return['error'] = lang('Membership.xin_error_msg');
				$this->output($Return);
				return;
			}

			$dt = date('Y-m-d H:i:s');

			// 1. Save header
			$header_data = [
				'user_id' => $user_id,
				'place_of_birth' => $this->request->getPost('place_of_birth'),
				'address' => $this->request->getPost('address'),
				'prepared_by_id' => $this->request->getPost('prepared_by_id') ?: null,
				'approved_by_id' => $this->request->getPost('approved_by_id') ?: null,
				'updated_at' => $dt,
			];

			$existing_header = $TrainingRecordHeaderModel->where('user_id', $user_id)->first();
			if($existing_header){
				$TrainingRecordHeaderModel->where('user_id', $user_id)->set($header_data)->update();
			} else {
				$header_data['created_at'] = $dt;
				$TrainingRecordHeaderModel->insert($header_data);
			}

			// 2. Save education
			// Delete existing and re-insert
			$EmployeeEducationModel->where('user_id', $user_id)->delete();
			$degrees = $this->request->getPost('degree');
			if(!empty($degrees)){
				foreach($degrees as $i => $degree){
					if(!empty($degree) || !empty($this->request->getPost('institution')[$i])){
						$edu_data = [
							'user_id' => $user_id,
							'degree' => $degree,
							'institution' => $this->request->getPost('institution')[$i] ?? '',
							'major' => $this->request->getPost('major')[$i] ?? '',
							'graduate_year' => $this->request->getPost('graduate_year')[$i] ?? '',
							'created_at' => $dt,
							'updated_at' => $dt,
						];
						$EmployeeEducationModel->insert($edu_data);
					}
				}
			}

			// 3. Save training history
			$EmployeeTrainingHistoryModel->where('user_id', $user_id)->delete();
			$course_titles = $this->request->getPost('course_title');
			if(!empty($course_titles)){
				foreach($course_titles as $i => $course_title){
					if(!empty($course_title) || !empty($this->request->getPost('course_objective')[$i])){
                        $raw_date = trim($this->request->getPost('date_completed')[$i] ?? '');
                        $date_completed = '';
                        if ($raw_date !== '') {
                            $date = \DateTime::createFromFormat('d-m-Y', $raw_date);
                            if (!$date) {
                                $date = \DateTime::createFromFormat('d/m/Y', $raw_date);
                            }
                            if (!$date) {
                                $date = date_create($raw_date);
                            }
                            $date_completed = $date ? $date->format('Y-m-d') : $raw_date;
                        }
                        $th_data = [
                            'user_id' => $user_id,
                            'course_title' => $course_title,
                            'course_objective' => $this->request->getPost('course_objective')[$i] ?? '',
                            'date_completed' => $date_completed,
                            'test_result' => $this->request->getPost('test_result')[$i] ?? '',
                            'total_hours' => $this->request->getPost('total_hours')[$i] ?? '',
                            'institution' => $this->request->getPost('institution')[$i] ?? '',
                            'location' => $this->request->getPost('location')[$i] ?? '',
                            'instructor_name' => $this->request->getPost('instructor_name')[$i] ?? '',
                            'created_at' => $dt,
                            'updated_at' => $dt,
						];
						$EmployeeTrainingHistoryModel->insert($th_data);
					}
				}
			}

			// 4. Save licenses
			$EmployeeLicenseModel->where('user_id', $user_id)->delete();
			$license_types = $this->request->getPost('license_type');
			if(!empty($license_types)){
				foreach($license_types as $i => $license_type){
					if(!empty($license_type)){
						$lic_data = [
							'user_id' => $user_id,
							'license_type' => $license_type,
							'license_no' => $this->request->getPost('license_no')[$i] ?? '',
							'expired_date' => $this->request->getPost('expired_date')[$i] ?? '',
							'rating' => $this->request->getPost('rating')[$i] ?? '',
							'created_at' => $dt,
							'updated_at' => $dt,
						];
						$EmployeeLicenseModel->insert($lic_data);
					}
				}
			}

		// 5. Save basic certificates
		$cert_data = [
			'user_id' => $user_id,
			'a1' => $this->request->getPost('a1') ? 1 : 0,
			'a2' => $this->request->getPost('a2') ? 1 : 0,
			'a3' => $this->request->getPost('a3') ? 1 : 0,
			'a4' => $this->request->getPost('a4') ? 1 : 0,
			'c1' => $this->request->getPost('c1') ? 1 : 0,
			'c2' => $this->request->getPost('c2') ? 1 : 0,
			'c4' => $this->request->getPost('c4') ? 1 : 0,
			'updated_at' => $dt,
		];

		$existing_cert = $EmployeeBasicCertificateModel->where('user_id', $user_id)->first();
		if($existing_cert){
			$EmployeeBasicCertificateModel->where('user_id', $user_id)->set($cert_data)->update();
		} else {
			$cert_data['created_at'] = $dt;
			$EmployeeBasicCertificateModel->insert($cert_data);
		}

		// 6. Save signatures (prepared_by & approved_by)
		$TrainingSignatureModel = new TrainingSignatureModel();

		$signature_types = ['prepared_by', 'approved_by'];
		foreach($signature_types as $sig_type) {
			if($this->request->getPost('delete_signature_' . $sig_type) == '1') {
				$existing_sig = $TrainingSignatureModel->where('user_id', $user_id)->where('signature_type', $sig_type)->first();
				if($existing_sig) {
					if(!empty($existing_sig['file_path']) && file_exists(ROOTPATH . $existing_sig['file_path'])) {
						unlink(ROOTPATH . $existing_sig['file_path']);
					}
					$TrainingSignatureModel->delete($existing_sig['signature_id']);
				}
			} else {
				$sig_file = $this->request->getFile($sig_type . '_signature_file');
				$saved_file_path = null;

				if($sig_file && $sig_file->isValid() && !$sig_file->hasMoved()) {
					$validationRule = [ $sig_type . '_signature_file' => 'uploaded['.$sig_type.'_signature_file]|mime_in['.$sig_type.'_signature_file,image/jpg,image/jpeg,image/png]|max_size['.$sig_type.'_signature_file,2048]' ];
					if($this->validate($validationRule)){
						$newName = $sig_file->getRandomName();
						$uploadPath = ROOTPATH . 'public/uploads/signatures/';
						if(!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);
						$sig_file->move($uploadPath, $newName);
						$saved_file_path = 'public/uploads/signatures/' . $newName;
					} else {
						$Return['error'] = 'Invalid signature file format for ' . str_replace('_', ' ', $sig_type) . '. Allowed: JPG, JPEG, PNG (max 2MB).';
						$this->output($Return);
						return;
					}
				} else if($sig_file && $sig_file->getError() !== UPLOAD_ERR_NO_FILE) {
					$Return['error'] = 'Signature upload failed for ' . str_replace('_', ' ', $sig_type) . '. Please try again.';
					$this->output($Return);
					return;
				}

				if(!empty($saved_file_path)) {
					$existing_sig = $TrainingSignatureModel->where('user_id', $user_id)->where('signature_type', $sig_type)->first();
					$signature_data = [
						'user_id' => $user_id,
						'signature_type' => $sig_type,
						'signature_data' => '',
						'file_path' => $saved_file_path,
						'updated_at' => $dt,
					];
					if($existing_sig) {
						if(!empty($existing_sig['file_path']) && file_exists(ROOTPATH . $existing_sig['file_path'])) {
							unlink(ROOTPATH . $existing_sig['file_path']);
						}
						$TrainingSignatureModel->update($existing_sig['signature_id'], $signature_data);
					} else {
						$signature_data['created_at'] = $dt;
						$TrainingSignatureModel->insert($signature_data);
					}
				}
			}
		}

		if($this->request->getPost('send_email_signatories') == '1') {
			$emp = $UsersModel->where('user_id', $user_id)->first();
			$emp_name = $emp ? ($emp['first_name'] . ' ' . $emp['last_name']) : 'Employee';
			$sender_name = $user_info['first_name'] . ' ' . $user_info['last_name'];
			$prepId = $this->request->getPost('prepared_by_id');
			$appvId = $this->request->getPost('approved_by_id');
			$subject = "Training Record Assignment";

			$message = "
				<p>Dear Signatory,</p>
				<p>This is a notification that you have been assigned to sign the Training Record for <strong>{$emp_name}</strong>.</p>
				<p>Please log in to your account https://gis.globalmaintenance.co.id and navigate to <strong>Record Approvals</strong> to review and sign the document.</p>
				<p>Assigned by: {$sender_name}</p>
				<p><br>Best Regards,<br>Global Maintenance Facility</p>
			";

			$email_service = \Config\Services::email();
			
			$targets = [];
			if($prepId) {
				$prep = $UsersModel->where('user_id', $prepId)->first();
				if($prep && !empty($prep['email'])) {
					$targets['Prepared By'] = $prep['email'];
				}
			}
			if($appvId) {
				$appv = $UsersModel->where('user_id', $appvId)->first();
				if($appv && !empty($appv['email'])) {
					$targets['Approved By'] = $appv['email'];
				}
			}

			$sent_to = [];
			foreach($targets as $role => $email_addr) {
				$email_service->clear(true);
				$email_service->setFrom('noreply@globalmaintenance.co.id', 'Global Maintenance Facility');
				$email_service->setTo($email_addr);
				$email_service->setSubject($subject);
				$email_service->setMessage($message);
				if($email_service->send()) {
					$sent_to[] = $role;
				}
			}

			if(!empty($sent_to)) {
				$Return['result'] = 'Training Record saved successfully. Email sent to ' . implode(' and ', $sent_to) . '.';
			} else {
				$Return['result'] = 'Training Record saved successfully, but no email address was found for the assigned staff.';
			}
		} else {
			$Return['result'] = 'Training Record saved successfully.';
		}

		$this->output($Return);
		}
	}

	public function print_cv()
	{
		$request = \Config\Services::request();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$user_id_param = udecode($request->uri->getSegment(3));

		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$StaffdetailsModel = new StaffdetailsModel();
		$DepartmentModel = new DepartmentModel();
		$DesignationModel = new DesignationModel();
		$TrainingRecordHeaderModel = new TrainingRecordHeaderModel();
		$EmployeeEducationModel = new EmployeeEducationModel();
		$EmployeeTrainingHistoryModel = new EmployeeTrainingHistoryModel();
		$EmployeeLicenseModel = new EmployeeLicenseModel();
		$EmployeeBasicCertificateModel = new EmployeeBasicCertificateModel();

		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if(!$session->has('sup_username')){
			return redirect()->to(site_url('erp/login'));
		}

		$employee = $UsersModel->where('user_id', $user_id_param)->first();
		if(!$employee){
			$session->setFlashdata('error_record', 'Employee not found.');
			return redirect()->to(site_url('erp/training-record'));
		}

		$staff_detail = $StaffdetailsModel->where('user_id', $user_id_param)->first();
		$department = $DepartmentModel->where('department_id', $staff_detail['department_id'] ?? 0)->first();
		$designation = $DesignationModel->where('designation_id', $staff_detail['designation_id'] ?? 0)->first();

		$data['employee'] = $employee;
		$data['staff_detail'] = $staff_detail;
		$data['department'] = $department;
		$data['designation'] = $designation;

		$data['header'] = $TrainingRecordHeaderModel->where('user_id', $user_id_param)->first();
		$data['education'] = $EmployeeEducationModel->where('user_id', $user_id_param)->orderBy('education_id', 'ASC')->findAll();
		$training_history = $EmployeeTrainingHistoryModel->where('user_id', $user_id_param)->findAll();
		usort($training_history, function($a, $b) {
			$dateA = 0;
			$dateB = 0;
			if (!empty($a['date_completed'])) {
				$date = \DateTime::createFromFormat('Y-m-d', $a['date_completed']);
				if (!$date) {
					$date = \DateTime::createFromFormat('d-m-Y', $a['date_completed']);
				}
				if (!$date) {
					$date = \DateTime::createFromFormat('d/m/Y', $a['date_completed']);
				}
				if ($date) {
					$dateA = $date->getTimestamp();
				}
			}
			if (!empty($b['date_completed'])) {
				$date = \DateTime::createFromFormat('Y-m-d', $b['date_completed']);
				if (!$date) {
					$date = \DateTime::createFromFormat('d-m-Y', $b['date_completed']);
				}
				if (!$date) {
					$date = \DateTime::createFromFormat('d/m/Y', $b['date_completed']);
				}
				if ($date) {
					$dateB = $date->getTimestamp();
				}
			}
			return $dateA <=> $dateB;
		});
		$data['training_history'] = $training_history;
		$data['licenses'] = $EmployeeLicenseModel->where('user_id', $user_id_param)->orderBy('license_id', 'ASC')->findAll();
		$data['certificates'] = $EmployeeBasicCertificateModel->where('user_id', $user_id_param)->first();
		$TrainingSignatureModel = new TrainingSignatureModel();
		$signatures = $TrainingSignatureModel->where('user_id', $user_id_param)->findAll();
		$data['signatures'] = $signatures;
		$EmployeeSignatureModel = new EmployeeSignatureModel();
		try {
			$data['employee_signature'] = $EmployeeSignatureModel->where('user_id', $user_id_param)->first();
		} catch (\Exception $e) {
			$data['employee_signature'] = null;
		}

		$data['logged_in_user_id'] = $usession['sup_user_id'];

		return view('erp/training_record/print_cv', $data);
	}

	public function my_record()
	{
		$request = \Config\Services::request();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$user_id_param = udecode($request->uri->getSegment(3));

		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$StaffdetailsModel = new StaffdetailsModel();
		$DepartmentModel = new DepartmentModel();
		$DesignationModel = new DesignationModel();
		$TrainingRecordHeaderModel = new TrainingRecordHeaderModel();
		$EmployeeEducationModel = new EmployeeEducationModel();
		$EmployeeTrainingHistoryModel = new EmployeeTrainingHistoryModel();
		$EmployeeLicenseModel = new EmployeeLicenseModel();
		$EmployeeBasicCertificateModel = new EmployeeBasicCertificateModel();

		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if(!$session->has('sup_username')){
			return redirect()->to(site_url('erp/login'));
		}

		// Only allow employee to view their own record
		if($user_info['user_id'] != $user_id_param){
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}

		$employee = $UsersModel->where('user_id', $user_id_param)->first();
		if(!$employee){
			$session->setFlashdata('error_record', 'Employee not found.');
			return redirect()->to(site_url('erp/desk'));
		}

		$staff_detail = $StaffdetailsModel->where('user_id', $user_id_param)->first();
		$department = $DepartmentModel->where('department_id', $staff_detail['department_id'] ?? 0)->first();
		$designation = $DesignationModel->where('designation_id', $staff_detail['designation_id'] ?? 0)->first();

		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = 'My Training Record | '.$xin_system['application_name'];
		$data['path_url'] = 'training_record';
		$data['breadcrumbs'] = 'My Training Record';

		$data['employee'] = $employee;
		$data['staff_detail'] = $staff_detail;
		$data['department'] = $department;
		$data['designation'] = $designation;

		$data['header'] = $TrainingRecordHeaderModel->where('user_id', $user_id_param)->first();
		$data['education'] = $EmployeeEducationModel->where('user_id', $user_id_param)->orderBy('education_id', 'ASC')->findAll();
		$data['training_history'] = $EmployeeTrainingHistoryModel->where('user_id', $user_id_param)->orderBy('training_history_id', 'ASC')->findAll();
		$data['licenses'] = $EmployeeLicenseModel->where('user_id', $user_id_param)->orderBy('license_id', 'ASC')->findAll();
		$data['certificates'] = $EmployeeBasicCertificateModel->where('user_id', $user_id_param)->first();

		$EmployeeSignatureModel = new EmployeeSignatureModel();
		try {
			$data['employee_signature'] = $EmployeeSignatureModel->where('user_id', $user_id_param)->first();
		} catch (\Exception $e) {
			$data['employee_signature'] = null;
		}

		$TrainingSignatureModel = new TrainingSignatureModel();
		$data['signatures'] = $TrainingSignatureModel->where('user_id', $user_id_param)->findAll();

		$data['subview'] = view('erp/training_record/my_record', $data);
		return view('erp/layout/layout_main', $data);
	}

	public function save_employee_signature()
	{
		if($this->request->getPost('type')=='save_employee_sig') {
			$Return = array('result'=>'', 'error'=>'', 'csrf_hash'=>'');
			$session = \Config\Services::session();
			$usession = $session->get('sup_username');
			$Return['csrf_hash'] = csrf_hash();

			$user_id = $this->request->getPost('user_id');
			$UsersModel = new UsersModel();
			$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

			if(!$session->has('sup_username')){
				$Return['error'] = 'Session expired.';
				$this->output($Return);
				return;
			}
			if($user_info['user_id'] != $user_id){
				$Return['error'] = 'Unauthorized.';
				$this->output($Return);
				return;
			}

			$dt = date('Y-m-d H:i:s');
			$EmployeeSignatureModel = new EmployeeSignatureModel();

			if($this->request->getPost('delete_employee_signature') == '1') {
				$existing = $EmployeeSignatureModel->where('user_id', $user_id)->first();
				if($existing) {
					if(!empty($existing['file_path']) && file_exists(ROOTPATH . $existing['file_path'])) {
						unlink(ROOTPATH . $existing['file_path']);
					}
					$EmployeeSignatureModel->delete($existing['signature_id']);
				}
				$Return['result'] = 'Signature deleted successfully.';
				$this->output($Return);
				return;
			}

			$sig_data_uri = $this->request->getPost('employee_sig');
			$sig_file = $this->request->getFile('employee_signature_file');

			$saved_file_path = null;
			$saved_base64 = null;

			if($sig_file && $sig_file->isValid() && !$sig_file->hasMoved()) {
				$validationRule = [ 'employee_signature_file' => 'uploaded[employee_signature_file]|mime_in[employee_signature_file,image/jpg,image/jpeg,image/png]|max_size[employee_signature_file,2048]' ];
				if($this->validate($validationRule)){
					$newName = $sig_file->getRandomName();
					$uploadPath = ROOTPATH . 'public/uploads/signatures/';
					if(!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);
					$sig_file->move($uploadPath, $newName);
					$saved_file_path = 'public/uploads/signatures/' . $newName;
				} else {
					$Return['error'] = 'Invalid file format. Allowed: JPG, JPEG, PNG (max 2MB).';
					$this->output($Return);
					return;
				}
			}
			else if(!empty($sig_data_uri) && strpos($sig_data_uri, 'data:image') === 0) {
				$saved_base64 = $sig_data_uri;
			}

			if(empty($saved_file_path) && empty($saved_base64)) {
				$Return['error'] = 'No signature provided.';
				$this->output($Return);
				return;
			}

			$existing = $EmployeeSignatureModel->where('user_id', $user_id)->first();
			$sig_data = [
				'user_id' => $user_id,
				'signature_data' => $saved_base64,
				'file_path' => $saved_file_path,
				'updated_at' => $dt,
			];
			if($existing) {
				$EmployeeSignatureModel->update($existing['signature_id'], $sig_data);
			} else {
				$sig_data['created_at'] = $dt;
				$EmployeeSignatureModel->insert($sig_data);
			}

			$Return['result'] = 'Signature saved successfully.';
			$this->output($Return);
		}
	}

	/**
	 * Sign record endpoint for Prepared By / Approved By
	 * Validates that the logged-in user matches the assigned role
	 */
	public function sign_record()
	{
		$Return = array('result'=>'', 'error'=>'', 'csrf_hash'=>'');
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$Return['csrf_hash'] = csrf_hash();

		if(!$session->has('sup_username')){
			$Return['error'] = 'Session expired.';
			$this->output($Return);
			return;
		}

		$user_id = $this->request->getPost('user_id'); // the employee whose record is being signed
		$raw_signature_type = strtolower(trim((string) $this->request->getPost('signature_type')));
		$signature_type = preg_replace('/[\s\-]+/', '_', $raw_signature_type);
		$signature_type = strtolower(trim($signature_type, '_'));
		$logged_in_user_id = $usession['sup_user_id'];

		// Validate signature_type
		if(!in_array($signature_type, ['prepared_by', 'approved_by'], true)) {
			$Return['error'] = 'Invalid signature type.';
			$Return['debug_signature_type'] = $raw_signature_type;
			$this->output($Return);
			return;
		}

		// Get header to validate assignment
		$TrainingRecordHeaderModel = new TrainingRecordHeaderModel();
		$header = $TrainingRecordHeaderModel->where('user_id', $user_id)->first();
		if(!$header) {
			$Return['error'] = 'Training record not found.';
			$this->output($Return);
			return;
		}

		// Security: validate the logged-in user is assigned to this role
		$assigned_field = $signature_type . '_id'; // prepared_by_id or approved_by_id
		if(($header[$assigned_field] ?? '') != $logged_in_user_id) {
			$Return['error'] = 'Unauthorized. You are not assigned to sign as ' . str_replace('_', ' ', $signature_type) . '.';
			$this->output($Return);
			return;
		}

		$dt = date('Y-m-d H:i:s');
		$TrainingSignatureModel = new TrainingSignatureModel();

		// Handle delete
		if($this->request->getPost('delete_signature') == '1') {
			$existing = $TrainingSignatureModel->where('user_id', $user_id)->where('signature_type', $signature_type)->first();
			if($existing) {
				if(!empty($existing['file_path']) && file_exists(ROOTPATH . $existing['file_path'])) {
					unlink(ROOTPATH . $existing['file_path']);
				}
				$TrainingSignatureModel->delete($existing['signature_id']);
			}
			$Return['result'] = 'Signature removed successfully.';
			$this->output($Return);
			return;
		}

		// Handle signature data
		$sig_data_uri = $this->request->getPost('sig_data');
		$sig_file = $this->request->getFile('sig_file');

		$saved_file_path = null;
		$saved_base64 = null;

		if($sig_file && $sig_file->isValid() && !$sig_file->hasMoved()) {
			$validationRule = [ 'sig_file' => 'uploaded[sig_file]|mime_in[sig_file,image/jpg,image/jpeg,image/png]|max_size[sig_file,2048]' ];
			if($this->validate($validationRule)){
				$newName = $sig_file->getRandomName();
				$uploadPath = ROOTPATH . 'public/uploads/signatures/';
				if(!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);
				$sig_file->move($uploadPath, $newName);
				$saved_file_path = 'public/uploads/signatures/' . $newName;
			} else {
				$Return['error'] = 'Invalid file format. Allowed: JPG, JPEG, PNG (max 2MB).';
				$this->output($Return);
				return;
			}
		} else if(!empty($sig_data_uri) && strpos($sig_data_uri, 'data:image') === 0) {
			$saved_base64 = $sig_data_uri;
		}

		if(empty($saved_file_path) && empty($saved_base64)) {
			$Return['error'] = 'No signature provided.';
			$this->output($Return);
			return;
		}

		$existing = $TrainingSignatureModel->where('user_id', $user_id)->where('signature_type', $signature_type)->first();
		$sig_data = [
			'user_id' => $user_id,
			'signature_type' => $signature_type,
			'signature_data' => $saved_base64,
			'file_path' => $saved_file_path,
			'updated_at' => $dt,
		];
		if($existing) {
			// Delete old file if replacing
			if(!empty($existing['file_path']) && file_exists(ROOTPATH . $existing['file_path'])) {
				unlink(ROOTPATH . $existing['file_path']);
			}
			$TrainingSignatureModel->update($existing['signature_id'], $sig_data);
		} else {
			$sig_data['created_at'] = $dt;
			$TrainingSignatureModel->insert($sig_data);
		}

		$Return['result'] = ucfirst(str_replace('_', ' ', $signature_type)) . ' signature saved successfully.';
		$this->output($Return);
	}

	/**
	 * Training Record Approvals page
	 * Shows records where the logged-in user is assigned as prepared_by or approved_by
	 */
	public function approvals()
	{
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$StaffdetailsModel = new StaffdetailsModel();
		$DesignationModel = new DesignationModel();
		$TrainingRecordHeaderModel = new TrainingRecordHeaderModel();

		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
		if(!$session->has('sup_username')){
			return redirect()->to(site_url('erp/login'));
		}

		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = 'Record Approvals | '.$xin_system['application_name'];
		$data['path_url'] = 'training_record';
		$data['breadcrumbs'] = 'Record Approvals';

		$logged_in_user_id = $usession['sup_user_id'];

		// Find records where logged-in user is prepared_by or approved_by
		$records = $TrainingRecordHeaderModel
			->groupStart()
				->where('prepared_by_id', $logged_in_user_id)
				->orWhere('approved_by_id', $logged_in_user_id)
			->groupEnd()
			->findAll();

		$approval_list = [];
		foreach($records as $rec) {
			$emp = $UsersModel->where('user_id', $rec['user_id'])->first();
			if(!$emp) continue;
			$staff_detail = $StaffdetailsModel->where('user_id', $rec['user_id'])->first();
			$designation = $DesignationModel->where('designation_id', $staff_detail['designation_id'] ?? 0)->first();
			$rec['employee_name'] = $emp['first_name'] . ' ' . $emp['last_name'];
			$rec['employee_id_no'] = $staff_detail['employee_id'] ?? '-';
			$rec['designation_name'] = $designation['designation_name'] ?? '-';
			$rec['encoded_user_id'] = uencode($rec['user_id']);
			$rec['role'] = [];
			if(($rec['prepared_by_id'] ?? '') == $logged_in_user_id) $rec['role'][] = 'Prepared By';
			if(($rec['approved_by_id'] ?? '') == $logged_in_user_id) $rec['role'][] = 'Approved By';
			$rec['role_text'] = implode(', ', $rec['role']);
			$approval_list[] = $rec;
		}
		$data['approvals'] = $approval_list;

		$data['subview'] = view('erp/training_record/approvals', $data);
		return view('erp/layout/layout_main', $data);
	}

	public function send_email_reminder()
	{
		$Return = array('result'=>'', 'error'=>'', 'csrf_hash'=>'');
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$Return['csrf_hash'] = csrf_hash();

		if(!$session->has('sup_username')){
			$Return['error'] = 'Session expired.';
			$this->output($Return);
			return;
		}

		$user_id = $this->request->getPost('user_id'); // employee ID
		$logged_in_user_id = $usession['sup_user_id'];

		$UsersModel = new \App\Models\UsersModel();
		$emp = $UsersModel->where('user_id', $user_id)->first();
		$sender = $UsersModel->where('user_id', $logged_in_user_id)->first();

		if(!$emp) {
			$Return['error'] = 'Employee not found.';
			$this->output($Return);
			return;
		}

		if(empty($emp['email'])) {
			$Return['error'] = 'Employee does not have an email address.';
			$this->output($Return);
			return;
		}

		$emp_name = $emp['first_name'] . ' ' . $emp['last_name'];
		$sender_name = $sender['first_name'] . ' ' . $sender['last_name'];
		
		$subject = 'Action Required: Training Record Signature Needed';
		$message = "
			<p>Dear {$emp_name},</p>
			<p>This is a reminder that your Training Record requires your signature (Employee Signed).</p>
			<p>Please log in to your account https://gis.globalmaintenance.co.id and navigate to <strong>My Training Record</strong> to review and sign the document.</p>
			<p>Requested by: {$sender_name}</p>
			<p><br>Best Regards,<br>Global Maintenance Facility</p>
		";

		$email = \Config\Services::email();
		$email->setFrom('noreply@globalmaintenance.co.id', 'Global Maintenance Facility');
		$email->setTo($emp['email']);
		$email->setSubject($subject);
		$email->setMessage($message);

		if ($email->send()) {
			$Return['result'] = 'Reminder email sent successfully to ' . $emp['email'];
		} else {
			$Return['error'] = 'Failed to send email. Debug: ' . $email->printDebugger(['headers']);
		}

		$this->output($Return);
	}
}
