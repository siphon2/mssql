<?php
date_default_timezone_set('Asia/Karachi');

// Decalre vital variables
$database = "AIMECSMS";
$user = "saller";
$password = "sky2000";
$status = $_COOKIE['status'];  // P | A
$seq = $_COOKIE['seq'];  // 1 | 2 | 3 | 4 | 5 | 6 | 7 | 8
$student_id = $_COOKIE['id'];
$date = $_COOKIE['date'];  // YYYY-MM-DD
$time = $_COOKIE['time'];
$conn = null;
$connection_string = 'DRIVER={SQL Server};SERVER=APP-SERVER2\SQLEXPRESS;DATABASE=' . $database;
$query = "
    UPDATE ST_DAILY_ATTENDENCE_DETAIL
    SET STATUS = '$status', APPROVED = '1', ATTENDANCE_TIME = '$time'
    WHERE SEQ_NO = $seq AND STUDENT_ID = $student_id AND ATTENDANCE_DATE = '$date';
";

$query = "UPDATE ST_DAILY_ATTENDENCE_DETAIL
SET STATUS = '$status', APPROVED = '1', ATTENDANCE_TIME = 
CASE 
    WHEN '$time' = 'null' THEN NULL
    ELSE '$time'
END
WHERE SEQ_NO = $seq AND STUDENT_ID = $student_id AND ATTENDANCE_DATE = '$date';
";


// Start database connection
$conn = @odbc_connect($connection_string, $user, $password);

if (!$conn)
{
    die("Connection failed: " . @odbc_errormsg());
}

// Execute MSSQL query and get results
$result = @odbc_exec($conn, $query);

// Format MSSQL query results
if ($result)
{
    $data = array();
    while ($row = @odbc_fetch_array($result))
    {
        $data[] = $row;
    }
}
else
{
    die("Error retrieving data: " . @odbc_errormsg());
}

// Close database connection
@odbc_close($conn);
header('Content-Type: application/json');
echo json_encode($data);


?>
