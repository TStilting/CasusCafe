<?php

session_start();

// Check voor login
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
  header("location: login.php");
  exit;
}

include("connection.php");

// stuur de user naar index als het geen admin is
if ($_SESSION['rol'] != 'admin') {
  header("location: index.php");
  exit;
}

// Haal alle bandnamen op
$bandNames = array();
foreach ($pdo->query("SELECT * FROM Band") as $row) {
  $bandNames[] = $row['bandNaam'];
}

// Doet alle acties na een submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $eventDate = $_POST['event_date'];
  $bandName = $_POST['band_name'];
  $hoofdAct = isset($_POST['hoofd_act']) ? 1 : 0;
  $eventTime = $_POST['event_time'];
  $eventDuration = $_POST['event_duration'];
  $entree = $_POST['entree'];

  // Haal het juiste bandId op
  $stmt = $pdo->prepare("SELECT bandId FROM Band WHERE bandNaam = ?");
  $stmt->execute([$bandName]);
  $band = $stmt->fetch(PDO::FETCH_ASSOC);
  $bandId = $band['bandId'];
  

  // Insert Query
  $stmt = $pdo->prepare("INSERT INTO `Event` (datum, entreePrijs, beginstijd) VALUES (?, ?, ?)");
  $stmt->execute([$eventDate, $entree ,$eventTime]);

  $lastInsertedId = $pdo->lastInsertId();

  $stmt = $pdo->prepare("INSERT INTO Act (hoofdAct, Event_eventId, Bands_bandId) VALUES (?, ?, ?)");
  $stmt->execute([$hoofdAct, $lastInsertedId, $bandId]);

  $lastInsertedActId = $pdo->lastInsertId();

  $stmt = $pdo->prepare("INSERT INTO `Set` (duurInMinuten, Act_actId) VALUES (?, ?)");
  $stmt->execute([$eventDuration, $lastInsertedActId]);

  echo 'Event Toegevoegd';

}

$stmt = $pdo->prepare("SELECT datum, Band.bandNaam, Act.hoofdAct, entreePrijs, beginstijd, `Set`.duurInMinuten
FROM `Event`
JOIN Act ON `Event`.eventId = Act.Event_eventId
JOIN Band ON Act.Bands_bandId = Band.bandId
JOIN `Set` ON Act.ActId = `Set`.Act_actId
ORDER BY datum, beginstijd");
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin Agenda</title>
</head>
<header>
    <a href="admin2.php">Band Toevoegen</a>
    <a href="index.php">Terug naar de Startpagina</a>
</header>

<body>
  <h2>Add Event</h2>
  <form method="POST" action="">

    <label for="event_month">Datum:</label>
    <input type="date" id="event_date" name="event_date" required>
    <br>

    <label for="band_name">Band Naam:</label>
    <select id="band_name" name="band_name">
      <?php foreach ($bandNames as $bandName) : ?>
        <option value="<?php echo $bandName; ?>"><?php echo $bandName; ?></option>
      <?php endforeach; ?>
    </select>
    <br>

    <label for="hoofd_act">Is het de hoofdact?</label>
    <input type="checkbox" id="hoofd_act" name="hoofd_act">
    <br>

    <label for="event_time">Start Tijd van het Event:</label>
    <input type="time" id="event_time" name="event_time" required>
    <br>

    <label for="event_duration">Duur (in minuten):</label>
    <input type="number" id="event_duration" name="event_duration" required>
    <br>

    <label for="entree">entreeprijs (in euro):</label>
    <input type="number" id="entree" name="entree" required>
    <br>

    <input type="submit" value="Add Event">
  </form>
  <div>
  <h2>Events</h2>
  <?php
  $currentDate = null;
  $currentTime = null;
  foreach ($events as $event) :
    $eventDate = $event['datum'];
    $eventTime = $event['beginstijd'];

    // Check of de datum en de tijd gelijk zijn aan het vorige ingevoegde event
    if ($eventDate !== $currentDate || $eventTime !== $currentTime) {
      // Maar als het een nieuwe datum of tijd is, eindig de huidige div en maak een nieuwe
      if ($currentDate !== null && $currentTime !== null) {
        echo '</div><hr>';
      }
      echo '<div>';
      echo '<p>Datum: ' . $eventDate . '</p>';
      echo '<p>Tijd: ' . $eventTime . '</p>';
      $currentDate = $eventDate;
      $currentTime = $eventTime;
    }

    echo '<p>Band: ' . $event['bandNaam'] . '</p>';
    if ($event['hoofdAct'] == 1) {
      echo '<p>Hoofd Act</p>'; // Display deze regel alleen als het de hoofdact is
    }
    echo '<p>Entree prijs: ' . $event['entreePrijs'] . '</p>';
    echo '<p>Duur van het event: ' . $event['duurInMinuten'] . ' minuten</p>';
    echo '<br>';
  endforeach;
  ?>
</div>
</body>
</html>