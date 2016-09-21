var karyawanHarianModule = (function(commonModule) {

    var datatableBaseURL = commonModule.datatableBaseURL + 'karyawan-harians';
    
    var existing_model = null;

    var init = function() {
        _applyDatatable();
    };
    
    
    var _applyDatatable = function() {
        /* Tambah Input Field di TFOOT */
        $('#datatable tfoot th').each(function() {
            var title = $(this).text();
            if (title != '') {
                $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" style="width: 100%;" />');
            }
            if (title == 'Created Date' || title == 'Updated Date') {
                $(this).html('<input type="text" class="datepicker form-control" placeholder="Search ' + title + '" style="width: 100%;" />');
            }
        });

        var table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": datatableBaseURL,
                "type": "POST"
            },
            language: {
                "decimal": ",",
                "thousands": "."
            },
            columns: [{
                data: 'status_karyawan_id',
                name: 'status_karyawans.keterangan'
            }, {
                data: 'nik',
                name: 'karyawan_harians.nik'
            }, {
                data: 'nama',
                name: 'karyawan_harians.nama'
            }, {
                data: 'alamat',
                name: 'karyawan_harians.alamat'
            }, {
                data: 'phone',
                name: 'karyawan_harians.phone'
            }, {
                data: 'lulusan',
                name: 'karyawan_harians.lulusan'
            }, {
                data: 'tgl_masuk',
                name: 'karyawan_harians.tgl_masuk'
            }, {
                data: 'nilai_upah',
                name: 'karyawan_harians.nilai_upah'
            }, {
                data: 'uang_makan',
                name: 'karyawan_harians.uang_makan'
            }, {
                data: 'uang_lembur',
                name: 'karyawan_harians.uang_lembur'
            }, {
                data: 'norek',
                name: 'karyawan_harians.norek'
            }, {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }]
        });

        /* Ketika Value pada Input di TFOOT berubah, Maka Search Sesuai Kolom */
        table.columns().every(function() {
            var that = this;
            $('input', this.footer()).on('keyup change', function() {

                var keyword = this.value;

                if (this.placeholder == 'Search Model' || this.placeholder == 'Search Brand') {
                    keyword = keyword.toUpperCase();
                }

                if (that.search() !== keyword) {
                    that
                        .search(keyword)
                        .draw();
                }
            });
        });

    };

    return {
        init: init
    };

})(commonModule);