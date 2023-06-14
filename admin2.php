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

// Haal de info van de bands uit de database
$bandDisplays = array();
foreach ($pdo->query("SELECT * FROM Band") as $row) {
  $bandDisplays[] = $row;
}

// Doet alle acties na een submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $band = $_POST['band_name'];
  $genre = $_POST['genre'];
  $prijs = $_POST['prijs'];
  $herkomst = $_POST['herkomst'];
  $omschrijving = $_POST['omschrijving'];

  // Insert Query
  $stmt = $pdo->prepare("INSERT INTO band (bandNaam, genre, prijs, herkomst, omschrijving) VALUES (?, ?, ?, ?, ?)");
  $stmt->execute([$band, $genre, $prijs, $herkomst, $omschrijving]);

  echo 'Band Toegevoegd';

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
    <h2>Bands</h2>
    <?php foreach ($bandDisplays as $bandDisplay) : ?>
      <div>
        <p>Band Name: <?php echo $bandDisplay['bandNaam']; ?></p>
        <p>Genre: <?php echo $bandDisplay['genre']; ?></p>
        <p>Prijs: <?php echo $bandDisplay['prijs']; ?></p>
        <p>Herkomst: <?php echo $bandDisplay['herkomst']; ?></p>
        <p>Omschrijving: <?php echo $bandDisplay['omschrijving']; ?></p>
      </div>
      <hr>
    <?php endforeach; ?>
  </div>
</body>
</html>