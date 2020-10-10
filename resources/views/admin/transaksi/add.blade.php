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
    <form action="{{ route('transaksiStore') }}" method="post" enctype="multipart/form-data">
      {{ csrf_field() }}
      <input type="hidden" name="id" id="id" value="{{ $id }}">
      <input type="hidden" name="edit" id="edit" value="{{ $edit }}">
      <div class="box-body">
        <div class="form-group">
          <label for="exampleInputEmail1">No Faktur</label>
          <input type="text" class="form-control" name="no_faktur" id="no_faktur" value="@if($edit == 'true' && !empty($data)) {{ $data->no_faktur }} @endif" placeholder="Kode">
        </div>
        <div class="form-group">
          <label for="exampleInputEmail1">Tanggal</label>
          <input type="text" class="form-control" name="tgl" id="tgl" value="@if($edit == 'true' && !empty($data)) {{ $data->tgl }} @endif" placeholder="Tanggal">
        </div>
        <div class="form-group">
          <label>Nama Pelanggan</label>
          <select class="form-control" name="pelanggan_id" id="pelanggan_id" required>
            <option selected disabled>.:: Pilih Nama ::.</option>
            @foreach ($pelanggan as $key)
              <option value="{{ $key->id_pelanggan}}" @if($edit == 'true' && !empty($data)) @if($key->id_pelanggan == $data->pelanggan_id) selected @endif @endif>{{ $key->nama_pelanggan }}</option>
            @endforeach
          </select>
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

  $('#tgl').datetimepicker({
    weekStart: 2,
    todayBtn:  1,
    autoclose: 1,
    todayHighlight: 1,
    startView: 2,
    format:'yyyy-mm-dd',
    minView: 2,
    forceParse: 0,
  });

  $('#pelanggan_id').chosen();
</script>
@stop
