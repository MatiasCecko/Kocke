// Modal z navodili
document.getElementById('btn-navodila').addEventListener('click', function () {
  Swal.fire({
    title: 'Game instructions',
    icon: 'info',
    html: `
      <ol style="text-align:left;">
        <li>Select the number of players, rounds, and dice.</li>
        <li>Enter the first and last name for each player.</li>
        <li>Click "Start" and wait for the dice roll animation.</li>
        <li>After 2 seconds, the results of the current round and a button for the next round will appear.</li>
        <li>After the final round, the final score and the winner will be displayed.</li>
        <li>The page will automatically redirect back to the homepage after 5 seconds.</li>
      </ol>
    `,
    confirmButtonText: 'Close',
    customClass: {
      confirmButton: 'my-swal-button'
    },
    buttonsStyling: false,
    heightAuto: false
  });
});

// Modal z vizitko
document.getElementById('btn-vizitka').addEventListener('click', function () {
  Swal.fire({
    title: 'Info',
    icon: 'info',
    html: `
      <p style="text-align:center;">
        <strong>Gambling Room</strong><br>
        Author: Matias Čečko<br>
        Github: <a href="https://github.com/MatiasCecko/" target="_blank">MatiasCecko</a><br>
      </p>
    `,
    confirmButtonText: 'Close',
    customClass: {
      confirmButton: 'my-swal-button'
    },
    buttonsStyling: false,
    heightAuto: false
  });
});

// Obstoječa logika za animacijo kock
window.onload = function () {
  setTimeout(function () {
    document.querySelectorAll(".dice").forEach(function (die) {
      let res = die.getAttribute("data-result");
      die.src = "slike/dice" + res + ".gif";
    });
    document.querySelectorAll(".dice-sum").forEach(el => el.style.display = "block");
    let btns = document.getElementById("round-buttons");
    if (btns) btns.style.display = "block";
  }, 2000);
};
