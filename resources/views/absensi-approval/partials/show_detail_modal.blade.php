<div class="modal fade" id="detail_modal" tabindex="-1" role="dialog">
<form class="form-horizontal" id="frmData" method="post" action="{{ url('absensi-approval/potongan') }}" autocomplete="off">
    <input name="_method" type="hidden" value="put">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">

    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4>Detail Absensi</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          </button>
          
          <div class="modal-body"></div>

          
        <div id="detail_modal2">
          <h4>Detail Karyawan</h4>
          <div class="modal-body2">
<!--           Diisi di absensiApproval.js -- show detail() -->
          </div>
        </div>
         <p style="text-align:right">Potongan lain-lain : <input type="text" name="potongan" /></p>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Confirm</button>
        </div>
      </div>
    </div>
    </form>
</div>