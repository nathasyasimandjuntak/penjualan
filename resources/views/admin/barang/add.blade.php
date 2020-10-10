@extends('admin.layout.app')

@section('css')

@stop

@section('content')
  
  <div class="box box-primary" id='panel-add'>
    <div class="box-header with-border">
      <h3 class="box-title">{{ ($edit=='false')?'Tambah':'Edit' }} {{ $title }}</h3>
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    <form action="{{ route('barangStore') }}" method="post" enctype="multipart/form-data">
      {{ csrf_field() }}
      <input type="hidden" name="id" id="id" value="{{ $id }}">
      <input type="hidden" name="edit" id="edit" value="{{ $edit }}">
      <div class="box-body">
        <div class="form-group">
          <label for="exampleInputEmail1">Kode</label>
          <input type="text" class="form-control" name="kode_barang" id="kode_barang" value="@if($edit == 'true' && !empty($data)) {{ $data->kode_barang }} @endif" placeholder="Kode">
        </div>
        <div class="form-group">
          <label for="exampleInputEmail1">Nama</label>
          <input type="text" class="form-control" name="nama_barang" id="nama_barang" value="@if($edit == 'true' && !empty($data)) {{ $data->nama_barang }} @endif" placeholder="Nama">
        </div>
        <div class="form-group">
          <label for="exampleInputEmail1">Harga</label>
          <input type="text" class="form-control" name="harga" id="harga" value="@if($edit == 'true' && !empty($data)) {{ $data->harga }} @endif" placeholder="Harga">
        </div>
      </div>
      <!-- /.box-body -->

      <div class="box-footer">
        <button type="button" class="btn btn-warning waves-effect btn-cancel" onclick="window.history.back()"><i class="fa fa-chevron-left fs-14 m-r-5 "></i> Kembali</button>
        <button type="submit" class="btn btn-primary pull-right">Simpan <span class="fa fa-save"></span></button>
      </div>
    </form>
  </div>
@stop
@section('js')
  <script type="text/javascript">
  var onLoad = (function() {
    $('#panel-add').animateCss('bounceInUp');
  })();

  $('.btn-cancel').click(function(e){
    e.preventDefault();
    $('#panel-add').animateCss('bounceOutDown');
    $('.other-page').fadeOut(function(){
      $('.other-page').empty();
      $('.main-layer').fadeIn();
    });
  });

  $('.mulai').datetimepicker({
    //language:  'fr',
    weekStart: 2,
    todayBtn:  1,
    autoclose: 1,
    todayHighlight: 1,
    startView: 2,
    format:'yyyy-mm-dd',
    minView: 2,
    forceParse: 0,
  });

  $('.selesai').datetimepicker({
    //language:  'fr',
    weekStart: 2,
    todayBtn:  1,
    autoclose: 1,
    todayHighlight: 1,
    startView: 2,
    format:'yyyy-mm-dd',
    minView: 2,
    forceParse: 0,
  });
</script>
@stop
