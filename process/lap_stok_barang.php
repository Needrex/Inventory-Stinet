<?php
require_once '../vendor/autoload.php'; // Pastikan sudah install PHPSpreadsheet
include('../config/conn.php');
include('../config/function.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


// Buat object spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set judul
$sheet->setCellValue('A1', 'LAPORAN STOK BARANG');
$sheet->mergeCells('A1:F1');

// Header tabel
$sheet->setCellValue('A3', 'NO');
$sheet->setCellValue('B3', 'NAMA BARANG');
$sheet->setCellValue('C3', 'MEREK');
$sheet->setCellValue('D3', 'KATEGORI');
$sheet->setCellValue('E3', 'KETERANGAN');
$sheet->setCellValue('F3', 'STOK');

// Ambil data
$query = mysqli_query($con, "SELECT x.*,x1.nama_merek,x2.nama_kategori 
                               FROM barang x 
                               JOIN merek x1 ON x1.idmerek=x.merek_id 
                               JOIN kategori x2 ON x2.idkategori=x.kategori_id 
                               ORDER BY x.idbarang DESC");

$row = 4;
$no = 1;
while ($data = mysqli_fetch_array($query)) {
    $sheet->setCellValue('A' . $row, $no);
    $sheet->setCellValue('B' . $row, $data['nama_barang']);
    $sheet->setCellValue('C' . $row, $data['nama_merek']);
    $sheet->setCellValue('D' . $row, $data['nama_kategori']);
    $sheet->setCellValue('E' . $row, $data['keterangan']);
    $sheet->setCellValue('F' . $row, $data['stok']);
    $row++;
    $no++;
}

// Styling
$sheet->getStyle('A1:F1')->getFont()->setBold(true);
$sheet->getStyle('A3:F3')->getFont()->setBold(true);
$sheet->getStyle('A1:F' . $row)->getAlignment()->setHorizontal('center');

// Auto size kolom
foreach (range('A', 'F') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Set border untuk tabel
$styleArray = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        ],
    ],
];
$sheet->getStyle('A3:F' . ($row - 1))->applyFromArray($styleArray);

// Output file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Laporan_Stok_Barang.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
