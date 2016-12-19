var absensiApprovalModule = (function(commonModule) {

    var datatableBaseURL = commonModule.datatableBaseURL + 'absensi-approvals';

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
                data: 'check',
                name: 'check',
                orderable: false,
                searchable: false
            }, {
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




                $('#detail_modal').find(".modal-body").append('<table class="table table-bordered table-striped"><thead><tr><th>NIK</th><th>Nama</th><th>Departemen</th><th>Scan Masuk</th><th>Scans Keluar</th><th>Jam Lembur</th></tr></thead><tbody>');

                $.each(response.records, function(i, record) {
                    $('#detail_modal').find("tbody").append("<tr><td><input name ='id' type='hidden' value='" + record.id + "' /><input name ='tanggal' type='hidden' value='" + record.tanggal + "' />" + record.nik + "</td><td>" + record.nama + "</td><td>" + record.departemen + "</td><td>" + record.scan_masuk + "</td><td>" + record.scan_pulang + "</td><td>" + record.jam_lembur + "</td></tr>");

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

    var confirmLembur = function() {
        var query = $('form#approve input[name="selected_karyawans[]"]').serialize();
        window.location = '/absensi-approval/confirmAbsen?' + query;



    };

    return {
        init: init,
        showDetail: showDetail,
        confirmLembur: confirmLembur,
    };

})(commonModule);