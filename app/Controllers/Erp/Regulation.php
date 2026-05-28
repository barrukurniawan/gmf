<?php
/**
 * Regulation Portal Controller
 * Document Management / Regulation Portal
 */
namespace App\Controllers\Erp;

use App\Controllers\BaseController;
use App\Models\RegulationDocumentsModel;
use App\Models\SystemModel;
use App\Models\UsersModel;

class Regulation extends BaseController
{
    protected $uploadPath = ROOTPATH . 'public/uploads/regulation_documents/';
    protected $uploadUrl  = 'public/uploads/regulation_documents/';

    /**
     * Index - Portal Page
     * URL: erp/regulation-portal/ or erp/regulation/view/{id}
     */
    public function index($documentId = null)
    {
        $session = \Config\Services::session();
        if (!$session->has('sup_username')) {
            $session->setFlashdata('err_not_logged_in', lang('Dashboard.err_not_logged_in'));
            return redirect()->to(site_url('erp/login'));
        }

        $SystemModel = new SystemModel();
        $UsersModel  = new UsersModel();
        $usession    = $session->get('sup_username');
        $user_info   = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

        $xin_system = $SystemModel->where('setting_id', 1)->first();

        $request = \Config\Services::request();
        $data['title']           = 'Company Manual Publication | ' . $xin_system['application_name'];
        $data['path_url']        = 'regulation';
        $data['breadcrumbs']     = 'Company Manual Publication';
        $data['active_doc']      = $documentId ? udecode($documentId) : null;
        $data['active_category'] = $request->getGet('category') ?: 'all';

        // Role-based CRUD permission: only company/super_user can CRUD
        $data['can_crud']     = ($user_info['user_type'] == 'company' || $user_info['user_type'] == 'super_user');
        // Download permission: staff cannot download
        $data['can_download'] = ($user_info['user_type'] == 'company' || $user_info['user_type'] == 'super_user');

        // Categories
        $data['categories'] = [
            'all'             => 'All Documents',
            'sop'             => 'SOP',
            'draft_regulasi'  => 'CMM/EMM/Others',
            'policy_letter'   => 'Policy Letter',
            'forms'           => 'Forms',
            'others'          => 'Others',
        ];

        $data['subview'] = view('erp/regulation/portal', $data);
        return view('erp/layout/layout_main', $data);
    }

