<?php
session_start();
require 'db.php';

// Folder simpan gambar hasil desain
$upload_dir = 'assets/designs/';
if (!is_dir($upload_dir)) @mkdir($upload_dir, 0777, true);

// Baca Data JSON dari Javascript
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['image'])) {
    $img = $data['image'];
    $product_id = $data['product_id'];
    $user_name = $data['user_name'] ?? 'Tamu';
    $contact = $data['contact'] ?? '-';

    // Bersihkan format Base64
    $img = str_replace('data:image/png;base64,', '', $img);
    $img = str_replace(' ', '+', $img);
    $img_data = base64_decode($img);

    // Simpan File Fisik
    $file_name = 'design_' . time() . '_' . uniqid() . '.png';
    $file_path = $upload_dir . $file_name;

    if (file_put_contents($file_path, $img_data)) {
        // Masukkan ke Database
        $stmt = $pdo->prepare("INSERT INTO custom_designs (product_id, user_name, contact, image_path, status) VALUES (?, ?, ?, ?, 'pending')");
        if ($stmt->execute([$product_id, $user_name, $contact, $file_path])) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal simpan ke database']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menulis file gambar']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
}
?>