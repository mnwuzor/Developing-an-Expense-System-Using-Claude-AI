<?php
require_once 'config.php';

// Fetch all expenses
$sql = "SELECT * FROM expenses ORDER BY exdate DESC";
$result = $conn->query($sql);

// Handle messages
$message = '';
$message_type = '';
if (isset($_SESSION['message']) && isset($_SESSION['message_type'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'];
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Tracker</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .table-responsive {
            overflow-x: auto;
        }
        .action-btns {
            white-space: nowrap;
        }
        @media (max-width: 768px) {
            .table th, .table td {
                font-size: 0.85rem;
            }
            .btn-sm {
                padding: 0.25rem 0.4rem;
                font-size: 0.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="row mb-3">
            <div class="col-md-6">
                <h2>Expense Tracker</h2>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="add_expense.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Expense
                </a>
            </div>
        </div>

        <?php if ($message): ?>
        <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="expenseTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Expense Code</th>
                                <th>Date</th>
                                <th>User Code</th>
                                <th>Company</th>
                                <th>Category</th>
                                <th>Amount</th>
                                <th>Description</th>
                                <th>Business Type</th>
                                <th>Added By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php //if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['expenseCode']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($row['exdate'])); ?></td>
                                        <td><?php echo htmlspecialchars($row['userCode']); ?></td>
                                        <td><?php echo htmlspecialchars($row['compCode']); ?></td>
                                        <td><?php echo htmlspecialchars($row['expSubChannel']); ?></td>
                                        <td><?php echo number_format($row['expcost'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($row['expdescrptn']); ?></td>
                                        <td><?php echo htmlspecialchars($row['biztypeexp']); ?></td>
                                        <td><?php echo htmlspecialchars($row['addedby']); ?></td>
                                        <td class="action-btns">
                                            <a href="edit_expense.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="javascript:void(0)" class="btn btn-sm btn-danger delete-btn" data-id="<?php echo $row['id']; ?>">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php //else: ?>
                                <!-- <tr>
                                    <td colspan="11" class="text-center">No expenses found</td>
                                </tr>-->
                            <?php //endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this expense? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" id="confirmDelete" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#expenseTable').DataTable({
                responsive: true,
                "order": [[ 0, "desc" ]],
                "pageLength": 10,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                "columnDefs": [
                    { "orderable": false, "targets": 10 }
                ]
            });

            // Delete confirmation modal
            $('.delete-btn').on('click', function() {
                const id = $(this).data('id');
                $('#confirmDelete').attr('href', 'delete_expense.php?id=' + id);
                $('#deleteModal').modal('show');
            });

            // Auto dismiss alert after 5 seconds
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
        });
    </script>
</body>
</html>