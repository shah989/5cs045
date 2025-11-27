<?php
$mysqli = new mysqli("localhost", "2417020", "Soumik3@wlv", "db2417020");

// Validate ID
if (!isset($_GET['id'])) die("No book selected.");
$id = intval($_GET['id']);

// Get book data
$stmt = $mysqli->prepare("SELECT * FROM `My movies list` WHERE `ID`=?");
if (!$stmt) die("SQL Error: " . $mysqli->error);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) die("Book not found.");
$book = $result->fetch_assoc();

// Update book
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['Name'];
    $description = $_POST['Description'];
    $release_date = $_POST['Release_date'];
    $rate = $_POST['Rate'];
    $category = $_POST['Category'];

    // If new image is uploaded
    if (!empty($_FILES['image']['name'])) {

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $uniqueName = uniqid("img_", true) . "." . $ext;

        if (!is_dir("uploads")) mkdir("uploads", 0777, true);
        $uploadPath = "uploads/" . $uniqueName;

        move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath);

        $update = $mysqli->prepare("
            UPDATE `My movies list`
            SET `Name`=?, `Description`=?, `Released-date`=?, `Rate`=?, `Category`=?, `image`=?
            WHERE `ID`=?
        ");

        $update->bind_param("sssissi",
            $name, $description, $release_date, $rate, $category, $uploadPath, $id
        );

    } else {
        // Without new image
        $update = $mysqli->prepare("
            UPDATE `My movies list`
            SET `Name`=?, `Description`=?, `Released-date`=?, `Rate`=?, `Category`=?
            WHERE `ID`=?
        ");

        $update->bind_param("sssdsi",
            $name, $description, $release_date, $rate, $category, $id
        );
    }

    if ($update->execute()) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error updating: " . $mysqli->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Book</title>

<style>
body {
    font-family: Arial;
    background: var(--bg);
    color: var(--text);
    margin: 0;
}
:root {
    --bg: #f2f2f2;
    --text: #000;
    --container: #fff;
}
body.dark {
    --bg: #1e1e1e;
    --text: #fff;
    --container: #2c2c2c;
}
.navbar {
    padding: 15px;
    background: var(--container);
    box-shadow: 0 2px 5px rgba(0,0,0,0.3);
}
.container {
    max-width: 800px;
    margin: auto;
    padding: 20px;
}
.card {
    background: var(--container);
    padding: 20px;
    border-radius: 10px;
}
label { font-weight: bold; margin-top: 15px; display: block; }
input, textarea, select {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    background: var(--bg);
    color: var(--text);
    border: 1px solid #666;
    border-radius: 5px;
}
button {
    padding: 12px 25px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    margin-top: 10px;
}
button:hover { transform: scale(1.05); }
img {
    width: 150px;
    border-radius: 10px;
    margin-bottom: 15px;
}
.back-btn {
    display: inline-block;
    margin-top: 15px;
    padding: 10px 20px;
    background: #999;
    color: white;
    text-decoration: none;
    border-radius: 6px;
}
</style>
</head>

<body>

<div class="navbar">
    <h2>✏ Edit Book</h2>
</div>

<div class="container">
    <div class="card">

        <form method="POST" enctype="multipart/form-data">

            <label>Book Name:</label>
            <input type="text" name="Name" value="<?php echo $book['Name']; ?>" required>

            <label>Description:</label>
            <textarea name="Description" required><?php echo $book['Description']; ?></textarea>

            <label>Release Date:</label>
            <input type="date" name="Release_date" value="<?php echo $book['Released-date']; ?>" required>

            <label>Rate (1–5):</label>
            <input type="number" step="0.1" min="1" max="5" name="Rate" value="<?php echo $book['Rate']; ?>" required>

            <label>Category:</label>
            <select name="Category" required>
                <option <?php if($book['Category']=="Fantasy") echo "selected"; ?>>Fantasy</option>
                <option <?php if($book['Category']=="Romance") echo "selected"; ?>>Romance</option>
                <option <?php if($book['Category']=="Sci-Fi") echo "selected"; ?>>Sci-Fi</option>
                <option <?php if($book['Category']=="Horror") echo "selected"; ?>>Horror</option>
                <option <?php if($book['Category']=="Mystery") echo "selected"; ?>>Mystery</option>
                <option <?php if($book['Category']=="Other") echo "selected"; ?>>Other</option>
            </select>

            <label>Current Image:</label><br>
            <img src="<?php echo $book['image']; ?>" alt="Book Image">

            <label>Upload New Image (optional):</label>
            <input type="file" name="image" accept="image/*">

            <button type="submit">💾 Save Changes</button>
        </form>

        <a href="index.php" class="back-btn">⬅ Back</a>

    </div>
</div>

<script>
if (localStorage.getItem("darkMode") === "enabled")
    document.body.classList.add("dark");
</script>

</body>
</html>
