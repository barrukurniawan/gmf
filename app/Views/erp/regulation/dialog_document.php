<?php
use App\Models\RegulationDocumentsModel;

$request = \Config\Services::request();
$session = \Config\Services::session();

$field_id = $request->getGet('field_id');
$RegulationDocumentsModel = new RegulationDocumentsModel();

if ($field_id) {
    $id = udecode($field_id);
    $document = $RegulationDocumentsModel->find($id);
    $is_edit = true;
} else {
    $document = [
        'id'              => '',
        'title'           => '',
        'document_number' => '',
        'category'        => 'sop',
        'publish_date'    => date('Y-m-d'),
        'file_path'       => '',
    ];
    $is_edit = false;
}

$categories = [
    'sop'            => 'SOP',
    'draft_regulasi' => 'CMM/EMM/Others',
    'policy_letter'  => 'Policy Letter',
    'forms'          => 'Forms',
    'others'         => 'Others',
];
?>

<div class="modal-header">
    <h5 class="modal-title"><?= $is_edit ? 'Edit Dokumen Regulasi' : 'Tambah Dokumen Regulasi' ?></h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<?php $attributes = ['name' => 'regulation-form', 'id' => 'regulation-form', 'autocomplete' => 'off', 'class' => 'm-b-1']; ?>
<?php $hidden = ['type' => $is_edit ? 'edit_record' : 'add_record', 'token' => $is_edit ? uencode($document['id']) : '']; ?>
<?= form_open_multipart('erp/regulation/' . ($is_edit ? 'edit' : 'add'), $attributes, $hidden); ?>

<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="title">Judul Dokumen <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="title" id="title" placeholder="Masukkan judul dokumen" value="<?= esc($document['title']) ?>">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="document_number">Nomor Dokumen</label>
                <input type="text" class="form-control" name="document_number" id="document_number" placeholder="Contoh: SOP-001/HR/2024" value="<?= esc($document['document_number']) ?>">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="publish_date">Tanggal Publish</label>
                <input type="date" class="form-control" name="publish_date" id="publish_date" value="<?= $document['publish_date'] ? date('Y-m-d', strtotime($document['publish_date'])) : '' ?>">
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label for="category">Kategori <span class="text-danger">*</span></label>
                <select class="form-control" name="category" id="category" data-plugin="select_hrm">
                    <?php foreach ($categories as $key => $label): ?>
                        <option value="<?= $key ?>" <?= $document['category'] === $key ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label for="document_file">File Dokumen <?= !$is_edit ? '<span class="text-danger">*</span>' : '' ?></label>
                <div class="custom-file">
                    <input type="file" class="custom-file-input" name="document_file" id="document_file" <?= !$is_edit ? 'required' : '' ?>>
                    <label class="custom-file-label" for="document_file">Pilih file...</label>
                </div>
                <small class="form-text text-muted">
                    Diizinkan: PDF, Word (doc/docx), Excel (xls/xlsx), Image (jpg/png/gif), TXT. Maks 10 MB.
                </small>
                <?php if ($is_edit && $document['file_path']): ?>
                    <div class="mt-2">
                        <span class="text-info"><i class="fas fa-paperclip mr-1"></i>File saat ini: <strong><?= esc($document['file_path']) ?></strong></span><br>
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengganti file.</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer text-right">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="submit" class="btn btn-primary ladda-button" data-style="expand-right">
        <span class="ladda-label"><?= $is_edit ? 'Perbarui' : 'Simpan' ?></span>
        <span class="ladda-spinner"></span>
    </button>
</div>

<?= form_close(); ?>

<script type="text/javascript">
$(document).ready(function() {
    // Show filename on file input change
    $('#regulation-form input[type="file"]').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);
    });

    // Initialize select2
    $('[data-plugin="select_hrm"]').select2({ width: '100%' });

    // Form submission
    $("#regulation-form").submit(function(e) {
        e.preventDefault();
        var form = $(this);
        var btn = form.find('button[type="submit"]');
        var laddaBtn = Ladda.create(btn[0]);
        laddaBtn.start();

        var formData = new FormData(this);
        formData.append('csrf_token', csrf_hash);

        $.ajax({
            type: "POST",
            url: form.attr('action'),
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    toastr.error(response.error);
                } else {
                    toastr.success(response.result);
                    $('#regulation-modal').modal('hide');
                    $('#documentsTable').DataTable().ajax.reload(null, false);
                }
                laddaBtn.stop();
                if (response.csrf_hash) {
                    csrf_hash = response.csrf_hash;
                }
            },
            error: function(xhr) {
                toastr.error('Terjadi kesalahan. Silakan coba lagi.');
                laddaBtn.stop();
                console.error(xhr.responseText);
            }
        });
    });
});
</script>
