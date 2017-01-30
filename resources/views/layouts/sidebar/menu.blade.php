<!-- Sidebar Menu -->
        <ul class="sidebar-menu">
          <li class="header">Main Navigation</li>
          <!-- Optionally, you can add icons to the links -->
          <!--<li {{ \Request::segment(1) == 'home' ? 'class=active' : '' }}><a href="{{ url('/home') }}"><i class="fa fa-tachometer"></i> <span>Dashboard</span></a></li>-->

          <li class="treeview {{ in_array(\Request::segment(1), ['karyawan-staff','karyawan-tetap', 'karyawan-harian']) ? 'active' : '' }}">
            <a href="#"><i class="fa fa-table"></i> <span>Master Data Karyawan</span> <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
              @if (in_array(100, session()->get('allowed_menus')))
                <li {{ \Request::segment(1) == 'karyawan-staff' ? 'class=active' : '' }}><a href="{{ url('/karyawan-staff') }}">Karyawan Staff</a></li>
              @endif
              @if (in_array(110, session()->get('allowed_menus')))
                <li {{ \Request::segment(1) == 'karyawan-tetap' ? 'class=active' : '' }}><a href="{{ url('/karyawan-tetap') }}">Karyawan Kontrak</a></li>
              @endif
              @if (in_array(120, session()->get('allowed_menus')))
                <li {{ \Request::segment(1) == 'karyawan-harian' ? 'class=active' : '' }}><a href="{{ url('/karyawan-harian') }}">Karyawan Lepas / Harian</a></li>
              @endif
            </ul>
          </li>

          <li class="treeview {{ in_array(\Request::segment(1), ['tujuan', 'barang', 'angkutan', 'konsumen', 'angkutan-tujuan', 'konsumen-barang', 'konsumen-branch', 'upah-jenis-barang']) ? 'active' : '' }}">
            <a href="#"><i class="fa fa-table"></i> <span>Master Data</span> <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
<<<<<<< Updated upstream
              <li {{ \Request::segment(1) == 'tujuan' ? 'class=active' : '' }}><a href="{{ url('/tujuan') }}">Tujuan Pengiriman</a></li>
              <li {{ \Request::segment(1) == 'angkutan' ? 'class=active' : '' }}><a href="{{ url('/angkutan') }}">Angkutan</a></li>
              <li {{ \Request::segment(1) == 'angkutan-tujuan' ? 'class=active' : '' }}><a href="{{ url('/angkutan-tujuan') }}">Biaya Angkutan</a></li>
              <li {{ \Request::segment(1) == 'barang' ? 'class=active' : '' }}><a href="{{ url('/barang') }}">Jenis Barang</a></li>
              <li {{ \Request::segment(1) == 'konsumen' ? 'class=active' : '' }}><a href="{{ url('/konsumen') }}">Distributor</a></li>
              <li {{ \Request::segment(1) == 'konsumen-branch' ? 'class=active' : '' }}><a href="{{ url('/konsumen-branch') }}">Toko</a></li>
              <li {{ \Request::segment(1) == 'konsumen-barang' ? 'class=active' : '' }}><a href="{{ url('/konsumen-barang') }}">Harga Barang</a></li>
              <li {{ \Request::segment(1) == 'upah-jenis-barang' ? 'class=active' : '' }}><a href="{{ url('/upah-jenis-barang') }}">Upah Jenis Barang</a></li>
=======
              @if (in_array(130, session()->get('allowed_menus')))
                <li {{ \Request::segment(1) == 'tujuan' ? 'class=active' : '' }}><a href="{{ url('/tujuan') }}">Tujuan Pengiriman</a></li>
              @endif
              @if (in_array(140, session()->get('allowed_menus')))
                <li {{ \Request::segment(1) == 'angkutan' ? 'class=active' : '' }}><a href="{{ url('/angkutan') }}">Angkutan</a></li>
              @endif
              @if (in_array(150, session()->get('allowed_menus')))
                <li {{ \Request::segment(1) == 'angkutan-tujuan' ? 'class=active' : '' }}><a href="{{ url('/angkutan-tujuan') }}">Biaya Angkutan</a></li>
              @endif
              @if (in_array(160, session()->get('allowed_menus')))
                <li {{ \Request::segment(1) == 'barang' ? 'class=active' : '' }}><a href="{{ url('/barang') }}">Jenis Barang</a></li>
              @endif
              @if (in_array(170, session()->get('allowed_menus')))
                <li {{ \Request::segment(1) == 'konsumen' ? 'class=active' : '' }}><a href="{{ url('/konsumen') }}">Distributor</a></li>
              @endif
              @if (in_array(180, session()->get('allowed_menus')))
                <li {{ \Request::segment(1) == 'konsumen-branch' ? 'class=active' : '' }}><a href="{{ url('/konsumen-branch') }}">Toko</a></li>
              @endif
              @if (in_array(190, session()->get('allowed_menus')))
                <li {{ \Request::segment(1) == 'konsumen-barang' ? 'class=active' : '' }}><a href="{{ url('/konsumen-barang') }}">Harga Barang</a></li>
              @endif
