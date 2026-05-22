<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RegulationDocumentsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'title'           => 'SOP Pengelolaan Dokumen Internal',
                'document_number' => 'SOP-001/HR/2024',
                'category'        => 'sop',
                'file_path'       => 'sop_001_dokumen_internal.pdf',
                'publish_date'    => '2024-01-15',
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
            [
                'title'           => 'SOP Perjalanan Dinas',
                'document_number' => 'SOP-002/HR/2024',
                'category'        => 'sop',
                'file_path'       => 'sop_002_perjalanan_dinas.pdf',
                'publish_date'    => '2024-02-10',
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
            [
                'title'           => 'Draft Regulasi Cuti Tahunan 2024',
                'document_number' => 'DRAFT-REG/2024/003',
                'category'        => 'draft_regulasi',
                'file_path'       => 'draft_regulasi_cuti_2024.pdf',
                'publish_date'    => '2024-03-01',
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
            [
                'title'           => 'Draft Perubahan Kebijakan Remote Working',
                'document_number' => 'DRAFT-REG/2024/004',
                'category'        => 'draft_regulasi',
                'file_path'       => 'draft_remote_working_2024.pdf',
                'publish_date'    => '2024-03-20',
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
            [
                'title'           => 'Surat Keputusan Direksi No. 05/2024',
                'document_number' => 'SK-005/DIR/2024',
                'category'        => 'policy_letter',
                'file_path'       => 'sk_005_direksi_2024.pdf',
                'publish_date'    => '2024-04-05',
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
            [
                'title'           => 'Surat Edaran Kenaikan Pangkat',
                'document_number' => 'SE-006/HR/2024',
                'category'        => 'policy_letter',
                'file_path'       => 'se_006_kenaikan_pangkat.pdf',
                'publish_date'    => '2024-04-12',
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
            [
                'title'           => 'Formulir Pengajuan Cuti',
                'document_number' => 'FORM-007/HR/2024',
                'category'        => 'forms',
                'file_path'       => 'form_pengajuan_cuti.docx',
                'publish_date'    => '2024-01-01',
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
            [
                'title'           => 'Formulir Evaluasi Karyawan',
                'document_number' => 'FORM-008/HR/2024',
                'category'        => 'forms',
                'file_path'       => 'form_evaluasi_karyawan.xlsx',
                'publish_date'    => '2024-02-01',
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
            [
                'title'           => 'Memo Rapat Koordinasi Q1',
                'document_number' => 'MEMO-009/2024',
                'category'        => 'others',
                'file_path'       => 'memo_rapat_q1.pdf',
                'publish_date'    => '2024-03-15',
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
            [
                'title'           => 'Notulen Rapat Manajemen',
                'document_number' => 'NTLN-010/2024',
                'category'        => 'others',
                'file_path'       => 'notulen_rapat_manajemen.pdf',
                'publish_date'    => '2024-04-18',
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('ci_regulation_documents')->insertBatch($data);
    }
}
