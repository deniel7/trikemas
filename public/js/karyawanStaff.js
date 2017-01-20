var table;

var validations = (function() {

    var init = function() {
        _applyValidation();
        _applyDatepicker();
        _applyAutoNumeric();
    };

    var _applyDatepicker = function() {
        $('.datepicker').datepicker({
                weekStart: 1,
                todayHighlight: true,
                clearBtn: true,
                format: 'yyyy-mm-dd',
                autoclose: true
            })
            .on("changeDate", function(e) {
                // Revalidate the date field
                $("#frmData").formValidation("revalidateField", "tgl_masuk");
            });
    };

    var _applyAutoNumeric = function() {
        $(".number").autoNumeric("init", {
            vMin: '0',
            vMax: '9999999999999.99'
        });
    };

    var _applyValidation = function() {

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
                    norek: {
                        validators: {
                            notEmpty: {
                                message: 'Nomor Rekening harus diisi'
                            }
                        }
                    }
                }
            })
            .off('success.form.fv')
            .on('success.form.fv', function(e) {
                var $form = $(e.target);
                var fv = $form.data('formValidation');

                fv.defaultSubmit();
            });

    };

    return {
        init: init
    };

})();

var confirmDelete = function(event, id) {
    event.preventDefault();

    swal({
            title: "Apakah anda yakin?",
            text: "Invoice dengan nomor " + id + " akan dihapus!",
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
                    url: "/invoice/" + id
                })
                .done(function(data) {
                    if (data === "success") {
                        // Redraw table
                        table.draw();
                        swal("", "Data berhasil dihapus.", "success");
                    } else {
                        swal("", data, "error");
                    }
                });
        });

};