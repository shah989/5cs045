<?php
if (!isset($_GET['id'])) {
    die("Movie ID not provided.");
}

$mysqli = new mysqli("localhost", "2417020", "Soumik3@wlv", "db2417020");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$id = (int) $_GET['id'];

$query = "SELECT * FROM `My movies list` WHERE ID = $id";
$result = $mysqli->query($query);

if ($result->num_rows == 0) {
    die("Movie not found.");
}

$movie = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $movie['Name']; ?> - Details</title>
    <style>
        body {
            font-family: Arial;
            background: #f2f2f2;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            background: white;
            margin: auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        img {
            width: 250px;
            border-radius: 10px;
        }
        .back-btn {
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }
    </style>
</head>
<body>

<div class="container">
    <a href="index.php" class="back-btn">⬅ Back to List</a>

    <h1><?php echo $movie['Name']; ?></h1>

    <img src="<?php echo $movie['image']; ?>" alt="Movie Image">

    <h3>Description:</h3>
    <p><?php echo nl2br($movie['Description']); ?></p>

    <h3>Release Date:</h3>
    <p><?php echo $movie['Released-date']; ?></p>

    <h3>Category:</h3>
    <p><?php echo $movie['Category']; ?></p>

    <h3>Rating:</h3>
    <p>
        <?php
            $stars = floor($movie['Rate']);
            for ($i = 0; $i < $stars; $i++) echo "⭐";
        ?>
    </p>
</div>

</body>
</html>
