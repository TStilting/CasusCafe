<?php

session_start();

// Check for login
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
  header("location: login.php");
  exit;
}

include("connection.php");

// Redirect if the role is not admin
if ($_SESSION['rol'] != 'admin') {
  header("location: index.php");
  exit;
}

// Fetch band names from the database
$bandNames = array();
foreach ($pdo->query("SELECT * FROM Band") as $row) {
  $bandNames[] = $row['bandNaam'];
}

// Process the event submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $band = $_POST['band_name'];
  $genre = $_POST['genre'];
  $prijs = $_POST['prijs'];
  $herkomst = $_POST['herkomst'];
  $omschrijving = $_POST['omschrijving'];

  // Store the event in the database
  $stmt = $pdo->prepare("INSERT INTO band (bandNaam, genre, prijs, herkomst, omschrijving) VALUES (?, ?, ?, ?, ?)");
  $stmt->execute([$band, $genre, $prijs, $herkomst, $omschrijving]);

}

?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin Agenda</title>
</head>
<header>
    <a href="admin.php">Event Toevoegen</a>
    <a href="index.php">Terug naar de Startpagina</a>
</header>
<body>
<h2>Add Event</h2>
  <form method="POST" action="">

    <label for="band_name">Band Naam:</label>
    <input type="text" id="band_name" name="band_name" required>
    <br>

    <label for="genre">Genre:</label>
    <input type="text" id="genre" name="genre" required>
    <br>

    <label for="prijs">Prijs (in euro):</label>
    <input type="number" id="prijs" name="prijs" required>
    <br>

    <label for="herkomst">Plaats van Herkomst:</label>
    <input type="text" id="herkomst" name="herkomst" required>
    <br>

    <label for="omschrijving">Omschrijving:</label>
    <input type="text" id="omschrijving" name="omschrijving" required>
    <br>

    <input type="submit" value="Add Band">
  </form>
  <div>
  <?php foreach ($bandNames as $bandName) :
    echo $bandName;
  endforeach; ?>
  </div>
</body>
</html>