<!-- Sidebar Menu -->
        <ul class="sidebar-menu">
          <li class="header">Main Navigation</li>
          <!-- Optionally, you can add icons to the links -->
          <li {{ \Request::segment(1) == 'home' ? 'class="active"' : '' }}><a href="{{ url('/home') }}"><i class="fa fa-tachometer"></i> <span>Dashboard</span></a></li>

          <li class="treeview">
            <a href="#"><i class="fa fa-table"></i> <span>Master Data Karyawan</span> <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
              <li {{ \Request::segment(1) == 'example' ? 'class="active"' : '' }}><a href="{{ url('/karyawan_tetap') }}">Karyawan Tetap / Kontrak</a></li>
              <li {{ \Request::segment(1) == 'example' ? 'class="active"' : '' }}><a href="{{ url('/karyawan_harian') }}">Karyawan Lepas / Harian</a></li>
            </ul>
          </li>

          <li class="treeview">
            <a href="#"><i class="fa fa-pencil"></i> <span>Master Data</span> <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
              <li {{ \Request::segment(1) == 'transaction' ? 'class="active"' : '' }}><a href="{{ url('/transaction') }}">Konsumen</a></li>
              <li {{ \Request::segment(1) == 'transaction' ? 'class="active"' : '' }}><a href="{{ url('/transaction') }}">Barang</a></li>
              <li {{ \Request::segment(1) == 'transaction' ? 'class="active"' : '' }}><a href="{{ url('/transaction') }}">Data Angkutan</a></li>
            </ul>
          </li>

          <li class="treeview">
            <a href="#"><i class="fa fa-table"></i> <span>Absensi</span> <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
              <li {{ \Request::segment(1) == 'example' ? 'class="active"' : '' }}><a href="{{ url('/item') }}">Harian / Kontrak</a></li>
              <li {{ \Request::segment(1) == 'example' ? 'class="active"' : '' }}><a href="{{ url('/item') }}">Packing</a></li>
            </ul>
          </li>

          <li class="treeview">
            <a href="#"><i class="fa fa-table"></i> <span>Invoice Penjualan</span> <i class="fa fa-angle-left pull-right"></i></a>
            
          </li>

          <li class="treeview">
            <a href="#"><i class="fa fa-table"></i> <span>Pembayaran Angkutan</span> <i class="fa fa-angle-left pull-right"></i></a>
            
          </li>

          <li class="treeview">
            <a href="#"><i class="fa fa-area-chart"></i> <span>Laporan</span> <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
              <li {{ \Request::segment(1) == 'report' ? 'class="active"' : '' }}><a href="{{ url('/report') }}">Penjualan</a></li>
              <li {{ \Request::segment(1) == 'report' ? 'class="active"' : '' }}><a href="{{ url('/report') }}">Absensi Karyawan Tetap</a></li>
              <li {{ \Request::segment(1) == 'report' ? 'class="active"' : '' }}><a href="{{ url('/report') }}">Absensi Karyawan Harian</a></li>
              <li {{ \Request::segment(1) == 'report' ? 'class="active"' : '' }}><a href="{{ url('/report') }}">Absensi Karyawan Packing</a></li>

            </ul>
          </li>

          

        </ul>
        <!-- /.sidebar-menu -->