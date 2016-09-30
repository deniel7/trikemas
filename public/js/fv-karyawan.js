var table;

var validation = (function() {
    
    var init = function() {
        _applyValidation();
        _applyAutoNumeric();
    };
    
    var _applyAutoNumeric = function() {
        $("#uang_makan").autoNumeric("init", {vMin: '0', vMax: '9999999999'})
        .on("keyup", function() {
            $("#frmData").formValidation("revalidateField", $("#uang_makan"));
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
              nik: {
                validators: {
                  notEmpty: {
                    message: 'NIK harus diisi'
                  },
                  stringLength: {
                    max: 100,
                    message: 'Nama tidak boleh lebih dari 100 karakter'
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
                    message: 'Phone harus diisi'
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
                    message: 'Uang Makan harus diisi'
                  }
                }
              },
              uang_lembur: {
                validators: {
                  notEmpty: {
                    message: 'Uang Lembur harus diisi'
                  }
                }
              },
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
        text: "Data barang dengan nama " + nama + " akan dihapus!",
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