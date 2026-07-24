{{-- ================== LIBRARIES ================== --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js"></script>

<script src="https://cdn.datatables.net/buttons/3.1.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.bootstrap5.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.colVis.min.js"></script>

{{-- ================== SCRIPT ================== --}}
<script>
$(document).ready(function () {

    /* ================== INIT DATATABLE ================== */
    let table = $('{{ $tableId }}').DataTable({
        language: {
            url: 'https://cdn.datatables.net/plug-ins/2.0.8/i18n/ar.json'
        },

        responsive: true,
        pageLength: 10,
        order: [[0, 'desc']],
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'الكل']],

        dom:
            "<'row mb-2'<'col-md-3'l><'col-md-5 text-center'B><'col-md-4'f>>" +
            "<'row'<'col-12'tr>>" +
            "<'row mt-2'<'col-md-6'i><'col-md-6'p>>",

        buttons: [
            { extend: 'copyHtml5',  text: '📋 نسخ',   className: 'btn btn-secondary btn-sm' },
            { extend: 'csvHtml5',   text: '🧾 CSV',   className: 'btn btn-info btn-sm' },
            { extend: 'excelHtml5', text: '📊 Excel', className: 'btn btn-success btn-sm' },
            { extend: 'pdfHtml5',   text: '📄 PDF',   className: 'btn btn-danger btn-sm' },
            { extend: 'print',      text: '🖨 طباعة', className: 'btn btn-dark btn-sm' },
            { extend: 'colvis',     text: '👁 الأعمدة', className: 'btn btn-warning btn-sm' }
        ]
    });

    /* ================== SEARCH INPUT STYLE ================== */
    $('{{ $tableId }}_filter input')
        .addClass('form-control form-control-sm')
        .attr('placeholder', '🔍 بحث سريع...');

    /* ================== OPTIONAL COLUMN FILTERS ================== */

    // فلتر الحالة (العمود 4)
    if ($('#filterEtat').length) {
        $('#filterEtat').on('change', function () {
            table.column(4).search(this.value).draw();
        });
    }

    // فلتر صاحب الملف (العمود 1)
    if ($('#filterOwner').length) {
        $('#filterOwner').on('keyup', function () {
            table.column(1).search(this.value).draw();
        });
    }

    // فلتر الحساب (العمود 2)
    if ($('#filterAccount').length) {
        $('#filterAccount').on('keyup', function () {
            table.column(2).search(this.value).draw();
        });
    }

    // فلتر العمر (العمود 3)
    if ($('#filterAge').length) {
        $('#filterAge').on('keyup change', function () {
            table.column(3).search(this.value).draw();
        });
    }

});
</script>
