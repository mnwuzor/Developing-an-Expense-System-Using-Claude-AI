<?php
require_once 'config.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('index.php', 'Invalid expense ID', 'danger');
}

$id = (int) $_GET['id'];

// Fetch expense data
$sql = "SELECT * FROM expenses WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    redirect('index.php', 'Expense not found', 'danger');
}

$expense = $result->fetch_assoc();
$stmt->close();

// Fetch expense sub channels for dropdown
$channelSql = "SELECT * FROM expense_sub_channels ORDER BY name ASC";
$channelResult = $conn->query($channelSql);
$channels = [];
if ($channelResult->num_rows > 0) {
    while ($row = $channelResult->fetch_assoc()) {
        $channels[] = $row;
    }
}

// Fetch business types for dropdown
$bizSql = "SELECT * FROM business_types ORDER BY name ASC";
$bizResult = $conn->query($bizSql);
$bizTypes = [];
if ($bizResult->num_rows > 0) {
    while ($row = $bizResult->fetch_assoc()) {
        $bizTypes[] = $row;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Sanitize inputs
        $expenseCode = sanitize($conn, $_POST['expenseCode']);
        $userCode = sanitize($conn, $_POST['userCode']);
        $compCode = sanitize($conn, $_POST['compCode']);
        $expSubChannel = sanitize($conn, $_POST['expSubChannel']);
        $expcost = (float) $_POST['expcost'];
        $expdescrptn = sanitize($conn, $_POST['expdescrptn']);
        $exdate = sanitize($conn, $_POST['exdate']);
        $biztypeexp = sanitize($conn, $_POST['biztypeexp']);
        $addedby = sanitize($conn, $_POST['addedby']);
        
        // Update the expense
        $updateSql = "UPDATE expenses SET 
                    expenseCode = ?, 
                    userCode = ?, 
                    compCode = ?, 
                    expSubChannel = ?, 
                    expcost = ?, 
                    expdescrptn = ?, 
                    exdate = ?, 
                    biztypeexp = ?, 
                    addedby = ? 
                    WHERE id = ?";
        
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("ssssdsssi", 
            $expenseCode, 
            $userCode, 
            $compCode, 
            $expSubChannel, 
            $expcost, 
            $expdescrptn, 
            $exdate, 
            $biztypeexp, 
            $addedby,
            $id
        );
        
        if ($updateStmt->execute()) {
            $updateStmt->close();
            redirect('index.php', 'Expense updated successfully', 'success');
        } else {
            throw new Exception("Error updating expense: " . $updateStmt->error);
        }
        
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Expense</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .expense-row {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
        .select2-container {
            width: 100% !important;
        }
        @media (max-width: 768px) {
            .form-label {
                font-size: 0.9rem;
            }
            .btn {
                width: 100%;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row mb-3">
            <div class="col-md-6">
                <h2>Edit Expense</h2>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
        
        <?php if (isset($errorMessage)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $errorMessage; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <form method="post" action="edit_expense.php?id=<?php echo $id; ?>">
                    <div class="expense-row">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="expenseCode" class="form-label">Expense Code</label>
                                <input type="text" class="form-control" id="expenseCode" name="expenseCode" 
                                       value="<?php echo htmlspecialchars($expense['expenseCode']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="userCode" class="form-label">User Code</label>
                                <input type="text" class="form-control" id="userCode" name="userCode" 
                                       value="<?php echo htmlspecialchars($expense['userCode']); ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="compCode" class="form-label">Company Code</label>
                                <input type="text" class="form-control" id="compCode" name="compCode" 
                                       value="<?php echo htmlspecialchars($expense['compCode']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="expSubChannel" class="form-label">Expense Category</label>
                                <select class="form-select select2" id="expSubChannel" name="expSubChannel" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($channels as $channel): ?>
                                        <option value="<?php echo htmlspecialchars($channel['name']); ?>" 
                                                <?php echo ($expense['expSubChannel'] === $channel['name']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($channel['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="expcost" class="form-label">Cost</label>
                                <input type="number" step="0.01" class="form-control" id="expcost" name="expcost" 
                                       value="<?php echo htmlspecialchars($expense['expcost']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="exdate" class="form-label">Date</label>
                                <input type="date" class="form-control" id="exdate" name="exdate" 
                                       value="<?php echo htmlspecialchars($expense['exdate']); ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="biztypeexp" class="form-label">Business Type</label>
                                <select class="form-select select2" id="biztypeexp" name="biztypeexp" required>
                                    <option value="">Select Business Type</option>
                                    <?php foreach ($bizTypes as $bizType): ?>
                                        <option value="<?php echo htmlspecialchars($bizType['name']); ?>" 
                                                <?php echo ($expense['biztypeexp'] === $bizType['name']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($bizType['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="addedby" class="form-label">Added By</label>
                                <input type="text" class="form-control" id="addedby" name="addedby" 
                                       value="<?php echo htmlspecialchars($expense['addedby']); ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="expdescrptn" class="form-label">Description</label>
                                <textarea class="form-control" id="expdescrptn" name="expdescrptn" rows="3"><?php echo htmlspecialchars($expense['expdescrptn']); ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Expense
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
            
            // Auto dismiss alert after 5 seconds
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
        });
    </script>
</body>
</html>