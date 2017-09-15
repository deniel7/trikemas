<div class="modal fade" id="print_modal" tabindex="-1" role="dialog">
  
  <form action="karyawan-tetap/print" method="post" target="_blank">
    
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

          <div class="row">
        <div class="col-lg-12">

        <div class="col-lg-6">Dari : <input type="text" name="dari" class="datepicker form-control" value="{{ date('Y-m-d') }}"  }}"></div>

        <div class="col-lg-6">Ke : <input type="text" name="ke" class="datepicker form-control" value="{{ date('Y-m-d') }}"  }}"></div>

        </div>
        </div>
        <br/>
        <div class="row">
        <div class="col-lg-12">
          <label for="tanggal_lahir" class="col-sm-2 control-label">Potongan</label>
          <div class="col-sm-10">
            <div class="input-group date">
              <select name="potongan" id="potongan">
                <option value="0">tidak ada</option>
                <option value="bpjs">BPJS</option>
              </select>
            </div>
          </div>
        </div>
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