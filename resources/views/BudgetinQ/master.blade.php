<!DOCTYPE html>
<html lang="en" ng-app="BudgeTinQ">

<head>
  @include('BudgetinQ.templates.head')
  @include('BudgetinQ.templates.css')
</head>
<script type="text/javascript">
    var timerStart = Date.now();
</script>

<body id="page-top">
<div class="fixed-top load-page-master progress">
    <div class="load-page progress-bar progress-bar-striped bg-info" role="progressbar" ng-model="persenBar" style="width: 0% ; z-index: 9999;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
  </div>
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
          <!-- Topbar Navbar -->
          <ul class="navbar-nav ml-auto">
            @include('BudgetinQ.templates.top_bar')
          </ul>
        </nav>
        <!-- End of Topbar -->
        <!-- Begin Page Content -->
        <div class="container-fluid">
          

          <!-- Page Heading -->
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <!-- Topbar Select Month-->

            @yield('topbar1')
            @yield('topbar2')

            <!-- <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a> -->
          </div>


          <!-- Content Row -->
          @yield('content')

        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->

      <!-- Footer -->
      <footer class="sticky-footer bg-white">
        @include('BudgetinQ.templates.footer')
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