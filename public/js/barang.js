var table;

var validation = (function() {
    
    var init = function() {
        _applyValidation();
        _applyAutoNumeric();
    };
    
    var _applyAutoNumeric = function() {
        $("#pcs").autoNumeric("init", {vMin: '0', vMax: '9999999999'})
        .on("keyup", function() {
            $("#frmData").formValidation("revalidateField", $("#pcs"));
        });
        
        $("#berat").autoNumeric("init", {vMin: '0', vMax: '9999.99'})
        .on("keyup", function() {
            $("#frmData").formValidation("revalidateField", $("#berat"));
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
              nama: {
                validators: {
                  notEmpty: {
                    message: 'Nama harus diisi'
                  },
                  stringLength: {
                    max: 100,
                    message: 'Nama tidak boleh lebih dari 100 karakter'
                  }
                }
              },
              pcs: {
                validators: {
                  notEmpty: {
                    message: 'Pcs harus diisi'
                  }
                }
              },
              berat: {
                validators: {
                  notEmpty: {
                    message: 'Berat harus diisi'
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

var confirmDelete = function(event, id, nama) {
    event.preventDefault();
    
    swal({
        title: "Apakah anda yakin?",
        text: "Data jenis barang dengan nama " + nama + " akan dihapus!",
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
            url: "/barang/" + id
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

    var datatablesURL = '/barang/list';

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
                {data: 'id', name: 'id', className: "text-right"},
                {data: 'nama', name: 'nama'},
                {data: 'jenis', name: 'jenis'},
                {data: 'pcs', name: 'pcs', className: "text-right"},
                {data: 'berat', name: 'berat', className: "text-right"},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });
        
    };
    
    return {
        init: init
    };

})();