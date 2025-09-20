<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');

    try {
        // اتصال به دیتابیس لوکال
        $local = new PDO("mysql:host=localhost;dbname=crush-plant;charset=utf8mb4", "root", "nst123");
        $local->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $serverUrl = "https://nsttechservices.com/cursh_plant_api/sync.php";

        $tables = [
            "customers",
            "customer_ledgers",
            "deliveries",
            "expenses",
            "products",
            "sales_invoices",
            "sales_invoice_items"
        ];

        $totalSynced = 0; // شمارنده رکوردهای آپدیت شده

        foreach ($tables as $table) {
            // گرفتن رکوردهای pending
            $stmt = $local->prepare("SELECT * FROM $table WHERE SyncStatus='pending'");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($rows)) {
                // ارسال به سرور
                $payload = json_encode(['table' => $table, 'data' => $rows], JSON_UNESCAPED_UNICODE);

                $ch = curl_init($serverUrl);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

                $response = curl_exec($ch);
                curl_close($ch);

                // آپدیت تمام رکوردهای pending بدون توجه به پاسخ سرور
                foreach ($rows as $row) {
                    $update = $local->prepare("UPDATE $table SET SyncStatus='synced' WHERE id = ?");
                    $update->execute([$row['id']]);
                    $totalSynced++;
                }
            }
        }

        echo json_encode([
            'status' => 'success',
            'message' => "سنکرون تکمیل شد",
            'synced_records' => $totalSynced
        ]);
        exit;
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8">
    <title>Sync Data | NST Tech Services</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0d6efd, #6610f2);
            color: #fff;
            font-family: 'Tahoma', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            background: #ffffff;
            color: #333;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            padding: 30px;
            max-width: 450px;
            width: 100%;
            text-align: center;
        }

        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #0d6efd;
        }

        .website a {
            color: #6610f2;
            text-decoration: none;
        }

        #loader {
            display: none;
            margin-top: 20px;
        }

        .btn-primary {
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="card">
        <h2 class="company-name">NST Tech Services</h2>
        <p class="website">
            <a href="https://nsttechservices.com/crush-plant/" target="_blank" rel="noopener noreferrer">
                اطلاعات آنلاین شما
            </a>
        </p>
        <hr>
        <h4>📡 ارسال دیتا به سرور</h4>
        <form method="post" id="syncForm" class="mt-3">
            <button type="submit" class="btn btn-primary">🚀 شروع ارسال</button>
        </form>

        <div id="loader" class="mt-3">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2">در حال ارسال دیتا، لطفاً صبر کنید...</p>
        </div>
        <div id="result" class="mt-2"></div>
    </div>

    <script>
        const form = document.getElementById('syncForm');
        const loader = document.getElementById('loader');
        const resultDiv = document.getElementById('result');

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            loader.style.display = 'block';
            resultDiv.innerHTML = '';

            fetch('sync.php', {
                    method: 'POST'
                })
                .then(res => res.json())
                .then(data => {
                    loader.style.display = 'none';
                    if (data.status === 'success') {
                        resultDiv.innerHTML = `<div class="alert alert-success">${data.message} (${data.synced_records} رکورد)</div>`;
                        // رول‌بک به همان صفحه بعد از 2 ثانیه
                        setTimeout(() => window.location.href = 'sync.php', 2000);
                    } else {
                        resultDiv.innerHTML = `<div class="alert alert-danger">خطا: ${data.message}</div>`;
                    }
                })
                .catch(err => {
                    loader.style.display = 'none';
                    resultDiv.innerHTML = `<div class="alert alert-danger">خطا در اتصال: ${err}</div>`;
                });
        });
    </script>
</body>

</html>