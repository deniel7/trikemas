var absensiPackingModule = (function(commonModule) {

    var datatableBaseURL = commonModule.datatableBaseURL + 'absensi-packings';

    var existing_model = null;

    var init = function() {
        $("#overlayForm").hide();
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

    var createAbsensi = function() {
        /* Show Overlay */
        $("#overlayForm").show();

        /* Send Data */
        _createAbsensi(commonModule.maxRetries, commonModule.retryInterval);
    };

    var _createAbsensi = function(maxRetries, retryInterval) {
        axios.post("/absensi-packing", $('#main_form').serialize())
            .then(function(response) {
                if (response.data.status == 1) {
                    swal({
                        title: "Good!",
                        text: response.data.message,
                        type: "success"

                    }, function() {
                        /* Reset and Focus */
                        $('form#main_form')[0].reset();

                        $("form#main_form:not(.filter) :input:visible:enabled:first").focus();
                    });
                } else {
                    swal({
                        title: "Oops!",
                        text: response.data.message,
                        type: "error"

                    });
                }
                /* Hide Overlay */
                $("#overlayForm").hide();
            })
            .catch(function(error) {
                switch (error.response.status) {
                    case 422:
                        swal({
                            title: "Oops!",
                            text: 'Failed form validation. Please check your input.',
                            type: "error"
                        });
                        break;
                    case 500:
                        swal({
                            title: "Oops!",
                            text: 'Something went wrong.',
                            type: "error"
                        });
                        break;
                }

                // Try again if we haven't reached maxRetries yet
                retryInterval = Math.min(commonModule.maxRetryInterval, retryInterval * commonModule.exponentMultiplication);
                if (maxRetries > 0) {
                    setTimeout(function() {
                        _createReduction(maxRetries - 1, retryInterval);
                    }, retryInterval);
                } else {
                    swal({
                        title: "Oops!",
                        text: "Please check your network connection.",
                        timer: 500,
                        type: "error"
                    });
                    /* Hide Overlay */
                    $("#overlayForm").hide();
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
                data: 'tanggal',
                name: 'absensi_packings.tanggal'
            }, {
                data: 'bagian',
                name: 'absensi_packings.bagian'
            }, {
                data: 'jenis',
                name: 'absensi_packings.jenis'
            }, {
                data: 'jumlah',
                name: 'absensi_packings.jumlah'

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
                    $('#detail_modal').find("tbody").append("<tr><td><input name ='id' type='hidden' value='" + record.id + "' /><input name ='tanggal' type='hidden' value='" + record.tanggal + "' />" + record.id + "</td><td>" + record.nama + "</td><td>" + record.departemen + "</td><td>" + record.scan_masuk + "</td><td>" + record.scan_pulang + "</td><td>" + record.jam_lembur + "</td></tr><tr><td colspan='5' align='right'>Koreksi Lembur</td><td><input name ='lembur' type='text' value='" + record.jam_lembur + "' /></td></tr>");

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
        createAbsensi: createAbsensi
    };

})(commonModule);