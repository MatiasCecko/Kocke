<?php
session_start();
$step = isset($_POST['step']) ? (int)$_POST['step'] : 0;

if ($step === 1) {
  $_SESSION['num_players'] = (int)$_POST['num_players'];
  $_SESSION['num_rounds'] = (int)$_POST['num_rounds'];
  $_SESSION['num_dice'] = (int)$_POST['num_dice'];
}

if ($step === 2) {
  if (isset($_POST['ime']) && isset($_POST['priimek'])) {
    $_SESSION['players'] = [];
    for ($i = 0; $i < $_SESSION['num_players']; $i++) {
      $_SESSION['players'][] = [
        'ime' => $_POST['ime'][$i],
        'priimek' => $_POST['priimek'][$i],
        'sum' => 0
      ];
    }
    $_SESSION['currentRound'] = 1;
  } elseif (isset($_POST['next_round'])) {
    $_SESSION['currentRound']++;
  }
}
?>
<!DOCTYPE html>
<html lang="sl">
<head>
  <meta charset="UTF-8">
  <title>Kocke</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="icon" href="slike/dice.png" type="image/png">
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="center-box">
  <h1 class="animated-title">Gambling Room</h1>

  <?php if ($step === 0): ?>
    <h2>Choose the number of players, rounds and dice</h2>
    <form method="post">
      <div class="selection-row">
        <div class="selection-item">
          <label>Number of players:</label>
          <select name="num_players">
            <?php for ($i = 1; $i <= 6; $i++) echo "<option value='$i'>$i</option>"; ?>
          </select>
        </div>
        <div class="selection-item">
          <label>Number of rounds:</label>
          <select name="num_rounds">
            <?php for ($r = 1; $r <= 10; $r++) echo "<option value='$r'>$r</option>"; ?>
          </select>
        </div>
        <div class="selection-item">
          <label>Number of dice:</label>
          <select name="num_dice">
            <?php for ($d = 1; $d <= 6; $d++) echo "<option value='$d'>$d</option>"; ?>
          </select>
        </div>
      </div>
      <input type="hidden" name="step" value="1">
      <input type="submit" value="Next">
    </form>

  <?php elseif ($step === 1): ?>
    <h2>Enter information for <?= $_SESSION['num_players'] ?> players</h2>
    <form method="post">
      <div class="fieldset-row">
        <?php for ($i = 1; $i <= $_SESSION['num_players']; $i++): ?>
          <fieldset>
            <legend>Player <?= $i ?></legend>
            <label>Name:</label><br>
            <input type="text" name="ime[]" required><br><br>
            <label>Surname:</label><br>
            <input type="text" name="priimek[]" required>
          </fieldset>
        <?php endfor; ?>
      </div>
      <input type="hidden" name="step" value="2">
      <input type="submit" value="Start">
    </form>

  <?php elseif ($step === 2): ?>
    <?php
    if ($_SESSION['currentRound'] > $_SESSION['num_rounds']) {
      echo '<form method="post"><input type="hidden" name="step" value="3">';
      echo '<script>document.forms[0].submit();</script></form>';
      exit;
    }

    $round = $_SESSION['currentRound'];
    ?>
    <h2>Round <?= $round ?> out of <?= $_SESSION['num_rounds'] ?></h2>
    <?php for ($i = 0; $i < $_SESSION['num_players']; $i++): ?>
      <?php
      $diceResults = [];
      for ($j = 0; $j < $_SESSION['num_dice']; $j++) {
        $diceResults[] = rand(1, 6);
      }
      $roundSum = array_sum($diceResults);
      $_SESSION['players'][$i]['sum'] += $roundSum;
      ?>
      <div class="user-box fade-in">
        <p><strong>Player <?= $i + 1 ?></strong><br>
        <?= $_SESSION['players'][$i]['ime'] . ' ' . $_SESSION['players'][$i]['priimek'] ?></p>
        <p>Roll results:<br>
          <?php foreach ($diceResults as $res): ?>
            <img class="dice" data-result="<?= $res ?>" src="slike/dice-anim.gif" alt="Kocka">
          <?php endforeach; ?>
        </p>
        <p class="dice-sum" style="display:none;">
          Points in this round: <strong><?= $roundSum ?></strong><br>
          Total so far: <strong><?= $_SESSION['players'][$i]['sum'] ?></strong>
        </p>
      </div>
    <?php endfor; ?>

    <div id="round-buttons" style="display:none;">
      <?php if ($round < $_SESSION['num_rounds']): ?>
        <form method="post">
          <input type="hidden" name="step" value="2">
          <button type="submit" name="next_round">Next round</button>
        </form>
      <?php else: ?>
        <form method="post">
          <input type="hidden" name="step" value="3">
          <input type="submit" value="Show the final result">
        </form>
      <?php endif; ?>
    </div>
    <script src="js/script.js"></script>

  <?php elseif ($step === 3): ?>
    <h2>Final results</h2>
    <?php
    $board = $_SESSION['players'];
    usort($board, fn($a, $b) => $b['sum'] <=> $a['sum']);
    ?>
    <table class="leaderboard-table">
      <tr><th>Place</th><th>Player</th><th>Final Result</th></tr>
      <?php
      $place = 1;
      $first = $board[0]['sum'];
      foreach ($board as $p) {
        echo "<tr><td>$place</td><td>{$p['ime']} {$p['priimek']}</td><td>{$p['sum']}</td></tr>";
        $place++;
      }
      ?>
    </table>
    <div class="winner-box fade-in">
      <?php
      $winners = array_filter($board, fn($x) => $x['sum'] == $first);
      if (count($winners) === 1) {
        $w = array_values($winners)[0];
        echo "<h3>Winner: {$w['ime']} {$w['priimek']}</h3>";
      } else {
        echo "<h3>Winners:</h3>";
        foreach ($winners as $w) echo "{$w['ime']} {$w['priimek']}<br>";
      }
      ?>
    </div>
    <p id="redirect-timer">
      Redirecting to the homepage in <span id="countdown">5</span> seconds...
    </p>
    <script>
      let sec = 5, sp = document.getElementById("countdown");
      setInterval(() => { if (sec > 0) sp.textContent = --sec; }, 1000);
      setTimeout(() => location.href = 'index.php', 5000);
    </script>
  <?php endif; ?>

  <div class="button-row">
    <button id="btn-navodila">Instructions</button>
    <button id="btn-vizitka">Info</button>
  </div>
</div>
<script src="js/script.js"></script>
</body>
</html>
