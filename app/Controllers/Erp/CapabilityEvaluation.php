<?php
namespace App\Controllers\Erp;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\SystemModel;
use App\Models\UsersModel;
use App\Models\CapabilityEvaluationModel;
use App\Models\CapabilityEvalTechDataModel;
use App\Models\CapabilityEvalFacilitiesModel;
use App\Models\CapabilityEvalToolsModel;
use App\Models\CapabilityEvalPersonnelModel;
use App\Models\CapabilityEvalApprovalsModel;
use App\Models\CapabilitySignatureModel;

class CapabilityEvaluation extends BaseController {

	public function index()
	{
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		if(!$session->has('sup_username')){
			$session->setFlashdata('err_not_logged_in', lang('Dashboard.err_not_logged_in'));
			return redirect()->to(site_url('erp/login'));
		}
		if($user_info['user_type'] != 'company' && $user_info['user_type'] != 'staff'){
			$session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
			return redirect()->to(site_url('erp/desk'));
		}

		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = 'Capability Evaluation | ' . $xin_system['application_name'];
		$data['path_url'] = 'capability_evaluation';
		$data['breadcrumbs'] = 'Capability Evaluation';

		$data['subview'] = view('erp/capability_evaluation/index', $data);
		return view('erp/layout/layout_main', $data);
	}

	public function maintenance()
	{
		return $this->list_view('maintenance');
	}

	public function component()
	{
		return $this->list_view('component');
	}

	private function list_view($type)
	{
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$CapabilityEvaluationModel = new CapabilityEvaluationModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		if(!$session->has('sup_username')){
			return redirect()->to(site_url('erp/login'));
		}

		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$title_prefix = $type == 'maintenance' ? 'Aircraft Maintenance' : 'Aircraft Component';
		$data['title'] = $title_prefix . ' Evaluation | ' . $xin_system['application_name'];
		$data['path_url'] = 'capability_evaluation';
		$data['breadcrumbs'] = $title_prefix . ' Records';
		$data['type'] = $type;

		if($user_info['user_type'] == 'company'){
			$company_id = $usession['sup_user_id'];
		} else {
			$company_id = $user_info['company_id'];
		}
		$data['all_staff'] = $UsersModel->where('company_id', $company_id)->where('user_type', 'staff')->orderBy('first_name', 'ASC')->findAll();

		$data['records'] = $CapabilityEvaluationModel->where('form_type', $type)->orderBy('id', 'DESC')->findAll();

		$data['subview'] = view('erp/capability_evaluation/list', $data);
		return view('erp/layout/layout_main', $data);
	}

	public function form($type = 'maintenance', $id = 0)
	{
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');
		$user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

		if(!$session->has('sup_username')){
			return redirect()->to(site_url('erp/login'));
		}

		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$title_prefix = $type == 'maintenance' ? 'Aircraft Maintenance' : 'Aircraft Component';
		$data['title'] = 'Form ' . $title_prefix . ' Evaluation | ' . $xin_system['application_name'];
		$data['path_url'] = 'capability_evaluation';
		$data['breadcrumbs'] = 'Form ' . $title_prefix;
		$data['type'] = $type;
		$data['id'] = $id;

		// Get company id to fetch staff list
		if($user_info['user_type'] == 'company'){
			$company_id = $usession['sup_user_id'];
		} else {
			$company_id = $user_info['company_id'];
		}
		$data['all_staff'] = $UsersModel->where('company_id', $company_id)->where('user_type', 'staff')->orderBy('first_name', 'ASC')->findAll();

		$CapabilityEvaluationModel = new CapabilityEvaluationModel();
		$CapabilityEvalTechDataModel = new CapabilityEvalTechDataModel();
		$CapabilityEvalFacilitiesModel = new CapabilityEvalFacilitiesModel();
		$CapabilityEvalToolsModel = new CapabilityEvalToolsModel();
		$CapabilityEvalPersonnelModel = new CapabilityEvalPersonnelModel();
		$CapabilityEvalApprovalsModel = new CapabilityEvalApprovalsModel();

		if($id > 0) {
			$data['header'] = $CapabilityEvaluationModel->find($id);
			$data['tech_data'] = $CapabilityEvalTechDataModel->where('eval_id', $id)->findAll();
			$data['facilities'] = $CapabilityEvalFacilitiesModel->where('eval_id', $id)->findAll();
			$data['tools'] = $CapabilityEvalToolsModel->where('eval_id', $id)->findAll();
			$data['personnel'] = $CapabilityEvalPersonnelModel->where('eval_id', $id)->findAll();
			$data['approvals_data'] = $CapabilityEvalApprovalsModel->where('eval_id', $id)->findAll();
		} else {
			$data['header'] = null;
			$data['tech_data'] = [];
			$data['facilities'] = [];
			$data['tools'] = [];
			$data['personnel'] = [];
			$data['approvals_data'] = [];
		}

		$data['subview'] = view('erp/capability_evaluation/form', $data);
		return view('erp/layout/layout_main', $data);
	}

