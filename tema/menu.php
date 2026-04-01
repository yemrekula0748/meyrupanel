<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Giriş kontrolü
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Kullanıcı giriş yapmamışsa login ekranına yönlendir
    exit;
}

// Kullanıcı adı oturumdan alınır
$user_name = $_SESSION['user_name'] ?? 'Kullanıcı Adı Bulunamadı';

// Çıkış işlemi
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit;
}

require_once 'DB.php'; // Veritabanı bağlantısı dosyasını dahil edin

try {
 
    $db = new DB();

    // Basılmamışlarr
    $basilmamis_query = "SELECT COUNT(*) AS toplam FROM siparisler WHERE barkod_basilma_durumu = ?";
    $basilmamis_params = ['Basılmamış'];
    $basilmamis_types = "s"; // Parametrenin string olduğunu belirtmek için "s" kullanılır

    $islem_query = "SELECT COUNT(*) AS toplam FROM siparisler WHERE islem = ?";
    $islem_params = [0];
    $islem_types = "i"; // Parametrenin integer olduğunu belirtmek için "i" kullanılır

    // Basılmamış siparişlerin sayısını al
    $basilmamis_result = $db->query($basilmamis_query, $basilmamis_params, $basilmamis_types);
    $basilmamis_row = $basilmamis_result->fetch_assoc();
    $basilmamis_siparis_sayisi = $basilmamis_row['toplam'];

    // İşlem kolonunda 0 olan kayıtların sayısını al
    $islem_result = $db->query($islem_query, $islem_params, $islem_types);
    $islem_row = $islem_result->fetch_assoc();
    $islem_0_sayisi = $islem_row['toplam'];

} catch (Exception $e) {
    // Hata durumunda sayıları 0 olarak ayarla
    $basilmamis_siparis_sayisi = 0;
    $islem_0_sayisi = 0;
}

?>