    /**
     * DataTables AJAX List
     */
    public function list()
    {
        $session = \Config\Services::session();
        if (!$session->has('sup_username')) {
            return $this->response->setJSON(['data' => []]);
        }

        $request = \Config\Services::request();
        $UsersModel = new UsersModel();
        $usession = $session->get('sup_username');
        $user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
        $canCrud = ($user_info['user_type'] == 'company' || $user_info['user_type'] == 'super_user');

        $RegulationDocumentsModel = new RegulationDocumentsModel();

        $category = $request->getGet('category');
        $search   = $request->getGet('search')['value'] ?? '';

        $builder = $RegulationDocumentsModel->builder();

        // Filter by category
        if ($category && $category !== 'all') {
            $builder->where('category', $category);
        }

        // Search
        if ($search) {
            $builder->groupStart()
                    ->like('title', $search)
                    ->orLike('document_number', $search)
                    ->groupEnd();
        }

        // Total filtered
        $totalFiltered = $builder->countAllResults(false);

        // Order
        $orderColumn = $request->getGet('order')[0]['column'] ?? 0;
        $orderDir    = $request->getGet('order')[0]['dir'] ?? 'desc';
        $columns     = ['id', 'title', 'document_number', 'category', 'publish_date'];
        $builder->orderBy($columns[$orderColumn] ?? 'publish_date', $orderDir);

        // Pagination
        $start  = (int) ($request->getGet('start') ?? 0);
        $length = (int) ($request->getGet('length') ?? 10);
        if ($length > 0) {
            $builder->limit($length, $start);
        }

        $documents = $builder->get()->getResultArray();

        $categories = [
            'sop'            => '<span class="badge bg-danger">SOP</span>',
            'draft_regulasi' => '<span class="badge bg-warning text-dark">CMM/EMM/Others</span>',
            'policy_letter'  => '<span class="badge bg-success">Policy Letter</span>',
            'forms'          => '<span class="badge bg-info text-dark">Forms</span>',
            'others'         => '<span class="badge" style="background-color: #1e3a5f; color: #fff;">Others</span>',
        ];

        $data = [];
        $no   = $start + 1;
        foreach ($documents as $doc) {
            $encId = uencode($doc['id']);

            $actions = '<div class="btn-group">';
            $actions .= '<button class="btn btn-sm btn-outline-primary btn-view-doc" data-id="' . $encId . '" title="Lihat Dokumen"><i class="fas fa-eye"></i></button>';
            if ($canCrud) {
                $actions .= '<button class="btn btn-sm btn-outline-info btn-edit-doc" data-id="' . $encId . '" title="Edit Dokumen" data-toggle="modal" data-target="#regulation-modal"><i class="fas fa-edit"></i></button>';
                $actions .= '<button class="btn btn-sm btn-outline-danger btn-delete-doc" data-id="' . $encId . '" title="Hapus Dokumen"><i class="fas fa-trash-alt"></i></button>';
                // Download button: only for company/super_user
                $actions .= '<a href="' . site_url('erp/regulation/download/' . $encId) . '" class="btn btn-sm btn-outline-success" title="Download" target="_blank"><i class="fas fa-download"></i></a>';
            }
            $actions .= '</div>';

            $data[] = [
                $no++,
                '<strong>' . esc($doc['title']) . '</strong>' .
                    ($doc['document_number'] ? '<br><small class="text-muted">No: ' . esc($doc['document_number']) . '</small>' : ''),
                $categories[$doc['category']] ?? '<span class="badge" style="background-color: #1e3a5f; color: #fff;">Others</span>',
                $doc['publish_date'] ? date('d M Y', strtotime($doc['publish_date'])) : '-',
                $actions,
                $encId, // hidden column for JS usage
            ];
        }

        $output = [
            'draw'            => (int) ($request->getGet('draw') ?? 1),
            'recordsTotal'    => (new RegulationDocumentsModel())->countAllResults(),
            'recordsFiltered' => $totalFiltered,
            'data'            => $data,
        ];

        return $this->response->setJSON($output);
    }

    /**
     * Get document preview JSON (AJAX)
     */
    public function preview($id)
    {
        $session = \Config\Services::session();
        if (!$session->has('sup_username')) {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
        }

        $id = udecode($id);
        $RegulationDocumentsModel = new RegulationDocumentsModel();
        $document = $RegulationDocumentsModel->find($id);

        if (!$document) {
            return $this->response->setJSON(['error' => 'Document not found'])->setStatusCode(404);
        }

        $filePath = $this->uploadPath . $document['file_path'];
        $fileUrl  = site_url('erp/regulation/file/' . uencode($document['id']));

        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $isPdf = ($ext === 'pdf');

        return $this->response->setJSON([
            'id'              => uencode($document['id']),
            'title'           => $document['title'],
            'document_number' => $document['document_number'],
            'category'        => $document['category'],
            'publish_date'    => $document['publish_date'],
            'file_url'        => $fileUrl,
            'is_pdf'          => $isPdf,
            'extension'       => $ext,
        ]);
    }

    /**
     * Download document
     */
    public function download($id)
    {
        $session = \Config\Services::session();
        if (!$session->has('sup_username')) {
            return redirect()->to(site_url('erp/login'));
        }

        // Block download for staff (employees)
        $usession   = $session->get('sup_username');
        $UsersModel = new UsersModel();
        $user_info  = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
        if ($user_info['user_type'] == 'staff') {
            $session->setFlashdata('error', 'Anda tidak memiliki izin untuk mengunduh dokumen ini.');
            return redirect()->to(site_url('erp/regulation-portal'));
        }

        $id = udecode($id);
        $RegulationDocumentsModel = new RegulationDocumentsModel();
        $document = $RegulationDocumentsModel->find($id);

        if (!$document) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Document not found');
        }