	public function save()
	{
		$session = \Config\Services::session();
		$request = \Config\Services::request();
		$usession = $session->get('sup_username');
		$Return = array('result'=>'', 'error'=>'', 'csrf_hash'=>'');
		$Return['csrf_hash'] = csrf_hash();

		if(!$session->has('sup_username')){
			$Return['error'] = 'Session expired.';
			$this->output($Return);
			return;
		}

		$CapabilityEvaluationModel = new CapabilityEvaluationModel();
		$CapabilityEvalTechDataModel = new CapabilityEvalTechDataModel();
		$CapabilityEvalFacilitiesModel = new CapabilityEvalFacilitiesModel();
		$CapabilityEvalToolsModel = new CapabilityEvalToolsModel();
		$CapabilityEvalPersonnelModel = new CapabilityEvalPersonnelModel();
		$CapabilityEvalApprovalsModel = new CapabilityEvalApprovalsModel();

		$id = $this->request->getPost('id');
		$type = $this->request->getPost('form_type');

		$header_data = [
			'capability_no' => $this->request->getPost('capability_no'),
			'evaluation_date' => $this->request->getPost('evaluation_date'),
			'type_of_aircraft' => $this->request->getPost('type_of_aircraft'),
			'manufacture' => $this->request->getPost('manufacture'),
			'ata_chapter' => $this->request->getPost('ata_chapter'),
			'rating' => $this->request->getPost('rating'),
			'scope_of_work' => $this->request->getPost('scope_of_work'),
			'form_type' => $type,
			'decision_status' => $this->request->getPost('decision_status'),
			'decision_remarks' => $this->request->getPost('decision_remarks'),
			'prepared_by_1_id' => $this->request->getPost('prepared_by_1_id') ?: null,
			'prepared_by_2_id' => $this->request->getPost('prepared_by_2_id') ?: null,
			'approved_by_id' => $this->request->getPost('approved_by_id') ?: null,
		];

		if($id > 0) {
			$CapabilityEvaluationModel->update($id, $header_data);
			$eval_id = $id;
		} else {
			$header_data['created_by'] = $usession['sup_user_id'];
			$eval_id = $CapabilityEvaluationModel->insert($header_data);
		}

		// Save Tech Data (Part B)
		$CapabilityEvalTechDataModel->where('eval_id', $eval_id)->delete();
		$td_reqs = $this->request->getPost('td_requirement');
		if(!empty($td_reqs)){
			foreach($td_reqs as $i => $req){
				if(!empty($req)){
					$CapabilityEvalTechDataModel->insert([
						'eval_id' => $eval_id,
						'requirement' => $req,
						'available' => $this->request->getPost('td_available')[$i] ?? '',
						'reference' => $this->request->getPost('td_reference')[$i] ?? ''
					]);
				}
			}
		}

		// Save Facilities (Part C)
		$CapabilityEvalFacilitiesModel->where('eval_id', $eval_id)->delete();
		$fac_reqs = $this->request->getPost('fac_requirement');
		if(!empty($fac_reqs)){
			foreach($fac_reqs as $i => $req){
				if(!empty($req)){
					$CapabilityEvalFacilitiesModel->insert([
						'eval_id' => $eval_id,
						'requirement' => $req,
						'available' => $this->request->getPost('fac_available')[$i] ?? '',
						'remarks' => $this->request->getPost('fac_remarks')[$i] ?? ''
					]);
				}
			}
		}

		// Save Tools (Part D)
		$CapabilityEvalToolsModel->where('eval_id', $eval_id)->delete();
		$tool_descs = $this->request->getPost('tool_description');
		if(!empty($tool_descs)){
			foreach($tool_descs as $i => $desc){
				if(!empty($desc) || !empty($this->request->getPost('tool_part_number')[$i])){
					$CapabilityEvalToolsModel->insert([
						'eval_id' => $eval_id,
						'description' => $desc,
						'part_number' => $this->request->getPost('tool_part_number')[$i] ?? '',
						'type' => $this->request->getPost('tool_type')[$i] ?? '',
						'remarks' => $this->request->getPost('tool_remarks')[$i] ?? ''
					]);
				}
			}
		}

		// Save Personnel (Part E)
		$CapabilityEvalPersonnelModel->where('eval_id', $eval_id)->delete();
		$pers_names = $this->request->getPost('pers_name');
		if(!empty($pers_names)){
			foreach($pers_names as $i => $name){
				if(!empty($name)){
					$CapabilityEvalPersonnelModel->insert([
						'eval_id' => $eval_id,
						'name' => $name,
						'position' => $this->request->getPost('pers_position')[$i] ?? '',
						'year_of_experience' => $this->request->getPost('pers_year')[$i] ?? '',
						'rating' => $this->request->getPost('pers_rating')[$i] ?? '',
						'amel_no' => $this->request->getPost('pers_amel')[$i] ?? ''
					]);
				}
			}
		}

		// Save Approvals (Part F)
		$CapabilityEvalApprovalsModel->where('eval_id', $eval_id)->delete();
		$app_items = $this->request->getPost('app_item');
		if(!empty($app_items)){
			foreach($app_items as $i => $item){
				if(!empty($item)){
					$CapabilityEvalApprovalsModel->insert([
						'eval_id' => $eval_id,
						'item' => $item,
						'status' => $this->request->getPost('app_status')[$i] ?? ''
					]);
				}
			}
		}

		$Return['result'] = 'Capability Evaluation saved successfully.';
		$this->output($Return);
	}

