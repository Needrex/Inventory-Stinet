<!DOCTYPE html>
<html>

<head>
   <title>Dashboard</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
   <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

   <div class="container-fluid p-4">
      <h2 class="mb-4">Dashboard Analytics</h2>

      <div class="row">
         <!-- Card Statistik -->
         <div class="col-md-3 mb-4">
            <div class="card bg-primary text-white">
               <div class="card-body">
                  <h5>Total Pengguna</h5>
                  <h2><?php echo countDataUser() ?></h2>
               </div>
            </div>
         </div>

         <div class="col-md-3 mb-4">
            <div class="card bg-info text-white">
               <div class="card-body">
                  <h5>Total Barang Masuk</h5>
                  <h2><?php echo countDataBarangMasuk() ?></h2>
               </div>
            </div>
         </div>

         <div class="col-md-3 mb-4">
            <div class="card bg-warning text-white">
               <div class="card-body">
                  <h5>Total Barang Keluar</h5>
                  <h2><?php echo countDataBarangKeluar() ?></h2>
               </div>
            </div>
         </div>

         <div class="col-md-3 mb-4">
            <div class="card bg-success text-white">
               <div class="card-body">
                  <h5>Total Barang</h5>
                  <h2><?php echo countDataBarangTotal() ?></h2>
               </div>
            </div>
         </div>
      </div>

      <div class="row">
         <!-- Grafik Penjualan -->
         <div class="col-md-8 mb-4">
            <div class="card">
               <div class="card-body">
                  <h5 class="card-title">Grafik Barang Masuk Perbulan</h5>
                  <canvas id="BarangperbulanChart"></canvas>
               </div>
            </div>
         </div>

         <!-- Grafik Pie -->
         <div class="col-md-4 mb-4">
            <div class="card">
               <div class="card-body">
                  <h5 class="card-title">Total Users</h5>
                  <canvas id="usersChart"></canvas>
               </div>
            </div>
         </div>
      </div>

      <div class="row">
         <!-- Grafik Bar -->
         <div class="col-md-8 mb-4">
            <div class="card">
               <div class="card-body">
                  <h5 class="card-title">Grafik Barang Keluar Perbulan</h5>
                  <canvas id="barangkeluarperbulanChart"></canvas>
               </div>
            </div>
         </div>


      </div>
   </div>

   <script>
   const getYears = new Date().getFullYear();

   // Data untuk Grafik Penjualan
   const BarangperbulanChart = new Chart(document.getElementById('BarangperbulanChart'), {
      type: 'bar',
      data: {
         labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
         datasets: [{
            label: `Grafik Barang Masuk ${getYears}`,
            data: <?php
                     $data = getDataPerBulan('barang_masuk');
                     if (json_last_error() !== JSON_ERROR_NONE) {
                        echo "[]"; // fallback ke array kosong jika ada error
                     } else {
                        echo json_encode($data);
                     }
                     ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.5)',
            tension: 0.1
         }]
      }
   });



   // Data untuk Grafik Produk
   const usersChart = new Chart(document.getElementById('usersChart'), {
      type: 'pie',
      data: {
         labels: ['Admin', 'User'],
         datasets: [{
            data: <?php
                     $data = getDataByRole();
                     if (json_last_error() !== JSON_ERROR_NONE) {
                        echo "[]"; // fallback ke array kosong jika ada error
                     } else {
                        echo json_encode($data);
                     }
                     ?>,
            backgroundColor: [

               'rgb(54, 162, 235)',
               'rgb(255, 205, 86)',

            ]
         }]
      }
   });

   // Data untuk Grafik Kategori
   const barangkeluarperbulanChart = new Chart(document.getElementById('barangkeluarperbulanChart'), {
      type: 'bar',
      data: {
         labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
         datasets: [{
            label: `Grafik Barang Keluar ${getYears}`,
            data: <?php
                     $data = getDataPerBulan('barang_keluar');
                     if (json_last_error() !== JSON_ERROR_NONE) {
                        echo "[]"; // fallback ke array kosong jika ada error
                     } else {
                        echo json_encode($data);
                     }
                     ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.5)',
            tension: 0.1
         }]
      }
   });
   </script>

</body>

</html>