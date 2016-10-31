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
            todayHighlight: true,
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
                $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" style="width: 100%;" />');
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
                data: 'created_at',
                name: 'absensi_harians.created_at'
            }, {
                data: 'nama',
                name: 'karyawans.nama'
            }, {
                data: 'jam_masuk',
                name: 'absensi_harians.jam_masuk'
            }, {
                data: 'jam_keluar',
                name: 'absensi_harians.jam_keluar'
            }, {
                data: 'jam_lembur',
                name: 'absensi_harians.jam_lembur'
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
                $('#absensi_modal').find(".modal-title").html("");
                $('#absensi_modal').find(".modal-body").html("");

                /* Insert Data to Modal Body */




                $('#absensi_modal').find(".modal-body").append('<table class="table table-bordered table-striped"><thead><tr><th>NIK</th><th>Nama</th><th>Status Karyawan</th></tr></thead><tbody>');

                $.each(response.records, function(i, record) {
                    $('#absensi_modal').find("tbody").append("<tr><td>" + record.nik + "</td><td>" + record.nama + "</td><td>" + record.keterangan + "</td></tr>");

                });

                $('#absensi_modal').find(".modal-body").append("</table>");


                /* Finally show */
                $('#absensi_modal').modal();
            } else {
                alert('not Saved');
            }

        }).fail(function(response) {

        });
    };

    return {
        init: init,
        showDetail: showDetail
    };

})(commonModule);