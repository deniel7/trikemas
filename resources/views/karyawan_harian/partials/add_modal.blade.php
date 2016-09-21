<div class="modal fade" id="upload_item" tabindex="-1" role="dialog">
  
  <form action="{{ url('item/upload') }}" method="post" enctype="multipart/form-data">
    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">Upload File</h4>
        </div>
        <div class="modal-body">
          <p><input type="file" name="file"></p>
          <p><a href="{{ asset('templates/item.xlsx') }}"><i class="fa fa-file-excel-o fa-fw"></i> Download Item Excel Template</a></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Upload</button>
        </div>
      </div>
    </div>
  </form>
</div>