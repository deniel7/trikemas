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
              angkutan_id: {
                validators: {
                  notEmpty: {
                    message: 'Angkutan harus diisi'
                  }
                }
              },
              tujuan_id: {
                validators: {
                  notEmpty: {
                    message: 'Tujuan harus diisi'
                  }
                }
              },
              harga: {
                validators: {
                  notEmpty: {
                    message: 'Biaya harus diisi'
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

var confirmDelete = function(event, id, angkutan, tujuan) {
    event.preventDefault();
    
    swal({
        title: "Apakah anda yakin?",
        text: "Data angkutan dengan nama " + angkutan + " dan tujuan " + tujuan + " akan dihapus!",
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
            url: "/angkutan-tujuan/" + id
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

    var datatablesURL = '/angkutan-tujuan/list';

    var init = function() {
        _applyDatatable();
    };

    var _applyDatatable = function() {

        table = $('#list').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Biaya Angkutan',
                    exportOptions: {
                        columns: [ 0, 1, 2 ]
                    }
                },
                {
                    extend: 'pdfHtml5',
                    title: 'Biaya Angkutan',
                    orientation: 'portrait',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: [ 0, 1, 2 ]
                    }
                }
            ],
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
                {data: 'nama_angkutan', name: 'angkutans.nama'},
                {data: 'nama_tujuan', name: 'tujuans.kota'},
                {data: 'harga', name: 'angkutan_tujuans.harga', className: "text-right"},
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