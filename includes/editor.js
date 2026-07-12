jQuery(function ($) {
    var saveTimer = null, dirty = false;

    function status(msg, type) {
        $('#bwsbfe-status').attr('data-type', type || 'ready').html('<span class="bwsbfe-dot"></span> ' + msg);
    }

    $(document).on('click', '.bwsbfe-tab', function () {
        var t = $(this).data('tab');
        $('.bwsbfe-tab').removeClass('active');
        $('.bwsbfe-panel').removeClass('active');
        $(this).addClass('active');
        $('.bwsbfe-panel[data-tab="' + t + '"]').addClass('active');
    });

    $(document).on('input', '.bwsbfe-input', function () {
        var $el = $(this), pid = $el.data('pid'), field = $el.data('field'), val = $el.val();
        dirty = true;
        status('Typing\u2026', 'typing');
        clearTimeout(saveTimer);
        saveTimer = setTimeout(function () {
            status('Saving\u2026', 'saving');
            $.post(bwsbfeData.ajaxUrl, {
                action: 'bwsbfe_save_cell',
                nonce: bwsbfeData.nonce,
                page_id: pid,
                field_name: field,
                value: val
            }, function (r) {
                dirty = false;
                status(r.success ? '\u2705 Saved' : '\u274C Error', r.success ? 'saved' : 'error');
                if (r.success) setTimeout(function () { status('Ready', 'ready'); }, 2000);
            });
        }, 700);
    });

    $(document).on('click', '.bwsbfe-delete-project', function () {
        if (!confirm('Delete this project? Page content will NOT be affected.')) return;
        $.post(bwsbfeData.ajaxUrl, {
            action: 'bwsbfe_delete_project',
            nonce: bwsbfeData.nonce,
            id: $(this).data('id')
        }, function (r) {
            if (r.success) location.reload();
        });
    });

    $('#bwsbfe-save-project').on('click', function () {
        var name = $('#bwsbfe-proj-name').val().trim();
        if (!name) { alert('Please enter a project name.'); return; }
        var ids = [];
        $('#bwsbfe-page-list input:checked').each(function () { ids.push($(this).val()); });
        if (!ids.length) { alert('Please select at least one page.'); return; }
        var $btn = $(this).prop('disabled', true).text('Saving\u2026');
        $.post(bwsbfeData.ajaxUrl, {
            action: 'bwsbfe_save_project',
            nonce: bwsbfeData.nonce,
            id: $(this).data('id'),
            name: name,
            description: $('#bwsbfe-proj-desc').val(),
            page_ids: ids.join(',')
        }, function (r) {
            if (r.success) {
                window.location = bwsbfeData.baseUrl + '&view=sheet&project=' + r.data.id;
            } else {
                $btn.prop('disabled', false).text('Save');
                alert('Error saving project.');
            }
        });
    });

    $('#bwsbfe-page-search').on('input', function () {
        var q = $(this).val().toLowerCase();
        $('#bwsbfe-page-list .bwsbfe-page-item').each(function () {
            $(this).toggle($(this).find('.bwsbfe-page-title').text().toLowerCase().indexOf(q) !== -1);
        });
    });

    $(document).on('change', '#bwsbfe-page-list input[type=checkbox]', function () {
        $(this).closest('.bwsbfe-page-item').toggleClass('checked', this.checked);
        $('#bwsbfe-sel-count').text($('#bwsbfe-page-list input:checked').length);
    });

    var lastBlur = 0;
    $(window).on('blur', function () { lastBlur = Date.now(); });
    $(window).on('focus', function () {
        if (!dirty && lastBlur && (Date.now() - lastBlur) > 30000) {
            status('Refreshing\u2026', 'saving');
            location.reload();
        }
    });

    function closeClearModal() {
        $('#bwsbfe-clear-overlay').fadeOut(150);
        $('#bwsbfe-do-clear').prop('disabled', false).text('Clear Text Fields');
    }

    $('#bwsbfe-clear-fields').on('click', function () {
        if (!bwsbfeData.projectId) return;
        $('#bwsbfe-clear-overlay').fadeIn(150);
    });
    $('#bwsbfe-clear-modal-close, #bwsbfe-clear-modal-cancel').on('click', closeClearModal);
    $('#bwsbfe-clear-overlay').on('click', function (e) {
        if ($(e.target).is('#bwsbfe-clear-overlay')) closeClearModal();
    });

    $('#bwsbfe-do-clear').on('click', function () {
        if (!bwsbfeData.projectId) return;
        if (!confirm('Empty all text fields for every page in this project?\n\nImage fields will be kept. This cannot be undone.')) return;

        var $btn = $(this).prop('disabled', true).text('Clearing\u2026');
        status('Clearing fields\u2026', 'saving');
        $.post(bwsbfeData.ajaxUrl, {
            action: 'bwsbfe_clear_fields',
            nonce: bwsbfeData.nonce,
            project_id: bwsbfeData.projectId
        }, function (r) {
            if (r.success) {
                closeClearModal();
                status('\u2705 Fields cleared', 'saved');
                setTimeout(function () { location.reload(); }, 1200);
            } else {
                var msg = r.data && typeof r.data === 'string' ? r.data : 'Clear failed';
                alert('\u274C ' + msg);
                status('\u274C Clear failed', 'error');
                $btn.prop('disabled', false).text('Clear Text Fields');
            }
        }).fail(function () {
            alert('\u274C Server error while clearing fields.');
            status('\u274C Server error', 'error');
            $btn.prop('disabled', false).text('Clear Text Fields');
        });
    });
});
