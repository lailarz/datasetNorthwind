<?php

$time_start = microtime(true); 

$mysqli = new mysqli("localhost", "root", "", "northwind");

mysqli_query($mysqli, "SET profiling = 1;");
if (mysqli_errno($mysqli)) { die( "ERROR ".mysqli_errno($link) . ": " . mysqli_error($link) ); }

$query="SELECT
    FirstSet.CustomerName,
    SecondSet.productName,
    COUNT(SecondSet.ProductName) Orders
FROM
(
    SELECT`orders`.`OrderID`, `customers`.`CompanyName` AS CustomerName, `order details`.`ProductID`, `customers`.`CustomerID`
    FROM `orders`
    INNER JOIN `customers` ON `orders`.`CustomerID` = `customers`.`CustomerID` 
    INNER JOIN `order details` ON `orders`.`OrderID` = `order details`.`OrderID`
) AS FirstSet
INNER JOIN
(
    SELECT `order details`.`ProductID`, `products`.`ProductName`, `orders`.`OrderID`, `orders`.`CustomerID`
    FROM `order details`
    INNER JOIN orders ON `order details`.OrderID = `orders`.OrderID
    INNER JOIN products ON `order details`.ProductID = `products`.ProductID
) AS SecondSet
ON FirstSet.OrderID = SecondSet.OrderID AND FirstSet.ProductID = SecondSet.ProductID
GROUP BY FirstSet.CustomerName, SecondSet.ProductName
ORDER BY orders DESC";
$result = mysqli_query($mysqli, $query);
if (mysqli_errno($mysqli)) { die( "ERROR ".mysqli_errno($mysqli) . ": " . mysqli_error($mysqli) ); }

$exec_time_result=mysqli_query($mysqli, "SELECT query_id, SUM(duration) FROM information_schema.profiling GROUP BY query_id ORDER BY query_id DESC LIMIT 1;");
if (mysqli_errno($mysqli)) { die( "ERROR ".mysqli_errno($mysqli) . ": " . mysqli_error($mysqli) ); }
$exec_time_row = mysqli_fetch_array($exec_time_result);

echo "<p>Query executed in ".$exec_time_row[1].' seconds';

?>