var ppn_pc = 0.1;
var table;

var detail = (function() {
    
    var init = function() {
        _applyBootstrapSelect();
        _applyDatePicker();
    };
    
    var _applyDatePicker = function() {    
        $("#tanggal_bayar").datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true
        })
        .on("changeDate", function(e) {
            // Revalidate the date field
            $("#frmModal").formValidation("revalidateField", "tanggal_bayar");
        });
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

var validationDetail = (function() {
    
    var init = function() {
        _applyValidation();
    };
    
    var _applyValidation = function() {
        
        $('#frmModal').formValidation({
            framework: "bootstrap",
            button: {
              selector: '#btnSubmit',
              disabled: 'disabled'
            },
            icon: null,
            fields: {
              tanggal_bayar: {
                validators: {
                  notEmpty: {
                    message: 'Tanggal pembayaran harus diisi'
                  }
                }
              },
              bank_tujuan_bayar: {
                validators: {
                  notEmpty: {
                    message: 'Bank tujuan harus diisi'
                  }
                }
              },
              keterangan: {
                validators: {
                  stringLength: {
                    max: 255,
                    message: 'Keterangan tidak boleh lebih dari 255 karakter'
                  }
                }
              }
            }
        })
        // Removed the previous success.form.fv handler
        .off('success.form.fv')
        .on('success.form.fv', function(e) {
            // Prevent form submission
            e.preventDefault();
            
            var id = $("#id").val();
            var dataString = "tgl_bayar=" + $("#tanggal_bayar").val() + "&bank_tujuan_bayar=" + $("#bank_tujuan_bayar option:selected").val() + "&keterangan=" + $("#keterangan").val();
            $.ajax({
                beforeSend: function(xhr) { xhr.setRequestHeader("X-CSRF-Token", $("meta[name='csrf-token']").attr("content")); },
                type: "POST",
                data: dataString,
                url: "/invoice/complete/" + id
            })
            .done(function(data) {
                // hide the modal
                $("#confirmModal").modal("hide");
                if (data === "success") {
                    swal({
                        title: "",
                        text: "Invoice telah dikonfirmasi Complete.",
                        type: "success"
                    },
                    function () {
                        location.href="/invoice";
                    });
                }
                else {
                    swal("", data, "error");
                }
            });
            
        });
    };
    
    return {
        init: init
    };
    
})();
    
var validation = (function() {
    
    var init = function() {
        _applyValidation();
        _applyAutoNumeric();
    };
    
    var _applyAutoNumeric = function() {
        $("#discount").autoNumeric("init", {vMin: '0', vMax: '9999999999999.99'});
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
              konsumen_id: {
                validators: {
                  notEmpty: {
                    message: 'Konsumen harus diisi'
                  }
                }
              },
              no_invoice: {
                validators: {
                  notEmpty: {
                    message: 'No. Invoice harus diisi'
                  },
                  stringLength: {
                    max: 20,
                    message: 'No. invoice tidak boleh lebih dari 20 karakter'
                  }
                }
              },
              tanggal_jatuh_tempo: {
                validators: {
                  notEmpty: {
                    message: 'Tanggal jatuh tempo harus diisi'
                  }
                }
              },
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
              no_mobil: {
                validators: {
                  notEmpty: {
                    message: 'No. mobil harus diisi'
                  },
                  stringLength: {
                    max: 20,
                    message: 'No. mobil tidak boleh lebih dari 20 karakter'
                  }
                }
              },
              no_po: {
                validators: {
                  stringLength: {
                    max: 20,
                    message: 'No. PO tidak boleh lebih dari 20 karakter'
                  }
                }
              },
              no_surat_jalan: {
                validators: {
                  notEmpty: {
                    message: 'No. surat jalan harus diisi'
                  },
                  stringLength: {
                    max: 20,
                    message: 'No. surat jalan tidak boleh lebih dari 20 karakter'
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
        });
    };
    
    return {
        init: init
    };
    
})();

var confirmDelete = function(event, id, nomor) {
    event.preventDefault();
    
    swal({
        title: "Apakah anda yakin?",
        text: "Invoice dengan nomor " + nomor + " akan dihapus!",
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
            url: "/invoice/" + id
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

    var datatablesURL = '/invoice/list';

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
            'filter': false,
            'ajax': {
                "url": datatablesURL,
                "data": function (d) {
                    d.no_invoice = $('#no_invoice').val();
                    d.no_po = $('#no_po').val();
                    d.no_surat_jalan = $('#no_surat_jalan').val();
                    d.tanggal_jatuh_tempo = $('#tanggal_jatuh_tempo').val();
                    d.konsumen_id = $('#konsumen_id option:selected').val();
                    d.tujuan_id = $('#tujuan_id option:selected').val();
                    d.angkutan_id = $('#angkutan_id option:selected').val();
                    d.no_mobil = $('#no_mobil').val();
                }
                //"type": "POST"
            },
            'columns': [
                {data: 'tanggal', name: 'tanggal'},
                {data: 'no_invoice', name: 'no_invoice'},
                {data: 'grand_total', name: 'grand_total', className: "text-right"},
                {data: 'tgl_jatuh_tempo', name: 'tgl_jatuh_tempo'},
                {data: 'notifikasi', name: 'notifikasi', orderable: false, searchable: false},
                {data: 'nama_konsumen', name: 'nama_konsumen'},
                {data: 'nama_konsumen_branch', name: 'nama_konsumen_branch'},
                {data: 'nama_tujuan', name: 'nama_tujuan'},
                {data: 'nama_angkutan', name: 'nama_angkutan'},
                {data: 'no_po', name: 'no_po'},
                {data: 'no_surat_jalan', name: 'no_surat_jalan'},
                {data: 'no_mobil', name: 'no_mobil'},
                {data: 'status_bayar', name: 'status_bayar'},
                {data: 'tanggal_bayar', name: 'tanggal_bayar'},
                {data: 'bank_tujuan_bayar', name: 'bank_tujuan_bayar'},
                {data: 'keterangan', name: 'keterangan'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            'order': [[ 0, 'desc' ]]
        });
        
        $('#search-form').on('submit', function(e) {
            table.draw();
            e.preventDefault();
        });
        
        $("#btnReset").on("click", function() {
            $("#search-form").trigger("reset");
            $(".selectpicker").selectpicker("val", "");
            table.draw();
        });
        
    };
    
    return {
        init: init
    };

})();

var searchbox = (function() {
    
    var init = function() {
        _applyDatePicker();
        _applyBootstrapSelect();
    };
    
    var _applyDatePicker = function() {    
        $("#tanggal_jatuh_tempo").datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true
        });
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
    
var advanceElements = (function() {
    
    var init = function() {
        var last_index = $("#last_index").val();
        
        _applyBootstrapSelect();
        _applyDatePicker();
        _applyAddTableRow();
        _applyRemoveTableRow();
        _applyKonsumenOnChange();
        
        //_applyAutocompleteBarang("1");
        //_applyAutoNumeric("1");
        //_applyBallOnKeyUp("1");
        
        for (var i = 1; i <= last_index; i++) {
            //_applyAutocompleteBarang(i);
            _applyBootstrapSelectBarang(i);
            _applyBarangOnChange(i);
            _applyAutoNumeric(i);
            _applyBallOnKeyUp(i);
        }
        
        _applyDiscountOnKeyUp();
        _applyPpnOnChange();
    };
    
    var _applyKonsumenOnChange = function() {
        $("#konsumen_id").on("change", function() {
            var id = $("option:selected", this).val();
            if (id !== "") {
                $.ajax({
                    type: "GET",
                    url: "/konsumen/branch/" + id 
                })
                .done(function(data) {
                    $("#konsumen_branch_id").html(data).selectpicker('refresh');
                });
            }
        });
    };
    
    var _applyDiscountOnKeyUp = function() {
        $("#discount").on("keyup", function() {
            var sub_total = $("#sub_total").val();
            if (sub_total !== "") {
                sub_total = parseFloat(sub_total.replace(/,/g, ""));
            }
            else {
                sub_total = 0;
            }
            
            var discount = $(this).val();
            if (discount !== "") {
                discount = parseFloat(discount.replace(/,/g, ""));
            }
            else {
                discount = 0;
            }
            
            var total = sub_total - discount;
            var ppn = 0;
            if ($("#chk_ppn").prop("checked")) {
                ppn = ppn_pc * total;    
            }
            var grand_total = total + ppn;
            
            $("#total").val(addCommas(total.toFixed(2)));
            $("#ppn").val(addCommas(ppn.toFixed(2)));
            $("#grand_total").val(addCommas(grand_total.toFixed(2)));
        });
    };
    
    var _applyBallOnKeyUp = function(idx) {
        $("#ball_"+idx).on("keyup", function() {
            var harga = $("#harga_"+idx).val();
            if (harga !== "") {
                harga = parseFloat(harga.replace(/,/g, "")); 
            }
            else {
                harga = 0;
            }
            
            var pcs = "";
            var ball = $(this).val();
            var pcs_in_ball = $("#pcs_in_ball_"+idx).val();
            if (ball !== "") {
                ball = parseInt(ball.replace(/,/g, "")); 
                pcs = pcs_in_ball * ball;
                var jumlah = pcs * harga;
                $("#pcs_"+idx).val(addCommas(pcs));
                $("#jumlah_"+idx).val(addCommas(jumlah.toFixed(2)));
            }
            else {
                $("#pcs_"+idx).val("");
                $("#jumlah_"+idx).val("");
            }
            
            //calculate invoice
            calcInvoice();
        });
    };
    
    var _applyAutoNumeric = function(idx) {
        $("#ball_"+idx).autoNumeric("init", {vMin: '0', vMax: '9999'});
        //$("#pcs_"+idx).autoNumeric("init", {vMin: '0', vMax: '9999'});
        //$("#harga_"+idx).autoNumeric("init", {vMin: '0', vMax: '9999999999999.99'});
        //$("#jumlah_"+idx).autoNumeric("init", {vMin: '0', vMax: '9999999999999.99'});
    };
    
     var _applyBootstrapSelectBarang = function(idx) {
        $("#nama_barang_"+idx).selectpicker({
            //style: "bg-teal",
            size: 10
        });
    };
    
    var _applyBarangOnChange = function(idx) {
        $("#nama_barang_"+idx).on("change", function() {
            var item_id = $("option:selected", this).val();
            if (item_id === "") {
                item_id = -1;
            }
            var konsumen_id = $("#konsumen_id option:selected").val();
            if (konsumen_id === "") {
                konsumen_id = -1;
            }
            $.ajax({
                type: "GET",
                url: "/konsumen-barang/get-price-by-id/" + item_id + "/" + konsumen_id
            })
            .done(function(data) {
                $("#pcs_in_ball_" + idx).val(data.pcs_in_ball);
                $("#pcs_"+idx).val(data.pcs_in_ball);
                var pcs = data.pcs_in_ball;
                var jumlah = pcs * data.harga;
                $("#harga_" + idx).val(addCommas(data.harga));
                $("#jumlah_" + idx).val(addCommas(jumlah.toFixed(2)));
                
                //calculate invoice
                calcInvoice();
            });
        });
    };
    
    var _applyAutocompleteBarang = function(idx) {
        var xhr;
        $("#nama_barang_"+idx).autoComplete({
            source: function(term, response){
                try { xhr.abort(); } catch(e) {}
                xhr = $.getJSON("/barang/autocomplete", { q: term }, function(data) { response(data); });
            },
            minChars: 2,
            onSelect: function(e, term, item) {
                //var item_name = encodeURIComponent(term);
                var item_id = -1;
                var item_name = "unknown";
                var a_item = term.split(" | ");
                if (a_item[0] !== null) {
                    item_id = a_item[0];    
                }
                if (a_item[1] !== null) {
                    item_name = encodeURIComponent(a_item[1]);    
                }
                var konsumen_id = $("#konsumen_id option:selected").val();
                if (konsumen_id === "") {
                    konsumen_id = -1;
                }
                $.ajax({
                    type: "GET",
                    //url: "/konsumen-barang/get-price/" + item_name + "/" + konsumen_id
                    url: "/konsumen-barang/get-price-by-id/" + item_id + "/" + konsumen_id
                })
                .done(function(data) {
                    $("#pcs_in_ball_" + idx).val(data.pcs_in_ball);
                    $("#pcs_"+idx).val(data.pcs_in_ball);
                    var pcs = data.pcs_in_ball;
                    var jumlah = pcs * data.harga;
                    $("#harga_" + idx).val(addCommas(data.harga));
                    $("#jumlah_" + idx).val(addCommas(jumlah.toFixed(2)));
                    
                    //calculate invoice
                    calcInvoice();
                });
            }
        });
        
        //$("#nama_barang_"+idx).on('click', function() {
        //    $(this).select();
        //});
    };
    
    
    var _applyBootstrapSelect = function() {
        
        $(".selectpicker").selectpicker({
            //style: "bg-teal",
            size: 10
        });
    };
    
    var _applyDatePicker = function() {
        
        $("#tanggal").datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true
        });
        
        $("#tanggal_jatuh_tempo").datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true
        });
    };
    
    var _applyAddTableRow = function() {
        $(".btn-add-row").on("click", function(event) {
            event.preventDefault();
            
            //"<input type='text' style='width: 300px;' class='form-control nama_barang' name='nama_barang[]' id='nama_barang_" + curr_index + "' placeholder='Nama barang'>" + 
            var opts = $("#opts").val();
            var curr_index = parseInt($("#last_index").val()) + 1;
            var row = "<tr>" + 
                        "<td>" +
                            "<select name='nama_barang[]' id='nama_barang_" + curr_index + "' class='nama_barang' title='-- Pilih barang --'>" +
                                opts + 
                            "</select>" +
                        "</td>" + 
                        "<td class='text-right'>" + 
                          "<input type='text' style='width: 70px;' class='form-control text-right ball' name='ball[]' id='ball_" + curr_index + "' placeholder='Ball' value='1'>" + 
                        "</td>" + 
                        "<td class='text-right'>" + 
                          "<input type='text' style='width: 70px; background: #EDF7FA;' class='form-control text-right pcs' name='pcs[]' id='pcs_" + curr_index + "' placeholder='Pcs' readonly>" + 
                        "</td>" + 
                        "<td class='text-right'>" + 
                          "<input type='text' style='width: 120px; background: #EDF7FA;' class='form-control text-right harga' name='harga[]' id='harga_" + curr_index + "' placeholder='Harga' readonly>" + 
                        "</td>" + 
                        "<td class='text-right'>" + 
                          "<input type='text' style='width: 120px; background: #EDF7FA;' class='form-control text-right jumlah' name='jumlah[]' id='jumlah_" + curr_index + "' placeholder='Jumlah' readonly>" + 
                        "</td>" + 
                        "<td>" +
                          "<input type='hidden' id='pcs_in_ball_" + curr_index + "'>" +
                          "<a href='#' class='btn btn-danger btn-flat btn-del-row'><i class='fa fa-minus-circle'></i></a>" + 
                        "</td>" + 
                      "</tr>";
            $("#tbl_detail tr:last").after(row);
            $("#last_index").val(curr_index);
            
            _applyRemoveTableRow();
            _applyBootstrapSelectBarang(curr_index);
            _applyBarangOnChange(curr_index);
            //_applyAutocompleteBarang(curr_index);
            _applyAutoNumeric(curr_index);
            _applyBallOnKeyUp(curr_index);
        });  
    };
    
    var _applyRemoveTableRow = function() {
        $(".btn-del-row").on("click", function(event) {
            event.preventDefault();
            //$(this).closest("tr").remove();
            
            $(this).parent().parent().fadeOut("slow", function() {
                $(this).remove();
                
                //calculate invoice
                calcInvoice();
                    
                //"<input type='text' style='width: 300px;' class='form-control nama_barang' name='nama_barang[]' id='nama_barang_1' placeholder='Nama barang'>" + 
                // leave at least one empty row
                var opts = $("#opts").val();
                if ($("#tbl_detail tr").length === 1) {
                    var row = "<tr>" +
                                "<td>" +
                                    "<select name='nama_barang[]' id='nama_barang_1' class='nama_barang' title='-- Pilih barang --'>" +
                                        opts + 
                                    "</select>" +
                                "</td>" + 
                                "<td class='text-right'>" + 
                                  "<input type='text' style='width: 70px;' class='form-control text-right ball' name='ball[]' id='ball_1' placeholder='Ball' value='1'>" + 
                                "</td>" + 
                                "<td class='text-right'>" + 
                                  "<input type='text' style='width: 70px; background: #EDF7FA;' class='form-control text-right pcs' name='pcs[]' id='pcs_1' placeholder='Pcs' readonly>" + 
                                "</td>" + 
                                "<td class='text-right'>" + 
                                  "<input type='text' style='width: 120px; background: #EDF7FA;' class='form-control text-right harga' name='harga[]' id='harga_1' placeholder='Harga' readonly>" + 
                                "</td>" + 
                                "<td class='text-right'>" + 
                                  "<input type='text' style='width: 120px; background: #EDF7FA;' class='form-control text-right jumlah' name='jumlah[]' id='jumlah_1' placeholder='Jumlah' readonly>" + 
                                "</td>" + 
                                "<td>" +
                                  "<input type='hidden' id='pcs_in_ball_1'>" +
                                  "<a href='#' class='btn btn-danger btn-flat btn-del-row'><i class='fa fa-minus-circle'></i></a>" + 
                                "</td>" + 
                              "</tr>";
                    $("#tbl_detail tr:last").after(row);
                    $("#last_index").val("1");
                    
                    _applyRemoveTableRow();
                    _applyBootstrapSelectBarang("1");
                    _applyBarangOnChange("1");
                    //_applyAutocompleteBarang("1");
                    _applyAutoNumeric("1");
                    _applyBallOnKeyUp("1");
                }
            });
            
        });  
    };
    
    var _applyPpnOnChange = function() {
        $("#chk_ppn").on("change", function() {
            var total = $("#total").val();
            if (total !== "") {
                total = parseFloat(total.replace(/,/g, ""));
            }
            else {
                total = 0;
            }
            
            var ppn = 0;
            if ($("#chk_ppn").prop("checked")) {
                ppn = ppn_pc * total;    
            }
            var grand_total = total + ppn;
            
            $("#ppn").val(addCommas(ppn.toFixed(2)));
            $("#grand_total").val(addCommas(grand_total.toFixed(2)));
        });
    };
    
    return {
        init: init
    };
    
    

})();

function calcInvoice() {
    var sub_total = 0;
    var jumlah = 0;
    $(".jumlah").each(function() {
        jumlah = $(this).val();
        if (jumlah !== "") {
            jumlah = parseFloat(jumlah.replace(/,/g, ""));
            sub_total += jumlah;
        }
    });
    
    var discount = $("#discount").val();
    if (discount !== "") {
        discount = parseFloat(discount.replace(/,/g, ""));
    }
    else {
        discount = 0;
    }
    
    var total = sub_total - discount;
    var ppn = 0;
    if ($("#chk_ppn").prop("checked")) {
        ppn = ppn_pc * total;    
    }
    var grand_total = total + ppn;
    
    $("#sub_total").val(addCommas(sub_total.toFixed(2)));
    $("#total").val(addCommas(total.toFixed(2)));
    $("#ppn").val(addCommas(ppn.toFixed(2)));
    $("#grand_total").val(addCommas(grand_total.toFixed(2)));
}

function addCommas(nStr) {
    nStr += '';
    var x = nStr.split('.');
    var x1 = x[0];
    var x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}
