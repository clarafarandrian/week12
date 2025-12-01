<?php
include 'koneksi.php';

// Ambil NIM untuk profil aktif
$nim = isset($_GET['nim']) ? mysqli_real_escape_string($conn, $_GET['nim']) : '2024081035';

// Ambil data user aktif
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE nim = '$nim'"));
if (!$user) die("Profil tidak ditemukan!");

// Ambil semua daftar user untuk dropdown ganti profil
$all_users = mysqli_query($conn, "SELECT nim, nama FROM users ORDER BY nama");

// Query data untuk section
$biodata    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM biodata WHERE nim = '$nim'"));
$pendidikan = mysqli_query($conn, "SELECT * FROM pendidikan WHERE nim = '$nim'");
$pengalaman = mysqli_query($conn, "SELECT * FROM pengalaman WHERE nim = '$nim'");
$keahlian   = mysqli_query($conn, "SELECT * FROM keahlian WHERE nim = '$nim'");
$publikasi  = mysqli_query($conn, "SELECT * FROM publikasi WHERE nim = '$nim'");
$aside_items = mysqli_query($conn, "SELECT * FROM aside WHERE nim = '$nim'");
$footer_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM footer WHERE nim = '$nim'"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Mahasiswa - <?= $user['nama'] ?></title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Poppins',sans-serif;
}

