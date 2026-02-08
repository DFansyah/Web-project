<?php
session_start();
require 'db.php';
$product_id = $_GET['product_id'] ?? 1;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();
$image_url = $product ? $product['image'] : 'assets/images/placeholder.jpg';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Studio Desain</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <link rel="stylesheet" href="assets/css/designer.css?v=<?php echo time(); ?>">
</head>
<body class="designer-body">

    <div class="designer-header">
        <a href="product.php?id=<?php echo $product_id; ?>" style="color:white; font-size:1.2rem;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="brand-logo">CUSTOM<b>STUDIO</b></div>
        <div>
            <button onclick="downloadDesign()" style="background:#34495e; color:white; border:none; width:35px; height:35px; border-radius:8px; margin-right:5px; cursor:pointer;">
                <i class="fas fa-download"></i>
            </button>
            <button onclick="finish()" style="background:#27ae60; color:white; border:none; padding:8px 15px; border-radius:8px; font-weight:600; cursor:pointer;">
                Simpan
            </button>
        </div>
    </div>

    <div class="workspace">
        <div class="tshirt-container" id="tshirt-wrapper">
            <img src="<?php echo htmlspecialchars($image_url); ?>" class="tshirt-bg" id="tshirt-img">
            
            <div id="drawing-area" class="drawing-area">
                <canvas id="tshirt-canvas"></canvas>
            </div>
            
            <div id="guide-lines"></div>
        </div>

        <div class="floating-actions" id="action-buttons" style="display:none;">
            <button class="float-btn" onclick="editSelectedText()" style="background:#3498db;">
                <i class="fas fa-pen"></i> Ubah
            </button>
            <button class="float-btn" onclick="deleteObj()" style="background:#e74c3c;">
                <i class="fas fa-trash"></i> Hapus
            </button>
        </div>
    </div>

    <div class="toolbar">
        <div class="tool-item" onclick="addText()">
            <div class="icon-box"><i class="fas fa-font"></i></div>
            <span>Teks</span>
        </div>
        
        <div class="tool-item">
            <input type="color" id="colorInput" class="ghost-input" value="#ffffff">
            <div class="icon-box" style="color:#e67e22;"><i class="fas fa-palette"></i></div>
            <span>Warna</span>
        </div>
        
        <div class="tool-item">
            <input type="file" id="fileInput" accept="image/*" class="ghost-input">
            <div class="icon-box" style="color:#3498db;"><i class="fas fa-cloud-upload-alt"></i></div>
            <span>Upload</span>
        </div>
        
        <div class="tool-item" onclick="alert('Segera Hadir')">
            <div class="icon-box" style="color:#f1c40f;"><i class="fas fa-smile"></i></div>
            <span>Stiker</span>
        </div>
    </div>

    <script src="assets/js/designer.js?v=<?php echo time(); ?>"></script>
</body>
</html>