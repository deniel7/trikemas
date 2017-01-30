insert into sys_user (username, password, name, email, rolename, active, created_at, created_by)
values ('admin', '$2y$10$8bmb//lX4U3vaaIuZiA8puV8M6ikBG15FQZK2Gdd8N3m2wL1WvPM2', 'Admin', 'admin@localhost', 'Administrator', 1, current_timestamp, 'system');

 ADM_MENU => 0: List, 1: Add, 2: Edit, 3: Delete, 4: Detail, 5: Print, 6: Extend, 7: Upload
-- MASTER, leads by 1, 2
insert into sys_menu (id_menu, title, created_at, created_by) values (100, 'Karyawan Staff - List', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (101, 'Karyawan Staff - Add', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (102, 'Karyawan Staff - Edit', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (103, 'Karyawan Staff - Delete', current_timestamp, 'system');

insert into sys_menu (id_menu, title, created_at, created_by) values (110, 'Karyawan Kontrak - List', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (111, 'Karyawan Kontrak - Add', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (112, 'Karyawan Kontrak - Edit', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (113, 'Karyawan Kontrak - Delete', current_timestamp, 'system');

insert into sys_menu (id_menu, title, created_at, created_by) values (120, 'Karyawan Lepas / Harian - List', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (121, 'Karyawan Lepas / Harian - Add', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (122, 'Karyawan Lepas / Harian - Edit', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (123, 'Karyawan Lepas / Harian - Delete', current_timestamp, 'system');

insert into sys_menu (id_menu, title, created_at, created_by) values (130, 'Tujuan Pengiriman - List', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (131, 'Tujuan Pengiriman - Add', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (132, 'Tujuan Pengiriman - Edit', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (133, 'Tujuan Pengiriman - Delete', current_timestamp, 'system');

insert into sys_menu (id_menu, title, created_at, created_by) values (140, 'Angkutan - List', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (141, 'Angkutan - Add', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (142, 'Angkutan - Edit', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (143, 'Angkutan - Delete', current_timestamp, 'system');

insert into sys_menu (id_menu, title, created_at, created_by) values (150, 'Biaya Angkutan - List', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (151, 'Biaya Angkutan - Add', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (152, 'Biaya Angkutan - Edit', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (153, 'Biaya Angkutan - Delete', current_timestamp, 'system');

insert into sys_menu (id_menu, title, created_at, created_by) values (160, 'Jenis Barang - List', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (161, 'Jenis Barang - Add', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (162, 'Jenis Barang - Edit', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (163, 'Jenis Barang - Delete', current_timestamp, 'system');

insert into sys_menu (id_menu, title, created_at, created_by) values (170, 'Distributor - List', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (171, 'Distributor - Add', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (172, 'Distributor - Edit', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (173, 'Distributor - Delete', current_timestamp, 'system');

insert into sys_menu (id_menu, title, created_at, created_by) values (180, 'Toko - List', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (181, 'Toko - Add', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (182, 'Toko - Edit', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (183, 'Toko - Delete', current_timestamp, 'system');

insert into sys_menu (id_menu, title, created_at, created_by) values (190, 'Harga Barang - List', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (191, 'Harga Barang - Add', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (192, 'Harga Barang - Edit', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (193, 'Harga Barang - Delete', current_timestamp, 'system');

-- TRANSACTION, leads by 3, 4
insert into sys_menu (id_menu, title, created_at, created_by) values (300, 'Absensi Harian / Kontrak - List', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (301, 'Absensi Harian / Kontrak - Add', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (302, 'Absensi Harian / Kontrak - Edit', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (303, 'Absensi Harian / Kontrak - Delete', current_timestamp, 'system');

insert into sys_menu (id_menu, title, created_at, created_by) values (310, 'Absensi Packing - List', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (311, 'Absensi Packing - Add', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (312, 'Absensi Packing - Edit', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (313, 'Absensi Packing - Delete', current_timestamp, 'system');

insert into sys_menu (id_menu, title, created_at, created_by) values (320, 'Absensi Approval', current_timestamp, 'system');

insert into sys_menu (id_menu, title, created_at, created_by) values (330, 'Invoice Penjualan - List', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (331, 'Invoice Penjualan - Add', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (332, 'Invoice Penjualan - Edit', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (333, 'Invoice Penjualan - Delete', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (334, 'Invoice Penjualan - Detail', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (335, 'Invoice Penjualan - Print', current_timestamp, 'system');

insert into sys_menu (id_menu, title, created_at, created_by) values (340, 'Konfirmasi Pembayaran Angkutan - List', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (341, 'Konfirmasi Pembayaran Angkutan - Add', current_timestamp, 'system');

-- REPORT, leads by 5, 6
insert into sys_menu (id_menu, title, created_at, created_by) values (500, 'Laporan Penjualan', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (501, 'Laporan Absensi Karyawan Staff', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (502, 'Laporan Absensi Karyawan Kontrak', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (503, 'Laporan Absensi Karyawan Harian', current_timestamp, 'system');
insert into sys_menu (id_menu, title, created_at, created_by) values (504, 'Laporan Absensi Karyawan Packing', current_timestamp, 'system');

-- ADMIN, leads by 9
insert into sys_menu (id_menu, title, created_at, created_by) values (900, 'Administrasi Sistem', current_timestamp, 'system');

-- ADM_ROLE_HDR
insert into sys_role (id, rolename, description, created_at, created_by) values (1, 'Administrator', 'Super user role', current_timestamp, 'system');
-- ADM_ROLE_DTL
insert into sys_role_dtl (id_hdr, id_menu) values (1, 100);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 101);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 102);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 103);

insert into sys_role_dtl (id_hdr, id_menu) values (1, 110);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 111);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 112);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 113);

insert into sys_role_dtl (id_hdr, id_menu) values (1, 120);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 121);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 122);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 123);

insert into sys_role_dtl (id_hdr, id_menu) values (1, 130);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 131);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 132);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 133);

insert into sys_role_dtl (id_hdr, id_menu) values (1, 140);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 141);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 142);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 143);

insert into sys_role_dtl (id_hdr, id_menu) values (1, 150);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 151);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 152);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 153);

insert into sys_role_dtl (id_hdr, id_menu) values (1, 160);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 161);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 162);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 163);

insert into sys_role_dtl (id_hdr, id_menu) values (1, 170);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 171);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 172);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 173);

insert into sys_role_dtl (id_hdr, id_menu) values (1, 180);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 181);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 182);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 183);

insert into sys_role_dtl (id_hdr, id_menu) values (1, 190);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 191);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 192);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 193);

insert into sys_role_dtl (id_hdr, id_menu) values (1, 300);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 301);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 302);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 303);

insert into sys_role_dtl (id_hdr, id_menu) values (1, 310);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 311);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 312);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 313);

insert into sys_role_dtl (id_hdr, id_menu) values (1, 320);

insert into sys_role_dtl (id_hdr, id_menu) values (1, 330);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 331);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 332);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 333);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 334);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 335);

insert into sys_role_dtl (id_hdr, id_menu) values (1, 340);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 341);

insert into sys_role_dtl (id_hdr, id_menu) values (1, 500);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 501);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 502);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 503);
insert into sys_role_dtl (id_hdr, id_menu) values (1, 504);

insert into sys_role_dtl (id_hdr, id_menu) values (1, 900);
