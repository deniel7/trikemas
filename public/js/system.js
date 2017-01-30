var tableRole;
var tableUser;

// --- role ---

var advanceElementsRole = (function() {
    var init = function() {
        _applyRadioButton();
    };
    
    var _applyRadioButton = function() {
        
        // check / uncheck all
        var checkAll = $('#checkAll'), label = checkAll.next(), label_text = label.text();
        var checkboxes = $('.cb-menu');
        
        label.remove();
        checkAll.iCheck({
            checkboxClass: 'icheckbox_line-aero',
            radioClass: 'iradio_line-aero',
            insert: '<div class="icheck_line-icon"></div>' + label_text
        });
        
        checkboxes.iCheck({
          checkboxClass: 'icheckbox_flat-green',
          radioClass: 'iradio_flat-green'
        });
        
        checkAll.on('ifChecked ifUnchecked', function(event) {
            if (event.type == 'ifChecked') {
                checkboxes.iCheck('check');
            }
            else {
                checkboxes.iCheck('uncheck');
            }
        });
        
        checkboxes.on('ifChanged', function(event) {
            // Revalidate form
            $('#frmData').formValidation('revalidateField', 'menu[]');
            
            var cntMenuChecked = checkboxes.filter(':checked').length;    
            if (cntMenuChecked == checkboxes.length) {
                checkAll.prop('checked', true);
            }
            else {
                checkAll.prop('checked', false);
            }
            checkAll.iCheck('update');
        });
        
    };
    
    return {
        init: init
    };
    
})();

var validationRole = (function() {
    
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
              name: {
                validators: {
                  notEmpty: {
                    message: 'Rolename cannot be empty'
                  },
                  stringLength: {
                    max: 20,
                    message: 'Rolename cannot be more than 20 characters'
                  }
                }
              },
              description: {
                validators: {
                  stringLength: {
                    max: 255,
                    message: 'Description cannot be more than 255 characters'
                  }
                }
              },
              'menu[]': {
                err: '#alertDayMessage',
                validators: {
                    choice: {
                        min: 1,
                        message: 'Please select at lease one menu'
                    }
                }
              }
            }
        });
    
    };
    
    return {
        init: init
    };
    
})();

var confirmDeleteRole = function(event, id, name) {
    event.preventDefault();
    
    swal({
        title: "Are you sure?",
        text: "Rolename " + name + " will be deleted!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "No, cancel please!",
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
    },
    function() {
        $.ajax({
            beforeSend: function(xhr) { xhr.setRequestHeader("X-CSRF-Token", $("meta[name='csrf-token']").attr("content")); },
            type: "POST",
            url: "/system/role/delete/" + id
        })
        .done(function(data) {
            if (data == "success") {
                // Redraw table
                tableRole.draw();
                swal("", "Data deleted successfully.", "success");    
            }
            else {
                swal("", data, "error"); 
            }
        });
    });
    
};

var datatablesRole = (function() {

    var datatablesURL = '/system/role/list';

    var init = function() {
        _applyDatatable();
    };

    var _applyDatatable = function() {

        tableRole = $('#list').DataTable({
            'processing': true,
            'serverSide': true,
            'paging': true,
            //'lengthChange': false,
            //'searching': false,
            'ordering': true,
            'info': true,
            'autoWidth': false,
            'ajax': {
                "url": datatablesURL,
                //"type": "POST"
            },
            'columns': [
                {data: 'rolename', name: 'rolename'},
                {data: 'description', name: 'description'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });
        
    };
    
    return {
        init: init
    };

})();


// --- user ---

var advanceElementsUser = (function() {
    var init = function() {
        _applyChangepwdClick();
        _applyRadioButton();
        _applyFileUpload();
    };
    
    var _applyFileUpload = function() {
        dropifyPhoto = $(".dropify-photo").dropify();
        dropifySignature = $(".dropify-signature").dropify();
        
        dropifyPhoto.on("dropify.afterClear", function(event, element) {
            $("#foto_asal").val("");
        });
        
        dropifySignature.on("dropify.afterClear", function(event, element) {
            $("#tanda_tangan_asal").val("");
        });
        
    };
    
    var _applyChangepwdClick = function() {
        
        $('#changepwd').on('ifChecked ifUnchecked', function(event) {
            if (event.type == 'ifChecked') {
                $('.password').prop('disabled', false);
            }
            else {
                $('.password').prop('disabled', true);
                
                // Remove the has-success and has-error class
                var $parent = $('.password').parents('.form-group');
                $parent.find('.help-block[data-fv-for="password"]').hide();
                $parent.find('.help-block[data-fv-for="confirm_password"]').hide();
                $parent.removeClass('has-success').removeClass('has-error');   
            }
            
            //$('#frmData').formValidation('revalidateField', 'password').formValidation('revalidateField', 'confirm_password');    
        });
        
    };
    
    var _applyRadioButton = function() {
        //Flat red color scheme for iCheck
        $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
          checkboxClass: 'icheckbox_flat-green',
          radioClass: 'iradio_flat-green'
        });
    };
    
    var _applyBootstrapSelect = function() {
        
        $(".selectpicker").selectpicker({
            //style: "bg-teal",
            size: 5
        });
    };
    
    return {
        init: init
    };
    
})();

