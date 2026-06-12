<?php
namespace App\Controllers\Erp;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\SystemModel;
use App\Models\UsersModel;

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
}
