<?php
session_start();

// Check for login
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
  header("location: login.php");
  exit;
}

include("connection.php");

// Redirect the user to index if they are not an admin
if ($_SESSION['rol'] != 'admin') {
  header("location: index.php");
  exit;
}

// Retrieve all band names
$bandNames = array();
$query = $pdo->query("SELECT bandNaam FROM Band");
while ($row = $query->fetch()) {
  $bandNames[] = $row['bandNaam'];
}

// Dit regelt alles na een submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $eventName = $_POST['event_name'];
  $eventDescription = $_POST['event_description'];
  $bandNames = isset($_POST['band_names']) ? $_POST['band_names'] : array();
  $eventStart = $_POST['event_start'];
  $eventEnd = $_POST['event_end'];
  $entree = $_POST['entree'];

  // Check if at least one band is selected
  if (count($bandNames) < 1) {
    $error = 'Er moet minstens 1 Band geselecteerd worden';
  } else {
    // Insert Event
    $stmt = $pdo->prepare("INSERT INTO `Event` (beginstijd, eindtijd, entreePrijs, naam, omschrijving) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$eventStart, $eventEnd, $entree, $eventName, $eventDescription]);
    $eventId = $pdo->lastInsertId(); // Neem het nieuwste eventId uit de database

    // Insert voor de Bands
    foreach ($bandNames as $bandName) {
      // pak het BandId
      $stmt = $pdo->prepare("SELECT bandId FROM Band WHERE bandNaam = ?");
      $stmt->execute([$bandName]);
      $band = $stmt->fetch(PDO::FETCH_ASSOC);
      $bandId = $band['bandId'];

      // en voeg bijde id's in Act
      $stmt = $pdo->prepare("INSERT INTO Act (Event_eventId, Bands_bandId) VALUES (?, ?)");
      $stmt->execute([$eventId, $bandId]);
    }

    //herlaad de pagina na de submit
    header("Location: admin.php");
    echo 'Event Toegevoegd';
    die();
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin Agenda</title>
</head>
<header>
    <a href="admin2.php">Band Toevoegen</a>
    <a href="admin3.php">Bandlid Toevoegen</a>
    <a href="index.php">Terug naar de Startpagina</a>
</header>

<body>
  <h2>Add Event</h2>
  <form method="POST" action="admin.php">

    <label for="event_name">Naam*:</label>
    <input type="text" id="event_name" name="event_name" required>
    <br>

    <label for="event_description">Omschrijving*:</label>
    <textarea id="event_description" name="event_description" required></textarea>
    <br>

    <label for="band_name">Bands*:</label>
    <br>
    <?php foreach ($bandNames as $bandName) : ?>
      <input type="checkbox" id="band_name" name="band_names[]" value="<?php echo $bandName; ?>">
      <label for="<?php echo $bandName; ?>"><?php echo $bandName; ?></label>
      <br>
    <?php endforeach; ?>
    <br>

    <label for="event_start">Het event begint om*: </label>
    <input type="datetime-local" id="event_start" name="event_start" required>
    <br>

    <label for="event_end">En duurt tot*: </label>
    <input type="datetime-local" id="event_end" name="event_end" required>
    <br>

    <label for="entree">Entreeprijs (in euro) (per kaartje)*:</label>
    <input type="number" id="entree" name="entree" required>
    <br>

    <input type="submit" value="Add Event">
  </form>

  <div>
  <h2>Events</h2>
  <?php
  // Retrieve all events with associated bands
  $stmt = $pdo->query("SELECT Event.eventId, DATE_FORMAT(Event.beginstijd, '%d-%m-%Y %H:%i') AS beginstijd, DATE_FORMAT(Event.eindtijd, '%d-%m-%Y %H:%i') AS eindtijd, Event.naam, Event.omschrijving, GROUP_CONCAT(Band.bandNaam SEPARATOR ', ') AS bandNamen, Event.entreePrijs
  FROM Event
  JOIN Act ON Event.eventId = Act.Event_eventId
  JOIN Band ON Act.Bands_bandId = Band.bandId GROUP BY Event.eventId
  ORDER BY beginstijd;");
  $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

  if (count($events) > 0) {
    foreach ($events as $event) {
      echo '<div>';
      echo '<p>Naam: ' . $event['naam'] . '</p>';
      echo '<p>Omschrijving: ' . $event['omschrijving'] . '</p>';
      echo '<p>Van: ' . $event['beginstijd'] . ' Tot: ' . $event['eindtijd'] . '</p>';
      echo '<p>Bands: ' . $event['bandNamen'] . '</p>';
      echo '<p>Entree prijs: ' . $event['entreePrijs'] . '</p>';
      echo '</div>';
    }
  } else {
    echo '<p>No events found.</p>';
  }
  ?>
</div>
</body>
</html>