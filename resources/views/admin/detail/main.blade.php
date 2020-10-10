@extends('admin.layout.app')
@section('css')

@stop

@section('content')
  <?php
  $routePage = $menuActive;
  $routePage .= (isset($menuSubActive) && !empty($menuSubActive))?$menuSubActive:'';
  ?>
  <section class="content">
  	<div class="row">
  		<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='padding:0px'>
  			<div class="box box-primary main-layer">
  				<div class="col-md-4 col-sm-4 col-xs-12 form-inline main-layer" style='padding:5px'>
            <a href="{{ route($routePage.'Create') }}">
              <button type="button" class="btn btn-sm btn-primary" id="btn-add">
                <span class="fa fa-plus"></span> &nbsp Tambah Detail
              </button>
            </a>
  				</div>
  				<!-- Search -->
  				<div class="col-md-8 col-sm-8 col-xs-12 form-inline main-layer" style="text-align: right;padding:5px;">
  					<div class="form-group">
  						<select class="input-sm form-control input-s-sm inline v-middle option-search" id="search-option"></select>
  					</div>
  					<div class="form-group">
  						<input type="text" class="input-sm form-control" placeholder="Search" id="search">
  					</div>
  				</div>
  				<div class='clearfix'></div>
  				<div class="col-md-12" style='padding:0px'>
  					<!-- Datagrid -->
  					<div class="table-responsive">
  						<table class="table table-striped b-t b-light" id="datagrid"></table>
  					</div>
  					<footer class="panel-footer">
  						<div class="row">
  							<!-- Page Option -->
  							<div class="col-sm-1 hidden-xs">
  								<select class="input-sm form-control input-s-sm inline v-middle option-page" id="option"></select>
  							</div>
  							<!-- Page Info -->
  							<div class="col-sm-6 text-center">
  								<small class="text-muted inline m-t-sm m-b-sm" id="info"></small>
  							</div>
  							<!-- Paging -->
  							<div class="col-sm-5 text-right text-center-xs">
  								<ul class="pagination pagination-sm m-t-none m-b-none" id="paging"></ul>
  							</div>
  						</div>
  					</footer>
  				</div>
  				<div class='clearfix'></div>
  			</div>
  			<div class="other-page"></div>
  			<div class="modal-dialog"></div>
  		</div>
  	</section>
@stop

@section('js')
  <script type="text/javascript">
    var datagrid = $("#datagrid").datagrid({
      url                     : "{!! route('detailDatagrid') !!}",
      primaryField            : 'id_detail',
      rowNumber               : true,
      rowCheck                : false,
      searchInputElement      : '#search',
      searchFieldElement      : '#search-option',
      pagingElement           : '#paging',
      optionPagingElement     : '#option',
      pageInfoElement         : '#info',
      columns                 : [
      // {field: 'no_laporan', title: 'No Laporan', editable: false, sortable: true, width: 200, align: 'left', search: true},
      {field: 'no_faktur', title: 'No Faktur', editable: false, sortable: true, width: 400, align: 'left', search: true},
      {field: 'nama_barang', title: 'Nama Barang', editable: false, sortable: true, width: 400, align: 'left', search: true},
      {field: 'qty', title: 'Jumlah', editable: false, sortable: true, width: 400, align: 'left', search: true},
      {field: 'menu', title: 'Action', sortable: false, width: 250, align: 'center', search: false,
      //
      rowStyler: function(rowData, rowIndex) {
        return menu(rowData, rowIndex);
      }
    }
    ]
  });
  $(document).ready(function() {
    datagrid.run();
  });

  function menu(rowData, rowIndex) {
  var tag = '';
  tag += '<div class="btn-group">';
  tag += '<a href="javascript:void(0);" class="dropdown-toggle btn bg-blue btn-xs" data-toggle="dropdown">';
  tag += '<i class="fa fa-bars fs-12 top-0 fs-12"></i>';
  tag += '</a>';
  tag += '<ul class="dropdown-menu pull-right">';
  tag += '<li onclick="detail('+rowIndex+')"><a href="javascript:void(0);" class=" waves-effect waves-block m-l-5"><i class="fa fa-eye"></i> Detail</a></li>';
  tag += '<li><a href="{{ route($routePage) }}/edit/'+rowData.id_detail+'" class=" waves-effect waves-block m-l-5"><i class="fa fa-edit"></i> Edit</a></li>';
  tag += '<li onclick="deleted('+rowIndex+')"><a href="javascript:void(0);" class=" waves-effect waves-block m-l-5"><i class="fa fa-trash"></i> Hapus</a></li>';
  tag += '</ul>';
  tag += '</div>';
  return tag;
}

function detail(rowIndex) {
    $('.main-layer').hide();
    var id = datagrid.getRowData(rowIndex).id_detail;
    $.post("{!! route($routePage.'Show') !!}",{id:id}).done(function(data){
      if(data.status == 'success'){
        $('.other-page').html(data.content).fadeIn();
      } else {
        $('.main-layer').show();
      }
    });
  }
  function deleted(rowIndex) {
    var rowData = datagrid.getRowData(rowIndex);
    swal({
      title: "Are you sure?",
      text: "Once deleted, you will not be able to recover this data!",
      icon: "warning",
      buttons: true,
      dangerMode: true,
    }).then((willDelete) => {
      if (willDelete) {
        $.post('{{ route($routePage.'Destroy') }}',{id:rowData.id_detail}).done(function(data) {
          if (data=='true') {
            swal('',"Data has been deleted!","success");
          }
          setTimeout(function(){ location.reload(); }, 1500);
        }).fail(function() {
          swal('',"Failed to delete data!","error");
        });
      } else {
      }
    });
  }
  </script>
@stop