	public function approvals()
	{
		$UsersModel = new UsersModel();
		$SystemModel = new SystemModel();
		$CapabilityEvaluationModel = new CapabilityEvaluationModel();
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		if(!$session->has('sup_username')){
			return redirect()->to(site_url('erp/login'));
		}

		$xin_system = $SystemModel->where('setting_id', 1)->first();
		$data['title'] = 'Evaluation Approvals | ' . $xin_system['application_name'];
		$data['path_url'] = 'capability_evaluation';
		$data['breadcrumbs'] = 'Evaluation Approvals';

		$logged_in_user_id = $usession['sup_user_id'];

		$data['records'] = $CapabilityEvaluationModel
			->groupStart()
				->where('prepared_by_1_id', $logged_in_user_id)
				->orWhere('prepared_by_2_id', $logged_in_user_id)
				->orWhere('approved_by_id', $logged_in_user_id)
			->groupEnd()
			->findAll();

		$data['subview'] = view('erp/capability_evaluation/approvals', $data);
		return view('erp/layout/layout_main', $data);
	}

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

		$eval_id = $this->request->getPost('eval_id');
		$signature_type = strtolower(trim($this->request->getPost('signature_type')));
		$logged_in_user_id = $usession['sup_user_id'];

		if(!in_array($signature_type, ['prepared_by_1', 'prepared_by_2', 'approved_by'], true)) {
			$Return['error'] = 'Invalid signature type.';
			$this->output($Return);
			return;
		}

