var table;

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
              hp: {
                validators: {
                  notEmpty: {
                    message: 'No. HP harus diisi'
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

var confirmDelete = function(event, id, nama, nama_grup) {
    event.preventDefault();
    
    swal({
        title: "Apakah anda yakin?",
        text: "Data konsumen branch dengan nama " + nama + " pada konsumen " + nama_grup + " akan dihapus!",
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
            url: "/konsumen-branch/" + id
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

    var datatablesURL = '/konsumen-branch/list';

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
                {data: 'nama', name: 'konsumen_branches.nama'},
                {data: 'alamat', name: 'konsumen_branches.alamat'},
                {data: 'hp', name: 'konsumen_branches.hp'},
                {data: 'nama_konsumen', name: 'konsumens.nama'},
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