<?php
include('../config/conn.php');
include('../config/function.php');
require_once '../vendor/autoload.php';
?>
<?php if (isset($_POST['excel'])) { ?>

<?php
   // Set header untuk output Excel
   header("Content-type: application/vnd.ms-excel");
   header("Content-Disposition: attachment; filename=Laporan_Barang_Masuk_" . date('Y-m-d') . ".xls");

   $tgl_awal = $_POST['tanggal_awal'];
   $tgl_akhir = $_POST['tanggal_akhir'];
   ?>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel"
   xmlns="http://www.w3.org/TR/REC-html40">

<head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
   <!--[if gte mso 9]>
    <xml>
        <x:ExcelWorkbook>
            <x:ExcelWorksheets>
                <x:ExcelWorksheet>
                    <x:Name>Laporan Barang Masuk</x:Name>
                    <x:WorksheetOptions>
                        <x:DisplayGridlines/>
                    </x:WorksheetOptions>
                </x:ExcelWorksheet>
            </x:ExcelWorksheets>
        </x:ExcelWorkbook>
    </xml>
    <![endif]-->
   <style>
   td {
      border: 0.5pt solid windowtext;
      padding: 5px;
      mso-number-format: "\@";
   }

   .number {
      mso-number-format: "0";
   }

   th {
      border: 0.5pt solid windowtext;
      padding: 5px;
      font-weight: bold;
      background-color: #CCCCCC;
   }

   table {
      border-collapse: collapse;
      width: 100%;
   }

   .text-center {
      text-align: center;
   }

   h2,
   h4 {
      text-align: center;
      margin: 5px;
   }
   </style>
</head>

<body>
   <h2>LAPORAN BARANG KELUAR</h2>
   <h4>TANGGAL <?= date('d-m-Y', strtotime($tgl_awal)) ?> SAMPAI <?= date('d-m-Y', strtotime($tgl_akhir)) ?></h4>
   <br>
   <table>
      <thead>
         <tr>
            <th>NO</th>
            <th>TGL</th>
            <th>NAMA BARANG</th>
            <th>MEREK</th>
            <th>KATEGORI</th>
            <th>KETERANGAN</th>
            <th>JUMLAH</th>
         </tr>
      </thead>
      <tbody>
         <?php
            $query = mysqli_query($con, "SELECT x.*,x1.nama_barang,x2.nama_merek,x3.nama_kategori 
                    FROM barang_keluar x 
                    JOIN barang x1 ON x1.idbarang=x.barang_id 
                    JOIN merek x2 ON x2.idmerek=x1.merek_id 
                    JOIN kategori x3 ON x3.idkategori=x1.kategori_id 
                    WHERE x.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir' 
                    ORDER BY x.idbarang_keluar DESC") or die(mysqli_error($con));
            $no = 1;
            while ($row = mysqli_fetch_array($query)):
            ?>
         <tr>
            <td class="text-center number"><?= $no++ ?></td>
            <td><?= date('d-m-Y', strtotime($row['tanggal'])) ?></td>
            <td><?= $row['nama_barang'] ?></td>
            <td><?= $row['nama_merek'] ?></td>
            <td><?= $row['nama_kategori'] ?></td>
            <td><?= $row['keterangan'] ?></td>
            <td class="number"><?= $row['jumlah'] ?></td>
         </tr>
         <?php endwhile; ?>
      </tbody>
   </table>
</body>

</html>
<?php } else {

  

   $tgl_awal = $_POST['tanggal_awal'];
   $tgl_akhir = $_POST['tanggal_akhir'];

   class PDF extends FPDF
   {
      // Header
      function Header()
      {
         $this->SetFont('Arial', 'B', 16);
         $this->Cell(0, 10, 'LAPORAN BARANG KELUAR', 0, 1, 'C');
         $this->SetFont('Arial', '', 12);
         $this->Cell(0, 10, 'TANGGAL ' . date('d-m-Y', strtotime($_POST['tanggal_awal'])) . ' SAMPAI ' . date('d-m-Y', strtotime($_POST['tanggal_akhir'])), 0, 1, 'C');
         $this->Ln(10);

         // Header Tabel
         $this->SetFont('Arial', 'B', 10);
         $this->Cell(10, 10, 'NO', 1, 0, 'C');
         $this->Cell(30, 10, 'TGL', 1, 0, 'C');
         $this->Cell(45, 10, 'NAMA BARANG', 1, 0, 'C');
         $this->Cell(30, 10, 'MEREK', 1, 0, 'C');
         $this->Cell(30, 10, 'KATEGORI', 1, 0, 'C');
         $this->Cell(25, 10, 'JUMLAH', 1, 0, 'C');
         $this->Cell(20, 10, 'KET', 1, 1, 'C');
      }

      // Footer
      function Footer()
      {
         $this->SetY(-15);
         $this->SetFont('Arial', 'I', 8);
         $this->Cell(0, 10, 'Halaman ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
      }
   }

   // Membuat objek PDF
   $pdf = new PDF('P', 'mm', 'A4');
   $pdf->AliasNbPages();
   $pdf->AddPage();
   $pdf->SetFont('Arial', '', 10);

   // Query data
   $query = mysqli_query($con, "SELECT x.*,x1.nama_barang,x2.nama_merek,x3.nama_kategori 
            FROM barang_keluar x 
            JOIN barang x1 ON x1.idbarang=x.barang_id 
            JOIN merek x2 ON x2.idmerek=x1.merek_id 
            JOIN kategori x3 ON x3.idkategori=x1.kategori_id 
            WHERE x.tanggal BETWEEN '$tgl_awal' AND '$tgl_akhir' 
            ORDER BY x.idbarang_keluar DESC") or die(mysqli_error($con));

   $no = 1;
   while ($row = mysqli_fetch_array($query)) {
      $pdf->Cell(10, 10, $no++, 1, 0, 'C');
      $pdf->Cell(30, 10, date('d-m-Y', strtotime($row['tanggal'])), 1, 0, 'C');
      $pdf->Cell(45, 10, $row['nama_barang'], 1, 0, 'L');
      $pdf->Cell(30, 10, $row['nama_merek'], 1, 0, 'L');
      $pdf->Cell(30, 10, $row['nama_kategori'], 1, 0, 'L');
      $pdf->Cell(25, 10, $row['jumlah'], 1, 0, 'C');
      $pdf->Cell(20, 10, $row['keterangan'], 1, 1, 'L');
   }

   // Output PDF
   $pdf->Output('Laporan_Barang_Keluar_' . date('Y-m-d') . '.pdf', 'D');
}