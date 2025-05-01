<?php
require_once 'config.php';

// Fetch all expenses
$sql = "SELECT * FROM expenses ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Tracker System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        .expense-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }
        .action-buttons {
            white-space: nowrap;
        }
        @media (max-width: 767px) {
            .table-responsive {
                font-size: 0.85rem;
            }
            .action-buttons .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-4 mb-5">
        <div class="row mb-4">
            <div class="col-md-6">
                <h1>Expense Tracker</h1>
            </div>
            <div class="col-md-6 text-end">
                <a href="add_expense.php" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Add New Expense
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="expense-header">
                            <tr>
                                <th>Expense Code</th>
                                <th>User</th>
                                <th>Company</th>
                                <th>Sub Channel</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Business Type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $row["expenseCode"] . "</td>";
                                    echo "<td>" . $row["userCode"] . "</td>";
                                    echo "<td>" . $row["compCode"] . "</td>";
                                    echo "<td>" . $row["expSubChannel"] . "</td>";
                                    echo "<td>$" . number_format($row["expcost"], 2) . "</td>";
                                    echo "<td>" . date('M d, Y', strtotime($row["exdate"])) . "</td>";
                                    echo "<td>" . $row["biztypeexp"] . "</td>";
                                    echo "<td class='action-buttons'>";
                                    echo "<a href='edit_expense.php?id=" . $row["id"] . "' class='btn btn-sm btn-outline-primary me-1'><i class='fas fa-edit'></i></a>";
                                    echo "<a href='#' class='btn btn-sm btn-outline-danger delete-btn' data-id='" . $row["id"] . "'><i class='fas fa-trash'></i></a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' class='text-center'>No expenses found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this expense?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" id="confirmDelete" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Handle delete confirmation
            $('.delete-btn').click(function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                $('#confirmDelete').attr('href', 'delete_expense.php?id=' + id);
                var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                deleteModal.show();
            });
        });
    </script>
</body>
</html>