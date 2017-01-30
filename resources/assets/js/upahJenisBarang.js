var upahJenisBarangModule = (function(commonModule) {

    var datatableBaseURL = commonModule.datatableBaseURL + 'upah-jenis-barangs';

    var existing_model = null;

    var init = function() {
        _applyDatatable();
        _applyDatepicker();
        _applyAutoNumeric();
    };



    var confirmDelete = function(event, id) {

        event.preventDefault();

        swal({
                title: "Apakah anda yakin?",
                text: "Data Karyawan akan dihapus!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Ya, lanjutkan!",
                cancelButtonText: "Tidak, batalkan!",
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            },
            function() {
                $.ajax({
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader("X-CSRF-Token", $("meta[name='csrf-token']").attr("content"));
                        },
                        type: "POST",
                        data: {
                            _method: 'DELETE'
                        },
                        url: "/karyawan-harian/" + id
                    })
                    .done(function(data) {
                        if (data === "success") {
                            // Redraw table
                            $('#datatable').DataTable().draw();
                            swal("", "Data berhasil dihapus.", "success");
                        } else {
                            swal("", data, "error");
                        }
                    });
            });

    };

    var _applyDatepicker = function() {
        $('.datepicker').datepicker({
            weekStart: 1,
            todayHighlight: true,
            clearBtn: true,
            format: 'yyyy-mm-dd',
            autoclose: true
        });
    };

    var _applyAutoNumeric = function() {
        $("#uang_makan").autoNumeric("init", {
                vMin: '0',
                vMax: '9999999999999.99'
            })
            .on("keyup", function() {
                $("#frmData").formValidation("revalidateField", $("#uang_makan"));
            });
        $("#nilai_upah").autoNumeric("init", {
                vMin: '0',
                vMax: '9999999999999.99'
            })
            .on("keyup", function() {
                $("#frmData").formValidation("revalidateField", $("#nilai_upah"));
            });
        $("#uang_lembur").autoNumeric("init", {
                vMin: '0',
                vMax: '9999999999999.99'
            })
            .on("keyup", function() {
                $("#frmData").formValidation("revalidateField", $("#uang_lembur"));
            });

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
                data: 'nama',
                name: 'nama'
            }, {
                data: 'upah',
                name: 'upah'
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
        init: init,
        confirmDelete: confirmDelete,
    };

})(commonModule);