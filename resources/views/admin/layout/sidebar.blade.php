<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">
    <!-- Sidebar user panel -->
    <div class="user-panel">
      <div class="pull-left image">
        <img src="{{ asset('img/ava.jpg') }}" class="img-circle" alt="User Image">
      </div>
      <div class="pull-left info">
        <p>{{Session::get('name')}}</p>
        <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
      </div>
    </div>
    <!-- search form -->
    {{-- <form action="#" method="get" class="sidebar-form">
      <div class="input-group">
        <input type="text" name="q" class="form-control" placeholder="Search...">
        <span class="input-group-btn">
              <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
              </button>
            </span>
      </div>
    </form> --}}
    <!-- /.search form -->
    <!-- sidebar menu: : style can be found in sidebar.less -->


    <ul class="sidebar-menu" data-widget="tree">
      <li class="header">MAIN NAVIGATION</li>
      <li class="treeview">
        <li class="nav-item ">
          <a href="{{ route('dashboard') }}" class="nav-link {{ (isset($menuActive) && !empty($menuActive) && $menuActive == 'dashboard')?'active':'' }}">
            <i class="fa fa-dashboard"></i>
            <span>Dashboard</span>
          </a>
        </li>
        <li class="nav-item ">
          <a href="{{ route('pelanggan') }}" class="nav-link {{ (isset($menuActive) && !empty($menuActive) && $menuActive == 'pelanggan')?'active':'' }}">
            <i class="fa fa-address-card"></i>
            <span>Pelanggan</span>
          </a>
        </li>
        <li class="nav-item ">
          <a href="{{ route('barang') }}" class="nav-link {{ (isset($menuActive) && !empty($menuActive) && $menuActive == 'barang')?'active':'' }}">
            <i class="fa fa-product-hunt"></i>
            <span>Barang</span>
          </a>
        </li>
        <li class="nav-item ">
          <a href="{{ route('transaksi') }}" class="nav-link {{ (isset($menuActive) && !empty($menuActive) && $menuActive == 'transaksi')?'active':'' }}">
            <i class="fa fa-credit-card-alt"></i>
            <span>Transaksi</span>
          </a>
        </li>
        <li class="nav-item ">
          <a href="{{ route('detail') }}" class="nav-link {{ (isset($menuActive) && !empty($menuActive) && $menuActive == 'detail')?'active':'' }}">
            <i class="fa fa-info"></i>
            <span>Detail</span>
          </a>
        </li>
      </li>
      {{-- <li class="treeview">
        <li class="nav-item ">
          <a href="{{ route('cause') }}" class="nav-link {{ (isset($menuActive) && !empty($menuActive) && $menuActive == 'cause')?'active':'' }}">
            <i class="fa fa-dashboard"></i>
            <span>Cause</span>
          </a>
        </li>
      </li>
      <li class="treeview">
        <li class="nav-item ">
          <a href="{{ route('donation') }}" class="nav-link {{ (isset($menuActive) && !empty($menuActive) && $menuActive == 'donation')?'active':'' }}">
            <i class="fa fa-dashboard"></i>
            <span>Donation</span>
          </a>
        </li>
      </li> --}}
    </ul>
  </section>
  <!-- /.sidebar -->
</aside>
