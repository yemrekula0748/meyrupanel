<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siparişler</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Siparişler Tablosu</h2>
        <table id="siparisler" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Kargo</th>
                    <th>Kargo Barkodu</th>
                    <th>Fatura Numarası</th>
                    <th>Ürünler</th>
                    <th>Müşteri Bilgileri</th>
                    <th>Faturalama Durumu</th>
                    <th>Barkod Durumu</th>
                    <th>Ödeme Şartı</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include 'DB.php';
                $db = new DB();
                $result = $db->query("SELECT * FROM siparisler WHERE islem = 0");

                while ($row = $result->fetch_assoc()) {
                    // Müşteri bilgileri tek hücrede alt alta
                    $musteriBilgileri = $row['musteri_ismi'] . "<br>" . $row['musteri_telefonu'] . "<br>" . $row['musteri_adresi'];

                    // Kargo durumu
                    $kargoDurumu = match($row['kargo']) {
                        'Ödeme Şartlı' => 'MH',
                        'Bedelsiz' => 'B',
                        'Ücreti Alıcıdan' => 'UA',
                        default => $row['kargo']
                    };

                    // Resmileşme durumu
                    $resmilesmeDurumu = $row['parasut_resmilesme_durumu'] === "Faturalandırılmadı" ? '❌' : '✅';

                    // Barkod durumu
                    $barkodDurumu = $row['barkod_basilma_durumu'] === "Basılmamış" ? '❌' : '✅';

                    // Ödeme şartı
                    $odemeSarti = $row['odeme_sarti'] . " TL";

                    echo "
                    <tr>
                        <td>{$kargoDurumu}</td>
                        <td>{$row['kargo_barkodu']}</td>
                        <td>{$row['parasut_fatura_numarasi']}</td>
                        <td>{$row['urunler']}</td>
                        <td>{$musteriBilgileri}</td>
                        <td>{$resmilesmeDurumu}</td>
                        <td>{$barkodDurumu}</td>
                        <td>{$odemeSarti}</td>
                        <td>
                            <button class='btn btn-sm btn-primary' data-bs-toggle='modal' data-bs-target='#duzenleModal{$row['id']}'>Düzenle</button>
                            <a href='siparissil.php?id={$row['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Silmek istediğinize emin misiniz?\")'>Sil</a>
                        </td>
                    </tr>";

                    // Modal
                    echo "
                    <div class='modal fade' id='duzenleModal{$row['id']}' tabindex='-1' aria-labelledby='modalLabel{$row['id']}' aria-hidden='true'>
                        <div class='modal-dialog'>
                            <div class='modal-content'>
                                <div class='modal-header'>
                                    <h5 class='modal-title' id='modalLabel{$row['id']}'>Sipariş Düzenle</h5>
                                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                                </div>
                                <form action='duzenle.php' method='POST'>
                                    <div class='modal-body'>
                                        <input type='hidden' name='id' value='{$row['id']}'>
                                        <div class='mb-3'>
                                            <label class='form-label'>Kargo</label>
                                            <input type='text' name='kargo' class='form-control' value='{$row['kargo']}'>
                                        </div>
                                        <div class='mb-3'>
                                            <label class='form-label'>Kargo Barkodu</label>
                                            <input type='text' name='kargo_barkodu' class='form-control' value='{$row['kargo_barkodu']}'>
                                        </div>
                                        <div class='mb-3'>
                                            <label class='form-label'>Fatura Numarası</label>
                                            <input type='text' name='parasut_fatura_numarasi' class='form-control' value='{$row['parasut_fatura_numarasi']}'>
                                        </div>
                                        <div class='mb-3'>
                                            <label class='form-label'>Ürünler</label>
                                            <input type='text' name='urunler' class='form-control' value='{$row['urunler']}'>
                                        </div>
                                        <div class='mb-3'>
                                            <label class='form-label'>Müşteri İsim</label>
                                            <input type='text' name='musteri_ismi' class='form-control' value='{$row['musteri_ismi']}'>
                                        </div>
                                    </div>
                                    <div class='modal-footer'>
                                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>İptal</button>
                                        <button type='submit' class='btn btn-primary'>Kaydet</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.0/dist/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#siparisler').DataTable();
        });
    </script>
</body>
</html>
