admin.php

<?php  
include 'koneksi.php';

// Menangani operasi CRUD
$table  = isset($_GET['table'])  ? $_GET['table']  : 'users';
$action = isset($_GET['action']) ? $_GET['action'] : '';

/* ========================= CREATE ========================= */
if (isset($_POST['simpan'])) {
    $fields = [];
    $values = [];
    
    // Ambil semua data POST kecuali 'simpan'
    foreach($_POST as $key => $value){
        if($key != 'simpan'){
            $fields[] = $key;
            $values[] = $value;
        }
    }
    
    // === UPLOAD FOTO/GAMBAR ===
    if(!empty($_FILES)){
        foreach($_FILES as $key=>$file){
            if($file['name']!="" && $file['error']==0){
                $namaFile = time().'_'.$file['name'];
                move_uploaded_file($file['tmp_name'], "foto/".$namaFile);
                $fields[] = $key;
                $values[] = $namaFile;
            }
        }
    }

    if(!empty($fields) && !empty($values)){
        $field_list = implode(', ', $fields);
        $value_list = "'" . implode("', '", array_map(function($val) use ($conn) {
            return mysqli_real_escape_string($conn, $val);
        }, $values)) . "'";

        $sql = "INSERT INTO $table ($field_list) VALUES ($value_list)";

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Data berhasil ditambahkan!'); window.location='admin.php?table=$table';</script>";
        } else {
            echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
        }
    } else {
        echo "<script>alert('Tidak ada data yang akan disimpan');</script>";
    }
}

/* ========================= UPDATE ========================= */
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $updates = [];

    // Ambil semua data POST kecuali 'update' dan 'id'
    foreach($_POST as $key => $value){
        if($key != 'update' && $key != 'id'){
            $updates[] = "$key = '" . mysqli_real_escape_string($conn, $value) . "'";
        }
    }

    // === UPLOAD FILE SAAT UPDATE ===
    if(!empty($_FILES)){
        foreach($_FILES as $key=>$file){
            if($file['name']!="" && $file['error']==0){
                $namaFile = time().'_'.$file['name'];
                move_uploaded_file($file['tmp_name'], "foto/".$namaFile);
                $updates[] = "$key = '$namaFile'";
            }
        }
    }

    // Cegah error SQL jika update kosong
    if(empty($updates)){
        echo "<script>alert('Tidak ada data yang diubah'); window.location='admin.php?table=$table';</script>";
        exit;
    }

    $update_string = implode(', ', $updates);
    $sql = "UPDATE $table SET $update_string WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Data berhasil diupdate!'); window.location='admin.php?table=$table';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}

