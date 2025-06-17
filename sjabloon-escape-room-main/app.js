let correctAnswers = 0;
const totalQuestions = 1; // per kamer 1 vraag

// Timer functie
function startTimer(duration) {
  const timerDisplay = document.getElementById('timer');
  let remaining = duration;

  function updateDisplay() {
    const minutes = Math.floor(remaining / 60);
    const seconds = remaining % 60;
    timerDisplay.innerText = `⏱️ Tijd over: ${minutes}:${seconds < 10 ? '0' + seconds : seconds}`;
  }

  const countdown = setInterval(() => {
    remaining--;
    updateDisplay();

    if (remaining <= 0) {
      clearInterval(countdown);
      window.location.href = 'evaluate.php'; // tijd voorbij, einde game
    }
  }, 1000);

  updateDisplay();
}

// Zet vraagteken random binnen scherm (min 250px van elke rand)
function placeQuestionMarkRandomly() {
  const questionBox = document.getElementById('questionBox');
  if (!questionBox) return;

  const padding = 20; // smaller padding for more visible area
  const boxWidth = questionBox.offsetWidth;
  const boxHeight = questionBox.offsetHeight;
  const width = window.innerWidth;
  const height = window.innerHeight;

  const x = Math.floor(Math.random() * (width - boxWidth - 2 * padding)) + padding;
  const y = Math.floor(Math.random() * (height - boxHeight - 2 * padding)) + padding;

  console.log(`Positioning question mark at x:${x}px, y:${y}px`);

  questionBox.style.left = `${x}px`;
  questionBox.style.top = `${y}px`;
}



// Open modal en zet vraag/hint/antwoord klaar
function openModal() {
  const box = document.getElementById('questionBox');
  const questionText = box.dataset.question;
  const correctAnswer = box.dataset.answer;
  const hint = box.dataset.hint;

  const modal = document.getElementById('modal');
  modal.dataset.answer = correctAnswer;

  document.getElementById('question').innerText = questionText;
  document.getElementById('answer').value = '';
  document.getElementById('feedback').innerText = '';
  
  const hintText = document.getElementById('hintText');
  hintText.style.display = 'none';
  hintText.innerText = hint;

  document.getElementById('overlay').style.display = 'block';
  modal.style.display = 'block';
}

// Sluit modal en verplaats vraagteken naar nieuwe plek
function closeModal() {
  document.getElementById('overlay').style.display = 'none';
  document.getElementById('modal').style.display = 'none';
  document.getElementById('feedback').innerText = '';
  document.getElementById('hintText').style.display = 'none';

  placeQuestionMarkRandomly();
}

// Controleer antwoord en ga door naar volgende kamer of winnaar
function checkAnswer(roomId) {
  const userAnswer = document.getElementById('answer').value.trim();
  const correctAnswer = document.getElementById('modal').dataset.answer;
  const feedback = document.getElementById('feedback');

  if (userAnswer.toLowerCase() === correctAnswer.toLowerCase()) {
    feedback.innerText = 'Correct! Goed gedaan!';
    feedback.style.color = 'green';

    correctAnswers++;

    setTimeout(() => {
      closeModal();

      if (correctAnswers >= totalQuestions) {
        if (roomId === 3) {
          // Na kamer 3 naar winnaar pagina
          window.location.href = 'winner.php';
        } else {
          // Naar volgende kamer
          window.location.href = `room_${roomId + 1}.php`;
        }
      }
    }, 1000);
  } else {
    feedback.innerText = 'Fout, probeer opnieuw!';
    feedback.style.color = 'red';
  }
}

// Laat hint zien
function showHint() {
  const hintText = document.getElementById('hintText');
  hintText.style.display = 'block';
}

// Start timer en zet vraagteken bij pagina laden
window.addEventListener('DOMContentLoaded', () => {
  const phpStartTime = parseInt(document.body.dataset.starttime, 10);
  const now = Math.floor(Date.now() / 1000);
  const elapsed = now - phpStartTime;
  const remainingTime = 60 - elapsed;

  if (remainingTime > 0) {
    startTimer(remainingTime);
  } else {
    window.location.href = 'gameover.php';
  }

  placeQuestionMarkRandomly();

  const questionBox = document.getElementById('questionBox');
  if (questionBox) {
    questionBox.addEventListener('click', openModal);
  }

  const overlay = document.getElementById('overlay');
  if (overlay) {
    overlay.addEventListener('click', closeModal);
  }
});
