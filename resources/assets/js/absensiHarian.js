var absensiHarianModule = (function(commonModule) {

    var datatableBaseURL = commonModule.datatableBaseURL + 'absensi-harians';

    var existing_model = null;

    var init = function() {
        _applyDatatable();
        _applyDatepicker();
        _applyThousandSeperator();

    };

    var _applyAutoNumeric = function() {
        $("#harga").autoNumeric("init", {
                vMin: '0',
                vMax: '9999999999999.99'
            })
            .on("keyup", function() {
                $("#frmData").formValidation("revalidateField", $("#harga"));
            });
    };

    var _applyValidation = function() {
        alert('hello');
        $('#frmData').formValidation({
            framework: "bootstrap",
            button: {
                selector: '#btnSubmit',
                disabled: 'disabled'
            },
            icon: null,
            fields: {
                status_karyawan_id: {
                    validators: {
                        notEmpty: {
                            message: 'Status Karyawan harus diisi'
                        }
                    }
                },
                nik: {
                    validators: {
                        notEmpty: {
                            message: 'NIK harus diisi'
                        }
                    }
                },
                nama: {
                    validators: {
                        notEmpty: {
                            message: 'Nama harus diisi'
                        }
                    }
                },
                alamat: {
                    validators: {
                        notEmpty: {
                            message: 'Alamat harus diisi'
                        }
                    }
                },
                phone: {
                    validators: {
                        notEmpty: {
                            message: 'Alamat harus diisi'
                        }
                    }
                },
                lulusan: {
                    validators: {
                        notEmpty: {
                            message: 'Lulusan harus diisi'
                        }
                    }
                },
                tgl_masuk: {
                    validators: {
                        notEmpty: {
                            message: 'Tanggal Masuk harus diisi'
                        }
                    }
                },
                nilai_upah: {
                    validators: {
                        notEmpty: {
                            message: 'Nilai Upah harus diisi'
                        }
                    }
                },
                uang_makan: {
                    validators: {
                        notEmpty: {
                            message: 'Alamat harus diisi'
                        }
                    }
                },
                uang_lembur: {
                    validators: {
                        notEmpty: {
                            message: 'Uang lembur harus diisi'
                        }
                    }
                },
                norek: {
                    validators: {
                        notEmpty: {
                            message: 'Nomor Rekening harus diisi'
                        }
                    }
                }
            }
        });

    };

    var _applyThousandSeperator = function() {
        $("input.number").each(function() {
            var input_name = $(this).data('input');

            $(this).autoNumeric('init', {
                aSep: ',',
                aDec: '.',
                aSign: 'Rp ',
                mDec: '0'
            });

            $(this).on('change keyup', function() {
                var value = $(this).val().replace('Rp ', '');
                $("input[name='" + input_name + "']").val(value);
            });
        });
    };

    var _applyDatepicker = function() {
        $('.datepicker').datepicker({
            weekStart: 1,
            todayHighlight: false,
            clearBtn: true,
            format: 'yyyy-mm-dd',
            autoclose: true
        });
    };


    var _applyDatatable = function() {
        /* Tambah Input Field di TFOOT */
        $('#datatable tfoot th').each(function() {
            var title = $(this).text();
            if (title != '') {
                $(this).html('<input type="text" class="form-control" placeholder="' + title + '" style="width: 100%;" />');
            }
            if (title == 'Created Date' || title == 'Updated Date' || title == 'Tanggal') {
                $(this).html('<input type="text" class="datepicker form-control" placeholder="Search ' + title + '" style="width: 100%;" />');
            }
        });

        var table = $('#datatable').DataTable({
            stateSave: true,

            fnStateSave: function(oSettings, oData) {
                localStorage.setItem('DataTables_' + window.location.pathname, JSON.stringify(oData));
            },
            fnStateLoad: function(oSettings) {
                var data = localStorage.getItem('DataTables_' + window.location.pathname);
                return JSON.parse(data);
            },

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
                data: 'tanggal',
                name: 'absensi_harians.tanggal'
            }, {
                data: 'nama',
                name: 'karyawans.nama'
            }, {
                data: 'jam_kerja',
                name: 'absensi_harians.jam_kerja'
            }, {
                data: 'jam_masuk',
                name: 'absensi_harians.jam_masuk'
            }, {
                data: 'jam_pulang',
                name: 'absensi_harians.jam_pulang'
            }, {
                data: 'scan_masuk',
                name: 'absensi_harians.scan_masuk'
            }, {
                data: 'scan_pulang',
                name: 'absensi_harians.scan_pulang'
            }, {
                data: 'terlambat',
                name: 'absensi_harians.terlambat'
            }, {
                data: 'plg_cepat',
                name: 'absensi_harians.plg_cepat'
            }, {
                data: 'jenis_lembur',
                name: 'absensi_harians.jenis_lembur'
            }, {
                data: 'jam_lembur',
                name: 'absensi_harians.jam_lembur'
            }, {
                data: 'konfirmasi_lembur',
                name: 'absensi_harians.konfirmasi_lembur'
            }, {
                data: 'jml_kehadiran',
                name: 'absensi_harians.jml_kehadiran'
            }, {
                data: 'status',
                name: 'absensi_harians.status'
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

    var showDetail = function(id) {

        $.ajax({
            method: "GET",
            url: "/upload-absen/" + id,
            dataType: "json",
        }).done(function(response) {

            if (response.status == 1) {

                /* Clear Modal Body */
                $('#detail_modal').find(".modal-title").html("");
                $('#detail_modal').find(".modal-body").html("");

                /* Insert Data to Modal Body */




                $('#detail_modal').find(".modal-body").append('<table class="table table-bordered table-striped"><thead><tr><th>NIK</th><th>Nama</th><th>Departemen</th><th>Scan Masuk</th><th>Scan Keluar</th><th>Lembur</th></tr></thead><tbody>');

                $.each(response.records, function(i, record) {
                    $('#detail_modal').find("tbody").append("<tr><td><input name ='id' type='hidden' value='" + record.id_absen + "' /><input name ='tanggal' type='hidden' value='" + record.tanggal + "' />" + record.karyawan_id + "</td><td>" + record.nama + "</td><td>" + record.departemen + "</td><td>" + record.scan_masuk + "</td><td>" + record.scan_pulang + "</td><td>" + record.jam_lembur + "</td></tr><tr><td colspan='5' align='right'>Jenis Lembur</td><td> <select name='jenis_lembur' class='form-control selectpicker' title='-- Pilih Jenis Lembur --'><option value='0'>Tidak Ada</option><option value='1'>Rutin</option><option value='2'>Biasa</option><option value='3'>Off</option></select></td></tr><tr><td colspan='5' align='right'>Jam Lembur</td><td><input name ='lembur' type='text' /></td></tr>");

                });

                $('#detail_modal').find(".modal-body").append("</table>");


                /* Finally show */
                $('#detail_modal').modal();
            } else {
                alert('Data Pegawai belum ada');
            }

        }).fail(function(response) {

        });
    };

    var showPrint = function(id) {

        $.ajax({
            method: "GET",
            url: "/absensi-harian/" + id,
            dataType: "json",
        }).done(function(response) {

            if (response.status == 1) {

                /* Clear Modal Body */
                $('#print_modal').find(".modal-title").html("");
                $('#print_modal').find(".modal-body").html("");

                /* Insert Data to Modal Body */
                $('#print_modal').find(".modal-body").append('<table class="table table-bordered table-striped"><thead><tr><th>NIK</th><th>Nama</th><th>Departemen</th></thead><tbody>');

                $.each(response.records, function(i, record) {
                    $('#print_modal').find("tbody").append("<tr><td><input name ='id' type='hidden' value='" + record.id + "' /><input name ='tanggal' type='hidden' value='" + record.tanggal + "' />" + record.id + "</td><td>" + record.nama + "</td><td>" + record.departemen + "</td></tr>");

                });

                $('#print_modal').find(".modal-body").append("</table>");


                /* Finally show */
                $('#print_modal').modal();
            } else {
                alert('Data Pegawai belum ada');
            }

        }).fail(function(response) {

        });
    };

    return {
        init: init,
        showDetail: showDetail,
        showPrint: showPrint,
    };

})(commonModule);



//
// Updates "Select all" control in a data table
//
function updateDataTableSelectAllCtrl(table) {
    var $table = table.table().node();
    var $chkbox_all = $('tbody input[type="checkbox"]', $table);
    var $chkbox_checked = $('tbody input[type="checkbox"]:checked', $table);
    var chkbox_select_all = $('thead input[name="select_all"]', $table).get(0);

    // If none of the checkboxes are checked
    if ($chkbox_checked.length === 0) {
        chkbox_select_all.checked = false;
        if ('indeterminate' in chkbox_select_all) {
            chkbox_select_all.indeterminate = false;
        }

        // If all of the checkboxes are checked
    } else if ($chkbox_checked.length === $chkbox_all.length) {
        chkbox_select_all.checked = true;
        if ('indeterminate' in chkbox_select_all) {
            chkbox_select_all.indeterminate = false;
        }

        // If some of the checkboxes are checked
    } else {
        chkbox_select_all.checked = true;
        if ('indeterminate' in chkbox_select_all) {
            chkbox_select_all.indeterminate = true;
        }
    }
}

$(document).ready(function() {
    // Array holding selected row IDs
    var rows_selected = [];
    var table = $('#example').DataTable({
        'ajax': 'https://api.myjson.com/bins/1us28',
        'columnDefs': [{
            'targets': 0,
            'searchable': false,
            'orderable': false,
            'width': '1%',
            'className': 'dt-body-center',
            'render': function(data, type, full, meta) {
                return '<input type="checkbox">';
            }
        }],
        'order': [1, 'asc'],
        'rowCallback': function(row, data, dataIndex) {
            // Get row ID
            var rowId = data[0];

            // If row ID is in the list of selected row IDs
            if ($.inArray(rowId, rows_selected) !== -1) {
                $(row).find('input[type="checkbox"]').prop('checked', true);
                $(row).addClass('selected');
            }
        }
    });

    // Handle click on checkbox
    $('#example tbody').on('click', 'input[type="checkbox"]', function(e) {
        var $row = $(this).closest('tr');

        // Get row data
        var data = table.row($row).data();

        // Get row ID
        var rowId = data[0];

        // Determine whether row ID is in the list of selected row IDs 
        var index = $.inArray(rowId, rows_selected);

        // If checkbox is checked and row ID is not in list of selected row IDs
        if (this.checked && index === -1) {
            rows_selected.push(rowId);

            // Otherwise, if checkbox is not checked and row ID is in list of selected row IDs
        } else if (!this.checked && index !== -1) {
            rows_selected.splice(index, 1);
        }

        if (this.checked) {
            $row.addClass('selected');
        } else {
            $row.removeClass('selected');
        }

        // Update state of "Select all" control
        updateDataTableSelectAllCtrl(table);

        // Prevent click event from propagating to parent
        e.stopPropagation();
    });

    // Handle click on table cells with checkboxes
    $('#example').on('click', 'tbody td, thead th:first-child', function(e) {
        $(this).parent().find('input[type="checkbox"]').trigger('click');
    });

    // Handle click on "Select all" control
    $('thead input[name="select_all"]', table.table().container()).on('click', function(e) {
        if (this.checked) {
            $('#example tbody input[type="checkbox"]:not(:checked)').trigger('click');
        } else {
            $('#example tbody input[type="checkbox"]:checked').trigger('click');
        }

        // Prevent click event from propagating to parent
        e.stopPropagation();
    });

    // Handle table draw event
    table.on('draw', function() {
        // Update state of "Select all" control
        updateDataTableSelectAllCtrl(table);
    });

    // Handle form submission event 
    $('#frm-example').on('submit', function(e) {
        var form = this;

        // Iterate over all selected checkboxes
        $.each(rows_selected, function(index, rowId) {
            // Create a hidden element 
            $(form).append(
                $('<input>')
                .attr('type', 'hidden')
                .attr('name', 'id[]')
                .val(rowId)
            );
        });

        // FOR DEMONSTRATION ONLY     

        // Output form data to a console     
        $('#example-console').text($(form).serialize());
        console.log("Form submission", $(form).serialize());

        // Remove added elements
        $('input[name="id\[\]"]', form).remove();

        // Prevent actual form submission
        e.preventDefault();
    });
});