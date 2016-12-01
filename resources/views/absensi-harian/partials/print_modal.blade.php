<div class="modal fade" id="print_modal" tabindex="-1" role="dialog">
  
<form class="form-horizontal" id="frmData" method="post" action="{{ url('upload-absen/lembur') }}" autocomplete="off">
    <input name="_method" type="hidden" value="put">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4>Data Pegawai</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          </button>
          
          <div class="modal-body">
            
          </div>

          <div class="modal-body2">
          </div>

          <div>
            <p>Periode</p>
          </div>
        </div>


        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </div>
    </div>
  </form>
</div>