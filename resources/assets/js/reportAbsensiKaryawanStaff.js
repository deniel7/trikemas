var reportAbsensiKaryawanStaffModule = (function(commonModule) {

    var existing_model = null;

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
                    bulan: {
                        validators: {
                            notEmpty: {
                                message: 'Bulan harus diisi'
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


                var bulan = $("#bulan").val();

                var url = "/report/absensi-karyawan-staff/preview/" + bulan;


                window.open(url, "_blank");
            });

    };


    return {
        init: init
    };

})(commonModule);