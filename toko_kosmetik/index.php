<?php
session_start();
require_once 'includes/config.php';
//require_once 'vendor/autoload.php';
//use PhpOffice\PhpSpreadsheet\Spreadsheet;
//use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (!isset($_SESSION['kasir_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil data pelanggan
$stmt = $conn->query("SELECT * FROM pelanggan");
$pelanggan = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil data produk
$stmt = $conn->query("SELECT * FROM produk");
$produk = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Proses penjualan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_penjualan'])) {
    $pelanggan_id = $_POST['pelanggan_id'];
    $produk_ids = $_POST['produk_id'];
    $jumlah = $_POST['jumlah'];
    $kasir_id = $_SESSION['kasir_id'];
    $waktu = date('Y-m-d H:i:s');

    // Insert ke tabel penjualan
    $stmt = $conn->prepare("INSERT INTO penjualan (pelanggan_id, kasir_id, waktu) VALUES (?, ?, ?)");
    $stmt->execute([$pelanggan_id, $kasir_id, $waktu]);
    $penjualan_id = $conn->lastInsertId();

    // Insert ke tabel penjualan_detail
    foreach ($produk_ids as $index => $produk_id) {
        $qty = $jumlah[$index];
        $stmt = $conn->prepare("SELECT harga FROM produk WHERE id = ?");
        $stmt->execute([$produk_id]);
        $harga = $stmt->fetchColumn();
        $subtotal = $harga * $qty;

        $stmt = $conn->prepare("INSERT INTO penjualan_detail (penjualan_id, produk_id, jumlah, subtotal) VALUES (?, ?, ?, ?)");
        $stmt->execute([$penjualan_id, $produk_id, $qty, $subtotal]);
    }

    echo "<script>alert('Penjualan berhasil dicatat!');</script>";
}

// Proses ekspor laporan ke Excel
if (isset($_GET['export'])) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Laporan Penjualan');

    // Header
    $sheet->setCellValue('A1', 'ID Penjualan');
    $sheet->setCellValue('B1', 'Pelanggan');
    $sheet->setCellValue('C1', 'Kasir');
    $sheet->setCellValue('D1', 'Waktu');
    $sheet->setCellValue('E1', 'Total');

    // Ambil data penjualan
    $stmt = $conn->query("SELECT p.id, pl.nama as pelanggan, k.nama as kasir, p.waktu, SUM(pd.subtotal) as total
                          FROM penjualan p
                          JOIN pelanggan pl ON p.pelanggan_id = pl.id
                          JOIN kasir k ON p.kasir_id = k.id
                          JOIN penjualan_detail pd ON p.id = pd.penjualan_id
                          GROUP BY p.id");
    $penjualan = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $row = 2;
    foreach ($penjualan as $data) {
        $sheet->setCellValue('A' . $row, $data['id']);
        $sheet->setCellValue('B' . $row, $data['pelanggan']);
        $sheet->setCellValue('C' . $row, $data['kasir']);
        $sheet->setCellValue('D' . $row, $data['waktu']);
        $sheet->setCellValue('E' . $row, $data['total']);
        $row++;
    }

    $writer = new Xlsx($spreadsheet);
    $filename = 'laporan_penjualan_' . date('YmdHis') . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Kasir Toko Kosmetik</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Sistem Kasir Toko Kosmetik</h1>
        
        <!-- Form Penjualan -->
        <form method="POST" class="bg-white p-6 rounded shadow-md mb-6">
            <h2 class="text-xl font-semibold mb-4">Tambah Penjualan</h2>
            <div class="mb-4">
                <label class="block text-sm font-medium">Pelanggan</label>
                <select name="pelanggan_id" class="w-full p-2 border rounded" required>
                    <option value="">Pilih Pelanggan</option>
                    <?php foreach ($pelanggan as $p): ?>
                        <option value="<?php echo $p['id']; ?>"><?php echo $p['nama']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div id="produk-list">
                <div class="produk-item mb-4">
                    <label class="block text-sm font-medium">Produk</label>
                    <select name="produk_id[]" class="w-full p-2 border rounded" required>
                        <option value="">Pilih Produk</option>
                        <?php foreach ($produk as $pr): ?>
                            <option value="<?php echo $pr['id']; ?>" data-harga="<?php echo $pr['harga']; ?>">
                                <?php echo $pr['nama']; ?> (Rp <?php echo number_format($pr['harga'], 0); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label class="block text-sm font-medium mt-2">Jumlah</label>
                    <input type="number" name="jumlah[]" class="w-full p-2 border rounded" min="1" required>
                </div>
            </div>
            <button type="button" onclick="tambahProduk()" class="bg-blue-500 text-white p-2 rounded">Tambah Produk</button>
            <button type="submit" name="submit_penjualan" class="bg-green-500 text-white p-2 rounded mt-2">Simpan Penjualan</button>
        </form>

        <!-- Laporan Penjualan -->
        <div class="bg-white p-6 rounded shadow-md">
            <h2 class="text-xl font-semibold mb-4">Laporan Penjualan</h2>
            <a href="?export=1" class="bg-yellow-500 text-white p-2 rounded mb-4 inline-block">Ekspor ke Excel</a>
            <table class="w-full border">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="p-2">ID</th>
                        <th class="p-2">Pelanggan</th>
                        <th class="p-2">Kasir</th>
                        <th class="p-2">Waktu</th>
                        <th class="p-2">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $conn->query("SELECT p.id, pl.nama as pelanggan, k.nama as kasir, p.waktu, SUM(pd.subtotal) as total
                                          FROM penjualan p
                                          JOIN pelanggan pl ON p.pelanggan_id = pl.id
                                          JOIN kasir k ON p.kasir_id = k.id
                                          JOIN penjualan_detail pd ON p.id = pd.penjualan_id
                                          GROUP BY p.id");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr>
                            <td class="p-2"><?php echo $row['id']; ?></td>
                            <td class="p-2"><?php echo $row['pelanggan']; ?></td>
                            <td class="p-2"><?php echo $row['kasir']; ?></td>
                            <td class="p-2"><?php echo $row['waktu']; ?></td>
                            <td class="p-2">Rp <?php echo number_format($row['total'], 0); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function tambahProduk() {
            const produkList = document.getElementById('produk-list');
            const produkItem = document.createElement('div');
            produkItem.className = 'produk-item mb-4';
            produkItem.innerHTML = `
                <label class="block text-sm font-medium">Produk</label>
                <select name="produk_id[]" class="w-full p-2 border rounded" required>
                    <option value="">Pilih Produk</option>
                    <?php foreach ($produk as $pr): ?>
                        <option value="<?php echo $pr['id']; ?>" data-harga="<?php echo $pr['harga']; ?>">
                            <?php echo $pr['nama']; ?> (Rp <?php echo number_format($pr['harga'], 0); ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <label class="block text-sm font-medium mt-2">Jumlah</label>
                <input type="number" name="jumlah[]" class="w-full p-2 border rounded" min="1" required>
            `;
            produkList.appendChild(produkItem);
        }
    </script>
</body>
</html>