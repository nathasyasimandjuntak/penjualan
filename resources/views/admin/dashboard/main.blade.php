@extends('admin.layout.app')
@section('css')
@stop

@section('content')

  <!-- Main Section -->
      <section class="main-section">
          <!-- Add Your Content Inside -->
          <div class="content">
              <!-- Remove This Before You Start -->
              <h1>SELAMAT DATANG DI HALAMAN ADMIN!</h1>
              <p>Hallo, {{Session::get('name')}}. Apa kabar?</p>
          </div>

          <div class="text-right">
            <a href="/logout" class="btn btn-primary btn-lg">Logout</a>
          </div>
          <!-- /.content -->
      </section>
      <!-- /.main-section -->

@stop

@section('js')
  <script type="text/javascript">
  function myFunction() {
    swal({
      title: "Whoops!",
      text: "You clicked the button!",
      icon: "error",
      button: "Aww yiss!",
    });
  }
</script>
