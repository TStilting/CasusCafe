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

$bandlidDisplays = array();
foreach ($pdo->query("SELECT * FROM Lineup") as $row) {
  $bandlidDisplays[] = $row;
}

// Retrieve all band names
$bandNames = array();
foreach ($pdo->query("SELECT * FROM Band") as $row) {
  $bandNames[$row['bandId']] = $row['bandNaam'];
}

// Doet alle acties na een submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $naam = $_POST['naam'];
  $tussenvoegsel = $_POST['tussenvoegsel'];
  $achternaam = $_POST['achternaam'];
  $band = $_POST['band'];
  $omschrijving = $_POST['omschrijving'];
  $foto = $_FILES['foto']['name'];

  // Upload photo
  $directory = "uploads/";
  $bestand = $directory . basename($_FILES["foto"]["name"]);
  move_uploaded_file($_FILES["foto"]["tmp_name"], $bestand);

  // Insert Query
  $stmt = $pdo->prepare("INSERT INTO Lineup (naam, tussenvoegsel, achternaam, Band_bandId, omschrijving, foto) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->execute([$naam, $tussenvoegsel, $achternaam, $band, $omschrijving, $foto]);

    //herlaad de pagina na de submit
    header("Location: admin3.php");
    echo 'Bandlid Toegevoegd';
    die();
}

?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin Agenda</title>
</head>
<header>
    <a href="admin.php">Event Toevoegen</a>
    <a href="admin2.php">Band Toevoegen</a>
    <a href="index.php">Terug naar de Startpagina</a>
</header>
<body>
<h2>Add Bandlid</h2>
  <form method="POST" action="" enctype="multipart/form-data">

    <label for="naam">Naam*:</label>
    <input type="text" id="naam" name="naam" required>
    <br>

    <label for="tussenvoegsel">Tussenvoegsel:</label>
    <input type="text" id="tussenvoegsel" name="tussenvoegsel">
    <br>

    <label for="achternaam">Achternaam*:</label>
    <input type="text" id="achternaam" name="achternaam" required>
    <br>

    <label for="band">Band*:</label>
    <select id="band" name="band" required>
      <option value="" disabled selected>Selecteer een band</option>
      <?php foreach ($bandNames as $bandId => $bandName) : ?>
        <option value="<?php echo $bandId; ?>"><?php echo $bandName; ?></option>
      <?php endforeach; ?>
    </select>
    <br>

    <label for="omschrijving">Omschrijving*:</label>
    <textarea id="omschrijving" name="omschrijving" required></textarea>
    <br>

    <label for="foto">Foto*:</label>
    <input type="file" id="foto" name="foto" required>
    <br>

    <input type="submit" value="Add Bandlid">
  </form>
  <div>
    <h2>Alle Bandleden</h2>
    <?php if (empty($bandlidDisplays)) : ?>
      <p>Er zijn op dit moment geen Bandleden ingevoerd</p>
    <?php else : ?>
    <?php foreach ($bandlidDisplays as $bandlidDisplay) : ?>
      <div>
        <p>Naam: <?php echo $bandlidDisplay['naam']; ?><?php if (!empty($bandlidDisplay['tussenvoegsel'])) echo ' ' . $bandlidDisplay['tussenvoegsel']; ?><?php echo ' '.$bandlidDisplay['achternaam']; ?></p>
        <p>Band: <?php echo $bandNames[$bandlidDisplay['Band_bandId']]; ?></p>
        <p>Omschrijving: <?php echo $bandlidDisplay['omschrijving']; ?></p>
        <p>Foto: <img src="uploads/<?php echo $bandlidDisplay['foto']; ?>" alt="Foto van <?php echo $bandlidDisplay['naam'].' '.$bandlidDisplay['tussenvoegsel'].' '.$bandlidDisplay['achternaam']; ?>"></p>
      </div>
      <hr>
    <?php endforeach; ?>
    <?php endif; ?>
  </div>
</body>
</html>
