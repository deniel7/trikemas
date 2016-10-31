<div class="modal fade" id="absensi_modal" tabindex="-1" role="dialog">
  
  <form action="{{ url('absensi-harian/upload') }}" method="post" enctype="multipart/form-data">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          </button>
          
          <div class="modal-body">
          </div>
          <div>
            <p>Tanggal Absen : <input type="text" name="date" class="datepicker form-control" value="{{ date('Y-m-d') }}"  }}"></p>
          </div>
          <div>
            <p><input type="file" name="file"></p>
          </div>
          </div>

        <div class="modal-footer">
       
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Upload</button>
        </div>
      </div>
    </div>
  </form>
</div>