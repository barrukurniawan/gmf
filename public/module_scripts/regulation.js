/**
 * Regulation Portal Scripts
 * DataTables + AJAX PDF Preview + CRUD
 */

$(document).ready(function() {
    var activeCategory = REGULATION.activeCategory || 'all';
    var table;
    var $previewBody = $('#previewBody');
    var $previewTitle = $('#previewTitle');
    var $previewDownload = $('#previewDownload');
    var $previewLoader = $('#previewLoader');
    var $emptyStateList = $('#emptyStateList');

    // Initialize DataTable
    table = $('#documentsTable').DataTable({
        processing: true,
        serverSide: true,
        searching: true,
        ordering: true,
        paging: true,
        lengthChange: true,
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        ajax: {
            url: REGULATION.baseUrl + '/list',
            type: 'GET',
            data: function(d) {
                d.category = activeCategory;
                d[REGULATION.csrfName] = REGULATION.csrfHash;
            },
            dataSrc: function(json) {
                if (json.data.length === 0) {
                    $emptyStateList.show();
                } else {
                    $emptyStateList.hide();
                }
                return json.data;
            }
        },
        columns: [
            { data: 0, orderable: false, className: 'text-center' }, // No
            { data: 1, orderable: true }, // Title
            { data: 2, orderable: true, className: 'text-center' }, // Category badge
            { data: 3, orderable: true, className: 'text-center' }, // Date
            { data: 4, orderable: false, className: 'text-center' }, // Actions
            { data: 5, visible: false } // hidden encId
        ],
        language: {
            lengthMenu: dt_lengthMenu,
            zeroRecords: dt_zeroRecords,
            info: dt_info,
            infoEmpty: dt_infoEmpty,
            infoFiltered: dt_infoFiltered,
            search: dt_search,
            paginate: {
                first: dt_first,
                previous: dt_previous,
                next: dt_next,
                last: dt_last
            },
            processing: '<div class="text-center py-3"><div class="spinner-portal"></div><div class="mt-2 text-muted">' + processing_request + '</div></div>'
        },
        dom: 'rtip',
        drawCallback: function(settings) {
            attachRowHandlers();
            var activeDocId = $('#activeDocId').val();
            if (activeDocId) {
                highlightRowById(activeDocId);
            }
        }
    });

    // Custom search binding
    $('#docSearch').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Custom length select binding
    $('#docLengthSelect').on('change', function() {
        table.page.len(parseInt($(this).val())).draw();
    });

    // Category filter
    $('.category-filter').on('click', function(e) {
        e.preventDefault();
        var $this = $(this);
        var cat = $this.data('category');

        $('.category-filter').removeClass('active');
        $this.addClass('active');

        activeCategory = cat;
        table.ajax.reload();

        var newUrl = window.location.pathname + '?category=' + cat;
        window.history.replaceState({}, '', newUrl);
    });

    // Attach row click handler
    function attachRowHandlers() {
        $('#documentsTable tbody tr').off('click').on('click', function(e) {
            if ($(e.target).closest('button, a').length) {
                return;
            }
            var data = table.row(this).data();
            if (data && data[5]) {
                loadPreview(data[5]);
                highlightRow($(this));
            }
        });

        // View button handler
        $('.btn-view-doc').off('click').on('click', function(e) {
            e.stopPropagation();
            var id = $(this).data('id');
            loadPreview(id);
            var $row = $(this).closest('tr');
            highlightRow($row);
        });

        // Edit button handler
        $('.btn-edit-doc').off('click').on('click', function(e) {
            e.stopPropagation();
            var id = $(this).data('id');
            loadModal(id);
        });

        // Delete button handler
        $('.btn-delete-doc').off('click').on('click', function(e) {
            e.stopPropagation();
            var id = $(this).data('id');
            $('#btn-confirm-delete').data('id', id);
            $('#delete-regulation-modal').modal('show');
        });
    }

    function highlightRow($row) {
        $('#documentsTable tbody tr').removeClass('doc-active');
        $row.addClass('doc-active');
    }

    function highlightRowById(encId) {
        $('#documentsTable tbody tr').each(function() {
            var data = table.row(this).data();
            if (data && data[5] === encId) {
                $(this).addClass('doc-active');
                loadPreview(encId);
                $('#activeDocId').val('');
                return false;
            }
        });
    }

    // Load preview via AJAX
    function loadPreview(encId) {
        $previewLoader.show();
        $previewTitle.html('<i class="fas fa-spinner fa-spin mr-2"></i> Memuat dokumen...');

        $.ajax({
            url: REGULATION.baseUrl + '/preview/' + encId,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.error) {
                    showPreviewError(res.error);
                    return;
                }

                var titleHtml = '<i class="fas fa-file mr-2" style="color:var(--portal-accent);"></i>' + escapeHtml(res.title);
                $previewTitle.html(titleHtml);
                
                if (REGULATION.canDownload) {
                    $previewDownload.attr('href', REGULATION.baseUrl + '/download/' + res.id).show();
                } else {
                    $previewDownload.hide();
                }

                var viewerHtml = '';
                if (res.is_pdf) {
                    var pdfUrl = res.file_url;
                    if (!REGULATION.canDownload) {
                        pdfUrl += '#toolbar=0'; // Hide native PDF viewer controls like Print & Download
                    }
                    viewerHtml = '<iframe src="' + pdfUrl + '" title="PDF Preview"></iframe>';
                } else if (['jpg','jpeg','png','gif','webp'].indexOf(res.extension) !== -1) {
                    viewerHtml = '<div class="d-flex align-items-center justify-content-center h-100 bg-light"><img src="' + res.file_url + '" style="max-width:100%; max-height:100%; object-fit:contain;" alt="Preview"></div>';
                } else {
                    viewerHtml = '<div class="empty-state"><i class="fas fa-file"></i><p>Pratinjau tidak tersedia untuk format .' + res.extension + '</p>';
                    if (REGULATION.canDownload) {
                        viewerHtml += '<a href="' + REGULATION.baseUrl + '/download/' + res.id + '" class="btn btn-primary btn-sm mt-2"><i class="fas fa-download mr-1"></i> Download File</a>';
                    }
                    viewerHtml += '</div>';
                }

                $previewBody.html(viewerHtml);

                var newUrl = REGULATION.viewUrl + '/' + res.id + '?category=' + activeCategory;
                window.history.pushState({docId: res.id, category: activeCategory}, '', newUrl);
            },
            error: function(xhr) {
                var msg = 'Gagal memuat dokumen.';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    msg = xhr.responseJSON.error;
                }
                showPreviewError(msg);
            },
            complete: function() {
                $previewLoader.hide();
            }
        });
    }

    function showPreviewError(message) {
        $previewTitle.html('<i class="fas fa-exclamation-triangle mr-2 text-danger"></i>Error');
        $previewBody.html('<div class="empty-state"><i class="fas fa-exclamation-circle text-danger"></i><p>' + escapeHtml(message) + '</p></div>');
        $previewDownload.hide();
    }

    function escapeHtml(text) {
        if (!text) return '';
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Handle browser back/forward
    $(window).on('popstate', function(e) {
        var state = e.originalEvent.state;
        if (state && state.docId) {
            loadPreview(state.docId);
            highlightRowById(state.docId);
        }
    });

    // ============================================================
    // CRUD - Add / Edit Modal
    // ============================================================

    // Add button
    $('.btn-add-doc').on('click', function(e) {
        e.preventDefault();
        loadModal(null);
    });

    function loadModal(encId) {
        var url = REGULATION.baseUrl + '/read';
        if (encId) {
            url += '?field_id=' + encId;
        }

        $.ajax({
            url: url,
            type: 'GET',
            success: function(html) {
                $('#regulation-modal-content').html(html);
                $('#regulation-modal').modal('show');
            },
            error: function() {
                toastr.error('Gagal memuat form. Silakan coba lagi.');
            }
        });
    }

    // ============================================================
    // CRUD - Delete
    // ============================================================

    $('#btn-confirm-delete').on('click', function() {
        var encId = $(this).data('id');
        if (!encId) return;

        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menghapus...');

        $.ajax({
            url: REGULATION.baseUrl + '/delete',
            type: 'POST',
            data: {
                _method: 'DELETE',
                _token: encId,
                csrf_token: csrf_hash
            },
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    toastr.error(response.error);
                } else {
                    toastr.success(response.result);
                    $('#delete-regulation-modal').modal('hide');
                    table.ajax.reload(null, false);
                    // Reset preview panel
                    $previewTitle.html('<i class="fas fa-file-pdf mr-2 text-danger"></i>Pratinjau Dokumen');
                    $previewBody.html('<div class="empty-state"><i class="fas fa-file-pdf"></i><p>Pilih dokumen dari daftar untuk melihat pratinjau.</p></div>');
                    $previewDownload.hide();
                }
                if (response.csrf_hash) {
                    csrf_hash = response.csrf_hash;
                }
            },
            error: function(xhr) {
                var msg = 'Gagal menghapus dokumen.';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    msg = xhr.responseJSON.error;
                }
                toastr.error(msg);
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="fas fa-trash-alt mr-1"></i> Hapus');
            }
        });
    });

    // Reset delete modal on hide
    $('#delete-regulation-modal').on('hidden.bs.modal', function() {
        $('#btn-confirm-delete').removeData('id');
    });
});
