<?php
$apiUrl = "https://app.rid.go.th/reservoir/api/reservoir/public/2022-01-09";

function fetchData($apiUrl) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode != 200) {
        return null;
    }

    return $response; // ส่งคืน JSON string
}

$response = fetchData($apiUrl);

if ($response === null) {
    echo "<h1>❌ เกิดข้อผิดพลาดในการดึงข้อมูลจาก API</h1>";
    exit;
}

// แปลง JSON เป็นอาร์เรย์
$data = json_decode($response, true);

if (!is_array($data) || empty($data['data'])) {
    echo "<h1>❌ รูปแบบข้อมูลที่ได้ไม่ถูกต้อง</h1>";
    exit;
}

$reservoirs = [];

foreach ($data['data'] as $regionData) {
    if (!empty($regionData['reservoir'])) {
        $reservoirs = array_merge($reservoirs, $regionData['reservoir']);
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลอ่างเก็บน้ำ</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            text-align: center;
            background-color: #f4f4f4;
        }
        h1 {
            color: #3a4d74;
        }
        table {
            width: 90%;
            margin: auto;
            border-collapse: collapse;
            background: white;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color:  #f1f1f1;
            color: #3a4d74;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
        .menu {
            background-color: #9b1c2f; /* Light blue background for the menu */
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 16px;
            gap: 20px;
            border-top: 4px solid #ccc;
            
        }
        .menu a {
            text-decoration: none;
            color: #fff;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 5px;
            background-color: #9b1c2f; 
            position: relative;
            overflow: hidden;
            transition: color 0.3s ease-in-out;
        }
        .menu a::after {
           content: "";
           position: absolute;
           left: 50%;
           bottom: 0;
           width: 60%;
           border-radius: 40%;
           height: 0; 
           background-color: #ffcb77; /* Light beige underline */
           transform: translateX(-50%);
           transition: height 0.3s ease-in-out;
        }
        .menu a:hover {
           color: #ffcb77; 
        }
        .menu a:hover::after {
           height: 5px; 
           border-radius: 40%;
        }
        .content {
            padding: 20px;
            text-align: left;
            background-color: #fff;
        }
        .content h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #3a4d74; /* Navy blue for the heading */
        }
        .content p {
            font-size: 16px;
            line-height: 1.6;
            color: #555;
        }
        .container {
            max-width: 1920px;
            margin: 0 auto;
            background-color: #fff;
            border: 1px solid #ccc;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>


<div class="container">
    <div class="menu">
    <a href="home.php">Home</a>
    <a href="add_product.php">Add Product</a>
    <a href="show_product.php">Show Products</a>
    <a href="search_product.php">Search</a>
    <a href="edit_product.php">Edit Product</a>
    <a href="delete_product.php">Delete</a>
    <a href="api_reservoir.php">Reservoir</a>
    </div>

    <h1>ข้อมูลอ่างเก็บน้ำ</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>ชื่ออ่างเก็บน้ำ</th>
                <th>ความจุทั้งหมด (ล้าน ลบ.ม.)</th>
                <th>ปริมาณน้ำต่ำสุด (ล้าน ลบ.ม.)</th>
                <th>ปริมาณน้ำปัจจุบัน (ล้าน ลบ.ม.)</th>
                <th>เปอร์เซ็นต์น้ำที่มี</th>
                <th>ปริมาณน้ำไหลเข้า (ล้าน ลบ.ม.)</th>
                <th>ปริมาณน้ำไหลออก (ล้าน ลบ.ม.)</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($reservoirs)): ?>
            <?php foreach ($reservoirs as $reservoir): ?>
                <tr>
                    <td><?= htmlspecialchars($reservoir['id'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($reservoir['name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($reservoir['storage'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($reservoir['dead_storage'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($reservoir['volume'] ?? '-') ?></td>
                    <td><?= isset($reservoir['percent_storage']) ? number_format($reservoir['percent_storage'], 2) . '%' : '-' ?></td>
                    <td><?= htmlspecialchars($reservoir['inflow'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($reservoir['outflow'] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8">❌ ไม่พบข้อมูล</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>