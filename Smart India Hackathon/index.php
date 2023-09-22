<?php

$hostname = 'localhost';
$username = 'root';
$password = '';
$database = 'student_dropout';


$csvFile = 'Merged_data.csv';


$file = fopen($csvFile, 'r');
  

$columnNames = fgetcsv($file);


$conn = new mysqli($hostname, $username, $password, $database);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$tableCreationSQL = "CREATE TABLE IF NOT EXISTS dropout_rate (";
foreach ($columnNames as $columnName) {
    $tableCreationSQL .= "`$columnName` VARCHAR(255), "; // You can adjust the data type as needed
}
$tableCreationSQL = rtrim($tableCreationSQL, ', ') . ")";


$columns = implode(', ', array_map(function($column) { return "`$column`"; }, $columnNames));
$placeholders = implode(', ', array_fill(0, count($columnNames), '?'));
$sql = "INSERT INTO dropout_rate ($columns) VALUES ($placeholders)";
$stmt = $conn->prepare($sql);

// Loop through each row in the CSV file and insert into the table
while (($data = fgetcsv($file)) !== false) {
    // Bind parameters dynamically based on the number of columns
    $types = str_repeat('s', count($columnNames)); // Assuming all columns are of type VARCHAR
    $stmt->bind_param($types, ...$data);

    // Execute the statement
    if (!$stmt->execute()) {
        echo "Error inserting data: " . $stmt->error;
    }
}

fclose($file);
$conn->close();
?>