/* ========================= DELETE ========================= */
if ($action == 'delete' && isset($_GET['id'])) {
    $id  = mysqli_real_escape_string($conn, $_GET['id']);
    $sql = "DELETE FROM $table WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Data berhasil dihapus!'); window.location='admin.php?table=$table';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}

/* ========================= GET COLUMNS ========================= */
$columns = [];
$result = mysqli_query($conn, "DESCRIBE $table");

while ($row = mysqli_fetch_assoc($result)) {
    $columns[] = $row;
}

/* ========================= EDIT MODE ========================= */
$edit_data = null;
if ($action == 'edit' && isset($_GET['id'])) {
    $id     = mysqli_real_escape_string($conn, $_GET['id']);
    $result = mysqli_query($conn, "SELECT * FROM $table WHERE id = $id");
    $edit_data = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CRUD</title>
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
        }

        /* HEADER */
        header{
            background:linear-gradient(90deg,#42a5f5,#1e88e5);
            color:white;
            padding:20px 25px;
            border-radius:14px;
            box-shadow:0 6px 14px rgba(33,150,243,0.25);
            margin-bottom:20px;
            display:flex;
            justify-content:space-between;
            align-items:center;
        }

        header h1{
            font-size:1.9em;
            font-weight:600;
            margin:0;
        }

        .header-links a{
            color:white;
            text-decoration:none;
            background:rgba(255,255,255,0.2);
            padding:8px 16px;
            border-radius:8px;
            margin-left:10px;
            transition:.3s;
        }

        .header-links a:hover{
            background:rgba(255,255,255,0.3);
        }

        /* MENU TAB */
        .nav-tabs{
            background:#fff;
            padding:15px;
            border-radius:12px;
            margin-bottom:20px;
            box-shadow:0 4px 12px rgba(0,0,0,0.08);
            display:flex;
            flex-wrap:wrap;
            gap:8px;
        }

        .nav-tabs a{
            display:inline-block;
            padding:10px 18px;
            text-decoration:none;
            color:#1565c0;
            background:#e3f2fd;
            border-radius:8px;
            transition:.3s;
            font-weight:500;
            font-size:.95em;
        }

        .nav-tabs a:hover{
            background:#bbdefb;
            color:#0d47a1;
        }

        .nav-tabs a.active{
            background:linear-gradient(135deg,#64b5f6,#2196f3);
            color:#fff;
            box-shadow:0 4px 10px rgba(33,150,243,.35);
        }

        /* GRID */
        .container{
            display:grid;
            grid-template-columns:1fr 2fr;
            gap:20px;
            max-width:1400px;
        }

        /* FORM & DATA SECTION */
        .form-section,.data-section{
            background:#fff;
            padding:28px;
            border-radius:12px;
            box-shadow:0 6px 16px rgba(0,0,0,.06);
        }

        h2{
            color:#1565c0;
            margin-bottom:20px;
            padding-bottom:10px;
            border-bottom:2px solid #bbdefb;
            font-weight:600;
        }

        .form-group{
            margin-bottom:15px;
        }

        label{
            font-weight:500;
            color:#2d3e50;
            margin-bottom:5px;
            display:block;
            font-size:.9em;
        }

        input[type=text],
        input[type=date],
        input[type=url],
        input[type=file],
        textarea{
            width:100%;
            padding:10px 14px;
            border:2px solid #e3f2fd;
            border-radius:8px;
            font-size:14px;
            transition:.3s;
            font-family:'Poppins',sans-serif;
        }

        input[type=text]:focus,
        input[type=date]:focus,
        input[type=url]:focus,
        textarea:focus{
            outline:none;
            border-color:#64b5f6;
            box-shadow:0 0 0 3px rgba(100,181,246,0.1);
        }

        textarea{
            min-height:80px;
            resize:vertical;
        }

        button{
            padding:12px 30px;
            background:linear-gradient(135deg,#64b5f6,#2196f3);
            color:white;
            border:none;
            border-radius:8px;
            font-size:14px;
            font-weight:600;
            cursor:pointer;
            transition:.3s;
            box-shadow:0 4px 10px rgba(33,150,243,.35);
        }

        button:hover{
            background:linear-gradient(135deg,#42a5f5,#1e88e5);
            transform:translateY(-2px);
            box-shadow:0 6px 14px rgba(33,150,243,.45);
        }

        /* TABLE */
        table{
            width:100%;
            border-collapse:collapse;
            margin-top:15px;
        }

        thead{
            background:linear-gradient(90deg,#42a5f5,#1e88e5);
            color:#fff;
        }

        th{
            padding:14px 12px;
            text-align:left;
            font-weight:600;
            font-size:.9em;
        }

        td{
            padding:12px;
            border-bottom:1px solid #e3f2fd;
            text-align:left;
            font-size:.9em;
        }

        tbody tr{
            transition:.2s;
        }

        tbody tr:hover{
            background:#e3f2fd;
        }

        .action-links{
            display:flex;
            gap:8px;
        }

        .action-links a{
            padding:6px 14px;
            text-decoration:none;
            border-radius:6px;
            font-size:13px;
            font-weight:500;
            transition:.3s;
            display:inline-block;
        }

        .edit-link{
            background:linear-gradient(135deg,#64b5f6,#2196f3);
            color:#fff;
        }

        .delete-link{
            background:linear-gradient(135deg,#ef5350,#e53935);
            color:#fff;
        }

        .edit-link:hover{
            background:linear-gradient(135deg,#42a5f5,#1e88e5);
            transform:translateY(-2px);
        }

        .delete-link:hover{
            background:linear-gradient(135deg,#e53935,#c62828);
            transform:translateY(-2px);
        }

        @media(max-width:968px){
            .container{
                grid-template-columns:1fr;
            }
            
            .nav-tabs{
                justify-content:center;
            }
            
            .action-links{
                flex-direction:column;
            }
        }
    </style>
</head>

<body>

<header>
    <h1><i class="fas fa-user-shield"></i> Admin Dashboard</h1>
    <div class="header-links">
        <a href="index.php"><i class="fas fa-eye"></i> Lihat Profil</a>
    </div>
</header>

<div class="nav-tabs">
    <a href="admin.php?table=users"       class="<?= $table=='users' ?'active':'' ?>"><i class="fas fa-users"></i> USERS</a>
    <a href="admin.php?table=biodata"     class="<?= $table=='biodata' ?'active':'' ?>"><i class="fas fa-id-card"></i> BIODATA</a>
    <a href="admin.php?table=pendidikan"  class="<?= $table=='pendidikan'?'active':'' ?>"><i class="fas fa-graduation-cap"></i> PENDIDIKAN</a>
    <a href="admin.php?table=pengalaman"  class="<?= $table=='pengalaman'?'active':'' ?>"><i class="fas fa-briefcase"></i> PENGALAMAN</a>
    <a href="admin.php?table=keahlian"    class="<?= $table=='keahlian'?'active':'' ?>"><i class="fas fa-cogs"></i> KEAHLIAN</a>
    <a href="admin.php?table=konten"      class="<?= $table=='konten'?'active':'' ?>"><i class="fas fa-file-alt"></i> KONTEN</a>
    <a href="admin.php?table=aside"       class="<?= $table=='aside'?'active':'' ?>"><i class="fas fa-star"></i> ASIDE</a>
    <a href="admin.php?table=footer"      class="<?= $table=='footer'?'active':'' ?>"><i class="fas fa-shoe-prints"></i> FOOTER</a>
    <a href="admin.php?table=publikasi"   class="<?= $table=='publikasi'?'active':'' ?>"><i class="fas fa-book"></i> PUBLIKASI</a>
    <a href="admin.php?table=nav_profile" class="<?= $table=='nav_profile'?'active':'' ?>"><i class="fas fa-bars"></i> NAV_PROFILE</a>
</div>

<div class="container">

    <!-- ========================= FORM ========================= -->
    <div class="form-section">
        <h2><?= $edit_data ? '<i class="fas fa-edit"></i> Edit Data' : '<i class="fas fa-plus-circle"></i> Form Input' ?></h2>

        <form method="POST" enctype="multipart/form-data">

            <?php foreach($columns as $col): ?>
                <?php if($col['Field']!='id' && $col['Field']!='created_at'): ?>
                    
                    <div class="form-group">
                        <label><?= strtoupper($col['Field']) ?></label>

                        <?php if(strpos($col['Type'],'text')!==false): ?>
                            <textarea name="<?= $col['Field'] ?>"><?= $edit_data[$col['Field']]??'' ?></textarea>

                        <?php elseif(strpos($col['Field'],'tanggal')!==false||$col['Type']=='date'): ?>
                            <input type="date" name="<?= $col['Field'] ?>" value="<?= $edit_data[$col['Field']]??'' ?>">

                        <?php elseif($col['Field']=='foto' || $col['Field']=='gambar'): ?>
                            <input type="file" name="<?= $col['Field'] ?>">
                            <?php if($edit_data && !empty($edit_data[$col['Field']])): ?>
                                <br><img src="foto/<?= $edit_data[$col['Field']] ?>" width="80" style="margin-top:6px;border-radius:6px;">
                            <?php endif; ?>

                        <?php else: ?>
                            <input type="text" name="<?= $col['Field'] ?>" value="<?= $edit_data[$col['Field']]??'' ?>">
                        <?php endif; ?>

                    </div>

                <?php endif; ?>
            <?php endforeach; ?>

            <?php if($edit_data): ?>
                <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                <button type="submit" name="update"><i class="fas fa-save"></i> UPDATE</button>
            <?php else: ?>
                <button type="submit" name="simpan"><i class="fas fa-check"></i> SIMPAN</button>
            <?php endif; ?>
        </form>
    </div>


    <!-- ========================= TABLE ========================= -->
    <div class="data-section">
        <h2><i class="fas fa-database"></i> Data <?= strtoupper($table) ?></h2>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <?php foreach($columns as $col): ?>
                        <?php if($col['Field']!='id' && $col['Field']!='created_at'): ?>
                            <th><?= strtoupper($col['Field']) ?></th>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <th>AKSI</th>
                </tr>
            </thead>

            <tbody>
                <?php 
                $no=1;
                $result=mysqli_query($conn,"SELECT * FROM $table");
                while($row=mysqli_fetch_assoc($result)): ?>
                
                <tr>
                    <td><?= $no++ ?></td>

                    <?php foreach($columns as $col): ?>
                        <?php if($col['Field']!='id' && $col['Field']!='created_at'): ?>
                            <td>
                                <?php if($col['Field']=='foto' || $col['Field']=='gambar'): ?>
                                    <?php if(!empty($row[$col['Field']])): ?>
                                        <img src="foto/<?= $row[$col['Field']] ?>" width="50" style="border-radius:4px;">
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?= substr($row[$col['Field']],0,50) ?><?= strlen($row[$col['Field']])>50?'...':'' ?>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <td>
                        <div class="action-links">
                            <a href="admin.php?table=<?= $table ?>&action=edit&id=<?= $row['id'] ?>" class="edit-link">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="admin.php?table=<?= $table ?>&action=delete&id=<?= $row['id'] ?>" 
                               onclick="return confirm('Yakin ingin menghapus?')" 
                               class="delete-link">
                                <i class="fas fa-trash"></i> Hapus
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>