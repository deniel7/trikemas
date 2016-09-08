var commonModule = (function() {

    var datatableBaseURL = 'http://dn7store.app/datatable/';

    var select2BaseURL = '/select2/';

    /* Auto Log Out Setelah Sekian Detik */
    var autoLogOut = function() {
        var delay = 1000; // 10 menit (10 * 60 detik * 1000 untuk miliseconds)
        window.setTimeout(function() {
            window.location.href = '/logout';
        }, delay);
    };

    return {
        autoLogOut: autoLogOut,
        datatableBaseURL: datatableBaseURL,
        select2BaseURL: select2BaseURL
    };

    autoLogOut();

})();
var itemModule = (function(commonModule) {

    var datatableBaseURL = commonModule.datatableBaseURL + 'items';
    var select2BaseURLItem = commonModule.select2BaseURL + 'items';


    var existing_model = null;

    var init = function() {
        _applyDatatable();
        _switchModel();
        _applyThousandSeperator();
    };

    var _applyThousandSeperator = function() {
        $("input.number").each(function() {
            var input_name = $(this).data('input');

            $(this).autoNumeric('init', {
                aSep: ',',
                aDec: '.',
                aSign: 'Rp ',
                mDec: '0'
            });

            $(this).on('change keyup', function() {
                var value = $(this).val().replace('Rp ', '');
                $("input[name='" + input_name + "']").val(value);
            });
        });
    };


    var _applySelect2Model = function() {

        $('select.item_ajax').select2({
            minimumInputLength: 2,
            placeholder: "Search existing item",
            ajax: {
                url: select2BaseURLItem,
                dataType: "json",
                type: "POST",
                data: function(params) {
                    var queryParameters = {
                        term: params.term
                    };
                    return queryParameters;
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.model,
                                id: item.model
                            };
                        })
                    };
                }
            }
        });
    };

    var _switchModel = function() {
        $('#switch_model').on('click', function() {
            var model_input = $('#model_input');
            var switch_model_button = $(this);

            if (existing_model == null) {
                existing_model = $("input#model").val();
            }

            if (switch_model_button.html() == '<i class="fa fa-search fa-fw"></i> Search') {
                model_input.html('<select name="model" class="item_ajax form-control"></select>');
                _applySelect2Model();
                switch_model_button.html('<i class="fa fa-keyboard-o fa-fw"></i> Type');
            } else {
                model_input.html('<input type="text" id="model" placeholder="Enter your new item" class="form-control" value="' + existing_model + '" name="model">');
                switch_model_button.html('<i class="fa fa-search fa-fw"></i> Search');
            }
        });
    };

    var _applyDatatable = function() {
        /* Tambah Input Field di TFOOT */
        $('#datatable tfoot th').each(function() {
            var title = $(this).text();
            if (title != '') {
                $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" style="width: 100%;" />');
            }
            if (title == 'Created Date' || title == 'Updated Date') {
                $(this).html('<input type="text" class="datepicker form-control" placeholder="Search ' + title + '" style="width: 100%;" />');
            }
        });

        var table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": datatableBaseURL,
                "type": "POST"
            },
            language: {
                "decimal": ",",
                "thousands": "."
            },
            columns: [{
                data: 'model',
                name: 'items.model'
            }, {
                data: 'size',
                name: 'items.size'
            }, {
                data: 'color',
                name: 'items.color'
            }, {
                data: 'stok',
                name: 'items.stok'
            }, {
                data: 'normal_price',
                name: 'items.normal_price'
            }, {
                data: 'reseller_price',
                name: 'items.reseller_price'
            }, {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }]
        });

        /* Ketika Value pada Input di TFOOT berubah, Maka Search Sesuai Kolom */
        table.columns().every(function() {
            var that = this;
            $('input', this.footer()).on('keyup change', function() {

                var keyword = this.value;

                if (this.placeholder == 'Search Model' || this.placeholder == 'Search Brand') {
                    keyword = keyword.toUpperCase();
                }

                if (that.search() !== keyword) {
                    that
                        .search(keyword)
                        .draw();
                }
            });
        });

    };

    return {
        init: init
    };

})(commonModule);
var transactionModule = (function(commonModule) {

    var datatableBaseURL = commonModule.datatableBaseURL + 'transactions';
    var select2BaseURLItem = commonModule.select2BaseURL + 'items';


    var init = function() {
        _applyDatatable();
        _applyDatepicker();
        _applySelect2Product();
        _submit();
        _disableNumberInputScroll();
        _printTransaction();
    };

    var _disableNumberInputScroll = function() {
        $('form').on('focus', 'input[type=number]', function(e) {
            $(this).on('mousewheel.disableScroll', function(e) {
                e.preventDefault();
            });
        });
        $('form').on('blur', 'input[type=number]', function(e) {
            $(this).off('mousewheel.disableScroll');
        });
    };

    var _saveItem = function() {
        $("#saveItem").on("click", function() {
            console.log($("#supplier_return_detail").serialize());
        });
    };

    var addItem = function() {
        var item_table = $("table#item_table");
        var template_row = $("#new_row").find('tbody');
        var new_row = template_row.clone();
        new_row.find('select').addClass('product_ajax');
        new_row = new_row.html();
        item_table.find('tbody').append(new_row);
        _applySelect2Product();
    };

    var removeItem = function(me) {
        me.closest('tr').remove();
    };

    var _submit = function() {

    };

    var _applyDatepicker = function() {
        $('.datepicker').datepicker({
            weekStart: 1,
            todayHighlight: true,
            clearBtn: true,
            format: 'yyyy-mm-dd',
            autoclose: true
        });
    };

    var _applySelect2Product = function() {

        $('select.product_ajax').select2({
            minimumInputLength: 2,
            ajax: {
                url: select2BaseURLItem,
                dataType: "json",
                type: "POST",
                data: function(params) {
                    /* Based on supplier selection TODO*/
                    var item_id = $("#item_id option:selected").val();

                    var queryParameters = {
                        term: params.term,
                        item_id: item_id
                    };
                    return queryParameters;
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.model + ' - ' + item.size + ' ' + item.color,
                                id: item.id
                            };
                        })
                    };
                }
            }

        });
    };



    var _applyDatatable = function() {
        /* Tambah Input Field di TFOOT */
        $('#datatable tfoot th').each(function() {
            var title = $(this).text();
            if (title !== '') {
                $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" style="width: 100%;" />');
            }
            if (title == 'Created at') {
                $(this).html('<input type="text" class="datepicker form-control" placeholder="Search ' + title + '" style="width: 100%;" />');
            }
        });

        var table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": datatableBaseURL,
                "type": "POST"
            },
            language: {
                "decimal": ",",
                "thousands": "."
            },
            columns: [{
                data: 'check',
                name: 'check',
                orderable: false,
                searchable: false
            }, {
                data: 'name',
                name: 'name'
            }, {
                data: 'address',
                name: 'address'
            }, {
                data: 'source',
                name: 'source'
            }, {
                data: 'total',
                name: 'total'
            }, {
                data: 'created_at',
                name: 'created_at'
            }, {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }]
        });

        /* Ketika Value pada Input di TFOOT berubah, Maka Search Sesuai Kolom */
        table.columns().every(function() {
            var that = this;
            $('input', this.footer()).on('keyup change', function() {
                var keyword = this.value;

                if (this.placeholder == 'Search Code' || this.placeholder == 'Search Supplier') {
                    keyword = keyword.toUpperCase();
                }

                if (this.placeholder == 'Search Status') {
                    keyword = keyword.replace(" ", "_").toLowerCase();
                }

                if (that.search() !== keyword) {
                    that
                        .search(keyword)
                        .draw();
                }
            });
        });

    };

    var _printTransaction = function() {
        $("button#printTransaction").on('click', function() {
            var query = $('form#print input[name="selected_transactions[]"]').serialize();
            window.location = '/transaction/print?' + query;
            // $.ajax({
            //     method: "POST",
            //     url: "/transaction/print",
            //     data: $('form#print').serialize(),
            //     dataType: 'json'
            // }).done(function(response) {
            //     if (response.status == 1) {
            //         swal({
            //             title: "Good!",
            //             text: response.message,
            //             type: "success",
            //             timer: 3000
            //         }, function() {
            //             window.location = "/transaction";
            //         });
            //     } else {
            //         swal({
            //             title: "Oops!",
            //             text: response.message,
            //             type: "error",
            //             timer: 3000
            //         });
            //     }
            // });

        });

    };

    return {
        init: init,
        addItem: addItem,
        removeItem: removeItem
    };

})(commonModule);
var reportModule = (function(commonModule) {

    var datatableBaseURL = commonModule.datatableBaseURL + 'report';
    var select2BaseURLItem = commonModule.select2BaseURL + 'items';


    var init = function() {
        _applyDatatable();
        _applyDatepicker();
        _applySelect2Product();
        _submit();
        _disableNumberInputScroll();

    };

    var _disableNumberInputScroll = function() {
        $('form').on('focus', 'input[type=number]', function(e) {
            $(this).on('mousewheel.disableScroll', function(e) {
                e.preventDefault();
            });
        });
        $('form').on('blur', 'input[type=number]', function(e) {
            $(this).off('mousewheel.disableScroll');
        });
    };

    var _saveItem = function() {
        $("#saveItem").on("click", function() {
            console.log($("#supplier_return_detail").serialize());
        });
    };

    var addItem = function() {
        var item_table = $("table#item_table");
        var template_row = $("#new_row").find('tbody');
        var new_row = template_row.clone();
        new_row.find('select').addClass('product_ajax');
        new_row = new_row.html();
        item_table.find('tbody').append(new_row);
        _applySelect2Product();
    };

    var removeItem = function(me) {
        me.closest('tr').remove();
    };

    var _submit = function() {

    };

    var _applyDatepicker = function() {
        $('.datepicker').datepicker({
            weekStart: 1,
            todayHighlight: true,
            clearBtn: true,
            format: 'yyyy-mm-dd',
            autoclose: true
        });
    };

    var _applySelect2Product = function() {

        $('select.product_ajax').select2({
            minimumInputLength: 2,
            ajax: {
                url: select2BaseURLItem,
                dataType: "json",
                type: "POST",
                data: function(params) {
                    /* Based on supplier selection TODO*/
                    var item_id = $("#item_id option:selected").val();

                    var queryParameters = {
                        term: params.term,
                        item_id: item_id
                    };
                    return queryParameters;
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.model + ' - ' + item.size + ' ' + item.color,
                                id: item.id
                            };
                        })
                    };
                }
            }

        });
    };



    var _applyDatatable = function() {
        /* Tambah Input Field di TFOOT */
        $('#datatable tfoot th').each(function() {
            var title = $(this).text();
            if (title !== '') {
                $(this).html('<input type="text" class="form-control" placeholder="Search ' + title + '" style="width: 100%;" />');
            }
            if (title == 'Created at') {
                $(this).html('<input type="text" class="datepicker form-control" placeholder="Search ' + title + '" style="width: 100%;" />');
            }
        });

        var table = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": datatableBaseURL,
                "type": "POST"
            },
            language: {
                "decimal": ",",
                "thousands": "."
            },
            columns: [{
                data: 'name',
                name: 'name'
            }, {
                data: 'address',
                name: 'address'
            }, {
                data: 'source',
                name: 'source'
            }, {
                data: 'total',
                name: 'total'
            }, {
                data: 'created_at',
                name: 'created_at'
            }, {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }]
        });

        /* Ketika Value pada Input di TFOOT berubah, Maka Search Sesuai Kolom */
        table.columns().every(function() {
            var that = this;
            $('input', this.footer()).on('keyup change', function() {
                var keyword = this.value;

                if (this.placeholder == 'Search Code' || this.placeholder == 'Search Supplier') {
                    keyword = keyword.toUpperCase();
                }

                if (this.placeholder == 'Search Status') {
                    keyword = keyword.replace(" ", "_").toLowerCase();
                }

                if (that.search() !== keyword) {
                    that
                        .search(keyword)
                        .draw();
                }
            });
        });

    };

    return {
        init: init,
        addItem: addItem,
        removeItem: removeItem
    };

})(commonModule);
//# sourceMappingURL=all.js.map
