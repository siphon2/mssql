<?php
date_default_timezone_set('Asia/Karachi');

// Declare vital variables
$database = $_COOKIE['db'];;
$user = $_COOKIE['user'];
$password = $_COOKIE['pass'];
$name = $_COOKIE['name'];
$date = $_COOKIE['date'];
$conn = null;
$connection_string = 'DRIVER={SQL Server};SERVER=APP-SERVER2\SQLEXPRESS;DATABASE=' . $database;
$conn = odbc_connect($connection_string, $user, $password);
$query = "SELECT
    S.STUDENTID, S.ROLLNO, S.STUDENTNAME,  
    SUBJECTNAME = CASE TTD.IS_BREAK WHEN '1' THEN 'BREAK' ELSE ISNULL(SS.SUBJECTNAME, '') END, TTD.REMARKS,
    DOCUMENT_DATE = cast(ttH.DOCUMENT_DATE AS DATE), TTD.SEQ_NO, START_TIME = CONVERT(VARCHAR, TTD.START_TIME, 108), END_TIME = CONVERT(VARCHAR, TTD.end_TIME, 108),
    EXAMINATIONNAME='',
    STATUS = CASE TTD.IS_BREAK WHEN '1' THEN 'B' ELSE ISNULL(DAD.STATUS, 'A') END,  
    DAD.ATTENDANCE_TIME,  EMPLOYEENAME='',
    ATTENDANCE_STATUS =  
    CASE  
        WHEN (SELECT COUNT(D.STATUS)  
                FROM ST_DAILY_ATTENDENCE_DETAIL AS D  
                WHERE D.APPROVED = '1' AND D.STUDENT_ID = S.STUDENTID AND D.SECTION_ID = SG.SECTIONID  
                AND D.ATTENDANCE_DATE = TTH.DOCUMENT_DATE AND STATUS IN ('A','T','P','L') AND D.LECTURE = '1'  
            ) < 1 THEN 'P'  
        WHEN (SELECT COUNT(D.STATUS)  
                FROM ST_DAILY_ATTENDENCE_DETAIL AS D  
                WHERE D.APPROVED = '1' AND D.STUDENT_ID = S.STUDENTID AND D.SECTION_ID = SG.SECTIONID  
                AND D.ATTENDANCE_DATE = TTH.DOCUMENT_DATE AND STATUS IN ('A','T') AND D.LECTURE = '1'  
            ) > 1 THEN 'A'  
        WHEN (SELECT COUNT(D.STATUS)  
                FROM ST_DAILY_ATTENDENCE_DETAIL AS D  
                WHERE D.APPROVED = '1' AND D.STUDENT_ID = S.STUDENTID AND D.SECTION_ID = SG.SECTIONID  
                AND D.ATTENDANCE_DATE = TTH.DOCUMENT_DATE AND STATUS IN ('A','T','P') AND D.LECTURE = '1'  
            ) < 1 THEN 'L'
        ELSE 'P'  
    END  
FROM
    TIME_TABLE_HEADER AS TTH
    JOIN TIME_TABLE_DETAIL AS TTD ON TTD.COMPANY_CODE = TTH.COMPANY_CODE AND TTD.DOCUMENT_NO = TTH.DOCUMENT_NO  
    LEFT JOIN STSUBJECTS AS SS ON SS.SUBJECTID = TTD.SUBJECT_ID  
    JOIN STSTUDENTGRADE AS SG ON SG.SECTIONID = TTH.GRADE_ID  
    JOIN STSTUDENT AS S ON S.STUDENTID = SG.STUDENTID  
    LEFT JOIN ST_DAILY_ATTENDENCE_DETAIL AS DAD ON DAD.SECTION_ID = TTH.GRADE_ID AND DAD.ATTENDANCE_DATE = TTH.DOCUMENT_DATE  
    AND DAD.SEQ_NO = TTD.SEQ_NO AND DAD.STUDENT_ID = S.STUDENTID AND DAD.APPROVED = '1'  
WHERE  
    S.STUDENTNAME = '$name'
    AND TTH.DOCUMENT_DATE BETWEEN Cast('$date' AS date) AND Cast('$date' AS date)
    AND (DAD.ATTENDANCE_TYPE = 'ALL' OR 'ALL' = 'ALL')  
ORDER BY TTD.SEQ_NO";

// Start database connection
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
