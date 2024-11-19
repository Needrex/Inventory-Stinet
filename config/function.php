<?php
function session_timeout()
{
    //lama waktu 30 menit = 1800
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
        session_unset();
        session_destroy();
        header("Location:" . base_url() . "login.php");
    }
    $_SESSION['LAST_ACTIVITY'] = time();
}
function delMask($str)
{
    return (int)implode('', explode('.', $str));
}
function hakAkses(array $a)
{
    $akses = $_SESSION['level'];
    if (!in_array($akses, $a)) {
        // header('Location:?');
        echo '<script>window.location = "?#";</script>';
    }
}
function bulan($bln)
{
    $bulan = [
        1 =>   'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];

    return $bulan[$bln];
}
function tahun()
{
    return [
        '2020',
        '2021',
        '2022',
        '2023',
        '2024',
        '2025'
    ];
}

function list_merek()
{
    include('conn.php');
    $query = mysqli_query($con, "SELECT * FROM merek ORDER BY nama_merek ASC");
    $opt = "";
    while ($row = mysqli_fetch_array($query)) {
        $opt .= "<option value=\"" . $row['idmerek'] . "\">" . $row['nama_merek'] . "</option>";
    }
    return $opt;
}

function list_kategori()
{
    include('conn.php');
    $query = mysqli_query($con, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
    $opt = "";
    while ($row = mysqli_fetch_array($query)) {
        $opt .= "<option value=\"" . $row['idkategori'] . "\">" . $row['nama_kategori'] . "</option>";
    }
    return $opt;
}

function list_barang()
{
    include('conn.php');

    // Periksa koneksi database
    if (!$con) {
        return "Error koneksi database";
    }

    $kategori = mysqli_query($con, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
    if (!$kategori) {
        return "Error query kategori: " . mysqli_error($con);
    }

    $opt = "";
    while ($row = mysqli_fetch_array($kategori)) {
        $barang = mysqli_query($con, "SELECT x.*,x1.nama_merek 
                                     FROM barang x 
                                     JOIN merek x1 ON x1.idmerek=x.merek_id 
                                     WHERE kategori_id='" . mysqli_real_escape_string($con, $row['idkategori']) . "' 
                                     ORDER BY nama_barang ASC");

        if (!$barang) {
            continue; // Skip jika query error
        }

        $opt .= "<optgroup label=\"" . htmlspecialchars($row['nama_kategori']) . " | " . htmlspecialchars($row['keterangan']) . "\">";
        while ($row2 = mysqli_fetch_array($barang)) {
            $opt .= "<option value=\"" . htmlspecialchars($row2['idbarang']) . "\">"
                . htmlspecialchars($row2['nama_barang']) . " - "
                . htmlspecialchars($row2['nama_merek']) . "</option>";
        }
        $opt .= "</optgroup>";
    }

    return $opt;
}

function encrypt($str)
{
    return base64_encode($str);
}
function decrypt($str)
{
    return base64_decode($str);
}

function base_url()
{
    $base_url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
    $base_url .= "://" . $_SERVER['HTTP_HOST'];
    $base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);
    return $base_url;
}

#Dashboard Analytics Function
function countDataUser()
{
    include('conn.php');
    $tableName = 'users';
    $query = mysqli_query($con, "SELECT COUNT(*) as total FROM " . mysqli_real_escape_string($con, $tableName));
    $row = mysqli_fetch_assoc($query);
    return $row['total'];
}

function countDataBarangMasuk()
{
    include('conn.php');
    $tableName = 'barang_masuk';
    $query = mysqli_query($con, "SELECT COUNT(*) as total FROM " . mysqli_real_escape_string($con, $tableName));
    $row = mysqli_fetch_assoc($query);
    return $row['total'];
}

function countDataBarangKeluar()
{
    include('conn.php');
    $tableName = 'barang_keluar';
    $query = mysqli_query($con, "SELECT COUNT(*) as total FROM " . mysqli_real_escape_string($con, $tableName));
    $row = mysqli_fetch_assoc($query);
    return $row['total'];
}

function countDataBarangTotal()
{
    include('conn.php');
    $tableName = 'barang';
    $query = mysqli_query($con, "SELECT COUNT(*) as total FROM " . mysqli_real_escape_string($con, $tableName));
    $row = mysqli_fetch_assoc($query);
    return $row['total'];
}

function getDataPerBulan($table)
{
    include('conn.php');
    // Inisialisasi array 12 bulan dengan nilai 0
    $hasil = array_fill(0, 12, 0);

    $query = "SELECT MONTH(tanggal) as bulan, COUNT(*) as jumlah 
             FROM $table 
             WHERE YEAR(tanggal) = YEAR(CURRENT_DATE())
             GROUP BY MONTH(tanggal)
             ORDER BY MONTH(tanggal)";

    $result = mysqli_query($con, $query);

    // Mengisi array sesuai data yang ada
    while ($row = mysqli_fetch_assoc($result)) {
        $bulan = (int)$row['bulan'];
        $hasil[$bulan - 1] = (int)$row['jumlah'];
    }

    return $hasil;
}

function getDataByRole()
{
    include('conn.php');
    // Inisialisasi array dengan nilai 0
    $hasil = array_fill(0, 2, 0); // array dengan 2 elemen [0,0]

    $query = "SELECT level, COUNT(*) as jumlah 
             FROM users 
             GROUP BY level
             ORDER BY FIELD(level, 'admin', 'user')";

    $result = mysqli_query($con, $query);

    // Mengisi array sesuai data
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['level'] == 'admin') {
            $hasil[0] = (int)$row['jumlah'];
        } else if ($row['level'] == 'user') {
            $hasil[1] = (int)$row['jumlah'];
        }
    }

    return $hasil;
}