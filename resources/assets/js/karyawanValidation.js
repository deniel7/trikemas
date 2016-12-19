var karyawanValidation = (function(commonModule) {


    var existing_model = null;

    var init = function() {
        _applyDatepicker();
        _applyAutoNumeric();
        _applyValidation();

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


    var _applyDatepicker = function() {
        $('.datepicker').datepicker({
            weekStart: 1,
            todayHighlight: true,
            clearBtn: true,
            format: 'yyyy-mm-dd',
            autoclose: true
        });
    };


    return {
        init: init
    };

})(commonModule);