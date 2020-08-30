<?php

$time_start = microtime(true); 

$mysqli = new mysqli("localhost", "root", "", "northwind");

mysqli_query($mysqli, "SET profiling = 1;");
if (mysqli_errno($mysqli)) { die( "ERROR ".mysqli_errno($link) . ": " . mysqli_error($link) ); }

$query="SELECT Categories.CategoryName,
`products`.ProductName,
SUM(`order details`.Quantity) AS ProductSales 
FROM `products` 
INNER JOIN `categories` ON `products`.CategoryID = `categories`.CategoryID 
INNER JOIN `order details` ON `products`.ProductID = `order details`.ProductID GROUP BY `products`.`ProductName`
";
$result = mysqli_query($mysqli, $query);
if (mysqli_errno($mysqli)) { die( "ERROR ".mysqli_errno($mysqli) . ": " . mysqli_error($mysqli) ); }

$exec_time_result=mysqli_query($mysqli, "SELECT query_id, SUM(duration) FROM information_schema.profiling GROUP BY query_id ORDER BY query_id DESC LIMIT 1;");
if (mysqli_errno($mysqli)) { die( "ERROR ".mysqli_errno($mysqli) . ": " . mysqli_error($mysqli) ); }
$exec_time_row = mysqli_fetch_array($exec_time_result);

echo "<p>Query executed in ".$exec_time_row[1].' seconds';

?>