>>>>>>> Stashed changes
            </ul>
          </li>

          <li class="treeview {{ in_array(\Request::segment(1), ['absensi-harian', 'absensi-packing']) ? 'active' : '' }}">
            <a href="#"><i class="fa fa-table"></i> <span>Absensi</span> <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
              @if (in_array(300, session()->get('allowed_menus')))
                <li {{ \Request::segment(1) == 'absensi-harian' ? 'class=active' : '' }}><a href="{{ url('/absensi-harian') }}">Harian / Kontrak</a></li>
              @endif
              @if (in_array(310, session()->get('allowed_menus')))
                <li {{ \Request::segment(1) == 'absensi-packing' ? 'class=active' : '' }}><a href="{{ url('/absensi-packing') }}">Packing</a></li>
              @endif
            </ul>
          </li>
          
          @if (in_array(320, session()->get('allowed_menus')))
            <li {{ \Request::segment(1) == 'home' ? 'class=active' : '' }}><a href="{{ url('/absensi-approval') }}"><i class="fa fa-tachometer"></i> <span>Absensi Approval</span></a></li>
          @endif
          
          <li class="treeview {{ in_array(\Request::segment(1), ['invoice']) ? 'active' : '' }}">
            <a href="#"><i class="fa fa-table"></i> <span>Invoice Penjualan</span> <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
              @if (in_array(330, session()->get('allowed_menus')))
                <li {{ \Request::segment(1) == 'invoice' ? 'class=active' : '' }}><a href="{{ url('/invoice') }}">Input Invoice</a></li>
              @endif
            </ul>
          </li>

          <li class="treeview {{ in_array(\Request::segment(1), ['pembayaran-angkutan']) ? 'active' : '' }}">
            <a href="#"><i class="fa fa-table"></i> <span>Pembayaran Angkutan</span> <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
              @if (in_array(341, session()->get('allowed_menus')))
                <li {{ \Request::segment(1) == 'pembayaran-angkutan' ? 'class=active' : '' }}><a href="{{ url('/pembayaran-angkutan') }}">Konfirmasi Pembayaran</a></li>
              @endif
            </ul>
          </li>
          
          <li class="treeview {{ in_array(\Request::segment(2), ['penjualan', 'absensi-karyawan-staff', 'absensi-karyawan-tetap', 'absensi-karyawan-harian', 'absensi-karyawan-packing']) ? 'active' : '' }}">
            <a href="#"><i class="fa fa-area-chart"></i> <span>Laporan</span> <i class="fa fa-angle-left pull-right"></i></a>
            <ul class="treeview-menu">
              @if (in_array(500, session()->get('allowed_menus')))
                <li {{ \Request::segment(2) == 'penjualan' ? 'class=active' : '' }}><a href="{{ url('/report/penjualan') }}">Penjualan</a></li>
              @endif
              @if (in_array(501, session()->get('allowed_menus')))
                <li {{ \Request::segment(2) == 'absensi-karyawan-staff' ? 'class=active' : '' }}><a href="{{ url('/report/absensi-karyawan-staff') }}">Absensi Karyawan Staff</a></li>
              @endif
              @if (in_array(502, session()->get('allowed_menus')))
                <li {{ \Request::segment(2) == 'absensi-karyawan-tetap' ? 'class=active' : '' }}><a href="{{ url('/report/absensi-karyawan-tetap') }}">Absensi Karyawan Kontrak</a></li>
              @endif
              @if (in_array(503, session()->get('allowed_menus')))
                <li {{ \Request::segment(2) == 'absensi-karyawan-harian' ? 'class=active' : '' }}><a href="{{ url('/report/absensi-karyawan-harian') }}">Absensi Karyawan Harian</a></li>
              @endif
              @if (in_array(504, session()->get('allowed_menus')))
                <li {{ \Request::segment(2) == 'absensi-karyawan-packing' ? 'class=active' : '' }}><a href="{{ url('/report/absensi-karyawan-packing') }}">Absensi Karyawan Packing</a></li>
              @endif
            </ul>
          </li>
          
         <li class="treeview {{ in_array(\Request::segment(1), ['system']) ? 'active' : '' }}">
          <a href="#"><i class="fa fa-wrench"></i> <span>Administrasi Sistem</span> <i class="fa fa-angle-left pull-right"></i></a>
          <ul class="treeview-menu">
            @if (in_array(900, session()->get('allowed_menus')))
              <li {{ \Request::segment(2) == 'role' ? 'class=active' : '' }}><a href="{{ url('/system/role') }}">Role</a></li>
              <li {{ \Request::segment(2) == 'user' ? 'class=active' : '' }}><a href="{{ url('/system/user') }}">User</a></li>
            @endif
          </ul>
        </li>

        </ul>
        <!-- /.sidebar-menu -->