body{
    background:linear-gradient(180deg,#e8f3ff,#cfe7ff,#b8dcff);
    color:#2d3e50;
    min-height:100vh;
    padding:20px;
    gap:20px;
    display:flex;
    flex-direction:column;
}

header{
    display:flex;
    align-items:center;
    gap:20px;
    background:linear-gradient(90deg,#42a5f5,#1e88e5);
    color:white;
    padding:20px 25px;
    border-radius:14px;
    box-shadow:0 6px 14px rgba(33,150,243,0.25);
    position:relative;
}

.profile-photo{
    width:85px;height:85px;border-radius:50%;
    border:3px solid #fff;object-fit:cover;
}
.header-right h1{font-size:1.9em;font-weight:600;}
.header-right p{color:#e3f2fd;}

.user-switch{
    position:absolute;right:25px;top:20px;
    background:white;padding:6px 10px;border-radius:8px;
    font-size:14px;cursor:pointer;border:none;outline:none;
    color:#1565c0;font-weight:500;
}

.admin-link{
    position:absolute;right:25px;bottom:20px;
    background:rgba(255,255,255,0.2);padding:6px 14px;border-radius:8px;
    font-size:13px;text-decoration:none;color:white;
    transition:.3s;font-weight:500;
}

.admin-link:hover{
    background:rgba(255,255,255,0.3);
}

main{display:flex;gap:20px;flex:1;}

nav{
    flex-basis:25%;background:#fff;border-radius:12px;padding:20px;
    box-shadow:0 4px 12px rgba(0,0,0,0.08);
}

nav ul{list-style:none;}
nav ul li a{
    text-decoration:none;color:#1565c0;font-weight:500;font-size:.95em;
    padding:10px 14px;margin:6px 0;display:block;border-radius:8px;transition:.3s;
}
nav ul li a:hover{background:#e3f2fd;color:#0d47a1;}
nav ul li a.active{
    background:linear-gradient(135deg,#64b5f6,#2196f3);color:#fff;
    box-shadow:0 4px 10px rgba(33,150,243,.35);
}

section{
    flex-basis:50%;background:#fff;border-radius:12px;padding:28px;
    box-shadow:0 6px 16px rgba(0,0,0,.06);
}

section h2{
    color:#1565c0;
    margin-bottom:20px;
    padding-bottom:10px;
    border-bottom:2px solid #bbdefb;
    font-weight:600;
}

.content-section{display:none;}
.content-section.active{display:block;animation:fadeIn .4s ease;}

@keyframes fadeIn{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}

.card-pengalaman, .card-publikasi{
    background:#f8fbff;
    padding:18px;
    border-radius:10px;
    margin-bottom:15px;
    border-left:4px solid #42a5f5;
    transition:.3s;
}

.card-pengalaman:hover, .card-publikasi:hover{
    box-shadow:0 4px 12px rgba(33,150,243,0.15);
    transform:translateX(5px);
}

.card-pengalaman img, .card-publikasi img{
    width:100%;
    max-width:200px;
    border-radius:8px;
    margin-bottom:10px;
}

.card-pengalaman h3, .card-publikasi h3{
    color:#1565c0;
    margin-bottom:8px;
    font-size:1.1em;
}

.card-publikasi a{
    color:#42a5f5;
    text-decoration:none;
    font-weight:500;
    transition:.3s;
}

.card-publikasi a:hover{
    color:#1e88e5;
}

aside{
    flex-basis:25%;background:#fff;border-radius:12px;padding:22px;
    box-shadow:0 6px 16px rgba(0,0,0,.06);
}

aside h3{
    color:#1565c0;margin-bottom:10px;border-bottom:2px solid #bbdefb;
    padding-bottom:5px;
}

.hobi-container{display:flex;flex-direction:column;gap:10px;margin-top:12px;}
.hobi-item{
    background:linear-gradient(135deg,#64b5f6,#2196f3);color:#fff;border-radius:10px;
    padding:10px 14px;font-weight:500;cursor:pointer;transition:.3s;
    display:flex;justify-content:center;gap:10px;align-items:center;
    border:none;
}
.hobi-item:hover{background:linear-gradient(135deg,#42a5f5,#1e88e5);transform:translateY(-2px);}

footer{
    display:grid;grid-template-columns:1fr 1fr 1fr;align-items:center;
    background:linear-gradient(90deg,#42a5f5,#1e88e5);
    color:white;padding:20px;border-radius:12px;
}

.social-media{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
}

.social-media a{
    color:white;
    text-decoration:none;
    background:rgba(255, 255, 255, 0.14);
    padding:8px 14px;
    border-radius:8px;
    transition:.3s;
    font-size:14px;
}

.social-media a:hover{
    background:rgba(255,255,255,0.3);
}

.footer-center{
    text-align:center;
    font-weight:500;
}

.footer-right{
    text-align:right;
    font-weight:500;
}

@media(max-width:768px){
    main{flex-direction:column;}
    nav,section,aside{flex-basis:100%;}
    footer{grid-template-columns:1fr;text-align:center;gap:10px;}
    header{flex-direction:column;text-align:center;}
    .user-switch,.admin-link{position:static;margin-top:10px;}
}
</style>
</head>

<body>

<header>
    <img src="foto/<?= $user['foto'] ?>" class="profile-photo" alt="<?= $user['nama'] ?>">

    <div class="header-right">
        <h1>Profil Mahasiswa</h1>
        <p><?= $user['nama'] ?></p>
    </div>

    <!-- Dropdown pilih user -->
    <form method="GET">
        <select name="nim" class="user-switch" onchange="this.form.submit()">
            <?php while($u = mysqli_fetch_assoc($all_users)): ?>
                <option value="<?= $u['nim'] ?>" <?=($u['nim']==$nim?'selected':'')?>>
                    <?= $u['nama'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <a href="admin.php" class="admin-link"><i class="fas fa-cog"></i> Admin Dashboard</a>
</header>

<main>

<nav>
<ul>
    <li><a href="#biodata" class="nav-link active"><i class="fas fa-user"></i> Biodata</a></li>
    <li><a href="#pendidikan" class="nav-link"><i class="fas fa-graduation-cap"></i> Pendidikan</a></li>
    <li><a href="#pengalaman" class="nav-link"><i class="fas fa-briefcase"></i> Pengalaman</a></li>
    <li><a href="#keahlian" class="nav-link"><i class="fas fa-cogs"></i> Keahlian</a></li>
    <li><a href="#publikasi" class="nav-link"><i class="fas fa-book"></i> Publikasi</a></li>
</ul>
</nav>

<section>
    <!-- Biodata -->
    <div id="biodata" class="content-section active">
        <h2><i class="fas fa-id-card"></i> Biodata</h2>
        <?php if ($biodata): ?>
            <p><strong>NIM:</strong> <?= $biodata['nim'] ?></p><br>
            <p><strong>Nama:</strong> <?= $biodata['nama'] ?></p><br>
            <p><strong>Agama:</strong> <?= $biodata['agama'] ?></p><br>
            <p><strong>Tanggal Lahir:</strong> <?= date('d F Y', strtotime($biodata['tanggal_lahir'])) ?></p><br>
            <p><strong>Tempat Lahir:</strong> <?= $biodata['tempat_lahir'] ?></p><br>
        <?php else: ?>
            <p>Data biodata belum tersedia.</p>
        <?php endif; ?>
    </div>

    <!-- Pendidikan -->
    <div id="pendidikan" class="content-section">
        <h2><i class="fas fa-graduation-cap"></i> Riwayat Pendidikan</h2>
        <?php while($row=mysqli_fetch_assoc($pendidikan)): ?>
            <div class="card-pengalaman">
                <h3><?= $row['judul']?></h3>
                <p><?= nl2br($row['isi'])?></p>
            </div>
        <?php endwhile;?>
    </div>

    <!-- Pengalaman -->
    <div id="pengalaman" class="content-section">
        <h2><i class="fas fa-briefcase"></i> Pengalaman</h2>
        <?php while($row=mysqli_fetch_assoc($pengalaman)): ?>
            <div class="card-pengalaman">
                <?php if(!empty($row['gambar'])): ?>
                    <img src="foto/<?= $row['gambar']?>" alt="<?= $row['judul']?>">
                <?php endif;?>
                <div>
                    <h3><?= $row['judul']?></h3>
                    <p><?= nl2br($row['isi'])?></p>
                </div>
            </div>
        <?php endwhile;?>
    </div>

    <!-- Keahlian -->
    <div id="keahlian" class="content-section">
        <h2><i class="fas fa-cogs"></i> Keahlian</h2>
        <ul style="line-height:2;">
            <?php while($row=mysqli_fetch_assoc($keahlian)): ?>
                <li><b><?= $row['judul']?></b> - <?= $row['isi']?></li>
            <?php endwhile;?>
        </ul>
    </div>

    <!-- Publikasi -->
    <div id="publikasi" class="content-section">
        <h2><i class="fas fa-book"></i> Publikasi</h2>
        <?php while($row=mysqli_fetch_assoc($publikasi)): ?>
            <div class="card-publikasi">
                <?php if(!empty($row['gambar'])): ?>
                    <img src="foto/<?= $row['gambar']?>" alt="<?= $row['judul']?>">
                <?php endif;?>
                <div>
                    <h3><?= $row['judul']?></h3>
                    <p>Oleh <?= $row['penulis']?></p>
                    <?php if(!empty($row['link'])): ?>
                        <a href="<?= $row['link']?>" target="_blank">Baca Selengkapnya →</a>
                    <?php endif;?>
                </div>
            </div>
        <?php endwhile;?>
    </div>
</section>

<aside>
    <h3><i class="fas fa-star"></i> Hobi</h3>
    <div class="hobi-container">
        <?php while($row=mysqli_fetch_assoc($aside_items)): ?>
            <button class="hobi-item"><i class="fa-solid fa-star"></i><?= $row['judul']?></button>
        <?php endwhile;?>
    </div>
</aside>

</main>

<footer>
    <div class="social-media">
        <?php if($footer_data): ?>
            <?php if(!empty($footer_data['github'])): ?>
                <a href="<?= $footer_data['github']?>" target="_blank"><i class="fab fa-github"></i> GitHub</a>
            <?php endif; ?>
            <?php if(!empty($footer_data['instagram'])): ?>
                <a href="<?= $footer_data['instagram']?>" target="_blank"><i class="fab fa-instagram"></i> Instagram</a>
            <?php endif; ?>
            <?php if(!empty($footer_data['website_name'])): ?>
                <a href="<?= $footer_data['website_name']?>" target="_blank"><i class="fas fa-globe"></i> Twitter</a>
            <?php endif; ?>
        <?php else: ?>
            <p>Tidak ada data footer.</p>
        <?php endif;?>
    </div>

    <div class="footer-center">© <?= date('Y')?> Profil Mahasiswa</div>
    <div class="footer-right"><?= $footer_data['tagline'] ?? 'happy' ?></div>
</footer>

<script>
const links=document.querySelectorAll(".nav-link");
const sections=document.querySelectorAll(".content-section");

links.forEach(l=>{
    l.onclick=(e)=>{
        e.preventDefault();
        links.forEach(a=>a.classList.remove("active"));
        sections.forEach(s=>s.classList.remove("active"));
        l.classList.add("active");
        document.querySelector(l.getAttribute("href")).classList.add("active");
    }
});
</script>

</body>
</html>