/**
 * CRUD Manager for reusable DataTable + AJAX actions
 * Supports: index, delete, toggle status, restore, and force delete
 *
 * Usage Example:
 * CRUDManager.init({
 *   tableSelector: '.yajra-datatable',
 *   entity: 'banner',
 *   routes: {
 *     data: "{{ route('admin.banner.data') }}",
 *     delete: "{{ route('admin.banner.delete', ':id') }}",
 *     toggleStatus: "{{ route('admin.banner.toggleStatus', ':id') }}",
 *     restore: "{{ route('admin.banner.restore', ':id') }}",
 *     forceDelete: "{{ route('admin.banner.forceDelete', ':id') }}"
 *   },
 *   columns: [
 *       {
            data: 'id',
            render: function(data) {
                return `<input type="checkbox" class="rowCheckbox" value="${data}">`;
            },
            orderable: false,
            searchable: false
        },
 *     {data: 'id', name: 'id'},
 *     {data: 'title', name: 'title'},
 *     {data: 'image', name: 'image', orderable: false, searchable: false},
 *     {data: 'status', name: 'status', orderable: false, searchable: false},
 *     {data: 'action', name: 'action', orderable: false, searchable: false},
 *   ]
 * });
 */
/**
 * CRUD Manager v2.0
 * ---------------------------
 * - Handles DataTables CRUD operations
 * - Supports bulk delete, restore, and force delete
 * - Works with dynamic entity types (banner, logo, etc.)
 * - Includes Select All + Row checkbox support
 */

window.CRUDManager = (function () {
    function init(config) {
        if (!config || !config.routes || !config.routes.data) {
            console.error("CRUDManager: Missing required configuration");
            return;
        }

        const entityName = capitalize(config.entity);
        const tableSelector = config.tableSelector || '.yajra-datatable';
        const table = $(tableSelector).DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: config.routes.data,
                data: function (d) {
                    if (typeof config.extraFilters === 'function') {
                        const filters = config.extraFilters();
                        Object.assign(d, filters);
                    }
                }
            },
            columns: config.columns,
            rowReorder: {
                selector: '.drag-handle',
                update: false
            },
            order: []
        });

        /* ==========================================================
         *  ROW REORDER (SORTING SYSTEM)
         * ========================================================== */
        table.on('row-reorder', function (e, diff, edit) {
            let order = [];
            for (let i = 0; i < diff.length; i++) {
                const rowData = table.row(diff[i].node).data();
                // DataTables provides newPosition (and oldPosition)
                order.push({
                    id: rowData.id,
                    position: parseInt(diff[i].newPosition, 10) // ensure it's a number
                });
            }

            if (order.length > 0) {
                ajaxRequest('POST', config.routes.sort, { order }, (res) => {
                    showToast('Success', res.message || 'Order updated successfully.', 'success');
                    table.ajax.reload(null, false);
                });
            }
        });

        /* ==========================================================
         *  SINGLE ITEM ACTIONS
         * ========================================================== */

        // Delete (Soft)
        $(document).on('click', `.delete${entityName}`, function () {
            const id = $(this).data('id');
            if (!confirm(`Are you sure you want to delete this ${config.entity}?`)) return;

            ajaxRequest('DELETE', config.routes.delete.replace(':id', id), {}, (res) => {
                showToast('Deleted', res.success || 'Record deleted.', 'warning');
                table.ajax.reload(null, false);
            });
        });

        // Toggle Status
        $(document).on('click', `.toggle${entityName}Status`, function () {
            const id = $(this).data('id');
            const checkbox = $(this);

            ajaxRequest('POST', config.routes.toggleStatus.replace(':id', id), {}, (res) => {
                if (res.success) {
                    showToast('Updated', res.status + ' successfully.', 'success');
                } else {
                    showToast('Error', 'Unable to update status.', 'error');
                    // Revert state if failed
                    checkbox.prop('checked', !checkbox.prop('checked'));
                }
            });
        });

        // Restore (Trash)
        $(document).on('click', `.restore${entityName}`, function () {
            const id = $(this).data('id');
            ajaxRequest('POST', config.routes.restore.replace(':id', id), {}, (res) => {
                showToast('Restored', res.success || 'Record restored successfully.', 'success');
                table.ajax.reload(null, false);
            });
        });

        // Force Delete (Trash)
        $(document).on('click', `.forceDelete${entityName}`, function () {
            const id = $(this).data('id');
            if (!confirm(`Are you sure you want to permanently delete this ${config.entity}?`)) return;

            ajaxRequest('DELETE', config.routes.forceDelete.replace(':id', id), {}, (res) => {
                showToast('Permanently Deleted', res.success || 'Record removed permanently.', 'error');
                table.ajax.reload(null, false);
            });
        });

        /* ==========================================================
         *  BULK ACTIONS (Delete / Restore / Force Delete)
         * ========================================================== */

        // Select All checkbox
        $(document).on('change', '#selectAll', function () {
            $('.rowCheckbox').prop('checked', $(this).prop('checked'));
        });

        // Bulk Delete
        $(document).on('click', '#bulkDelete', function () {
            handleBulkAction({
                ids: getSelectedIds(),
                url: config.routes.bulkDelete,
                type: 'DELETE',
                confirmMsg: `Delete selected ${config.entity}s?`,
                successMsg: 'Selected items deleted successfully.',
                icon: 'warning'
            });
        });

        // Bulk Restore (Trash)
        $(document).on('click', '#bulkRestore', function () {
            handleBulkAction({
                ids: getSelectedIds(),
                url: config.routes.bulkRestore,
                type: 'POST',
                confirmMsg: `Restore selected ${config.entity}s?`,
                successMsg: 'Selected items restored successfully.',
                icon: 'success'
            });
        });

        // Bulk Force Delete (Trash)
        $(document).on('click', '#bulkForceDelete', function () {
            handleBulkAction({
                ids: getSelectedIds(),
                url: config.routes.bulkForceDelete,
                type: 'DELETE',
                confirmMsg: `Permanently delete selected ${config.entity}s?`,
                successMsg: 'Selected items permanently deleted.',
                icon: 'error'
            });
        });

        /* ==========================================================
         *  HELPERS
         * ========================================================== */

        function getSelectedIds() {
            let ids = [];
            $('.rowCheckbox:checked').each(function () {
                ids.push($(this).val());
            });
            return ids;
        }

        function handleBulkAction({ ids, url, type, confirmMsg, successMsg, icon }) {
            if (!ids.length) {
                showToast('Warning', 'Please select at least one record.', 'warning');
                return;
            }

            if (!confirm(confirmMsg)) return;

            ajaxRequest(type, url, { ids }, (res) => {
                showToast('Success', res.success || successMsg, icon);
                $('#selectAll').prop('checked', false);
                table.ajax.reload(null, false);
            });
        }

        function ajaxRequest(type, url, data, successCallback, errorCallback) {
            $.ajax({
                url,
                type,
                data: Object.assign({ _token: $('meta[name="csrf-token"]').attr('content') }, data),
                success: successCallback,
                error: function (xhr) {
                    showToast('Error', xhr.responseJSON?.message || 'Something went wrong.', 'error');
                    if (errorCallback) errorCallback(xhr);
                }
            });
        }

        function showToast(heading, text, icon) {
            $.toast({
                heading,
                text,
                showHideTransition: 'slide',
                icon,
                position: 'top-right',
                loaderBg: icon === 'success' ? '#5ba035' : '#bf441d',
                hideAfter: 3000
            });
        }

        function capitalize(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        }
    }

    return { init };
})();
