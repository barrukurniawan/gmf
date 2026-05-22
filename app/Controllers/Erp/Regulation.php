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
        $data['title']           = 'Regulation Portal | ' . $xin_system['application_name'];
        $data['path_url']        = 'regulation';
        $data['breadcrumbs']     = 'Regulation Portal';
        $data['active_doc']      = $documentId ? udecode($documentId) : null;
        $data['active_category'] = $request->getGet('category') ?: 'all';

        // Categories
        $data['categories'] = [
            'all'             => 'All Documents',
            'sop'             => 'SOP',
            'draft_regulasi'  => 'Draft Regulasi',
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
            'sop'            => '<span class="badge bg-primary">SOP</span>',
            'draft_regulasi' => '<span class="badge bg-warning text-dark">Draft Regulasi</span>',
            'policy_letter'  => '<span class="badge bg-success">Policy Letter</span>',
            'forms'          => '<span class="badge bg-info text-dark">Forms</span>',
            'others'         => '<span class="badge bg-secondary">Others</span>',
        ];

        $data = [];
        $no   = $start + 1;
        foreach ($documents as $doc) {
            $encId = uencode($doc['id']);

            $actions = '<div class="btn-group">';
            $actions .= '<button class="btn btn-sm btn-outline-primary btn-view-doc" data-id="' . $encId . '" title="Lihat Dokumen"><i class="fas fa-eye"></i></button>';
            $actions .= '<a href="' . site_url('erp/regulation/download/' . $encId) . '" class="btn btn-sm btn-outline-success" title="Download" target="_blank"><i class="fas fa-download"></i></a>';
            $actions .= '</div>';

            $data[] = [
                $no++,
                '<strong>' . esc($doc['title']) . '</strong>' .
                    ($doc['document_number'] ? '<br><small class="text-muted">No: ' . esc($doc['document_number']) . '</small>' : ''),
                $categories[$doc['category']] ?? '<span class="badge bg-secondary">Others</span>',
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
}
