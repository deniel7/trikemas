var table;

var detail = (function() {

    var init = function() {
        //_applyBootstrapSelect();
        _applyDatePicker();
        _applyAutoNumeric();
        _applyDiscountOnKeyUp();
        _applyResetModal();
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

    var _applyAutoNumeric = function() {
        $("#discount").autoNumeric("init", {
            vMin: '0',
            vMax: '9999999999999.99'
        });
    };

    var _applyDiscountOnKeyUp = function() {
        $("#discount").on("keyup", function() {
            //calculate netto
            calcNetto();
        });
    };

    var _applyResetModal = function() {
        $("#confirmModal").on("hidden.bs.modal", function(e) {
            $("#id").val("");
            $("#tanggal_bayar").val($("#default_date").val());
            $("#no_srt_jln").val("");
            $("#biaya_angkutan").val("");
            $("#discount").val("");
            $("#jumlah_bayar").val("");
            $("#keterangan").val("");

            // Remove the has-success and has-error class
            var $parent = $("#tanggal_bayar").parents(".form-group");
            $parent.find(".help-block[data-fv-for='tanggal_bayar']").hide();
            $parent.removeClass('has-success').removeClass('has-error');
            $parent = $("#keterangan").parents(".form-group");
            $parent.find(".help-block[data-fv-for='keterangan']").hide();
            $parent.removeClass('has-success').removeClass('has-error');
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
                    //bank_tujuan_bayar: {
                    //  validators: {
                    //    notEmpty: {
                    //      message: 'Bank tujuan harus diisi'
                    //    }
                    //  }
                    //},
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
                var dataString = "tanggal_bayar=" + $("#tanggal_bayar").val() + "&discount=" + $("#discount").val() + "&jumlah_bayar=" + $("#jumlah_bayar").val() + "&keterangan=" + $("#keterangan").val() + "&confirm=" + $("#confirm").val();
                //var dataString = "tgl_bayar=" + $("#tanggal_bayar").val() + "&discount=" + $("#discount").val() + "&jumlah_bayar=" + $("#jumlah_bayar").val() + "&bank_tujuan_bayar=" + $("#bank_tujuan_bayar option:selected").val() + "&keterangan=" + $("#keterangan").val();
                $.ajax({
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader("X-CSRF-Token", $("meta[name='csrf-token']").attr("content"));
                        },
                        type: "POST",
                        data: dataString,
                        url: "/pembayaran-angkutan/complete/" + id
                    })
                    .done(function(data) {
                        // hide the modal
                        $("#confirmModal").modal("hide");
                        if (data === "success") {
                            swal({
                                    title: "",
                                    text: "Surat jalan telah dikonfirmasi pembayarannya.",
                                    type: "success"
                                },
                                function() {
                                    table.draw();
                                });
                        } else {
                            swal("", data, "error");
                        }
                    });

            });
    };

    return {
        init: init
    };

})();

var confirmComplete = function(event, id, nomor, harga) {
    event.preventDefault();

    $("#id").val(id);
    $("#no_srt_jln").val(nomor);
    $("#biaya_angkutan").val(addCommas(harga));
    calcNetto();

    $("#confirmModal").modal("show");
};

var searchbox = (function() {

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

var datatables = (function() {

    var datatablesURL = '/pembayaran-angkutan/list';

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
                "data": function(d) {
                        d.no_surat_jalan = $('#no_surat_jalan').val();
                        d.konsumen_id = $('#konsumen_id option:selected').val();
                        d.tujuan_id = $('#tujuan_id option:selected').val();
                        d.angkutan_id = $('#angkutan_id option:selected').val();
                        d.no_mobil = $('#no_mobil').val();
                    }
                    //"type": "POST"
            },
            'columns': [{
                data: 'no_surat_jalan',
                name: 'no_surat_jalan'
            }, {
                data: 'nama_angkutan',
                name: 'nama_angkutan'
            }, {
                data: 'no_mobil',
                name: 'no_mobil'
            }, {
                data: 'nama_tujuan',
                name: 'nama_tujuan'
            }, {
                data: 'harga_angkutan',
                name: 'harga_angkutan',
                className: "text-right"
            }, {
                data: 'diskon_bayar_angkutan',
                name: 'diskon_bayar_angkutan',
                className: "text-right"
            }, {
                data: 'jumlah_bayar_angkutan',
                name: 'jumlah_bayar_angkutan',
                className: "text-right"
            }, {
                data: 'status_bayar_angkutan',
                name: 'status_bayar_angkutan',
                orderable: false,
                searchable: false
            }, {
                data: 'tanggal_bayar_angkutan',
                name: 'tanggal_bayar_angkutan'
            }, {
                data: 'keterangan_bayar_angkutan',
                name: 'keterangan_bayar_angkutan'
            }, {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }]
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

function calcNetto() {
    var subtotal = $("#biaya_angkutan").val();
    if (subtotal !== "") {
        subtotal = parseFloat(subtotal.replace(/,/g, ""));
    } else {
        subtotal = 0;
    }
    var discount = $("#discount").val();
    if (discount !== "") {
        discount = parseFloat(discount.replace(/,/g, ""));
    } else {
        discount = 0;
    }
    var total = subtotal - discount;

    $("#jumlah_bayar").val(addCommas(total.toFixed(2)));
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