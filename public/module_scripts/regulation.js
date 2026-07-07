/**
 * Regulation Portal Scripts
 * DataTables + PDF preview/annotation + CRUD
 */

$(document).ready(function() {
    var activeCategory = REGULATION.activeCategory || 'all';
    var table;
    var $previewBody = $('#previewBody');
    var $previewTitle = $('#previewTitle');
    var $previewDownload = $('#previewDownload');
    var $previewLoader = $('#previewLoader');
    var $emptyStateList = $('#emptyStateList');

    var PDF_ANNOTATOR = {
        pdfDoc: null,
        originalBytes: null,
        currentDocId: null,
        currentFileUrl: '',
        pageCount: 0,
        currentPage: 1,
        scale: 1.15,
        annotations: {},
        selectedAnnotationId: null,
        pendingInput: null,
        history: [],
        textToolActive: false,
        isDirty: false,
        lastLoadedSignature: ''
    };

    if (window.pdfjsLib) {
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    }

    table = $('#documentsTable').DataTable({
        processing: true,
        serverSide: true,
        searching: true,
        ordering: true,
        paging: true,
        lengthChange: true,
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, 'All']],
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
            { data: 0, orderable: false, className: 'text-center' },
            { data: 1, orderable: true },
            { data: 2, orderable: true, className: 'text-center' },
            { data: 3, orderable: true, className: 'text-center' },
            { data: 4, orderable: false, className: 'text-center' },
            { data: 5, visible: false }
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
        drawCallback: function() {
            attachRowHandlers();
            var activeDocId = $('#activeDocId').val();
            if (activeDocId) {
                highlightRowById(activeDocId);
            }
        }
    });

    $('#docSearch').on('keyup', function() {
        table.search(this.value).draw();
    });

    $('#docLengthSelect').on('change', function() {
        table.page.len(parseInt($(this).val(), 10)).draw();
    });

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

        $('.btn-view-doc').off('click').on('click', function(e) {
            e.stopPropagation();
            var id = $(this).data('id');
            loadPreview(id);
            highlightRow($(this).closest('tr'));
        });

        $('.btn-edit-doc').off('click').on('click', function(e) {
            e.stopPropagation();
            loadModal($(this).data('id'));
        });

        $('.btn-delete-doc').off('click').on('click', function(e) {
            e.stopPropagation();
            $('#btn-confirm-delete').data('id', $(this).data('id'));
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

    function loadPreview(encId) {
        $previewLoader.show();
        $previewTitle.html('<i class="fas fa-spinner fa-spin mr-2"></i> Memuat dokumen...');
        teardownPdfEditor();

        $.ajax({
            url: REGULATION.baseUrl + '/preview/' + encId,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (res.error) {
                    showPreviewError(res.error);
                    return;
                }

                $previewTitle.html('<i class="fas fa-file mr-2" style="color:var(--portal-accent);"></i>' + escapeHtml(res.title));

                if (REGULATION.canDownload) {
                    $previewDownload.attr('href', REGULATION.baseUrl + '/download/' + res.id).show();
                } else {
                    $previewDownload.hide();
                }

                if (res.is_pdf) {
                    if (REGULATION.canAnnotate && window.pdfjsLib && window.PDFLib) {
                        renderPdfEditorShell();
                        initializePdfEditor(res);
                    } else {
                        renderPdfIframe(res);
                    }
                } else if (['jpg', 'jpeg', 'png', 'gif', 'webp'].indexOf(res.extension) !== -1) {
                    $previewBody.html('<div class="d-flex align-items-center justify-content-center h-100 bg-light"><img src="' + res.file_url + '" style="max-width:100%; max-height:100%; object-fit:contain;" alt="Preview"></div>');
                } else {
                    renderUnsupportedPreview(res);
                }

                var newUrl = REGULATION.viewUrl + '/' + res.id + '?category=' + activeCategory;
                window.history.pushState({ docId: res.id, category: activeCategory }, '', newUrl);
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

    function renderPdfIframe(res) {
        var pdfUrl = res.file_url;
        if (!REGULATION.canDownload) {
            pdfUrl += '#toolbar=0';
        }
        $previewBody.html('<iframe src="' + pdfUrl + '" title="PDF Preview"></iframe>');
    }

    function renderUnsupportedPreview(res) {
        var viewerHtml = '<div class="empty-state"><i class="fas fa-file"></i><p>Pratinjau tidak tersedia untuk format .' + res.extension + '</p>';
        if (REGULATION.canDownload) {
            viewerHtml += '<a href="' + REGULATION.baseUrl + '/download/' + res.id + '" class="btn btn-primary btn-sm mt-2"><i class="fas fa-download mr-1"></i> Download File</a>';
        }
        viewerHtml += '</div>';
        $previewBody.html(viewerHtml);
    }

    function renderPdfEditorShell() {
        var toolbarHtml = '' +
            '<div class="pdf-editor-shell">' +
                '<div class="pdf-editor-toolbar">' +
                    '<div class="pdf-editor-toolbar-group">' +
                        '<button type="button" class="btn btn-sm btn-outline-primary" id="pdfTextTool"><i class="fas fa-font mr-1"></i> Text</button>' +
                        '<button type="button" class="btn btn-sm btn-outline-secondary" id="pdfUndoBtn" disabled><i class="fas fa-undo mr-1"></i> Undo</button>' +
                        '<button type="button" class="btn btn-sm btn-outline-danger" id="pdfDeleteAnnotationBtn" disabled><i class="fas fa-trash-alt mr-1"></i> Hapus Text</button>' +
                    '</div>' +
                    '<div class="pdf-editor-toolbar-group">' +
                        '<label for="pdfFontSize">Size</label>' +
                        '<input type="number" id="pdfFontSize" class="form-control form-control-sm" min="8" max="72" step="1" value="14">' +
                        '<label for="pdfFontColor">Color</label>' +
                        '<input type="color" id="pdfFontColor" class="form-control form-control-sm" value="#1e3a5f">' +
                        '<button type="button" class="btn btn-sm btn-outline-secondary" id="pdfZoomOutBtn"><i class="fas fa-search-minus"></i></button>' +
                        '<span class="pdf-editor-status" id="pdfZoomValue">115%</span>' +
                        '<button type="button" class="btn btn-sm btn-outline-secondary" id="pdfZoomInBtn"><i class="fas fa-search-plus"></i></button>' +
                    '</div>' +
                    '<div class="pdf-editor-toolbar-group">' +
                        '<button type="button" class="btn btn-sm btn-outline-secondary" id="pdfPrevPageBtn"><i class="fas fa-chevron-left mr-1"></i> Prev</button>' +
                        '<span class="pdf-editor-status" id="pdfPageIndicator">Page 1/1</span>' +
                        '<button type="button" class="btn btn-sm btn-outline-secondary" id="pdfNextPageBtn">Next <i class="fas fa-chevron-right ml-1"></i></button>' +
                        '<button type="button" class="btn btn-sm btn-primary" id="pdfSaveBtn"><i class="fas fa-save mr-1"></i> Save</button>' +
                    '</div>' +
                '</div>' +
                '<div class="pdf-editor-readonly">Edit di sini menambahkan text annotation baru ke PDF. Isi dokumen asli tidak diubah per karakter.</div>' +
                '<div class="pdf-editor-canvas-area" id="pdfCanvasArea">' +
                    '<div class="pdf-canvas-stage" id="pdfCanvasStage">' +
                        '<canvas id="pdfRenderCanvas"></canvas>' +
                        '<div class="annotation-layer" id="pdfAnnotationLayer"></div>' +
                        '<div class="annotation-hint" id="pdfAnnotationHint"><i class="fas fa-mouse-pointer"></i><span>Klik area PDF untuk menaruh text</span></div>' +
                    '</div>' +
                '</div>' +
            '</div>';

        $previewBody.html(toolbarHtml);
        bindPdfEditorEvents();
    }

    function bindPdfEditorEvents() {
        $previewBody.off('click.pdfeditor', '#pdfTextTool').on('click.pdfeditor', '#pdfTextTool', function() {
            PDF_ANNOTATOR.textToolActive = !PDF_ANNOTATOR.textToolActive;
            updateTextToolState();
        });

        $previewBody.off('click.pdfeditor', '#pdfUndoBtn').on('click.pdfeditor', '#pdfUndoBtn', function() {
            undoAnnotationChange();
        });

        $previewBody.off('click.pdfeditor', '#pdfDeleteAnnotationBtn').on('click.pdfeditor', '#pdfDeleteAnnotationBtn', function() {
            deleteSelectedAnnotation();
        });

        $previewBody.off('click.pdfeditor', '#pdfZoomInBtn').on('click.pdfeditor', '#pdfZoomInBtn', function() {
            setZoom(PDF_ANNOTATOR.scale + 0.15);
        });

        $previewBody.off('click.pdfeditor', '#pdfZoomOutBtn').on('click.pdfeditor', '#pdfZoomOutBtn', function() {
            setZoom(PDF_ANNOTATOR.scale - 0.15);
        });

        $previewBody.off('click.pdfeditor', '#pdfPrevPageBtn').on('click.pdfeditor', '#pdfPrevPageBtn', function() {
            if (PDF_ANNOTATOR.currentPage > 1) {
                PDF_ANNOTATOR.currentPage -= 1;
                renderCurrentPdfPage();
            }
        });

        $previewBody.off('click.pdfeditor', '#pdfNextPageBtn').on('click.pdfeditor', '#pdfNextPageBtn', function() {
            if (PDF_ANNOTATOR.currentPage < PDF_ANNOTATOR.pageCount) {
                PDF_ANNOTATOR.currentPage += 1;
                renderCurrentPdfPage();
            }
        });

        $previewBody.off('click.pdfeditor', '#pdfSaveBtn').on('click.pdfeditor', '#pdfSaveBtn', function() {
            saveAnnotatedPdf();
        });

        $previewBody.off('click.pdfeditor', '#pdfAnnotationLayer').on('click.pdfeditor', '#pdfAnnotationLayer', function(e) {
            if (!PDF_ANNOTATOR.textToolActive || $(e.target).closest('.annotation-item, .annotation-input').length) {
                return;
            }
            openAnnotationInputAtEvent(e);
        });

        $previewBody.off('click.pdfeditor', '.annotation-item').on('click.pdfeditor', '.annotation-item', function(e) {
            e.stopPropagation();
            selectAnnotation($(this).data('annotationId'));
        });

        $previewBody.off('dblclick.pdfeditor', '.annotation-item').on('dblclick.pdfeditor', '.annotation-item', function(e) {
            e.stopPropagation();
            editAnnotation($(this).data('annotationId'));
        });

        $previewBody.off('keydown.pdfeditor', '.annotation-input').on('keydown.pdfeditor', '.annotation-input', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                e.preventDefault();
                commitPendingAnnotation();
            }
            if (e.key === 'Escape') {
                e.preventDefault();
                cancelPendingAnnotation();
            }
        });

        $previewBody.off('blur.pdfeditor', '.annotation-input').on('blur.pdfeditor', '.annotation-input', function() {
            setTimeout(function() {
                if (PDF_ANNOTATOR.pendingInput) {
                    commitPendingAnnotation();
                }
            }, 100);
        });
    }

    async function initializePdfEditor(res) {
        try {
            var signature = res.id + '|' + res.file_url;
            if (PDF_ANNOTATOR.lastLoadedSignature !== signature) {
                resetAnnotatorState();
                PDF_ANNOTATOR.lastLoadedSignature = signature;
            }

            PDF_ANNOTATOR.currentDocId = res.id;
            PDF_ANNOTATOR.currentFileUrl = res.file_url;

            var response = await fetch(res.file_url, { credentials: 'same-origin' });
            if (!response.ok) {
                throw new Error('Tidak dapat mengambil file PDF.');
            }

            PDF_ANNOTATOR.originalBytes = await response.arrayBuffer();
            PDF_ANNOTATOR.pdfDoc = await pdfjsLib.getDocument({ data: PDF_ANNOTATOR.originalBytes.slice(0) }).promise;
            PDF_ANNOTATOR.pageCount = PDF_ANNOTATOR.pdfDoc.numPages;
            PDF_ANNOTATOR.currentPage = Math.min(PDF_ANNOTATOR.currentPage, PDF_ANNOTATOR.pageCount) || 1;

            updateToolbarState();
            renderCurrentPdfPage();
        } catch (error) {
            showPreviewError(error.message || 'Gagal membuka editor PDF.');
        }
    }

    function resetAnnotatorState() {
        PDF_ANNOTATOR.pdfDoc = null;
        PDF_ANNOTATOR.originalBytes = null;
        PDF_ANNOTATOR.currentDocId = null;
        PDF_ANNOTATOR.currentFileUrl = '';
        PDF_ANNOTATOR.pageCount = 0;
        PDF_ANNOTATOR.currentPage = 1;
        PDF_ANNOTATOR.scale = 1.15;
        PDF_ANNOTATOR.annotations = {};
        PDF_ANNOTATOR.selectedAnnotationId = null;
        PDF_ANNOTATOR.pendingInput = null;
        PDF_ANNOTATOR.history = [];
        PDF_ANNOTATOR.textToolActive = false;
        PDF_ANNOTATOR.isDirty = false;
    }

    function teardownPdfEditor() {
        cancelPendingAnnotation(true);
        $previewBody.off('.pdfeditor');
    }

    async function renderCurrentPdfPage() {
        if (!PDF_ANNOTATOR.pdfDoc) {
            return;
        }

        cancelPendingAnnotation(true);

        var page = await PDF_ANNOTATOR.pdfDoc.getPage(PDF_ANNOTATOR.currentPage);
        var viewport = page.getViewport({ scale: PDF_ANNOTATOR.scale });
        var canvas = document.getElementById('pdfRenderCanvas');
        var context = canvas.getContext('2d');
        canvas.width = viewport.width;
        canvas.height = viewport.height;
        canvas.style.width = viewport.width + 'px';
        canvas.style.height = viewport.height + 'px';

        var $stage = $('#pdfCanvasStage');
        var $layer = $('#pdfAnnotationLayer');
        $stage.css({ width: viewport.width + 'px', height: viewport.height + 'px' });
        $layer.css({ width: viewport.width + 'px', height: viewport.height + 'px' });

        await page.render({ canvasContext: context, viewport: viewport }).promise;
        renderAnnotationsForCurrentPage();
        updateToolbarState();
    }

    function renderAnnotationsForCurrentPage() {
        var $layer = $('#pdfAnnotationLayer');
        var pageAnnotations = PDF_ANNOTATOR.annotations[PDF_ANNOTATOR.currentPage] || [];
        var html = '';

        $.each(pageAnnotations, function(_, annotation) {
            var selectedClass = annotation.id === PDF_ANNOTATOR.selectedAnnotationId ? ' is-selected' : '';
            html += '<div class="annotation-item' + selectedClass + '" data-annotation-id="' + annotation.id + '" style="left:' + annotation.x + 'px;top:' + annotation.y + 'px;font-size:' + annotation.fontSize + 'px;color:' + annotation.color + ';max-width:' + annotation.maxWidth + 'px;">' + nl2br(escapeHtml(annotation.text)) + '</div>';
        });

        $layer.html(html);
        updateToolbarState();
    }

    function openAnnotationInputAtEvent(event, annotation) {
        cancelPendingAnnotation(true);

        var $layer = $('#pdfAnnotationLayer');
        var offset = $layer.offset();
        var rawX = event.pageX - offset.left;
        var rawY = event.pageY - offset.top;
        var maxWidth = Math.max(120, $layer.width() - rawX - 12);

        var pending = {
            page: PDF_ANNOTATOR.currentPage,
            id: annotation ? annotation.id : buildAnnotationId(),
            x: annotation ? annotation.x : clamp(rawX, 8, $layer.width() - 24),
            y: annotation ? annotation.y : clamp(rawY, 8, $layer.height() - 24),
            text: annotation ? annotation.text : '',
            fontSize: annotation ? annotation.fontSize : parseInt($('#pdfFontSize').val(), 10) || 14,
            color: annotation ? annotation.color : ($('#pdfFontColor').val() || '#1e3a5f'),
            maxWidth: annotation ? annotation.maxWidth : maxWidth,
            isEditing: !!annotation
        };

        PDF_ANNOTATOR.pendingInput = pending;

        var $input = $('<textarea class="annotation-input" rows="3" placeholder="Ketik text, lalu Ctrl+Enter atau klik di luar"></textarea>');
        $input.val(pending.text);
        $input.css({
            left: pending.x + 'px',
            top: pending.y + 'px',
            color: pending.color,
            fontSize: pending.fontSize + 'px',
            maxWidth: pending.maxWidth + 'px'
        });

        $layer.append($input);
        $input.trigger('focus');
        updateToolbarState();
    }

    function commitPendingAnnotation() {
        if (!PDF_ANNOTATOR.pendingInput) {
            return;
        }

        var $input = $('.annotation-input');
        var text = $.trim($input.val());
        var pending = PDF_ANNOTATOR.pendingInput;
        var inputWidth = parseInt($input.outerWidth(), 10) || pending.maxWidth;
        pending.fontSize = parseInt($('#pdfFontSize').val(), 10) || pending.fontSize;
        pending.color = $('#pdfFontColor').val() || pending.color;
        pending.maxWidth = Math.max(120, inputWidth);

        $input.remove();
        PDF_ANNOTATOR.pendingInput = null;

        if (!text) {
            renderAnnotationsForCurrentPage();
            return;
        }

        pushHistorySnapshot();

        var pageAnnotations = PDF_ANNOTATOR.annotations[pending.page] || [];
        var existingIndex = findAnnotationIndex(pageAnnotations, pending.id);
        var annotationData = {
            id: pending.id,
            x: pending.x,
            y: pending.y,
            text: text,
            fontSize: pending.fontSize,
            color: pending.color,
            maxWidth: pending.maxWidth
        };

        if (existingIndex !== -1) {
            pageAnnotations[existingIndex] = annotationData;
        } else {
            pageAnnotations.push(annotationData);
        }

        PDF_ANNOTATOR.annotations[pending.page] = pageAnnotations;
        PDF_ANNOTATOR.selectedAnnotationId = annotationData.id;
        PDF_ANNOTATOR.isDirty = true;
        renderAnnotationsForCurrentPage();
    }

    function cancelPendingAnnotation(silent) {
        if ($('.annotation-input').length) {
            $('.annotation-input').remove();
        }
        PDF_ANNOTATOR.pendingInput = null;
        if (!silent) {
            renderAnnotationsForCurrentPage();
        }
    }

    function editAnnotation(annotationId) {
        var annotation = getAnnotationById(PDF_ANNOTATOR.currentPage, annotationId);
        if (!annotation) {
            return;
        }

        PDF_ANNOTATOR.selectedAnnotationId = annotationId;
        $('#pdfFontSize').val(annotation.fontSize);
        $('#pdfFontColor').val(annotation.color);
        PDF_ANNOTATOR.textToolActive = false;
        updateTextToolState();

        var eventMock = {
            pageX: $('#pdfAnnotationLayer').offset().left + annotation.x,
            pageY: $('#pdfAnnotationLayer').offset().top + annotation.y
        };
        openAnnotationInputAtEvent(eventMock, annotation);
    }

    function selectAnnotation(annotationId) {
        PDF_ANNOTATOR.selectedAnnotationId = annotationId;
        var annotation = getAnnotationById(PDF_ANNOTATOR.currentPage, annotationId);
        if (annotation) {
            $('#pdfFontSize').val(annotation.fontSize);
            $('#pdfFontColor').val(annotation.color);
        }
        renderAnnotationsForCurrentPage();
    }

    function deleteSelectedAnnotation() {
        if (!PDF_ANNOTATOR.selectedAnnotationId) {
            return;
        }

        var pageAnnotations = PDF_ANNOTATOR.annotations[PDF_ANNOTATOR.currentPage] || [];
        var annotationIndex = findAnnotationIndex(pageAnnotations, PDF_ANNOTATOR.selectedAnnotationId);
        if (annotationIndex === -1) {
            return;
        }

        pushHistorySnapshot();
        pageAnnotations.splice(annotationIndex, 1);
        PDF_ANNOTATOR.annotations[PDF_ANNOTATOR.currentPage] = pageAnnotations;
        PDF_ANNOTATOR.selectedAnnotationId = null;
        PDF_ANNOTATOR.isDirty = true;
        renderAnnotationsForCurrentPage();
    }

    function undoAnnotationChange() {
        if (!PDF_ANNOTATOR.history.length) {
            return;
        }

        var snapshot = PDF_ANNOTATOR.history.pop();
        PDF_ANNOTATOR.annotations = snapshot.annotations;
        PDF_ANNOTATOR.selectedAnnotationId = null;
        PDF_ANNOTATOR.isDirty = hasAnyAnnotation();
        renderAnnotationsForCurrentPage();
    }

    function pushHistorySnapshot() {
        PDF_ANNOTATOR.history.push({
            annotations: deepClone(PDF_ANNOTATOR.annotations)
        });

        if (PDF_ANNOTATOR.history.length > 25) {
            PDF_ANNOTATOR.history.shift();
        }
    }

    function setZoom(nextZoom) {
        PDF_ANNOTATOR.scale = clamp(nextZoom, 0.6, 2.2);
        renderCurrentPdfPage();
    }

    async function saveAnnotatedPdf() {
        if (!PDF_ANNOTATOR.currentDocId || !PDF_ANNOTATOR.originalBytes) {
            toastr.error('PDF belum siap disimpan.');
            return;
        }

        cancelPendingAnnotation(true);

        var $saveBtn = $('#pdfSaveBtn');
        $saveBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Saving...');

        try {
            var pdfDoc = await PDFLib.PDFDocument.load(PDF_ANNOTATOR.originalBytes.slice(0));
            var font = await pdfDoc.embedFont(PDFLib.StandardFonts.Helvetica);

            $.each(PDF_ANNOTATOR.annotations, function(pageNumber, pageAnnotations) {
                var page = pdfDoc.getPage(parseInt(pageNumber, 10) - 1);
                if (!page) {
                    return;
                }
                var pageHeight = page.getHeight();

                $.each(pageAnnotations, function(_, annotation) {
                    var rgb = hexToRgb(annotation.color);
                    var lines = annotation.text.split(/\r?\n/);
                    var pdfFontSize = annotation.fontSize / PDF_ANNOTATOR.scale;
                    var pdfX = annotation.x / PDF_ANNOTATOR.scale;
                    var pdfY = annotation.y / PDF_ANNOTATOR.scale;
                    var pdfMaxWidth = annotation.maxWidth / PDF_ANNOTATOR.scale;
                    var lineHeight = pdfFontSize + 2;

                    $.each(lines, function(index, lineText) {
                        page.drawText(lineText, {
                            x: pdfX,
                            y: pageHeight - pdfY - pdfFontSize - (index * lineHeight),
                            size: pdfFontSize,
                            font: font,
                            color: PDFLib.rgb(rgb.r / 255, rgb.g / 255, rgb.b / 255),
                            maxWidth: pdfMaxWidth,
                            lineHeight: lineHeight
                        });
                    });
                });
            });

            var savedBytes = await pdfDoc.save();
            var fileBlob = new Blob([savedBytes], { type: 'application/pdf' });
            var formData = new FormData();
            formData.append('token', PDF_ANNOTATOR.currentDocId);
            formData.append('annotated_pdf', fileBlob, 'annotated.pdf');
            formData.append(REGULATION.csrfName, csrf_hash);

            $.ajax({
                url: REGULATION.baseUrl + '/save-annotated',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.csrf_hash) {
                        csrf_hash = response.csrf_hash;
                        REGULATION.csrfHash = response.csrf_hash;
                    }

                    if (response.error) {
                        toastr.error(response.error);
                        return;
                    }

                    toastr.success(response.result || 'Perubahan PDF berhasil disimpan.');
                    PDF_ANNOTATOR.isDirty = false;
                    PDF_ANNOTATOR.lastLoadedSignature = '';
                    loadPreview(PDF_ANNOTATOR.currentDocId);
                    table.ajax.reload(null, false);
                },
                error: function(xhr) {
                    var msg = 'Gagal menyimpan anotasi PDF.';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        msg = xhr.responseJSON.error;
                    }
                    toastr.error(msg);
                },
                complete: function() {
                    $saveBtn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save');
                }
            });
        } catch (error) {
            toastr.error(error.message || 'Gagal memproses PDF.');
            $saveBtn.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Save');
        }
    }

    function updateToolbarState() {
        $('#pdfPageIndicator').text('Page ' + PDF_ANNOTATOR.currentPage + '/' + (PDF_ANNOTATOR.pageCount || 1));
        $('#pdfPrevPageBtn').prop('disabled', PDF_ANNOTATOR.currentPage <= 1);
        $('#pdfNextPageBtn').prop('disabled', PDF_ANNOTATOR.currentPage >= PDF_ANNOTATOR.pageCount);
        $('#pdfZoomValue').text(Math.round(PDF_ANNOTATOR.scale * 100) + '%');
        $('#pdfUndoBtn').prop('disabled', !PDF_ANNOTATOR.history.length);
        $('#pdfDeleteAnnotationBtn').prop('disabled', !PDF_ANNOTATOR.selectedAnnotationId);
        updateTextToolState();
    }

    function updateTextToolState() {
        $('#pdfTextTool').toggleClass('btn-primary', PDF_ANNOTATOR.textToolActive).toggleClass('btn-outline-primary', !PDF_ANNOTATOR.textToolActive);
        $('#pdfAnnotationHint').toggleClass('is-visible', PDF_ANNOTATOR.textToolActive);
    }

    function getAnnotationById(pageNumber, annotationId) {
        var annotations = PDF_ANNOTATOR.annotations[pageNumber] || [];
        var match = null;

        $.each(annotations, function(_, annotation) {
            if (annotation.id === annotationId) {
                match = annotation;
                return false;
            }
        });

        return match;
    }

    function findAnnotationIndex(annotations, annotationId) {
        var index = -1;
        $.each(annotations, function(i, annotation) {
            if (annotation.id === annotationId) {
                index = i;
                return false;
            }
        });
        return index;
    }

    function hasAnyAnnotation() {
        var hasAnnotation = false;
        $.each(PDF_ANNOTATOR.annotations, function(_, pageAnnotations) {
            if (pageAnnotations && pageAnnotations.length) {
                hasAnnotation = true;
                return false;
            }
        });
        return hasAnnotation;
    }

    function buildAnnotationId() {
        return 'ann_' + Date.now() + '_' + Math.floor(Math.random() * 100000);
    }

    function deepClone(value) {
        return JSON.parse(JSON.stringify(value || {}));
    }

    function hexToRgb(hex) {
        var cleanHex = (hex || '#000000').replace('#', '');
        if (cleanHex.length === 3) {
            cleanHex = cleanHex.charAt(0) + cleanHex.charAt(0) + cleanHex.charAt(1) + cleanHex.charAt(1) + cleanHex.charAt(2) + cleanHex.charAt(2);
        }
        var bigint = parseInt(cleanHex, 16);
        return {
            r: (bigint >> 16) & 255,
            g: (bigint >> 8) & 255,
            b: bigint & 255
        };
    }

    function clamp(value, min, max) {
        return Math.min(Math.max(value, min), max);
    }

    function nl2br(text) {
        return (text || '').replace(/\n/g, '<br>');
    }

    function showPreviewError(message) {
        $previewTitle.html('<i class="fas fa-exclamation-triangle mr-2 text-danger"></i>Error');
        $previewBody.html('<div class="empty-state"><i class="fas fa-exclamation-circle text-danger"></i><p>' + escapeHtml(message) + '</p></div>');
        $previewDownload.hide();
    }

    function escapeHtml(text) {
        if (!text) {
            return '';
        }
        return text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    $(window).on('popstate', function(e) {
        var state = e.originalEvent.state;
        if (state && state.docId) {
            loadPreview(state.docId);
            highlightRowById(state.docId);
        }
    });

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

    $('#btn-confirm-delete').on('click', function() {
        var encId = $(this).data('id');
        if (!encId) {
            return;
        }

        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Menghapus...');

        var payload = {
            _method: 'DELETE',
            _token: encId
        };
        payload[REGULATION.csrfName] = csrf_hash;

        $.ajax({
            url: REGULATION.baseUrl + '/delete',
            type: 'POST',
            data: payload,
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    toastr.error(response.error);
                } else {
                    toastr.success(response.result);
                    $('#delete-regulation-modal').modal('hide');
                    table.ajax.reload(null, false);
                    $previewTitle.html('<i class="fas fa-file-pdf mr-2 text-danger"></i>Pratinjau Dokumen');
                    $previewBody.html('<div class="empty-state"><i class="fas fa-file-pdf"></i><p>Pilih dokumen dari daftar untuk melihat pratinjau.</p></div>');
                    $previewDownload.hide();
                }
                if (response.csrf_hash) {
                    csrf_hash = response.csrf_hash;
                    REGULATION.csrfHash = response.csrf_hash;
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

    $('#delete-regulation-modal').on('hidden.bs.modal', function() {
        $('#btn-confirm-delete').removeData('id');
    });
});
