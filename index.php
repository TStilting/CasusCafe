<?php

include("connection.php");

$stmt = $pdo->prepare("SELECT datum, Band.bandNaam, Act.hoofdAct, entreePrijs, beginstijd, `Set`.duurInMinuten
FROM `Event`
JOIN Act ON `Event`.eventId = Act.Event_eventId
JOIN Band ON Act.Bands_bandId = Band.bandId
JOIN `Set` ON Act.ActId = `Set`.Act_actId
ORDER BY datum, beginstijd");
$stmt->execute();
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!doctype html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="style.css">
    <script src="java.js"></script>
  </head>
  <header>
    <a href="login.php">login</a>
  </header>
  <body>
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