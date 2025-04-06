<?php
include('config/dbconnect.php'); // Assuming dbconnect.php contains your database connection

// SQL query to count the number of orders per product category
$sql = "
    SELECT 
        p.product_category, 
        COUNT(oi.product_id) AS category_count
    FROM 
        order_items oi
    JOIN 
        products p ON oi.product_id = p.product_id
    GROUP BY 
        p.product_category
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for the chart
$categories = [];
$counts = [];

foreach ($results as $row) {
    $categories[] = $row['product_category']; // Store categories
    $counts[] = $row['category_count']; // Store count of products sold in each category
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <div style="width: 600px;">
        <canvas id="myChart" width="400" height="200"></canvas>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const data = {
            labels: <?php echo json_encode($categories); ?>, // Categories from PHP
            datasets: [{
                label: 'Product Orders by Category',
                data: <?php echo json_encode($counts); ?>, // Count of products sold per category from PHP
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 205, 86)',
                    'rgb(75, 192, 192)',
                    'rgb(153, 102, 255)',
                    'rgb(255, 159, 64)'
                ], // You can add more colors if you have more categories
                hoverOffset: 4
            }]
        };

        const config = {
            type: 'pie',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                let total = tooltipItem.dataset.data.reduce((a, b) => a + b, 0);
                                let currentValue = tooltipItem.raw;
                                let percentage = Math.round((currentValue / total) * 100);
                                return tooltipItem.label + ': ' + percentage + '% (' + currentValue + ')';
                            }
                        }
                    }
                }
            }
        };

        const ctx = document.getElementById('myChart').getContext('2d');
        new Chart(ctx, config);
    </script>

</body>

</html>