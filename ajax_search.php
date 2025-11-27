<?php
// Connect to database
$mysqli = new mysqli("localhost", "2417020", "Soumik3@wlv", "db2417020");

if ($mysqli->connect_error) {
    die(json_encode(["error" => $mysqli->connect_error]));
}

// -----------------------------
//  AJAX SEARCH + CATEGORY FILTER
// -----------------------------
$search = isset($_GET['search']) ? $mysqli->real_escape_string($_GET['search']) : "";
$category = isset($_GET['category']) ? $mysqli->real_escape_string($_GET['category']) : "";

// Build SQL query
$sql = "SELECT * FROM `My movies list` WHERE 1";

// Search keyword
if (!empty($search)) {
    $sql .= " AND Name LIKE '%$search%'";
}

// Category filter
if (!empty($category)) {
    $sql .= " AND Category = '$category'";
}

// Sort by date
$sql .= " ORDER BY `Released-date` DESC";

// Run query and convert to JSON
$result = $mysqli->query($sql)->fetch_all(MYSQLI_ASSOC);

header("Content-Type: application/json");
echo json_encode($result);
?>