		$CapabilityEvaluationModel = new CapabilityEvaluationModel();
		$header = $CapabilityEvaluationModel->find($eval_id);
		if(!$header) {
			$Return['error'] = 'Record not found.';
			$this->output($Return);
			return;
		}

		// Security: validate the logged-in user is assigned to this role
		$assigned_field = $signature_type . '_id'; 
		if(($header[$assigned_field] ?? '') != $logged_in_user_id) {
			$Return['error'] = 'Unauthorized. You are not assigned to sign as ' . str_replace('_', ' ', $signature_type) . '.';
			$this->output($Return);
			return;
		}

		$dt = date('Y-m-d H:i:s');
		$CapabilitySignatureModel = new CapabilitySignatureModel();

		if($this->request->getPost('delete_signature') == '1') {
			$existing = $CapabilitySignatureModel->where('eval_id', $eval_id)->where('signature_type', $signature_type)->first();
			if($existing) {
				if(!empty($existing['file_path']) && file_exists(ROOTPATH . $existing['file_path'])) {
					unlink(ROOTPATH . $existing['file_path']);
				}
				$CapabilitySignatureModel->delete($existing['signature_id']);
			}
			$Return['result'] = 'Signature removed successfully.';
			$this->output($Return);
			return;
		}

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

		$existing = $CapabilitySignatureModel->where('eval_id', $eval_id)->where('signature_type', $signature_type)->first();
		$sig_data = [
			'eval_id' => $eval_id,
			'user_id' => $logged_in_user_id,
			'signature_type' => $signature_type,
			'signature_data' => $saved_base64,
			'file_path' => $saved_file_path,
		];
		
		if($existing) {
			if(!empty($existing['file_path']) && file_exists(ROOTPATH . $existing['file_path'])) {
				unlink(ROOTPATH . $existing['file_path']);
			}
			$CapabilitySignatureModel->update($existing['signature_id'], $sig_data);
		} else {
			$CapabilitySignatureModel->insert($sig_data);
		}

