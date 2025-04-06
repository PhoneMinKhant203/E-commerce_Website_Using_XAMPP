<?php
include('includes/header.php');
include('config/dbconnect.php');

// Initialize the total amounts for today and yesterday
$total_amount_today = 0;
$total_amount_yesterday = 0;

// Query to fetch the total transaction amount of today where status is completed
$stmt_today = $pdo->prepare("
    SELECT SUM(transaction_amount) AS total_amount 
    FROM transactions 
    WHERE DATE(created_at) = CURDATE() 
    AND transaction_status = 'completed'
");
$stmt_today->execute();
$result_today = $stmt_today->fetch(PDO::FETCH_ASSOC);

// Check if result is null and set to 0 if there are no transactions
if ($result_today && $result_today['total_amount'] !== null) {
  $total_amount_today = $result_today['total_amount'];
} else {
  $total_amount_today = 0; // No completed transactions today
}

// Query to fetch the total transaction amount of yesterday where status is completed
$stmt_yesterday = $pdo->prepare("
    SELECT SUM(transaction_amount) AS total_amount 
    FROM transactions 
    WHERE DATE(created_at) = CURDATE() - INTERVAL 1 DAY 
    AND transaction_status = 'completed'
");
$stmt_yesterday->execute();
$result_yesterday = $stmt_yesterday->fetch(PDO::FETCH_ASSOC);

// Check if result is null and set to 0 if there are no transactions
if ($result_yesterday && $result_yesterday['total_amount'] !== null) {
  $total_amount_yesterday = $result_yesterday['total_amount'];
} else {
  $total_amount_yesterday = 0; // No completed transactions yesterday
}

// Calculate the percentage change compared to yesterday
$percentage_change = 0;

// Avoid division by zero when yesterday's total is 0
if ($total_amount_yesterday > 0) {
  $percentage_change = (($total_amount_today - $total_amount_yesterday) / $total_amount_yesterday) * 100;
} elseif ($total_amount_today > 0) {
  $percentage_change = 100; // If today has transactions but yesterday had none, show 100% increase
} else {
  $percentage_change = 0; // No transactions for both today and yesterday
}

$sql = "SELECT COUNT(*) as user_count FROM users";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$userCount = $row ? $row['user_count'] : 0;


$currentDate = new DateTime();
$startOfWeek = $currentDate->modify('monday this week')->format('Y-m-d');
$endOfWeek = $currentDate->modify('sunday this week')->format('Y-m-d');

$startOfLastWeek = $currentDate->modify('monday last week')->format('Y-m-d');
$endOfLastWeek = $currentDate->modify('sunday last week')->format('Y-m-d');

// Query to calculate the total amount sold this week
$sqlThisWeek = "SELECT SUM(transaction_amount) as total_sold FROM transactions WHERE created_at BETWEEN :startOfWeek AND :endOfWeek";
$stmtThisWeek = $pdo->prepare($sqlThisWeek);
$stmtThisWeek->execute(['startOfWeek' => $startOfWeek, 'endOfWeek' => $endOfWeek]);
$rowThisWeek = $stmtThisWeek->fetch(PDO::FETCH_ASSOC);
$totalSoldThisWeek = $rowThisWeek ? $rowThisWeek['total_sold'] : 0;

// Query to calculate the total amount sold last week
$sqlLastWeek = "SELECT SUM(transaction_amount) as total_sold FROM transactions WHERE created_at BETWEEN :startOfLastWeek AND :endOfLastWeek";
$stmtLastWeek = $pdo->prepare($sqlLastWeek);
$stmtLastWeek->execute(['startOfLastWeek' => $startOfLastWeek, 'endOfLastWeek' => $endOfLastWeek]);
$rowLastWeek = $stmtLastWeek->fetch(PDO::FETCH_ASSOC);
$totalSoldLastWeek = $rowLastWeek ? $rowLastWeek['total_sold'] : 0;

// Calculate percentage change
$percentageChange = $totalSoldLastWeek > 0 ? (($totalSoldThisWeek - $totalSoldLastWeek) / $totalSoldLastWeek) * 100 : 0;
$formattedPercentageChange = number_format($percentageChange, 2);



// Get the current date and calculate the past 7 days including today
$currentDate = new DateTime();
$endOfPeriod = $currentDate->format('Y-m-d'); // Today
$startOfPeriod = $currentDate->modify('-6 days')->format('Y-m-d'); // 6 days before today

// Initialize arrays to hold labels (dates) and data (sales amounts)
$labels = [];
$data = [];

// Loop through each day in the last 7 days
$period = new DatePeriod(
  new DateTime($startOfPeriod),
  new DateInterval('P1D'),
  new DateTime($endOfPeriod . ' +1 day')
);

foreach ($period as $date) {
  $currentDateStr = $date->format('Y-m-d');

  // Add the date to labels
  $labels[] = $currentDateStr;

  // Query to get total transaction amount for the current date (only for completed transactions)
  $sql = "SELECT SUM(transaction_amount) as total_sold FROM transactions WHERE DATE(created_at) = :currentDate AND transaction_status = 'completed'";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(['currentDate' => $currentDateStr]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  // Add the sales amount to data (if null, set to 0)
  $data[] = $row['total_sold'] ? $row['total_sold'] : 0;
}


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



<div class="main-container d-flex">
  <aside class="sidebar">

    <?php include('includes/sidebar.php'); ?>
  </aside>
  

  <div class="content-container container">
    

    <div class="row mt-4">

      <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
          <div class="card-header p-3 pt-2">

            <div class="text-end pt-1">
              <p class="text-sm mb-0 text-capitalize">Today's Income</p>
              <h4 class="mb-0">$<?= number_format($total_amount_today, 2) ?></h4>
            </div>
          </div>
          <hr class="dark horizontal my-0">
          <div class="card-footer p-3">
            <p class="mb-0">
              <span class="text-<?= $percentage_change >= 0 ? 'success' : 'danger' ?> text-sm font-weight-bolder">
                <?= number_format($percentage_change, 2) ?>%
              </span>
              <?= $percentage_change >= 0 ? 'more' : 'less' ?> than yesterday
            </p>
          </div>
        </div>
      </div>



      <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
          <div class="card-header p-3 pt-2">

            <div class="text-end pt-1">
              <p class="text-sm mb-0 text-capitalize">Today's Users</p>
              <h4 class="mb-0"><?php echo number_format($userCount); ?></h4> <!-- Display the user count -->
            </div>
          </div>
          <hr class="dark horizontal my-0">
          <div class="card-footer p-3">
            <p class="mb-0"><span class="text-success text-sm font-weight-bolder">+3% </span>than last month</p>
          </div>
        </div>
      </div>



      <div class="col-xl-3 col-sm-6">
        <div class="card">
          <div class="card-header p-3 pt-2">

            <div class="text-end pt-1">
              <p class="text-sm mb-0 text-capitalize">Sales</p>
              <h4 class="mb-0">$<?php echo number_format($totalSoldThisWeek, 2); ?></h4> <!-- Display the total sales this week -->
            </div>
          </div>
          <hr class="dark horizontal my-0">
          <div class="card-footer p-3">
            <p class="mb-0">
              <span class="<?php echo $percentageChange > 0 ? 'text-success' : 'text-danger'; ?> text-sm font-weight-bolder">
                <?php echo $formattedPercentageChange; ?>%
              </span>
              <?php echo $percentageChange > 0 ? 'more' : 'less'; ?> than last week
            </p>
          </div>
        </div>
      </div>

    </div>

    <div class="chart-container">

      <!-- Bar Chart -->
      <div style="width: 600px;">
        <h4>Everyday Revenue</h4>
        <canvas id="barChart" width="400" height="200"></canvas>
      </div>


      <!-- Pie Chart -->
      <div style="width: 400px;">
        <h4>Percentage of Total Orders for Each Product Category</h4>
        <canvas id="pieChart" width="400" height="200"></canvas>
      </div>

    </div>

    <br>
    <h3>Wishlist Products</h3>

    <?php
    include('config/dbconnect.php');

    try {
      // Prepare the SQL query
      $query = "
        SELECT 
            p.product_id, 
            p.product_name, 
            p.product_category,
            COUNT(DISTINCT w.user_id) AS user_count
        FROM 
            products p
        JOIN 
            wishlists w ON p.product_id = w.product_id
        GROUP BY 
            p.product_id, p.product_name, p.product_category;
    ";

      // Execute the query
      $stmt = $pdo->prepare($query);
      $stmt->execute();

      // Fetch the results
      $wishlists = $stmt->fetchAll(PDO::FETCH_ASSOC);

      // Check if there are results
      if ($wishlists) {
        // Display the results in a table
        echo "<style>
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 20px 0;
                    font-size: 18px;
                    text-align: left;
                    background-color: #f9f9f9;
                }
                th, td {
                    padding: 12px;
                    border: 1px solid #ddd;
                }
                th {
                    background-color: #4CAF50;
                    color: white;
                }
                tr:nth-child(even) {
                    background-color: #f2f2f2;
                }
                tr:hover {
                    background-color: #ddd;
                }
                caption {
                    margin-bottom: 10px;
                    font-size: 24px;
                    font-weight: bold;
                }
              </style>";

        echo "<table>

                <tr>
                    <th>Product ID</th>
                    <th>Name</th>
                    <th>Brand</th>
                    <th>User Count</th>
                </tr>";

        foreach ($wishlists as $wishlist) {
          echo "<tr>
                    <td>{$wishlist['product_id']}</td>
                    <td>{$wishlist['product_name']}</td>
                    <td>{$wishlist['product_category']}</td>
                    <td>{$wishlist['user_count']}</td>
                </tr>";
        }

        echo "</table>";
      } else {
        echo "No products found in the wishlist.";
      }
    } catch (PDOException $e) {
      echo "Error: " . $e->getMessage();
    }
    ?>





  </div>

</div>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  // Bar Chart Data
  const barData = {
    labels: <?php echo json_encode($labels); ?>, // Dates for the last 7 days
    datasets: [{
      label: 'Total Sales ($)',
      data: <?php echo json_encode($data); ?>, // Total sales amounts
      backgroundColor: 'rgba(54, 162, 235, 0.5)',
      borderColor: 'rgba(54, 162, 235, 1)',
      borderWidth: 1
    }]
  };

  // Bar Chart Configuration
  const barConfig = {
    type: 'bar',
    data: barData,
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'Amount ($)',
            color: '#000',
            font: {
              size: 12
            }
          },
          ticks: {
            color: '#000',
            font: {
              size: 12
            }
          }
        },
        x: {
          title: {
            display: true,
            text: 'Date',
            color: '#000',
            font: {
              size: 14
            }
          },
          ticks: {
            color: '#000',
            font: {
              size: 12
            }
          }
        }
      }
    }
  };

  // Pie Chart Data
  const pieData = {
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
      ],
      hoverOffset: 4
    }]
  };

  // Pie Chart Configuration
  const pieConfig = {
    type: 'pie',
    data: pieData,
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'top'
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

  // Initialize Bar Chart
  const barCtx = document.getElementById('barChart').getContext('2d');
  new Chart(barCtx, barConfig);

  // Initialize Pie Chart
  const pieCtx = document.getElementById('pieChart').getContext('2d');
  new Chart(pieCtx, pieConfig);
</script>





<?php include('includes/footer.php'); ?>