        $filePath = $this->uploadPath . $document['file_path'];

        if (!file_exists($filePath)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('File not found');
        }

        return $this->response->download($filePath, null)->setFileName($document['file_path']);
    }

    /**
     * Serve file inline for iframe preview
     * URL: erp/regulation/file/{id}
     */
    public function file($id)
    {
        $session = \Config\Services::session();
        if (!$session->has('sup_username')) {
            return redirect()->to(site_url('erp/login'));
        }

        $id = udecode($id);
        $RegulationDocumentsModel = new RegulationDocumentsModel();
        $document = $RegulationDocumentsModel->find($id);

        if (!$document) {
            die('Document not found');
        }

        $filePath = $this->uploadPath . $document['file_path'];
        if (!file_exists($filePath)) {
            $session = \Config\Services::session();
            $session->setFlashdata('error', 'File dokumen tidak ditemukan di server.');
            return redirect()->to(site_url('erp/regulation-portal'));
        }

        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if ($ext === 'pdf') {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
            header('Cache-Control: private, max-age=0, must-revalidate');
            header('Pragma: public');
        } else {
            $mime = function_exists('mime_content_type') ? mime_content_type($filePath) : 'application/octet-stream';
            header('Content-Type: ' . $mime);
            header('Content-Disposition: inline; filename="' . basename($filePath) . '"');
        }

        readfile($filePath);
        exit;
    }

    /**
     * Read record - return modal view for edit
     */
    public function read()
    {
        $session = \Config\Services::session();
        $request = \Config\Services::request();

        if (!$session->has('sup_username')) {
            return redirect()->to(site_url('erp/login'));
        }

        $usession = $session->get('sup_username');
        $UsersModel = new UsersModel();
        $user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();

        if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'super_user') {
            $session->setFlashdata('unauthorized_module', lang('Dashboard.xin_error_unauthorized_module'));
            return redirect()->to(site_url('erp/desk'));
        }

        $id = $request->getGet('field_id');
        $data = [
            'field_id' => $id,
        ];

        if ($session->has('sup_username')) {
            return view('erp/regulation/dialog_document', $data);
        } else {
            return redirect()->to(site_url('erp/login'));
        }
    }

    /**
     * Add new regulation document (AJAX)
     */
    public function add()
    {
        $validation = \Config\Services::validation();
        $session    = \Config\Services::session();
        $request    = \Config\Services::request();
        $usession   = $session->get('sup_username');

        if (!$session->has('sup_username')) {
            return redirect()->to(site_url('erp/login'));
        }

        $UsersModel = new UsersModel();
        $user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
        if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'super_user') {
            $Return = ['result' => '', 'error' => lang('Dashboard.xin_error_unauthorized_module'), 'csrf_hash' => csrf_hash()];
            $this->output($Return);
            exit;
        }

        if ($this->request->getPost('type') !== 'add_record') {
            $Return = ['result' => '', 'error' => lang('Main.xin_error_msg'), 'csrf_hash' => csrf_hash()];
            $this->output($Return);
            exit;
        }

        $Return = ['result' => '', 'error' => '', 'csrf_hash' => csrf_hash()];

        // Validation rules
        $rules = [
            'title' => [
                'rules'  => 'required|min_length[3]',
                'errors' => ['required' => 'Judul dokumen wajib diisi.']
            ],
            'category' => [
                'rules'  => 'required|in_list[sop,draft_regulasi,policy_letter,forms,others]',
                'errors' => ['required' => 'Kategori wajib dipilih.']
            ],
            'document_file' => [
                'rules'  => 'uploaded[document_file]|max_size[document_file,10240]|mime_in[document_file,application/pdf,application/force-download,application/x-download,application/x-pdf,image/png,image/jpg,image/jpeg,image/gif,text/plain,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document]',
                'errors' => ['uploaded' => 'File dokumen wajib diupload.']
            ]
        ];

        if (!$this->validate($rules)) {
            $errors = [
                'title'         => $validation->getError('title'),
                'category'      => $validation->getError('category'),
                'document_file' => $validation->getError('document_file'),
            ];
            foreach ($errors as $err) {
                if ($err) {
                    $Return['error'] = $err;
                    $this->output($Return);
                    exit;
                }
            }
        }

        // Handle file upload
        $document_file = $this->request->getFile('document_file');
        $file_ext = $document_file->getClientExtension();
        if ($file_ext === '' || $file_ext === null) {
            $file_ext = $document_file->getExtension();
        }

        $allowed_exts = ['pdf', 'png', 'jpg', 'jpeg', 'gif', 'txt', 'xls', 'xlsx', 'doc', 'docx'];
        if (!in_array(strtolower($file_ext), $allowed_exts)) {
            $Return['error'] = 'Ekstensi file tidak valid. Diizinkan: pdf, png, jpg, jpeg, gif, txt, xls, xlsx, doc, docx';
            $this->output($Return);
            exit;
        }

        $original_name = $document_file->getName();
        $random_number = mt_rand(10000, 99999);
        $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
        $file_name = 'reg_' . $random_number . '_' . time() . '.' . $file_extension;

        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }

        $document_file->move($this->uploadPath, $file_name);

        $title           = $this->request->getPost('title');
        $document_number = $this->request->getPost('document_number');
        $category        = $this->request->getPost('category');
        $publish_date    = $this->request->getPost('publish_date');

        $RegulationDocumentsModel = new RegulationDocumentsModel();
        $data = [
            'title'           => $title,
            'document_number' => $document_number,
            'category'        => $category,
            'file_path'       => $file_name,
            'publish_date'    => $publish_date ?: null,
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ];

        $result = $RegulationDocumentsModel->insert($data);
        $Return['csrf_hash'] = csrf_hash();

        if ($result) {
            $Return['result'] = 'Dokumen regulasi berhasil ditambahkan.';
        } else {
            $Return['error'] = lang('Main.xin_error_msg');
        }

        $this->output($Return);
        exit;
    }

    /**
     * Edit regulation document (AJAX)
     */
    public function edit()
    {
        $validation = \Config\Services::validation();
        $session    = \Config\Services::session();
        $request    = \Config\Services::request();

        if (!$session->has('sup_username')) {
            return redirect()->to(site_url('erp/login'));
        }

        $usession = $session->get('sup_username');
        $UsersModel = new UsersModel();
        $user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
        if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'super_user') {
            $Return = ['result' => '', 'error' => lang('Dashboard.xin_error_unauthorized_module'), 'csrf_hash' => csrf_hash()];
            $this->output($Return);
            exit;
        }

        if ($this->request->getPost('type') !== 'edit_record') {
            $Return = ['result' => '', 'error' => lang('Main.xin_error_msg'), 'csrf_hash' => csrf_hash()];
            $this->output($Return);
            exit;
        }

        $Return = ['result' => '', 'error' => '', 'csrf_hash' => csrf_hash()];

        // Validation rules
        $rules = [
            'title' => [
                'rules'  => 'required|min_length[3]',
                'errors' => ['required' => 'Judul dokumen wajib diisi.']
            ],
            'category' => [
                'rules'  => 'required|in_list[sop,draft_regulasi,policy_letter,forms,others]',
                'errors' => ['required' => 'Kategori wajib dipilih.']
            ],
        ];

        if (!$this->validate($rules)) {
            $errors = [
                'title'    => $validation->getError('title'),
                'category' => $validation->getError('category'),
            ];
            foreach ($errors as $err) {
                if ($err) {
                    $Return['error'] = $err;
                    $this->output($Return);
                    exit;
                }
            }
        }

        $id = udecode($this->request->getPost('token'));
        $title           = $this->request->getPost('title');
        $document_number = $this->request->getPost('document_number');
        $category        = $this->request->getPost('category');
        $publish_date    = $this->request->getPost('publish_date');

        $RegulationDocumentsModel = new RegulationDocumentsModel();
        $document = $RegulationDocumentsModel->find($id);

        if (!$document) {
            $Return['error'] = 'Dokumen tidak ditemukan.';
            $this->output($Return);
            exit;
        }

        $data = [
            'title'           => $title,
            'document_number' => $document_number,
            'category'        => $category,
            'publish_date'    => $publish_date ?: null,
            'updated_at'      => date('Y-m-d H:i:s'),
        ];

        // Handle file replace (optional)
        $document_file = $this->request->getFile('document_file');
        if ($document_file && $document_file->isValid() && !$document_file->hasMoved()) {
            $validated = $this->validate([
                'document_file' => [
                    'rules'  => 'max_size[document_file,10240]|mime_in[document_file,application/pdf,application/force-download,application/x-download,application/x-pdf,image/png,image/jpg,image/jpeg,image/gif,text/plain,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document]',
                ]
            ]);

            if (!$validated) {
                $Return['error'] = $validation->getError('document_file');
                $this->output($Return);
                exit;
            }

            $file_ext = $document_file->getClientExtension();
            if ($file_ext === '' || $file_ext === null) {
                $file_ext = $document_file->getExtension();
            }
            $allowed_exts = ['pdf', 'png', 'jpg', 'jpeg', 'gif', 'txt', 'xls', 'xlsx', 'doc', 'docx'];
            if (!in_array(strtolower($file_ext), $allowed_exts)) {
                $Return['error'] = 'Ekstensi file tidak valid.';
                $this->output($Return);
                exit;
            }

            $random_number = mt_rand(10000, 99999);
            $file_extension = pathinfo($document_file->getName(), PATHINFO_EXTENSION);
            $file_name = 'reg_' . $random_number . '_' . time() . '.' . $file_extension;
            $document_file->move($this->uploadPath, $file_name);

            // Delete old file
            $oldFilePath = $this->uploadPath . $document['file_path'];
            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }

            $data['file_path'] = $file_name;
        }

        $result = $RegulationDocumentsModel->update($id, $data);
        $Return['csrf_hash'] = csrf_hash();

        if ($result) {
            $Return['result'] = 'Dokumen regulasi berhasil diperbarui.';
        } else {
            $Return['error'] = lang('Main.xin_error_msg');
        }

        $this->output($Return);
        exit;
    }

    /**
     * Delete regulation document (AJAX)
     */
    public function delete()
    {
        $session = \Config\Services::session();
        $request = \Config\Services::request();
        $usession = $session->get('sup_username');

        $UsersModel = new UsersModel();
        $user_info = $UsersModel->where('user_id', $usession['sup_user_id'])->first();
        if ($user_info['user_type'] != 'company' && $user_info['user_type'] != 'super_user') {
            $Return = ['result' => '', 'error' => lang('Dashboard.xin_error_unauthorized_module'), 'csrf_hash' => csrf_hash()];
            $this->output($Return);
            exit;
        }

        if ($this->request->getPost('_method') !== 'DELETE') {
            $Return = ['result' => '', 'error' => lang('Main.xin_error_msg'), 'csrf_hash' => csrf_hash()];
            $this->output($Return);
            exit;
        }

        $id = udecode($this->request->getPost('_token'));
        $Return = ['result' => '', 'error' => '', 'csrf_hash' => csrf_hash()];

        $RegulationDocumentsModel = new RegulationDocumentsModel();
        $document = $RegulationDocumentsModel->find($id);

        if (!$document) {
            $Return['error'] = 'Dokumen tidak ditemukan.';
            $this->output($Return);
            exit;
        }

        // Delete physical file
        $filePath = $this->uploadPath . $document['file_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $result = $RegulationDocumentsModel->delete($id);

        if ($result) {
            $Return['result'] = 'Dokumen regulasi berhasil dihapus.';
        } else {
            $Return['error'] = lang('Main.xin_error_msg');
        }

        $this->output($Return);
        exit;
    }
}