		$Return['result'] = 'Signature saved successfully.';
		$this->output($Return);
	}

	public function print_pdf($id)
	{
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		if(!$session->has('sup_username')){
			return redirect()->to(site_url('erp/login'));
		}

		$CapabilityEvaluationModel = new CapabilityEvaluationModel();
		$header = $CapabilityEvaluationModel->find($id);
		
		if(!$header){
			$session->setFlashdata('error_record', 'Record not found.');
			return redirect()->to(site_url('erp/capability-evaluation'));
		}

		$CapabilityEvalTechDataModel = new CapabilityEvalTechDataModel();
		$CapabilityEvalFacilitiesModel = new CapabilityEvalFacilitiesModel();
		$CapabilityEvalToolsModel = new CapabilityEvalToolsModel();
		$CapabilityEvalPersonnelModel = new CapabilityEvalPersonnelModel();
		$CapabilityEvalApprovalsModel = new CapabilityEvalApprovalsModel();
		$CapabilitySignatureModel = new CapabilitySignatureModel();
		$UsersModel = new UsersModel();

		$data['header'] = $header;
		$data['tech_data'] = $CapabilityEvalTechDataModel->where('eval_id', $id)->findAll();
		$data['facilities'] = $CapabilityEvalFacilitiesModel->where('eval_id', $id)->findAll();
		$data['tools'] = $CapabilityEvalToolsModel->where('eval_id', $id)->findAll();
		$data['personnel'] = $CapabilityEvalPersonnelModel->where('eval_id', $id)->findAll();
		$data['approvals_data'] = $CapabilityEvalApprovalsModel->where('eval_id', $id)->findAll();

		// Get Signatures
		$signatures = $CapabilitySignatureModel->where('eval_id', $id)->findAll();
		$sigs = [];
		foreach($signatures as $s) {
			$sigs[$s['signature_type']] = $s;
		}
		$data['signatures'] = $sigs;

		// Get Users details for signatures
		$data['prep1_user'] = $header['prepared_by_1_id'] ? $UsersModel->find($header['prepared_by_1_id']) : null;
		$data['prep2_user'] = $header['prepared_by_2_id'] ? $UsersModel->find($header['prepared_by_2_id']) : null;
		$data['appv_user'] = $header['approved_by_id'] ? $UsersModel->find($header['approved_by_id']) : null;

		return view('erp/capability_evaluation/print_pdf', $data);
	}

	public function print_recap($type)
	{
		$session = \Config\Services::session();
		$usession = $session->get('sup_username');

		if(!$session->has('sup_username')){
			return redirect()->to(site_url('erp/login'));
		}

		$CapabilityEvaluationModel = new CapabilityEvaluationModel();
		$CapabilityEvalToolsModel = new CapabilityEvalToolsModel();
		
		$records = $CapabilityEvaluationModel->where('form_type', $type)->orderBy('id', 'ASC')->findAll();

		// Fetch part_number and tool type for each record (expand if multiple tools exist)
		$expanded_records = [];
		foreach($records as $r) {
			if(empty($r['decision_status'])) {
				$r['decision_status'] = 'pending';
			}

			$tools = $CapabilityEvalToolsModel->where('eval_id', $r['id'])->findAll();
			if($tools && count($tools) > 0) {
				foreach($tools as $t) {
					$row = $r;
					$row['part_number'] = $t['part_number'];
					$row['tool_type'] = $t['type'];
					$expanded_records[] = $row;
				}
			} else {
				$row = $r;
				$row['part_number'] = '';
				$row['tool_type'] = '';
				$expanded_records[] = $row;
			}
		}

		$data['records'] = $expanded_records;
		$data['type'] = $type;

		$prep_by_id = $this->request->getGet('prep_by');
		$check_by_id = $this->request->getGet('check_by');
		$appv_by_id = $this->request->getGet('appv_by');

		$UsersModel = new \App\Models\UsersModel();
		$StaffdetailsModel = new \App\Models\StaffdetailsModel();
		$DesignationModel = new \App\Models\DesignationModel();

		$getSigner = function($user_id) use ($UsersModel, $StaffdetailsModel, $DesignationModel) {
			if (!$user_id) return null;
			$u = $UsersModel->find($user_id);
			if (!$u) return null;
			$staff = $StaffdetailsModel->where('user_id', $user_id)->first();
			$designation_name = 'Manajer'; 
			if ($staff && $staff['designation_id']) {
				$desig = $DesignationModel->find($staff['designation_id']);
				if ($desig) {
					$designation_name = $desig['designation_name'];
				}
			}
			return [
				'name' => $u['first_name'] . ' ' . $u['last_name'],
				'designation' => $designation_name
			];
		};

		$data['prep'] = $getSigner($prep_by_id) ?? ['name' => 'Gema Usman', 'designation' => 'Manajer'];
		$data['check'] = $getSigner($check_by_id) ?? ['name' => 'Ajat Suhernajat', 'designation' => 'Manajer'];
		$data['appv'] = $getSigner($appv_by_id) ?? ['name' => 'Bambang Widyantoro', 'designation' => 'Manajer'];

		return view('erp/capability_evaluation/print_recap', $data);
	}

	public function debug_data() {
		$CapabilityEvaluationModel = new CapabilityEvaluationModel();
		$CapabilityEvalToolsModel = new CapabilityEvalToolsModel();
		
		$r = $CapabilityEvaluationModel->where('capability_no', 'SE-38247727')->first();
		echo "<pre>";
		echo "EVAL RECORD:\n";
		print_r($r);
		
		if($r) {
			echo "\nTOOLS:\n";
			$tools = $CapabilityEvalToolsModel->where('eval_id', $r['id'])->findAll();
			print_r($tools);
		}
		echo "</pre>";
		exit;
	}
}
