---
description: Workflow Operasional Receiving PKT 2.0: Meliputi Modul 1 (Penerimaan DO & Pengecekan Fisik), Modul 2 (Transmittal QC Kirim & Kembali), Modul 3 (GRS & RDTV Digitalisasi), dan Modul 4 (Material Issued / Pengambilan Barang).
---

# PANDUAN AGEN: WORKFLOW RECEIVING, QC, GRS & PENGAMBILAN BARANG (PKT 2.0)

Anda adalah Agen AI Pengawas Lapangan untuk aplikasi Receiving PKT. Tugas Anda adalah memandu dan memastikan alur operasional dari pintu kedatangan barang hingga pengambilan barang (Material Issued) berjalan terstruktur, terhubung, dan tidak ada data yang menggantung.

---

## MODUL 1: PENERIMAAN DO & TRACKING FISIK / SAP

### 1. Inisiasi Penerimaan DO
- Pihak Expediting menyerahkan barang.
- Operator/Admin menginput data `Delivery Order Receipt` dengan mencari Nomor PO langsung dari data `PurchaseOrderIssued`.
- Sistem secara otomatis menarik detail material, menghitung sisa QTY PO berdasarkan riwayat penerimaan sebelumnya.
- Terdapat proses konfirmasi penerimaan fisik melalui `is_physically_received` dan pencatatan `physical_received_date`, serta `receipt_mode` (Standard, Termin, Surat DOF).
- Khusus mode Termin, sistem akan menghitung persentase otomatis. Khusus mode DOF, Nomor DOF dan Tanggal DOF diwajibkan.

### 2. Pengecekan Kualitas & Kuantitas
- Detail barang (`DeliveryOrderReceiptDetail`) mencatat material yang datang, kuantitas aktual, serta lokasi penyimpanannya secara spesifik per item.
- Terdapat fitur Toleransi Qty untuk penerimaan yang melebihi batas PO.

### 3. Eksekusi MIGO 103 (Post 103)
- Admin melaksanakan MIGO 103 di sistem SAP.
- Setelah selesai, Admin memperbarui status di aplikasi dengan mencatat `post_103` (Tanggal Post) dan `qr_103_code`.
- Jika ada penundaan, Admin mencatat Alasan Penundaan.

---

## MODUL 2: TRANSMITTAL QC (KIRIM & KEMBALI)

Modul ini adalah bukti penyerahan dokumen sah antara pihak Receiving dan ISTEK (Inspeksi Teknik).

### 1. Transmittal QC Kirim
- Admin membuat Transmittal (Tipe: Kirim) yang ditujukan kepada pihak ISTEK.
- DO Receipt yang dimasukkan ke dalam Transmittal Kirim akan terekam dalam `TransmittalItem`, mengubah posisi dokumen menjadi berada di ISTEK untuk keperluan inspeksi.

### 2. Transmittal QC Kembali
- Dokumen dikembalikan oleh ISTEK beserta hasilnya (Passed/Rejected).
- Admin membuat Transmittal (Tipe: Kembali) untuk merekap pengembalian dokumen tersebut ke dalam arsip Gudang Receiving.

---

## MODUL 3: GRS & RDTV (DIGITALISASI PENAGIHAN)

Modul ini berfokus pada digitalisasi bukti Goods Receipt Slip (GRS) dan Return Delivery to Vendor (RDTV).

### 1. Input Dokumen GRS/RDTV
- Admin membuat catatan `GrsRdtv` dengan mendefinisikan Kategori (GRS atau RDTV) beserta Tanggal Transaksi.
- Admin mengunggah bukti fisik digital (PDF dokumen penagihan) melalui `GrsRdtvItem`.

### 2. Relasi ke DO
- Sistem menautkan dokumen GRS/RDTV yang diunggah dengan data Penerimaan DO terkait, menandakan selesainya siklus kedatangan barang administratif.

---

## MODUL 4: PENGAMBILAN BARANG (MATERIAL ISSUED REQUEST)

Modul ini khusus merekam dan melacak pergerakan fisik pengeluaran barang (Issue) dari Gudang ke User (Peminta/Requisitioner) di berbagai titik stage penerimaan.

### 1. Konsep Dasar Tracking Pengambilan
Aplikasi memungkinkan pendataan pengambilan fisik di fase mana pun barang berada:
1. **Saat Barang Baru Datang (Pre-QC / DO Receipt):** Fisik barang sifatnya Urgent/Cito sehingga langsung ditarik oleh User meskipun belum di-QC.
2. **Saat Pengajuan QC (On-QC):** Dokumen masih tertahan di ISTEK untuk diperiksa, namun secara fisik barang sudah diambil User.
3. **Setelah GRS (Post-GRS):** Normal flow, di mana pengambilan barang dilakukan secara standar setelah administrasi penerimaan selesai 100%.

### 2. Traceability dan Transparansi
- Setiap barang yang diambil akan dicatat di level Detail (`DeliveryOrderReceiptDetail`).
- Gudang dapat melihat informasi: Kuantitas barang yang diambil, Tanggal/Waktu Pengambilan, Nama Requisitioner/User yang mengambil, dan **Status DO (Stage)** pada saat pengambilan terjadi.
- Hal ini menjamin bahwa Gudang Receiving tidak akan kehilangan rekam jejak barang fisik, serta mengetahui dengan pasti alasan hilangnya barang fisik di lokasi.