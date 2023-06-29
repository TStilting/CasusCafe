<?php

include("connection.php");

// Get the event ID from the URL parameter
if (isset($_GET['band_id'])) {
  $bandId = $_GET['band_id'];
} else {
  // If the event ID is not provided, redirect to the events page
  header("location: index.php");
  exit;
}

// Retrieve the event details
$stmt = $pdo->prepare("SELECT Band.bandId, Band.bandNaam, Band.genre, Band.herkomst, Band.omschrijving,
GROUP_CONCAT(CONCAT('<a href=\"bandlid.php?bandlid_id=', Lineup.bandlidId, '\">', CONCAT(Lineup.naam, ' ', Lineup.tussenvoegsel, ' ', Lineup.achternaam), '</a>')
SEPARATOR ', ') AS bandleden
FROM Band
JOIN Lineup ON Band.bandId = Lineup.Band_bandId
WHERE Band.bandId = ?");
$stmt->execute([$bandId]);
$band = $stmt->fetch(PDO::FETCH_ASSOC);


// If the event does not exist, redirect to the events page
if (!$band) {
  header("location: index.php");
  exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" type="text/css" href="casuscss.css">
  <title>Band Details</title>
</head>
<header>
  <a href="index.php">Terug naar de event lijst</a>
</header>

<body>
<div class="container">
  <div class="display-details">
    <h1><?php echo $band['bandNaam']; ?></h1>
    <p class="sub-detail"><b>Bandleden:</b> <?php echo $band['bandleden']; ?></p>
    <p class="sub-detail"><b>Genre:</b> <?php echo $band['genre']; ?></p>
    <p class="sub-detail"><b>Plaats van herkomst:</b> <?php echo $band['herkomst']; ?></p>
    <p><?php echo $band['omschrijving']; ?></p>
  </div>
</body>
</html>