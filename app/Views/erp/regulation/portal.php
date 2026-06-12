<?php
$categories = [
    'all'            => ['label' => 'All Documents', 'icon' => 'fa-folder-open'],
    'sop'            => ['label' => 'SOP', 'icon' => 'fa-clipboard-check'],
    'draft_regulasi' => ['label' => 'CMM/EMM/Others', 'icon' => 'fa-file-signature'],
    'policy_letter'  => ['label' => 'Policy Letter', 'icon' => 'fa-envelope-open-text'],
    'forms'          => ['label' => 'Forms', 'icon' => 'fa-file-alt'],
    'others'         => ['label' => 'Others', 'icon' => 'fa-ellipsis-h'],
];
$activeCategory = $active_category ?? 'all';
$activeDoc = $active_doc ?? null;
?>

<style>
/* Regulation Portal Custom Styles */
.regulation-portal {
    --portal-primary: #1e3a5f;
    --portal-secondary: #f4f6f9;
    --portal-accent: #2b579a;
    --portal-border: #e2e8f0;
}

.reg-tabs {
    display: flex;
    overflow-x: auto;
    gap: 0.5rem;
    padding-bottom: 0.5rem;
    margin-bottom: 1.5rem;
    border-bottom: 1px solid var(--portal-border);
}

.reg-tabs::-webkit-scrollbar {
    height: 4px;
}
.reg-tabs::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.reg-tabs .nav-link {
    color: #475569;
    font-weight: 500;
    padding: 0.5rem 1rem;
    border: 1px solid transparent;
    border-radius: 0.5rem;
    white-space: nowrap;
    transition: all 0.25s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: #ffffff;
}

.reg-tabs .nav-link:hover {
    background: #f8fafc;
    color: var(--portal-accent);
    border-color: #e2e8f0;
}

.reg-tabs .nav-link.active {
    background: #eef2ff;
    color: var(--portal-accent);
    border-color: var(--portal-accent);
}

.reg-tabs .category-count {
    background: #e2e8f0;
    color: #475569;
    font-size: 0.75rem;
    padding: 0.1rem 0.4rem;
    border-radius: 999px;
    font-weight: 600;
}

.reg-tabs .nav-link.active .category-count {
    background: var(--portal-accent);
    color: #fff;
}

.reg-content {
    background: #f8fafc;
    min-height: calc(100vh - 200px);
    padding: 1.5rem;
}

.reg-card {
    background: #ffffff;
    border: 1px solid var(--portal-border);
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}

.reg-card-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--portal-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.reg-card-header h5 {
    margin: 0;
    font-weight: 600;
    color: #1e293b;
}

.reg-search-box {
    position: relative;
    max-width: 320px;
}

.reg-search-box input {
    padding-left: 2.5rem;
    border-radius: 0.5rem;
    border: 1px solid var(--portal-border);
    background: #f8fafc;
    font-size: 0.875rem;
    height: calc(1.5em + 0.75rem + 2px);
}

.reg-search-box i {
    position: absolute;
    left: 0.9rem;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 0.875rem;
}

.table-documents {
    margin: 0 !important;
    width: 100% !important;
}

.table-documents thead th {
    background: #f1f5f9;
    color: #334155;
    font-weight: 600;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.025em;
    border-bottom: 2px solid var(--portal-border);
    padding: 0.85rem 1rem;
    white-space: nowrap;
}

.table-documents tbody td {
    padding: 1rem;
    vertical-align: middle;
    color: #475569;
    font-size: 0.9rem;
    border-bottom: 1px solid #f1f5f9;
}

.table-documents tbody tr:hover {
    background: #f8fafc;
}

.table-documents tbody tr.doc-active {
    background: #eef2ff !important;
}

.badge-category {
    font-size: 0.75rem;
    padding: 0.35em 0.65em;
    border-radius: 0.375rem;
    font-weight: 600;
}

    .pdf-viewer-panel {
        background: #ffffff;
        border: 1px solid var(--portal-border);
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: calc(100vh - 220px);
        min-height: 480px;
    }

.pdf-viewer-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--portal-border);
    background: #f8fafc;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.pdf-viewer-header h6 {
    margin: 0;
    font-weight: 600;
    color: #1e293b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 70%;
}

.pdf-viewer-body {
    flex: 1;
    background: #e2e8f0;
    position: relative;
}

.pdf-viewer-body iframe {
    width: 100%;
    height: 100%;
    border: 0;
    display: block;
}

.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: #94a3b8;
    padding: 3rem 1rem;
    text-align: center;
}

