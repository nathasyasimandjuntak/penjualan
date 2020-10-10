<div class="container">
<div class="box box-info">
  <div class="box-header">
    <h3 class="box-title">Detail Barang</h3>
  </div>
    <!-- /.box-header -->
    <div class="box-body">
      <form role="form">
        <div class="row">
          <div class="col-sm-6">
            <!-- text input -->
            <div class="form-group">
              <label>Kode</label>
              <input type="text" class="form-control" value="{{ $data->kode_barang }}" readonly>
            </div>
            <div class="form-group">
              <label>Nama</label>
              <input type="text" class="form-control" value="{{ $data->nama_barang }}" readonly>
            </div>
            <div class="form-group">
              <label>Harga</label>
              <input type="text" class="form-control" value="Rp. {{ number_format($data->harga,0,',','.') }}" readonly>
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
