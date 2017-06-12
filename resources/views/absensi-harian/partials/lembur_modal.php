<div class="modal fade" id="lembur_modal" tabindex="-1" role="dialog">
  
  <form action="absensi-harian/lembur" method="post" enctype="multipart/form-data">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4>Upload Absensi Lembur</h4>
          <hr>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          </button>
          
          <div class="modal-body">
          
            <div class="row">
              <div class="col-lg-12">
                <div class="col-lg-6">
                  Input File Excel Absensi Lembur :
                </div>

                <div class="col-lg-6">
                  <input type="file" name="file">
                </div>
              </div>
            </div><br/>

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