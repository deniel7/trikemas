var reportAbsensiKaryawanHarianModule = (function(commonModule) {

    var existing_model = null;

    var init = function() {
        _applyValidation();
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

                var tanggal = $("#tanggal").val();
                var hingga = $("#hingga").val();

                var url = "/report/absensi-karyawan-harian/preview/" + tanggal + "/" + hingga;


                window.open(url, "_blank");
            });

    };


    return {
        init: init
    };

})(commonModule);