var validationUser = (function() {
    
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
              username: {
                validators: {
                  notEmpty: {
                    message: 'Username cannot be empty'
                  },
                  stringLength: {
                    max: 20,
                    message: 'Username cannot be more than 20 characters'
                  }
                }
              },
              /*email: {
                validators: {
                  notEmpty: {
                    message: 'Email is required'
                  }
                }
              },*/
              name: {
                validators: {
                  notEmpty: {
                    message: 'Name cannot be empty'
                  },
                  stringLength: {
                    min: 3,
                    max: 40,
                    message: 'Please enter between 3 and 40 length'
                  }
                }
              },
              password: {
                validators: {
                  notEmpty: {
                    message: 'Please enter password'
                  },
                  stringLength: {
                    min: 6,
                    message: 'Please enter more than 6 length'
                  }
                }
              },
              confirm_password: {
                validators: {
                  notEmpty: {
                    message: 'Please confirm password'
                  },
                  identical: {
                    field: 'password',
                    message: 'The password and its confirm are not the same'
                  }
                }
              },
              rolename: {
                validators: {
                  notEmpty: {
                    message: 'Please select rolename'
                  }
                }
              },
              active: {
                validators: {
                  notEmpty: {
                    message: 'Please select active status'
                  }
                }
              }
            }
        })
        // submit button always enable
        .on('err.field.fv', function(e, data) {
            // $(e.target)  --> The field element
            // data.fv      --> The FormValidation instance
            // data.field   --> The field name
            // data.element --> The field element

            data.fv.disableSubmitButtons(false);
        })
        .on('success.field.fv', function(e, data) {
            // e, data parameters are the same as in err.field.fv event handler
            // Despite that the field is valid, by default, the submit button will be disabled if all the following conditions meet
            // - The submit button is clicked
            // - The form is invalid
            data.fv.disableSubmitButtons(false);
        });
    
    };
    
    return {
        init: init
    };
    
})();

var confirmDeleteUser = function(event, id, name) {
    event.preventDefault();
    
    swal({
        title: "Are you sure?",
        text: "Username " + name + " will be deleted!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "No, cancel please!",
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
    },
    function() {
        $.ajax({
            beforeSend: function(xhr) { xhr.setRequestHeader("X-CSRF-Token", $("meta[name='csrf-token']").attr("content")); },
            type: "POST",
            url: "/system/user/delete/" + id
        })
        .done(function(data) {
            if (data == "success") {
                // Redraw table
                tableUser.draw();
                swal("", "Data deleted successfully.", "success");    
            }
            else {
                swal("", data, "error"); 
            }
        });
    });
    
};

var datatablesUser = (function() {

    var datatablesURL = '/system/user/list';

    var init = function() {
        _applyDatatable();
    };

    var _applyDatatable = function() {

        tableUser = $('#list').DataTable({
            'processing': true,
            'serverSide': true,
            'paging': true,
            //'lengthChange': false,
            //'searching': false,
            'ordering': true,
            'info': true,
            'autoWidth': false,
            'ajax': {
                "url": datatablesURL,
                //"type": "POST"
            },
            'columns': [
                {data: 'username', name: 'username'},
                {data: 'name', name: 'name'},
                {data: 'email', name: 'email'},
                {data: 'rolename', name: 'rolename'},
                {data: 'active', name: 'active', className: "text-center"},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });
        
    };
    
    return {
        init: init
    };

})();
