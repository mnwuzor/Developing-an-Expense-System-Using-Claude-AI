<?php
require_once 'config.php';

// Fetch expense sub channels for select2 dropdown
$channelSql = "SELECT * FROM exp_sub_channels ORDER BY channel_name";
$channelResult = $conn->query($channelSql);

// Fetch business expense types for select2 dropdown
$typeSql = "SELECT * FROM biz_expense_types ORDER BY type_name";
$typeResult = $conn->query($typeSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Expense</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <!-- Custom CSS -->
    <style>
        .expense-row {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            border: 1px solid #dee2e6;
        }
        .remove-row {
            cursor: pointer;
            color: #dc3545;
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
                <h1>Add New Expense</h1>
            </div>
            <div class="col-md-6 text-end">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Back to List
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form id="expenseForm">
                    <div id="expenseContainer">
                        <div class="expense-row" data-row="1">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="expenseCode1" class="form-label">Expense Code</label>
                                    <input type="text" class="form-control" id="expenseCode1" name="expenseCode[]" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="userCode1" class="form-label">User Code</label>
                                    <input type="text" class="form-control" id="userCode1" name="userCode[]" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="compCode1" class="form-label">Company Code</label>
                                    <input type="text" class="form-control" id="compCode1" name="compCode[]" required>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="expSubChannel1" class="form-label">Expense Sub Channel</label>
                                    <select class="form-select select2-dropdown" id="expSubChannel1" name="expSubChannel[]" required>
                                        <option value="">Select Channel</option>
                                        <?php 
                                        if ($channelResult->num_rows > 0) {
                                            while($channel = $channelResult->fetch_assoc()) {
                                                echo "<option value='" . $channel["channel_name"] . "'>" . $channel["channel_name"] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="biztypeexp1" class="form-label">Business Expense Type</label>
                                    <select class="form-select select2-dropdown" id="biztypeexp1" name="biztypeexp[]" required>
                                        <option value="">Select Type</option>
                                        <?php 
                                        if ($typeResult->num_rows > 0) {
                                            while($type = $typeResult->fetch_assoc()) {
                                                echo "<option value='" . $type["type_name"] . "'>" . $type["type_name"] . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="expcost1" class="form-label">Expense Cost</label>
                                    <input type="number" step="0.01" min="0" class="form-control" id="expcost1" name="expcost[]" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="exdate1" class="form-label">Expense Date</label>
                                    <input type="date" class="form-control" id="exdate1" name="exdate[]" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="addedby1" class="form-label">Added By</label>
                                    <input type="text" class="form-control" id="addedby1" name="addedby[]" required>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-12">
                                    <label for="expdescrptn1" class="form-label">Expense Description</label>
                                    <textarea class="form-control" id="expdescrptn1" name="expdescrptn[]" rows="2"></textarea>
                                </div>
                            </div>
                            
                            <div class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-danger remove-expense-row" 
                                        style="display: none;"><i class="fas fa-times"></i> Remove</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-12">
                            <button type="button" id="addRow" class="btn btn-secondary">
                                <i class="fas fa-plus-circle"></i> Add Another Expense
                            </button>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 offset-md-6">
                            <div class="d-grid">
                                <button type="submit" id="submitForm" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Save Expenses
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Loading Spinner Modal -->
    <div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center py-4">
                    <div class="spinner-border text-primary mb-3" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h5>Processing Your Request...</h5>
                    <p class="text-muted">Please wait while we save your expenses.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div class="modal fade" id="errorModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Error</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="errorModalBody">
                    Something went wrong. Please try again.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
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
            // Initialize Select2 for dropdown
            initSelect2();
            
            // Row counter
            let rowCount = 1;
            
            // Add new expense row
            $('#addRow').click(function() {
                rowCount++;
                
                // Show remove button on all rows
                $('.remove-expense-row').show();

                // Clone the channel options
                let channelOptions = '';
                $('#expSubChannel1 option').each(function() {
                    let selected = $(this).is(':selected') ? 'selected' : '';
                    channelOptions += `<option value="${$(this).val()}" ${selected}>${$(this).text()}</option>`;
                });
                
                // Clone the business type options
                let typeOptions = '';
                $('#biztypeexp1 option').each(function() {
                    let selected = $(this).is(':selected') ? 'selected' : '';
                    typeOptions += `<option value="${$(this).val()}" ${selected}>${$(this).text()}</option>`;
                });
                
                // Create new row HTML
                const newRow = `
                <div class="expense-row" data-row="${rowCount}">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="expenseCode${rowCount}" class="form-label">Expense Code</label>
                            <input type="text" class="form-control" id="expenseCode${rowCount}" name="expenseCode[]" required>
                        </div>
                        <div class="col-md-4">
                            <label for="userCode${rowCount}" class="form-label">User Code</label>
                            <input type="text" class="form-control" id="userCode${rowCount}" name="userCode[]" required>
                        </div>
                        <div class="col-md-4">
                            <label for="compCode${rowCount}" class="form-label">Company Code</label>
                            <input type="text" class="form-control" id="compCode${rowCount}" name="compCode[]" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="expSubChannel${rowCount}" class="form-label">Expense Sub Channel</label>
                            <select class="form-select select2-dropdown" id="expSubChannel${rowCount}" name="expSubChannel[]" required>
                                ${channelOptions}
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="biztypeexp${rowCount}" class="form-label">Business Expense Type</label>
                            <select class="form-select select2-dropdown" id="biztypeexp${rowCount}" name="biztypeexp[]" required>
                                ${typeOptions}
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="expcost${rowCount}" class="form-label">Expense Cost</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="expcost${rowCount}" name="expcost[]" required>
                        </div>
                        <div class="col-md-4">
                            <label for="exdate${rowCount}" class="form-label">Expense Date</label>
                            <input type="date" class="form-control" id="exdate${rowCount}" name="exdate[]" required>
                        </div>
                        <div class="col-md-4">
                            <label for="addedby${rowCount}" class="form-label">Added By</label>
                            <input type="text" class="form-control" id="addedby${rowCount}" name="addedby[]" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="expdescrptn${rowCount}" class="form-label">Expense Description</label>
                            <textarea class="form-control" id="expdescrptn${rowCount}" name="expdescrptn[]" rows="2"></textarea>
                        </div>
                    </div>
                    
                    <div class="text-end">
                        <button type="button" class="btn btn-sm btn-outline-danger remove-expense-row">
                            <i class="fas fa-times"></i> Remove
                        </button>
                    </div>
                </div>`;
                
                $('#expenseContainer').append(newRow);
                
                // Initialize Select2 for new dropdowns
                initSelect2();
            });
            
            // Remove expense row
            $(document).on('click', '.remove-expense-row', function() {
                $(this).closest('.expense-row').remove();
                
                // If only one row remains, hide its remove button
                if ($('.expense-row').length === 1) {
                    $('.remove-expense-row').hide();
                }
            });
            
            // Form submission with AJAX
            $('#expenseForm').submit(function(e) {
                e.preventDefault();
                
                // Show loading modal
                const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
                loadingModal.show();
                
                // Serialize form data
                const formData = $(this).serialize();
                
                // Send AJAX request
                $.ajax({
                    url: 'save_expense.php',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        loadingModal.hide();
                        try {
                            const result = JSON.parse(response);
                            if (result.status === 'success') {
                                // Redirect to index page
                                window.location.href = 'index.php';
                            } else {
                                // Show error modal
                                $('#errorModalBody').text(result.message);
                                const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                                errorModal.show();
                            }
                        } catch (e) {
                            // Show error modal for parsing error
                            $('#errorModalBody').text('Invalid response from server');
                            const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                            errorModal.show();
                        }
                    },
                    error: function(xhr, status, error) {
                        loadingModal.hide();
                        // Show error modal
                        $('#errorModalBody').text('Server error: ' + error);
                        const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                        errorModal.show();
                    }
                });
            });
            
            // Initialize Select2 function
            function initSelect2() {
                $('.select2-dropdown').select2({
                    theme: 'bootstrap-5',
                    width: 'resolve',
                    dropdownParent: $('body')
                });
            }
        });
    </script>
</body>
</html>