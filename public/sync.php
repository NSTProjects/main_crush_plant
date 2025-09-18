<?php
// اتصال به دیتابیس لوکال
$local = new PDO("mysql:host=localhost;dbname=crush-plant;charset=utf8mb4", "root", "nst123");

// آدرس سرور مرکزی (API)
$serverUrl = "https://nsttechservices.com/cursh_plant_api/sync.php";

// جدول‌هایی که نیاز به سنکرون دارند
$tables = [
    "customers",
    "customer_ledgers",
    "deliveries",
    "expenses",
    "products",
    "sales_invoices",
    "sales_invoice_items"
];

foreach ($tables as $table) {
    // گرفتن ردیف‌های pending
    $stmt = $local->prepare("SELECT * FROM $table WHERE SyncStatus='pending' AND IsDeleted=0");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    print_r($rows);
    // if (!empty($rows)) {
    //     // ارسال به سرور
    //     $ch = curl_init($serverUrl);
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    //         'table' => $table,
    //         'data'  => $rows
    //     ]));

    //     $response = curl_exec($ch);
    //     curl_close($ch);

    //     // پاسخ سرور
    //     $res = json_decode($response, true);
    //     print_r($res);
    //     // exit();

    //     if ($res && isset($res['status']) && $res['status'] == "success") {
    //         // اگر انتقال موفق شد، وضعیت رکوردها را synced کنیم
    //         $ids = array_column($rows, "id");
    //         $placeholders = rtrim(str_repeat('?,', count($ids)), ',');
    //         $update = $local->prepare("UPDATE $table SET SyncStatus='synced' WHERE id IN ($placeholders)");
    //         $update->execute($ids);
    //     }
    // }
}
exit();

echo "Sync completed!";