.empty-state i {
    font-size: 3.5rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state p {
    font-size: 1rem;
    margin: 0;
}

.loading-overlay {
    position: absolute;
    inset: 0;
    background: rgba(255,255,255,0.85);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
}

.spinner-portal {
    width: 40px;
    height: 40px;
    border: 3px solid #e2e8f0;
    border-top-color: var(--portal-accent);
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Responsive adjustments */
@media (max-width: 991.98px) {
    .pdf-viewer-panel {
        height: auto;
        min-height: 380px;
    }
}

/* DataTables pagination & info center */
.dataTables_info {
    text-align: center !important;
    padding: 0.75rem 1rem 0.25rem !important;
    font-size: 0.85rem;
    color: #64748b;
}

.dataTables_paginate {
    text-align: center !important;
    padding: 0.5rem 1rem 1rem !important;
}

.dataTables_paginate .paginate_button {
    display: inline-block;
}
</style>

<div class="regulation-portal reg-content">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb bg-transparent p-0 m-0" style="font-size:0.875rem;">
            <li class="breadcrumb-item"><a href="<?= site_url('erp/desk') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Company Manual Publication</li>
        </ol>
    </nav>

    <!-- Categories Tabs -->
    <div class="reg-tabs">
        <?php foreach ($categories as $key => $cat): ?>
            <a href="javascript:void(0);" 
               class="nav-link category-filter <?= $activeCategory === $key ? 'active' : '' ?>" 
               data-category="<?= $key ?>">
                <i class="fas <?= $cat['icon'] ?>"></i>
                <span><?= $cat['label'] ?></span>
                <span class="category-count" id="count-<?= $key ?>">-</span>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="row" style="align-items:flex-start;">
        <!-- Document List -->
        <div class="col-lg-5 mb-3">
            <div class="reg-card h-100">
                <div class="reg-card-header" style="display:flex; align-items:center; justify-content:space-between; flex-wrap:nowrap; gap:0.4rem; padding:0.75rem 1rem;">
                    <div class="d-flex align-items-center flex-shrink-0" style="gap:0.3rem;">
                        <span style="font-size:0.8rem; color:#475569; white-space:nowrap;">Show</span>
                        <select id="docLengthSelect" class="form-control form-control-sm" style="width:58px; font-size:0.8rem;">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="-1">All</option>
                        </select>
                    </div>
                    <div class="d-flex align-items-center flex-shrink-0" style="gap:0.35rem;">
                        <div class="reg-search-box" style="max-width:130px;">
                            <i class="fas fa-search"></i>
                            <input type="text" id="docSearch" class="form-control form-control-sm" placeholder="Cari..." style="font-size:0.8rem;">
                        </div>
                        <?php if ($can_crud ?? false): ?>
                        <button type="button" class="btn btn-primary btn-sm btn-add-doc" data-toggle="modal" data-target="#regulation-modal" style="white-space:nowrap; font-size:0.8rem; padding:0.25rem 0.6rem;">
                            <i class="fas fa-plus mr-1"></i> Tambah
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                            <div class="p-0">
                                <div class="table-responsive">
                                    <table class="table table-documents" id="documentsTable" style="width:100% !important;">
                                        <thead>
                                            <tr>
                                                <th style="width:40px;">No</th>
                                                <th>Nama Dokumen</th>
                                                <th style="width:110px;">Kategori</th>
                                                <th style="width:100px;">Tanggal</th>
                                                <th style="width:100px;">Aksi</th>
                                                <th style="display:none;">encId</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <div id="emptyStateList" class="empty-state" style="display:none;">
                                    <i class="fas fa-inbox"></i>
                                    <p>Tidak ada dokumen ditemukan.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PDF Preview -->
                    <div class="col-lg-7 mb-3">
                        <div class="pdf-viewer-panel" id="pdfViewerPanel">
                            <div class="pdf-viewer-header">
                                <h6 id="previewTitle"><i class="fas fa-file-pdf mr-2 text-danger"></i>Pratinjau Dokumen</h6>
                                <div>
                                    <?php if ($can_download ?? false): ?>
                                    <a href="#" id="previewDownload" class="btn btn-sm btn-outline-primary" style="display:none;" target="_blank">
                                        <i class="fas fa-download mr-1"></i> Download
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="pdf-viewer-body" id="previewBody">
                                <div class="empty-state">
                                    <i class="fas fa-file-pdf"></i>
                                    <p>Pilih dokumen dari daftar untuk melihat pratinjau.</p>
                                </div>
                                <div class="loading-overlay" id="previewLoader" style="display:none;">
                                    <div class="spinner-portal"></div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="activeDocId" value="<?= $activeDoc ? uencode($activeDoc) : '' ?>">

<!-- Add/Edit Modal -->
<div class="modal fade" id="regulation-modal" tabindex="-1" role="dialog" aria-labelledby="regulation-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" id="regulation-modal-content">
            <!-- Content loaded via AJAX -->
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="delete-regulation-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus dokumen ini?</p>
                <p class="text-muted small">File dokumen juga akan dihapus dari server.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="btn-confirm-delete">
                    <i class="fas fa-trash-alt mr-1"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// DataTables initialization will be in module_scripts/regulation.js
var REGULATION = {
    baseUrl: '<?= site_url('erp/regulation') ?>',
    viewUrl: '<?= site_url('erp/regulation/view') ?>',
    activeCategory: '<?= $activeCategory ?>',
    activeDocId: '<?= $activeDoc ? uencode($activeDoc) : '' ?>',
    csrfName: '<?= csrf_token() ?>',
    csrfHash: '<?= csrf_hash() ?>',
    canDownload: <?= ($can_download ?? false) ? 'true' : 'false' ?>,
    categories: <?= json_encode(array_map(function($c){return $c['label'];}, $categories)) ?>
};
// Global csrf_hash for form submissions (used by modal forms)
var csrf_hash = '<?= csrf_hash() ?>';
</script>
