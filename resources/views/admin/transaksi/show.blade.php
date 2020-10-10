<div class="container">
<div class="box box-info">
  <div class="box-header">
    <h3 class="box-title">Detail Transaksi</h3>
  </div>
    <!-- /.box-header -->
    <div class="box-body">
      <form role="form">
        <div class="row">
          <div class="col-sm-6">
            <!-- text input -->
            <div class="form-group">
              <label>No Faktur</label>
              <input type="text" class="form-control" value="{{ $data->no_faktur }}" readonly>
            </div>
            <div class="form-group">
              <label>Tanggal</label>
              <input type="text" class="form-control" value="{{ $data->tgl }}" readonly>
            </div>
            <div class="form-group">
              <label>Nama</label>
              <input type="text" class="form-control" value="{{ $data->nama_pelanggan }}" readonly>
            </div>
            <div class="form-group">
              <button type="button" name="button" class="btn btn-warning btn-cancel">Kembali</button>
            </div>
          </div>
        </div>
      </form>
    </div>
    <!-- /.box-body -->
  </div>
</div>
<script type="text/javascript">
var onLoad = (function() {
  $('#panel-add').animateCss('bounceInDown');
})();

$('.btn-cancel').click(function(e){
  e.preventDefault();
  $('#panel-add').animateCss('bounceOutDown');
  $('.other-page').fadeOut(function(){
    $('.other-page').empty();
    $('.main-layer').fadeIn();
  });
});
</script>
<script src="{{asset('ckeditor/ckeditor.js')}}"></script>
<script type="text/javascript">
CKEDITOR.replace('preview');
CKEDITOR.replace('description');
</script>
