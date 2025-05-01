<?php
require_once 'config.php';

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Expense</title>
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
            position: relative;
        }
        .remove-row {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .select2-container {
            width: 100% !important;
        }
        @media (max-width: 768px) {
            .expense-row {
                padding: 10px;
            }
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
                <h2>Add New Expense</h2>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <form id="expenseForm">
                    <div id="expense-container">
                        <div class="expense-row" data-index="0">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="expenseCode0" class="form-label">Expense Code</label>
                                    <input type="text" class="form-control" id="expenseCode0" name="expenses[0][expenseCode]" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="userCode0" class="form-label">User Code</label>
                                    <input type="text" class="form-control" id="userCode0" name="expenses[0][userCode]" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="compCode0" class="form-label">Company Code</label>
                                    <input type="text" class="form-control" id="compCode0" name="expenses[0][compCode]" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="expSubChannel0" class="form-label">Expense Category</label>
                                    <select class="form-select select2" id="expSubChannel0" name="expenses[0][expSubChannel]" required>
                                        <option value="">Select Category</option>
                                        <?php foreach ($channels as $channel): ?>
                                            <option value="<?php echo htmlspecialchars($channel['name']); ?>"><?php echo htmlspecialchars($channel['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="expcost0" class="form-label">Cost</label>
                                    <input type="number" step="0.01" class="form-control" id="expcost0" name="expenses[0][expcost]" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="exdate0" class="form-label">Date</label>
                                    <input type="date" class="form-control" id="exdate0" name="expenses[0][exdate]" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="biztypeexp0" class="form-label">Business Type</label>
                                    <select class="form-select select2" id="biztypeexp0" name="expenses[0][biztypeexp]" required>
                                        <option value="">Select Business Type</option>
                                        <?php foreach ($bizTypes as $bizType): ?>
                                            <option value="<?php echo htmlspecialchars($bizType['name']); ?>"><?php echo htmlspecialchars($bizType['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="addedby0" class="form-label">Added By</label>
                                    <input type="text" class="form-control" id="addedby0" name="expenses[0][addedby]" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="expdescrptn0" class="form-label">Description</label>
                                    <textarea class="form-control" id="expdescrptn0" name="expenses[0][expdescrptn]" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <button type="button" id="addRowBtn" class="btn btn-success">
                                <i class="fas fa-plus"></i> Add Another Expense
                            </button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" id="submitBtn" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Expenses
                            </button>
                            <button type="button" id="resetBtn" class="btn btn-warning ms-2">
                                <i class="fas fa-undo"></i> Reset Form
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Loading Spinner Modal -->
    <div class="modal fade" id="loadingModal" tabindex="-1" aria-labelledby="loadingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Saving expenses. Please wait...</p>
                </div>
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
            // Initialize today's date for date inputs
            var today = new Date().toISOString().split('T')[0];
            $('input[type="date"]').val(today);
            
            // Initialize Select2
            initializeSelect2();
            
            // Row counter
            let rowIndex = 1;
            
            // Add row button
            $('#addRowBtn').on('click', function() {
                const newRow = `
                    <div class="expense-row" data-index="${rowIndex}">
                        <button type="button" class="btn btn-sm btn-danger remove-row">
                            <i class="fas fa-times"></i>
                        </button>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="expenseCode${rowIndex}" class="form-label">Expense Code</label>
                                <input type="text" class="form-control" id="expenseCode${rowIndex}" name="expenses[${rowIndex}][expenseCode]" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="userCode${rowIndex}" class="form-label">User Code</label>
                                <input type="text" class="form-control" id="userCode${rowIndex}" name="expenses[${rowIndex}][userCode]" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="compCode${rowIndex}" class="form-label">Company Code</label>
                                <input type="text" class="form-control" id="compCode${rowIndex}" name="expenses[${rowIndex}][compCode]" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="expSubChannel${rowIndex}" class="form-label">Expense Category</label>
                                <select class="form-select select2-new" id="expSubChannel${rowIndex}" name="expenses[${rowIndex}][expSubChannel]" required>
                                    <option value="">Select Category</option>
                                    <?php foreach ($channels as $channel): ?>
                                        <option value="<?php echo htmlspecialchars($channel['name']); ?>"><?php echo htmlspecialchars($channel['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="expcost${rowIndex}" class="form-label">Cost</label>
                                <input type="number" step="0.01" class="form-control" id="expcost${rowIndex}" name="expenses[${rowIndex}][expcost]" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="exdate${rowIndex}" class="form-label">Date</label>
                                <input type="date" class="form-control" id="exdate${rowIndex}" name="expenses[${rowIndex}][exdate]" value="${today}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="biztypeexp${rowIndex}" class="form-label">Business Type</label>
                                <select class="form-select select2-new" id="biztypeexp${rowIndex}" name="expenses[${rowIndex}][biztypeexp]" required>
                                    <option value="">Select Business Type</option>
                                    <?php foreach ($bizTypes as $bizType): ?>
                                        <option value="<?php echo htmlspecialchars($bizType['name']); ?>"><?php echo htmlspecialchars($bizType['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="addedby${rowIndex}" class="form-label">Added By</label>
                                <input type="text" class="form-control" id="addedby${rowIndex}" name="expenses[${rowIndex}][addedby]" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="expdescrptn${rowIndex}" class="form-label">Description</label>
                                <textarea class="form-control" id="expdescrptn${rowIndex}" name="expenses[${rowIndex}][expdescrptn]" rows="3"></textarea>
                            </div>
                        </div>
                    </div>
                `;
                
                $('#expense-container').append(newRow);
                $('.select2-new').select2({
                    theme: 'bootstrap-5'
                }).removeClass('select2-new');
                
                rowIndex++;
            });
            
            // Remove row button
            $(document).on('click', '.remove-row', function() {
                $(this).closest('.expense-row').remove();
            });
            
            // Reset form button
            $('#resetBtn').on('click', function() {
                $('#expenseForm').trigger('reset');
                $('.expense-row:not(:first)').remove();
                $('.select2').val(null).trigger('change');
            });
            
            // Form submission
            $('#expenseForm').on('submit', function(e) {
                e.preventDefault();
                
                // Show loading modal
                $('#loadingModal').modal('show');
                
                // Get form data
                const formData = $(this).serializeArray();
                
                // Use AJAX to submit the form
                $.ajax({
                    url: 'save_expense.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        // Hide loading modal
                        $('#loadingModal').modal('hide');
                        
                        if (response.status === 'success') {
                            // Redirect to index page
                            window.location.href = 'index.php';
                        } else {
                            // Show error message
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        // Hide loading modal
                        $('#loadingModal').modal('hide');
                        
                        // Show error message
                        alert('An error occurred while saving expenses. Please try again.');
                        console.error(xhr.responseText);
                    }
                });
            });
            
            // Initialize Select2 function
            function initializeSelect2() {
                $('.select2').select2({
                    theme: 'bootstrap-5',
                    width: '100%'
                });
            }
        });
    </script>
</body>
</html>