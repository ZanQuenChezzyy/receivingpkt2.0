---
description: Workflow Operasional Receiving PKT 2.0: Meliputi Modul 1 (Penerimaan Fisik, Cetak QR, MIGO 103, Crosscheck AVP & Exception Handling) dan Modul 2 (Transmittal QC Kirim & Kembali via Bulk Barcode Scan).
---

# PANDUAN AGEN: WORKFLOW RECEIVING & TRANSMITTAL QC (PKT 2.0)

Anda adalah Agen AI Pengawas Lapangan untuk aplikasi Receiving PKT. Tugas Anda adalah memandu dan memastikan alur operasional dari pintu kedatangan barang hingga serah terima dokumen QC berjalan tanpa ada data yang menggantung (terbengkalai).

---

## MODUL 1: PENERIMAAN DO & TRACKING SAP 103

### 1. Kedatangan & Pengecekan Fisik

- Pihak Expediting menyerahkan barang ke Gudang Receiving PKT.
- Instruksikan Operator untuk menghitung fisik barang dan mencocokkannya dengan kertas DO.

### 2. Input Sistem & Cetak QR (Operator)

- Operator membuka menu "Penerimaan DO" di aplikasi.
- Operator menginput data penerimaan (fokus pada Sparepart/ZSP/ZSM/ZRM).
- Setelah data tersimpan, wajibkan Operator mencetak **QR Material** (ditempel di barang) dan **QR Dokumen** (ditempel di map berkas).
- Map berkas diserahkan ke meja Admin Receiving.

### 3. Eksekusi MIGO 103 & Log Kendala (Admin)

Admin wajib melakukan transaksi tcode `/MIGO` (103) di SAP, lalu mengklik tombol **POST 103** di tabel aplikasi untuk mencatat tanggal penyelesaian.

- **ATURAN WAJIB (EXCEPTION HANDLING):** Jika Admin menunda klik POST 103 lebih dari 1x24 jam sejak dokumen dibuat, sistem harus menagih alasan penundaan.
- Sediakan kolom "Keterangan Tertunda" dengan pilihan wajib:
    1. PO Belum Confirm
    2. Barang Diambil User Langsung (Tanpa Monitor)
    3. Fisik Kelebihan Kirim (Over-delivery)
    4. Lainnya (Input Teks)
- _Tujuan:_ Tidak boleh ada dokumen menumpuk di meja admin tanpa status yang jelas di sistem.

### 4. Crosscheck Berjenjang (Staff -> AVP)

- Admin menyerahkan dokumen ke Staff Receiving untuk validasi silang (DO vs PO vs Fisik).
- Jika **TIDAK SESUAI**: Admin harus melakukan Cancellation/Tarik Kembali 103 di SAP, merevisi data di sistem, dan mengulang pengajuan.
- Jika **SESUAI**: Dokumen naik ke AVP Receiving.
- AVP melakukan validasi akhir dan mengembalikan dokumen sah ke Admin.
- Hal ini tidak dilakukan menggunakan Aplikasi Receeiving PKT (Tetap Manual)

---

## MODUL 2: TRANSMITTAL QC (KIRIM & KEMBALI)

Modul ini adalah bukti sah hitam di atas putih penyerahan dokumen antara Receiving dan ISTEK (Inspeksi Teknik). Gunakan mode pemindaian cepat (Bulk Scan).

### 1. Transmittal QC Kirim (Receiving ke ISTEK)

- Admin membuka menu "Transmittal Kirim" dan memilih Tanggal.
- **ATURAN UI/UX:** Aktifkan mode _Looping Scan_. Admin tidak perlu mengklik tombol "Tambah" berulang kali.
- Admin memindai **QR Dokumen** disusul memindai **QR 103 / Pengajuan QC** secara berurutan menggunakan barcode scanner.
- Sistem otomatis merekap seluruh pindaian ke dalam satu daftar (misal: 10 dokumen sekaligus).
- Admin mencetak Lembar Transmittal sebanyak 2 copy (1 untuk arsip ISTEK, 1 untuk arsip Receiving).

### 2. Transmittal QC Kembali (ISTEK ke Receiving)

- Setelah proses inspeksi selesai, dokumen dikembalikan oleh ISTEK ke Receiving beserta hasilnya (COA/Passed/Rejected).
- Admin membuka menu "Transmittal Kembali" dan memilih Tanggal.
- **ATURAN UI/UX:** Aktifkan mode _Looping Scan_.
- Admin hanya perlu memindai **QR Dokumen** yang ada di map secara bergantian hingga semua berkas hari itu masuk ke dalam daftar terima sistem.

---

## MODUL 3: GRS & RDTV (DIGITALISASI PENAGIHAN)

Modul ini mengelola pencatatan Goods Receipt Slip (GRS) dan Return Delivery to Vendor (RDTV) yang kini sepenuhnya digital (tanpa berkas fisik fisik penagihan) menggunakan basis Surat DOF. Bukti GRS/RDTV ini sangat krusial untuk diteruskan ke tahap Invoicing.

### 1. Inisiasi Input (Admin)
- Admin membuka navigasi "GRS & RDTV", lalu mengklik tombol "Tambah".
- Admin memilih **Tanggal** eksekusi (contoh: 19/06/2026).
- Admin memilih jenis **Kategori Dokumen** (GRS atau RDTV).

### 2. Bulk Upload & Smart Parsing (Otomatisasi)
- **ATURAN UI/UX:** Sistem menyediakan area unggah *Multiple Files* (Drag-and-Drop) untuk menangani puluhan dokumen PDF sekaligus.
- **IDENTIFIKASI NAMA FILE:** Agen/Sistem dilarang meminta input manual untuk pemetaan. Sistem wajib membaca **Nama File** yang diunggah karena nama file tersebut adalah **Kode Dokumen** unik.
  *(Contoh: Jika file bernama `5300057474-10-5208-17062026.pdf`, sistem mengekstrak string `5300057474-10-5208-17062026`)*.

### 3. Auto-Mapping & Update Status Lintas Modul (Managing Relationship)
- Begitu file diunggah, sistem melakukan *Query* ke database untuk mencari record di "Penerimaan DO" yang memiliki `Kode Dokumen` identik dengan nama file tersebut.
- Jika cocok (*match*), sistem mengeksekusi 2 tindakan latar belakang:
  1. **Relasi Database:** File GRS/RDTV yang diunggah otomatis ditautkan secara digital ke data Penerimaan DO terkait.
  2. **Auto-Status Update:** Status dokumen pada Penerimaan DO otomatis berubah menjadi **`GRS`** (atau **`RDTV`**).
- *Tujuan:* Otomatisasi mutlak. Admin tidak perlu mencari dan me-link dokumen satu per satu. Begitu PDF di-upload, status penerimaan DO langsung tertutup dan siap masuk antrean *Invoicing*.