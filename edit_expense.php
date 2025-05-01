<?php
require_once 'config.php';

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = intval($_GET['id']);

// Fetch expense data
$stmt = $conn->prepare("SELECT * FROM expenses WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: index.php');
    exit;
}

$expense = $result->fetch_assoc();
$stmt->close();

// Fetch expense sub channels for select2 dropdown
$channelSql = "SELECT * FROM exp_sub_channels ORDER BY channel_name";
$channelResult = $conn->query($channelSql);

// Fetch business expense types for select2 dropdown
$typeSql = "SELECT * FROM biz_expense_types ORDER BY type_name";
$typeResult = $conn->query($typeSql);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Sanitize inputs
        $expenseCode = sanitize_input($_POST['expenseCode']);
        $userCode = sanitize_input($_POST['userCode']);
        $compCode = sanitize_input($_POST['compCode']);
        $expSubChannel = sanitize_input($_POST['expSubChannel']);
        $expcost = floatval($_POST['expcost']);
        $expdescrptn = sanitize_input($_POST['expdescrptn']);
        $exdate = sanitize_input($_POST['exdate']);
        $biztypeexp = sanitize_input($_POST['biztypeexp']);
        $addedBy = sanitize_input($_POST['addedby']);
        
        // Validate inputs
        if (empty($expenseCode) || empty($userCode) || empty($compCode) || 
            empty($expSubChannel) || $expcost <= 0 || empty($exdate) || 
            empty($biztypeexp) || empty($addedBy)) {
            throw new Exception('All fields are required and expense cost must be greater than zero.');
        }
        
        // Validate date format
        if (!validateDate($exdate)) {
            throw new Exception('Invalid date format. Please use YYYY-MM-DD format.');
        }
        
        // Update expense
        $updateStmt = $conn->prepare("UPDATE expenses SET expenseCode = ?, userCode = ?, compCode = ?, 
                                     expSubChannel = ?, expcost = ?, expdescrptn = ?, exdate = ?, 
                                     biztypeexp = ?, addedby = ? WHERE id = ?");
        
        $updateStmt->bind_param("ssssdssssi", 
            $expenseCode, 
            $userCode, 
            $compCode, 
            $expSubChannel, 
            $expcost, 
            $expdescrptn, 
            $exdate, 
            $biztypeexp, 
            $addedBy,
            $id
        );
        
        if ($updateStmt->execute()) {
            // Redirect to index page after successful update
            header('Location: index.php');
            exit;
        } else {
            throw new Exception('Error updating expense: ' . $updateStmt->error);
        }
        
    } catch (Exception $e) {
        $error = $e->getMessage();
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <style>
        .expense-form {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
        .select2-container {
            width: 100% !important;
        }
        @media (max-width: 767px) {
            .form-label {
                font-size: 0.9rem;
            }
            .btn {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-4 mb-5">
        <div class="row mb-4">
            <div class="col-md-6">
                <h1>Edit Expense</h1>
            </div>
            <div class="col-md-6 text-end">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>
            </div>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="post" action="edit_expense.php?id=<?php echo $id; ?>">
                    <div class="expense-form">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="expenseCode" class="form-label">Expense Code</label>
                                <input type="text" class="form-control" id="expenseCode" name="expenseCode" 
                                       value="<?php echo htmlspecialchars($expense['expenseCode']); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label for="userCode" class="form-label">User Code</label>
                                <input type="text" class="form-control" id="userCode" name="userCode" 
                                       value="<?php echo htmlspecialchars($expense['userCode']); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label for="compCode" class="form-label">Company Code</label>
                                <input type="text" class="form-control" id="compCode" name="compCode" 
                                       value="<?php echo htmlspecialchars($expense['compCode']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="expSubChannel" class="form-label">Expense Sub Channel</label>
                                <select class="form-select select2-dropdown" id="expSubChannel" name="expSubChannel" required>
                                    <option value="">Select Channel</option>
                                    <?php 
                                    if ($channelResult->num_rows > 0) {
                                        while($channel = $channelResult->fetch_assoc()) {
                                            $selected = ($channel["channel_name"] == $expense['expSubChannel']) ? 'selected' : '';
                                            echo "<option value='" . $channel["channel_name"] . "' $selected>" . $channel["channel_name"] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="biztypeexp" class="form-label">Business Expense Type</label>
                                <select class="form-select select2-dropdown" id="biztypeexp" name="biztypeexp" required>
                                    <option value="">Select Type</option>
                                    <?php 
                                    if ($typeResult->num_rows > 0) {
                                        while($type = $typeResult->fetch_assoc()) {
                                            $selected = ($type["type_name"] == $expense['biztypeexp']) ? 'selected' : '';
                                            echo "<option value='" . $type["type_name"] . "' $selected>" . $type["type_name"] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="expcost" class="form-label">Expense Cost</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="expcost" name="expcost" 
                                       value="<?php echo htmlspecialchars($expense['expcost']); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label for="exdate" class="form-label">Expense Date</label>
                                <input type="date" class="form-control" id="exdate" name="exdate" 
                                       value="<?php echo htmlspecialchars($expense['exdate']); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label for="addedby" class="form-label">Added By</label>
                                <input type="text" class="form-control" id="addedby" name="addedby" 
                                       value="<?php echo htmlspecialchars($expense['addedby']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="expdescrptn" class="form-label">Expense Description</label>
                                <textarea class="form-control" id="expdescrptn" name="expdescrptn" rows="3"><?php echo htmlspecialchars($expense['expdescrptn']); ?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <a href="index.php" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                        </div>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-save me-1"></i> Update Expense
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Custom JS -->
    <script>
        $(document).ready(function() {
            // Initialize Select2 for dropdowns
            $('.select2-dropdown').select2({
                theme: 'bootstrap-5',
                width: 'resolve',
                dropdownParent: $('body')
            });
        });
    </script>
</body>
</html>