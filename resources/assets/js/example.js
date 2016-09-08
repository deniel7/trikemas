var exampleModule = (function (commonModule) {

  var datatableBaseURL = commonModule.datatableBaseURL+'examples';

  var init = function(){
    _applyDatatable();
  }

  var _applyDatatable = function(){
    /* Tambah Input Field di TFOOT */
    $('#datatable tfoot th').each( function() {
      var title = $(this).text();
      if(title != '')
      {
        $(this).html('<input type="text" placeholder="Search '+title+'" />');
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
      columns: [
            {data: 'name', name: 'name'},
            {data: 'description', name: 'description'},
            {data: 'created_at', name: 'created_at'},
            {data: 'updated_at', name: 'updated_at'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
      ]
    });

    /* Ketika Value pada Input di TFOOT berubah, Maka Search Sesuai Kolom */
    table.columns().every( function () {
      var that = this;
      $('input', this.footer() ).on('keyup change', function() {
        if ( that.search() !== this.value ) {
          that
          .search(this.value)
          .draw();
          }
      });
    });

  }
 
  return {
    init : init
  };
 
})(commonModule);