<!-- Sidebar Menu -->
        <ul class="sidebar-menu">
          <li class="header">Main Navigation</li>
          <!-- Optionally, you can add icons to the links -->
          <li {{ \Request::segment(1) == 'home' ? 'class=active' : '' }}><a href="{{ url('/home') }}"><i class="fa fa-tachometer"></i> <span>Dashboard</span></a></li>

          <li class="treeview {{ in_array(\Request::segment(1), ['karyawan-staff','karyawan-tetap', 'karyawan-harian']) ? 'active' : '' }}">
            <a href="#"><i class="fa fa-table"></i> <span>Master Data Karyawan</span> <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
              <li {{ \Request::segment(1) == 'karyawan-staff' ? 'class=active' : '' }}><a href="{{ url('/karyawan-staff') }}">Karyawan Staff</a></li>
              <li {{ \Request::segment(1) == 'karyawan-tetap' ? 'class=active' : '' }}><a href="{{ url('/karyawan-tetap') }}">Karyawan Kontrak</a></li>
              <li {{ \Request::segment(1) == 'karyawan-harian' ? 'class=active' : '' }}><a href="{{ url('/karyawan-harian') }}">Karyawan Lepas / Harian</a></li>
            </ul>
          </li>

          <li class="treeview {{ in_array(\Request::segment(1), ['tujuan', 'barang', 'angkutan', 'konsumen', 'angkutan-tujuan', 'konsumen-barang', 'konsumen-branch']) ? 'active' : '' }}">
            <a href="#"><i class="fa fa-table"></i> <span>Master Data</span> <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
              <li {{ \Request::segment(1) == 'tujuan' ? 'class=active' : '' }}><a href="{{ url('/tujuan') }}">Tujuan Pengiriman</a></li>
              <li {{ \Request::segment(1) == 'angkutan' ? 'class=active' : '' }}><a href="{{ url('/angkutan') }}">Angkutan</a></li>
              <li {{ \Request::segment(1) == 'angkutan-tujuan' ? 'class=active' : '' }}><a href="{{ url('/angkutan-tujuan') }}">Biaya Angkutan</a></li>
              <li {{ \Request::segment(1) == 'barang' ? 'class=active' : '' }}><a href="{{ url('/barang') }}">Jenis Barang</a></li>
              <li {{ \Request::segment(1) == 'konsumen' ? 'class=active' : '' }}><a href="{{ url('/konsumen') }}">Distributor</a></li>
              <li {{ \Request::segment(1) == 'konsumen-branch' ? 'class=active' : '' }}><a href="{{ url('/konsumen-branch') }}">Toko</a></li>
              <li {{ \Request::segment(1) == 'konsumen-barang' ? 'class=active' : '' }}><a href="{{ url('/konsumen-barang') }}">Harga Barang</a></li>
            </ul>
          </li>

          <li class="treeview {{ in_array(\Request::segment(1), ['absensi-harian', 'absensi-packing']) ? 'active' : '' }}">
            <a href="#"><i class="fa fa-table"></i> <span>Absensi</span> <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
              <li {{ \Request::segment(1) == 'absensi-harian' ? 'class=active' : '' }}><a href="{{ url('/absensi-harian') }}">Harian / Kontrak</a></li>
              <li {{ \Request::segment(1) == 'absensi-packing' ? 'class=active' : '' }}><a href="{{ url('/absensi-packing') }}">Packing</a></li>
            </ul>
          </li>

          <li class="treeview {{ in_array(\Request::segment(1), ['invoice']) ? 'active' : '' }}">
            <a href="#"><i class="fa fa-table"></i> <span>Invoice Penjualan</span> <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
              <li {{ \Request::segment(1) == 'invoice' ? 'class=active' : '' }}><a href="{{ url('/invoice') }}">Input Invoice</a></li>
            </ul>
          </li>

          <li class="treeview {{ in_array(\Request::segment(1), ['pembayaran-angkutan']) ? 'active' : '' }}">
            <a href="#"><i class="fa fa-table"></i> <span>Pembayaran Angkutan</span> <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
              <li {{ \Request::segment(1) == 'pembayaran-angkutan' ? 'class=active' : '' }}><a href="{{ url('/pembayaran-angkutan') }}">Konfirmasi Pembayaran</a></li>
            </ul>
          </li>

          <li class="treeview {{ in_array(\Request::segment(2), ['penjualan', 'absensi-karyawan-staff', 'absensi-karyawan-tetap', 'absensi-karyawan-harian', 'absensi-karyawan-packing']) ? 'active' : '' }}">
            <a href="#"><i class="fa fa-area-chart"></i> <span>Laporan</span> <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
              <li {{ \Request::segment(2) == 'penjualan' ? 'class=active' : '' }}><a href="{{ url('/report/penjualan') }}">Penjualan</a></li>
              <li {{ \Request::segment(2) == 'absensi-karyawan-staff' ? 'class=active' : '' }}><a href="{{ url('/report/absensi-karyawan-staff') }}">Absensi Karyawan Staff</a></li>
              <li {{ \Request::segment(2) == 'absensi-karyawan-tetap' ? 'class=active' : '' }}><a href="{{ url('/report/absensi-karyawan-tetap') }}">Absensi Karyawan Kontrak</a></li>
              <li {{ \Request::segment(2) == 'absensi-karyawan-harian' ? 'class=active' : '' }}><a href="{{ url('/report/absensi-karyawan-harian') }}">Absensi Karyawan Harian</a></li>
              <li {{ \Request::segment(2) == 'absensi-karyawan-packing' ? 'class=active' : '' }}><a href="{{ url('/report/absensi-karyawan-packing') }}">Absensi Karyawan Packing</a></li>

            </ul>
          </li>


          <li {{ \Request::segment(1) == 'home' ? 'class=active' : '' }}><a href="{{ url('/absensi-approval') }}"><i class="fa fa-tachometer"></i> <span>Absensi Approval</span></a></li>
          

        </ul>
        <!-- /.sidebar-menu -->