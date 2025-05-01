<?php
// Include database connection
require_once 'config.php';

// Get expense statistics
$stats = [
    'total_expenses' => 0,
    'total_amount' => 0,
    'recent_expenses' => []
];

// Get total count
$query = "SELECT COUNT(*) FROM expenses";
$result = $conn->query($query);
if ($result) {
    $stats['total_expenses'] = $result->fetch_row()[0];
}

// Get total amount
$query = "SELECT SUM(expcost) FROM expenses";
$result = $conn->query($query);
if ($result) {
    $stats['total_amount'] = $result->fetch_row()[0] ?: 0;
}

// Get recent expenses
$query = "SELECT * FROM expenses ORDER BY created_at DESC LIMIT 10";
$result = $conn->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $stats['recent_expenses'][] = $row;
    }
}

// Get expense channels summary
$query = "SELECT expSubChannel, COUNT(*) as count, SUM(expcost) as total FROM expenses GROUP BY expSubChannel ORDER BY total DESC LIMIT 5";
$channel_stats = [];
$result = $conn->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $channel_stats[] = $row;
    }
}

// Get business type summary
$query = "SELECT biztypeexp, COUNT(*) as count, SUM(expcost) as total FROM expenses GROUP BY biztypeexp ORDER BY total DESC LIMIT 5";
$biztype_stats = [];
$result = $conn->query($query);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $biztype_stats[] = $row;
    }
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Tracking Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Expense Tracker</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="expense_form.php">Add Expenses</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <!-- Stats Cards -->
        <div class="row">
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white">Total Expenses</h6>
                                <h2 class="text-white"><?php echo $stats['total_expenses']; ?></h2>
                            </div>
                            <i class="fas fa-receipt fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white">Total Amount</h6>
                                <h2 class="text-white">$<?php echo number_format($stats['total_amount'], 2); ?></h2>
                            </div>
                            <i class="fas fa-dollar-sign fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card bg-info text-white h-100">
                    <div class="card-body">
                        <a href="expense_form.php" class="text-decoration-none">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white">Add New</h6>
                                    <h2 class="text-white">Expense</h2>
                                </div>
                                <i class="fas fa-plus-circle fa-3x opacity-50"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card bg-warning text-white h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white">Today's Date</h6>
                                <h2 class="text-white"><?php echo date('M d, Y'); ?></h2>
                            </div>
                            <i class="fas fa-calendar-day fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Summary Cards -->
        <div class="row">
            <!-- Recent Expenses -->
            <div class="col-lg-8 mb-4">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Recent Expenses</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Expense Code</th>
                                        <th>Channel</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th>Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($stats['recent_expenses']) > 0): ?>
                                        <?php foreach ($stats['recent_expenses'] as $expense): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($expense['expenseCode']); ?></td>
                                                <td><?php echo htmlspecialchars($expense['expSubChannel']); ?></td>
                                                <td>$<?php echo number_format($expense['expcost'], 2); ?></td>
                                                <td><?php echo date('M d, Y', strtotime($expense['exdate'])); ?></td>
                                                <td><?php echo htmlspecialchars($expense['biztypeexp']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No expenses found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Summary by Categories -->
            <div class="col-lg-4">
                <!-- Channel Summary -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Top Expense Channels</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <?php if (count($channel_stats) > 0): ?>
                                <?php foreach ($channel_stats as $channel): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?php echo htmlspecialchars($channel['expSubChannel']); ?>
                                        <span class="badge bg-primary rounded-pill">
                                            $<?php echo number_format($channel['total'], 2); ?>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item">No data available</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                
                <!-- Business Type Summary -->
                <div class="card shadow">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Top Business Types</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <?php if (count($biztype_stats) > 0): ?>
                                <?php foreach ($biztype_stats as $biztype): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?php echo htmlspecialchars($biztype['biztypeexp']); ?>
                                        <span class="badge bg-success rounded-pill">
                                            $<?php echo number_format($biztype['total'], 2); ?>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="list-group-item">No data available</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light py-3 mt-5">
        <div class="container text-center">
            <p class="mb-0">Expense Tracking System &copy; <?php echo date('Y'); ?></p>
        </div>
    </footer>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>