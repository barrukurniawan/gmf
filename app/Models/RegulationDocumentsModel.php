<?php

namespace App\Models;

use CodeIgniter\Model;

class RegulationDocumentsModel extends Model
{
    protected $table            = 'ci_regulation_documents';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['title', 'document_number', 'category', 'file_path', 'publish_date', 'created_at', 'updated_at'];
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $validationRules  = [
        'title'    => 'required|min_length[3]|max_length[255]',
        'category' => 'required|in_list[sop,draft_regulasi,policy_letter,forms,others]',
        'file_path' => 'required|max_length[255]',
    ];

    protected $validationMessages = [
        'title' => [
            'required' => 'Judul dokumen wajib diisi.',
        ],
        'category' => [
            'required' => 'Kategori wajib dipilih.',
        ],
        'file_path' => [
            'required' => 'File dokumen wajib diupload.',
        ],
    ];

    /**
     * Get documents by category
     */
    public function getByCategory(string $category)
    {
        return $this->where('category', $category)
                    ->orderBy('publish_date', 'DESC')
                    ->findAll();
    }

    /**
     * Search documents
     */
    public function search(string $keyword, ?string $category = null)
    {
        $builder = $this->builder();
        $builder->groupStart()
                ->like('title', $keyword)
                ->orLike('document_number', $keyword)
                ->groupEnd();

        if ($category && $category !== 'all') {
            $builder->where('category', $category);
        }

        return $builder->orderBy('publish_date', 'DESC')
                       ->get()
                       ->getResultArray();
    }
}
