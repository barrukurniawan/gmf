<?php namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class Noauth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
		$session = \Config\Services::session();
		if($session->has('sup_username')){
			$session->setFlashdata('unauthorized_module',lang('Dashboard.err_already_logged_in_to_system'));
			return redirect()->to(site_url('erp/desk'));
		}
    }

    //--------------------------------------------------------------------

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}