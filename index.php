<?php
$mysqli = new mysqli("localhost", "2417020", "Soumik3@wlv", "db2417020");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Insert new movie
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $mysqli->real_escape_string($_POST['Name']);
    $description = $mysqli->real_escape_string($_POST['Description']);
    $release_date = $mysqli->real_escape_string($_POST['Release_date']);
    $rate = $mysqli->real_escape_string($_POST['Rate']);
    $category = $mysqli->real_escape_string($_POST['Category']);

    // Handle image upload
    $imageName = $_FILES['image']['name'];
    $imageTmp = $_FILES['image']['tmp_name'];

    if (!is_dir("uploads")) {
        mkdir("uploads", 0777, true);
    }

    $uploadPath = "uploads/" . basename($imageName);
    move_uploaded_file($imageTmp, $uploadPath);

    $insert = "
        INSERT INTO My movies list (Name, Description, Released-date, Rate, image, Category)
        VALUES ('$name', '$description', '$release_date', '$rate', '$uploadPath', '$category')
    ";

    if (!$mysqli->query($insert)) {
        echo "Error: " . $mysqli->error;
    }
}

// ----- SEARCH & FILTER -----
$search = isset($_GET['search']) ? $mysqli->real_escape_string($_GET['search']) : "";
$filterCategory = isset($_GET['category']) ? $mysqli->real_escape_string($_GET['category']) : "";

// Build query
$query = "SELECT * FROM My movies list WHERE 1";

if (!empty($search)) {
    $query .= " AND Name LIKE '%$search%'";
}

if (!empty($filterCategory)) {
    $query .= " AND Category = '$filterCategory'";
}

$query .= " ORDER BY Released-date DESC";

$result = $mysqli->query($query);

// Fetch categories
$catQuery = $mysqli->query("SELECT DISTINCT Category FROM My movies list");
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Movies List</title>

<style>
body {
    font-family: Arial;
    background: var(--bg);
    color: var(--text);
    margin: 0;
    transition: 0.3s;
}
:root {
    --bg: #f2f2f2;
    --text: #000;
    --container: #fff;
    --nav: #fff;
}
body.dark {
    --bg: #1e1e1e;
    --text: #fff;
    --container: #2c2c2c;
    --nav: #2c2c2c;
}

.navbar {
    width: 100%;
    padding: 15px 30px;
    background: var(--nav);
    display: flex;
    justify-content: space-between;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    position: sticky;
    top: 0;
}
.navbar h1 { margin: 0; }
.toggle-btn {
    padding: 8px 15px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.container { max-width: 1200px; margin: auto; padding: 20px; }

.form-container, .movie-list {
    background: var(--container);
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    margin-bottom: 20px;
    animation: fadeIn 1s forwards;
    opacity: 0;
}
@keyframes fadeIn { to { opacity: 1; } }

input, textarea, select {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
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
}
button:hover { transform: scale(1.05); }

table { width: 100%; border-collapse: collapse; }
th, td { padding: 12px; border-bottom: 1px solid #666; }
th {
    background: #007bff;
    color: white;
}
img {
    width: 100px;
    border-radius: 8px;
}

.details-btn { padding: 8px 12px; background: green; color: white; border-radius: 6px; text-decoration: none; }
.edit-btn { padding: 8px 12px; background: orange; color: white; border-radius: 6px; text-decoration: none; }
.delete-btn { padding: 8px 12px; background: red; color: white; border-radius: 6px; text-decoration: none; }

.search-bar {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}
.search-bar input, .search-bar select {
    flex: 1;
}
</style>

</head>
<body>

<div class="navbar">
    <h1>🎬 My Movies</h1>
    <button class="toggle-btn" onclick="toggleDarkMode()">🌙 Dark Mode</button>
</div>

<div class="container">

<!-- SEARCH + FILTER -->
<div class="movie-list">
    <h2>Search & Filter</h2>

    <form method="GET" class="search-bar">
        <input type="text" name="search" placeholder="Search by movie name..." value="<?php echo htmlspecialchars($search); ?>">

        <select name="category">
            <option value="">All Categories</option>
            <?php while ($c = $catQuery->fetch_assoc()): ?>
                <option value="<?php echo $c['Category']; ?>" 
                    <?php if ($c['Category'] == $filterCategory) echo "selected"; ?>>
                    <?php echo $c['Category']; ?>
                </option>
            <?php endwhile; ?>
        </select>

        <button type="submit">Search</button>
    </form>
</div>

<!-- ADD MOVIE -->
<div class="form-container">
    <h2>Add a New Movie</h2>

    <form method="POST" enctype="multipart/form-data">
        <label>Movie Name:</label>
        <input type="text" name="Name" required>

        <label>Description:</label>
        <textarea name="Description" required></textarea>

        <label>Release Date:</label>
        <input type="date" name="Release_date" required>

        <label>Rating (1–5):</label>
        <input type="number" step="0.1" name="Rate" required>

        <label>Category:</label>
        <input type="text" name="Category" required>

        <label>Image:</label>
        <input type="file" name="image" accept="image/*" required>

        <button type="submit">Add Movie</button>
    </form>
</div>

<!-- MOVIE LIST -->
<div class="movie-list">
    <h2>Movies List</h2>

    <table>
        <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Release Date</th>
            <th>Rate</th>
            <th>Category</th>
            <th>Image</th>
            <th>Details</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['Name']; ?></td>
            <td><?php echo $row['Description']; ?></td>
            <td><?php echo $row['Released-date']; ?></td>

            <td>
                <?php
                    $stars = floor($row['Rate']);
                    for ($i = 0; $i < $stars; $i++) echo "⭐";
                ?>
            </td>

            <td><?php echo $row['Category']; ?></td>

            <td><img src="<?php echo $row['image']; ?>"></td>

            <td><a class="details-btn" href="details.php?id=<?php echo $row['ID']; ?>">View</a></td>
            <td><a class="edit-btn" href="edit.php?id=<?php echo $row['ID']; ?>">Edit</a></td>
            <td><a class="delete-btn" href="delete.php?id=<?php echo $row['ID']; ?>" onclick="return confirm('Delete this movie?')">Delete</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

</div>

<script>
// Dark mode
function toggleDarkMode() {
    document.body.classList.toggle("dark");
    localStorage.setItem("darkMode", document.body.classList.contains("dark") ? "enabled" : "");
}
if (localStorage.getItem("darkMode") === "enabled") document.body.classList.add("dark");
</script>

</body>
</html>