<!-- Topbar Start -->
<div class="topbar-custom">
    <div class="container-fluid">
        <div class="d-flex justify-content-between">
            <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center">
                <!-- Hamburger Menü (Sadece Mobil) -->
                <li class="d-lg-none">
                    <button class="button-toggle-menu nav-link hamburger-btn">
                        <i data-feather="menu" class="noti-icon"></i>
                    </button>
                </li>
                
                <li class="d-none d-lg-block">
                    <h6 class="mb-0">Satış Paneline Hoş Geldin, <?= htmlspecialchars($user_name) ?></h6>
                </li>

                <style>
                @keyframes pulse {
                    0% {
                        box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.5);
                    }
                    70% {
                        box-shadow: 0 0 0 10px rgba(255, 193, 7, 0);
                    }
                    100% {
                        box-shadow: 0 0 0 0 rgba(255, 193, 7, 0);
                    }
                }

                .animate-pulse {
                    animation: pulse 1.5s infinite;
                    border-radius: 5px;
                    padding: 10px 15px;
                }
                
                /* Hamburger Menü Stilleri */
                .hamburger-btn {
                    background: none;
                    border: none;
                    padding: 10px;
                    cursor: pointer;
                    display: none;
                }
                
                .mobile-sidebar {
                    position: fixed;
                    top: 0;
                    left: -300px;
                    width: 280px;
                    height: 100%;
                    background: #fff;
                    z-index: 1000;
                    transition: all 0.3s ease;
                    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
                    overflow-y: auto;
                }
                
                .mobile-sidebar.active {
                    left: 0;
                }
                
                .sidebar-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(0,0,0,0.5);
                    z-index: 999;
                    display: none;
                }
                
                .sidebar-overlay.active {
                    display: block;
                }
                
                .mobile-sidebar-header {
                    padding: 15px;
                    border-bottom: 1px solid #eee;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }
                
                .close-sidebar {
                    background: none;
                    border: none;
                    font-size: 20px;
                    cursor: pointer;
                }
                
                @media (max-width: 992px) {
                    .hamburger-btn {
                        display: block;
                    }
                }
                </style>
            </ul>

            <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center">
                <!-- Tam Ekran Modu Ekstra Menü -->
                <li class="d-none d-lg-block">
            

                <!-- Genel Arama Alanı -->
                <li>
  <div class="position-relative topbar-search">
    <form id="searchForm" action="siparis_ara.php" method="GET">
      <input type="text" name="search" id="globalSearch" class="form-control custom-search-input" placeholder="Sipariş Ara">
      <button type="submit" class="search-results"></button>
    </form>
  </div>
</li>


                <!-- Modalların JS ile ekleneceği container -->
                <div id="modalsContainer"></div>

                <!-- Kullanıcı profil menüsü -->
                <li class="dropdown notification-list topbar-dropdown">
                    <a class="nav-link dropdown-toggle nav-user me-0" data-bs-toggle="dropdown" href="#" 
                       role="button" aria-haspopup="false" aria-expanded="false">
                        <img src="assets/images/users/user-5.jpg" alt="user-image" class="rounded-circle">
                        <span class="pro-user-name ms-1">
                            <?= htmlspecialchars($user_name) ?> <i class="mdi mdi-chevron-down"></i> 
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end profile-dropdown ">
                        <!-- item-->
                        <div class="dropdown-header noti-title">
                            <h6 class="text-overflow m-0">
                                Hoş Geldin ! <?=($user_name) ?>
                            </h6>
                        </div>
                        <!-- item-->
                        <a href="pages-profile.html" class="dropdown-item notify-item">
                            <i class="mdi mdi-account-circle-outline fs-16 align-middle"></i>
                            <span>Hesabım</span>
                        </a>
                        <!-- item-->
                        <a href="auth-lock-screen.php" class="dropdown-item notify-item">
                            <i class="mdi mdi-lock-outline fs-16 align-middle"></i>
                            <span>Paneli Kilitle</span>
                        </a>
                        <div class="dropdown-divider"></div>
                        <!-- item-->
                        <a href="auth-logout.php" class="dropdown-item notify-item">
                            <i class="mdi mdi-location-exit fs-16 align-middle"></i>
                            <span>Çıkış Yap</span>
                        </a>
                    </div>
                </li>

            </ul>
        </div>
    </div>
</div>
<!-- end Topbar -->

<!-- Mobile Sidebar (Sadece Mobil) -->
<div class="sidebar-overlay"></div>
<div class="mobile-sidebar">
    <div class="mobile-sidebar-header">
        <h5>Menü</h5>
        <button class="close-sidebar">&times;</button>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link" href="index.php">Anasayfa</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="girilen_siparisler.php">Siparişler(<?= $islem_0_sayisi; ?>)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="tüm_siparisler.php">Siparişler (Tümü)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="printbarcode.php">Barkodlar(<?= $basilmamis_siparis_sayisi; ?>)</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="ayni_siparisler.php">Aynı Siparişler</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="iptalislemler.php">Gider Pusulası</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="barkodlar.php">Yüklü Barkodlar</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="barkodyukleptt.php">Barkod Yükle</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="ikassiparisler.php?year=2025">İkas Siparişlerim</a>
        </li>
    </ul>
</div>

<!-- Left Sidebar Start -->
<div class="app-sidebar-menu">
    <div class="h-100" data-simplebar>
        <div id="sidebar-menu">
            <div class="logo-box">
                <a href="index.php" class="logo logo-light text-decoration-none">
                    <span class="logo-sm" style="font-family:'Inter',sans-serif;font-weight:800;font-size:1rem;color:#c41a1a;letter-spacing:-0.5px;">MK</span>
                    <span class="logo-lg" style="font-family:'Inter',sans-serif;font-weight:800;font-size:1.15rem;color:#c41a1a;letter-spacing:-0.5px;">MeyruKids</span>
                </a>
                <a href="index.php" class="logo logo-dark text-decoration-none">
                    <span class="logo-sm" style="font-family:'Inter',sans-serif;font-weight:800;font-size:1rem;color:#c41a1a;letter-spacing:-0.5px;">MK</span>
                    <span class="logo-lg" style="font-family:'Inter',sans-serif;font-weight:800;font-size:1.15rem;color:#c41a1a;letter-spacing:-0.5px;">MeyruKids</span>
                </a>
            </div>

            <ul id="side-menu">
                <li class="menu-title">Menu</li>
                <li>
                <a href="index.php" class="tp-link">Anasayfa</a>


                        <span class="menu-arrow"></span>
                    </a>
                </li>

                <li class="menu-title">İşlemler</li>
                <li>
                <li>
                    <a href="girilen_siparisler.php" class="tp-link">
                        <i data-feather="list"></i>
                        <span> Siparişler(<?= $islem_0_sayisi; ?>) </span>
                    </a>
                </li>

                <li>
                    <a href="tüm_siparisler.php" class="tp-link">
                        <i data-feather="list"></i>
                        <span> Siparişler (Tümü) </span>
                    </a>
                </li>

                <li>
                    <a href="printbarcode.php" class="tp-link">
                        <i data-feather="columns"></i>
                        <span>Barkodlar(<?= $basilmamis_siparis_sayisi; ?>)</span>
                    </a>
                </li>

                <li>
                    <a href="ayni_siparisler.php" class="tp-link">
                        <i data-feather="repeat"></i>
                        <span> Aynı Siparişler </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="iptalislemler.php">
					<i data-feather="x"></i>
                       <span>Gider Pusulası</span>
                    </a>
                </li>

                <li>
                    <a href="barkodlar.php" class="tp-link">
                        <i data-feather="map-pin"></i>
                        <span>Yüklü Barkodlar</span>
                    </a>
                </li>
                
                <li>
                    <a href="barkodyukleptt.php" class="tp-link">
                        <i data-feather="columns"></i>
                        <span>Barkod Yükle</span>
                    </a>
                </li>

                <li>
                    <a href="ikassiparisler.php?year=2025" class="tp-link">
                        <i data-feather="columns"></i>
                        <span>İkas Siparişlerim</span>
                    </a>
                </li>
                </li>
            </ul>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<!-- End Sidebar -->


<div class="modal fade" id="staticModal" tabindex="-1" aria-labelledby="staticModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticModalLabel">Sipariş Detayları</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></button>
            </div>
            <div class="modal-body">
                <div id="modalContent">Modal içeriği burada görünecek.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const activeBarkodCount = <?= json_encode($activeBarkodCount) ?>;

        if (activeBarkodCount <= 5) {
            const barkodAlert = document.getElementById('barkodAlert');
            barkodAlert.style.display = 'block';

            // Kullanıcıyı yönlendirmek için otomatik uyarı
            setTimeout(function () {
                window.location.href = 'barkodyukleptt.php';
            }, 15000); // 15 saniye sonra otomatik yönlendirme
        }
        
        // Hamburger menü fonksiyonları
        const hamburgerBtn = document.querySelector('.hamburger-btn');
        const mobileSidebar = document.querySelector('.mobile-sidebar');
        const sidebarOverlay = document.querySelector('.sidebar-overlay');
        const closeSidebar = document.querySelector('.close-sidebar');
        
        hamburgerBtn.addEventListener('click', function() {
            mobileSidebar.classList.add('active');
            sidebarOverlay.classList.add('active');
        });
        
        closeSidebar.addEventListener('click', function() {
            mobileSidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
        });
        
        sidebarOverlay.addEventListener('click', function() {
            mobileSidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
        });
    });
