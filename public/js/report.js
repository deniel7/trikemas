var advanceElements = (function() {

    var init = function() {
        _applyDatePicker();
    };

    var _applyDatePicker = function() {

        $("#tanggal").datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true
        }).on("change", function() {
            // Revalidate form
            $('#frmData').formValidation('revalidateField', 'tanggal');
        });

        $("#hingga").datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true
        }).on("change", function() {
            // Revalidate form
            $('#frmData').formValidation('revalidateField', 'tanggal');
        });
    };

    return {
        init: init
    };

})();

var validation = (function() {

    var init = function() {
        _applyValidation();
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
                    ppn: {
                        validators: {
                            notEmpty: {
                                message: 'Pilih status PPN'
                            }
                        }
                    },
                    tanggal: {
                        validators: {
                            notEmpty: {
                                message: 'Tanggal harus diisi'
                            }
                        }
                    }
                }
            })
            // submit button always enable
            .on('err.field.fv', function(e, data) {
                data.fv.disableSubmitButtons(false);
            })
            .on('success.field.fv', function(e, data) {
                data.fv.disableSubmitButtons(false);
            })
            .on('success.form.fv', function(e) {
                // Prevent form submission
                e.preventDefault();

                var url = "";
                var ppn = $("#ppn option:selected").val();
                var tanggal = $("#tanggal").val();
                var hingga = $("#hingga").val();
                if (hingga !== "") {
                    url = "/report/penjualan/preview/" + ppn + "/" + tanggal + "/" + hingga;
                } else {
                    url = "/report/penjualan/preview/" + ppn + "/" + tanggal;
                }

                window.open(url, "_blank");
            });

    };

    return {
        init: init
    };

})();

var validationPenerimaanPembayaran = (function() {

    var init = function() {
        _applyValidation();
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
                    tanggal: {
                        validators: {
                            notEmpty: {
                                message: 'Tanggal harus diisi'
                            }
                        }
                    },
                    hingga: {
                        validators: {
                            notEmpty: {
                                message: 'Hingga harus diisi'
                            }
                        }
                    }
                }
            })
            // submit button always enable
            .on('err.field.fv', function(e, data) {
                data.fv.disableSubmitButtons(false);
            })
            .on('success.field.fv', function(e, data) {
                data.fv.disableSubmitButtons(false);
            })
            .on('success.form.fv', function(e) {
                // Prevent form submission
                e.preventDefault();

                // var bulan = $("#bulan option:selected").val();
                // var tahun = $("#tahun option:selected").val();

                var url = "";
                var angkutan = $("#angkutan option:selected").val();
                var tanggal = $("#tanggal").val();
                var hingga = $("#hingga").val();
                var url = "/report/penerimaan-pembayaran-angkutan/preview/" + angkutan + "/" + tanggal + "/" + hingga;

                window.open(url, "_blank");
            });

    };

    return {
        init: init
    };

})();

var validationSlipGaji = (function() {

    var init = function() {
        _applyValidation();
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
                    tanggal: {
                        validators: {
                            notEmpty: {
                                message: 'Tanggal harus diisi'
                            }
                        }
                    },
                    status: {
                        validators: {
                            notEmpty: {
                                message: 'Status karyawan harus dipilih'
                            }
                        }
                    }
                }
            })
            // submit button always enable
            .on('err.field.fv', function(e, data) {
                data.fv.disableSubmitButtons(false);
            })
            .on('success.field.fv', function(e, data) {
                data.fv.disableSubmitButtons(false);
            })
            .on('success.form.fv', function(e) {
                // Prevent form submission
                e.preventDefault();

                var status = $("#status option:selected").val();
                var tanggal = $("#tanggal").val();
                var hingga = $("#hingga").val();
                var potongan = $("#potongan option:selected").val();
                if (hingga == "") hingga = tanggal;
                var url = "/report/slip-gaji/preview/" + status + "/" + tanggal + "/" + hingga + "/" + potongan;

                window.open(url, "_blank");
            });

    };

    return {
        init: init
    };

})();
