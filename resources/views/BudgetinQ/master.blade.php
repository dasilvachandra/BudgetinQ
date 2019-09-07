<!DOCTYPE html>
<html lang="en" ng-app="BudgeTinQ">

<head>
  @include('BudgetinQ.templates.head')
  @include('BudgetinQ.templates.css')
</head>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar -->

    <!-- End of Sidebar -->
    @include('BudgetinQ.templates.sidebar')
    <!-- Content Wrapper -->

    <div id="content-wrapper" class="d-flex flex-column">
      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

          <!-- Sidebar Toggle (Topbar) -->
          <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
          </button>
          <!-- Topbar Search -->
          @include('BudgetinQ.templates.search')
        </nav>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Page Heading -->
          <div class="d-sm-flex align-items-center justify-content-between mb-4">

            @include('BudgetinQ.templates.selectMonth')

            <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
          </div>

          <!-- Topbar Select Month-->


          <!-- Content Row -->
          @yield('content')

        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->

      <!-- Footer -->
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; BudgetinQ @php echo date("Y") @endphp</span>
          </div>
        </div>
      </footer>
      <!-- End of Footer -->

    </div>

    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Logout Modal-->
  @include('BudgetinQ.templates.logout')
  <!-- Bootstrap core JavaScript-->
  @include('BudgetinQ.templates.javascript')
</body>

</html>