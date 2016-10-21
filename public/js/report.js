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
            var tanggal = $("#tanggal").val();
            var hingga = $("#hingga").val();
            if (hingga !== "") {
                url = "/report/penjualan/preview/" + tanggal + "/" + hingga;
            }
            else {
                url = "/report/penjualan/preview/" + tanggal;
            }
            
            window.open(url, "_blank");
        });
    
    };
    
    return {
        init: init
    };
    
})();
