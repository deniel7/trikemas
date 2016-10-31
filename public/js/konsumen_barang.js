var table;

var validation = (function() {
    
    var init = function() {
        _applyValidation();
        _applyAutoNumeric();
    };
    
    var _applyAutoNumeric = function() {
        $("#harga").autoNumeric("init", {vMin: '0', vMax: '9999999999999.99'})
        .on("keyup", function() {
            $("#frmData").formValidation("revalidateField", $("#harga"));
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
              konsumen_id: {
                validators: {
                  notEmpty: {
                    message: 'Konsumen harus diisi'
                  }
                }
              },
              barang_id: {
                validators: {
                  notEmpty: {
                    message: 'Barang harus diisi'
                  }
                }
              },
              harga: {
                validators: {
                  notEmpty: {
                    message: 'Harga harus diisi'
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

var confirmDelete = function(event, id, konsumen, barang) {
    event.preventDefault();
    
    swal({
        title: "Apakah anda yakin?",
        text: "Data harga dengan konsumen " + konsumen + " dan barang " + barang + " akan dihapus!",
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
            beforeSend: function(xhr) { xhr.setRequestHeader("X-CSRF-Token", $("meta[name='csrf-token']").attr("content")); },
            type: "POST",
            data: {_method: 'DELETE'},
            url: "/konsumen-barang/" + id
        })
        .done(function(data) {
            if (data === "success") {
                // Redraw table
                table.draw();
                swal("", "Data berhasil dihapus.", "success");    
            }
            else {
                swal("", data, "error"); 
            }
        });
    });
    
};

var datatables = (function() {

    var datatablesURL = '/konsumen-barang/list';

    var init = function() {
        _applyDatatable();
    };

    var _applyDatatable = function() {

        table = $('#list').DataTable({
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
                {data: 'konsumen_id', name: 'konsumen_id'},
                {data: 'barang_id', name: 'barang_id', className: "text-right"},
                {data: 'barang_name', name: 'barang_name'},
                {data: 'harga', name: 'harga', className: "text-right"},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });
        
    };
    
    return {
        init: init
    };

})();

var advanceElements = (function() {
    
    var init = function() {
        _applyBootstrapSelect();
    };
    
    var _applyBootstrapSelect = function() {
        
        $(".selectpicker").selectpicker({
            //style: "bg-teal",
            size: 10
        });
    };

    return {
        init: init
    };

})();