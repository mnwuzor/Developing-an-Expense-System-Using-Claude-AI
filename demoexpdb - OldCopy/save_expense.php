<?php
require_once 'config.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get arrays from form
    $expenseCodes = isset($_POST['expenseCode']) ? $_POST['expenseCode'] : array();
    $userCodes = isset($_POST['userCode']) ? $_POST['userCode'] : array();
    $compCodes = isset($_POST['compCode']) ? $_POST['compCode'] : array();
    $expSubChannels = isset($_POST['expSubChannel']) ? $_POST['expSubChannel'] : array();
    $expcosts = isset($_POST['expcost']) ? $_POST['expcost'] : array();
    $expdescrptns = isset($_POST['expdescrptn']) ? $_POST['expdescrptn'] : array();
    $exdates = isset($_POST['exdate']) ? $_POST['exdate'] : array();
    $biztypeexps = isset($_POST['biztypeexp']) ? $_POST['biztypeexp'] : array();
    $addedby = isset($_POST['addedby']) ? $_POST['addedby'] : array();
    
    // Count rows
    $count = count($expenseCodes);
    $successCount = 0;
    $errorMessage = '';
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Loop through all expense rows
        for ($i = 0; $i < $count; $i++) {
            // Sanitize inputs
            $expenseCode = sanitize_input($expenseCodes[$i]);
            $userCode = sanitize_input($userCodes[$i]);
            $compCode = sanitize_input($compCodes[$i]);
            $expSubChannel = sanitize_input($expSubChannels[$i]);
            $expcost = floatval($expcosts[$i]);
            $expdescrptn = sanitize_input($expdescrptns[$i]);
            $exdate = sanitize_input($exdates[$i]);
            $biztypeexp = sanitize_input($biztypeexps[$i]);
            $addedBy = sanitize_input($addedby[$i]);
            
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
            
            // Prepare SQL statement
            $stmt = $conn->prepare("INSERT INTO expenses (expenseCode, userCode, compCode, expSubChannel, expcost, expdescrptn, exdate, biztypeexp, addedby) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            // Bind parameters
            $stmt->bind_param("ssssdssss", 
                $expenseCode, 
                $userCode, 
                $compCode, 
                $expSubChannel, 
                $expcost, 
                $expdescrptn, 
                $exdate, 
                $biztypeexp, 
                $addedBy
            );
            
            // Execute query
            if ($stmt->execute()) {
                $successCount++;
            } else {
                throw new Exception('Error inserting expense: ' . $stmt->error);
            }
            
            // Close statement
            $stmt->close();
        }
        
        // Commit transaction
        $conn->commit();
        
        // Return success response
        echo json_encode(array(
            'status' => 'success',
            'message' => $successCount . ' expense(s) added successfully.'
        ));
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        
        // Return error response
        echo json_encode(array(
            'status' => 'error',
            'message' => $e->getMessage()
        ));
    }
} else {
    // Return error for non-POST requests
    echo json_encode(array(
        'status' => 'error',
        'message' => 'Invalid request method.'
    ));
}

// Close database connection
$conn->close();