</script>

<style>
/* Arama input */
.custom-search-input {
    background-color: #f9f9f9;
    border: 1px solid #ccc;
    border-radius: 30px;
    padding: 8px 8px 8px 40px;
    box-shadow: 0px 2px 5px rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.custom-search-input:focus {
    border-color: #5a99d4;
    outline: none;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
}

.custom-search-icon {
    position: absolute;
    top: 50%;
    left: 10px;
    transform: translateY(-50%);
    font-size: 12px;
    color: #999;
    pointer-events: none;
    transition: color 0.3s ease;
}

.custom-search-input:focus + .custom-search-icon {
    color: #5a99d4;
}

.search-results {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    z-index: 411; /* Modal veya üst öğelerle çakışmasın diye yüksek bir z-index */
    background: white;
    border: 1px solid #ddd;
    border-radius: 10px;
    max-height: 400px;
    overflow-y: auto;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
}

.search-result-item {
    padding: 10px;
    cursor: pointer;
    border-bottom: 1px solid #ddd;
}

.search-result-item:last-child {
    border-bottom: none;
}

.search-result-item:hover {
    background: rgb(9, 210, 146);
}

.search-result-item a {
    text-decoration: none;
    color: #333;
    font-weight: 500;
}

/* Mobile Sidebar Stilleri */
.mobile-sidebar {
    padding: 20px;
}

.mobile-sidebar .nav-item {
    margin-bottom: 10px;
}

.mobile-sidebar .nav-link {
    padding: 10px 15px;
    border-radius: 5px;
    color: #495057;
    transition: all 0.3s;
}

.mobile-sidebar .nav-link:hover {
    background-color: #f8f9fa;
    color: #0d6efd;
}

@media (min-width: 992px) {
    .mobile-sidebar, .sidebar-overlay {
        display: none !important;
    }
}
</style>