// exam.js - Exam timer and submission logic
let timerInterval;

function startTimer(durationInSeconds, displayElement) {
    let timer = durationInSeconds;

    timerInterval = setInterval(function () {
        const hours = parseInt(timer / 3600, 10);
        const minutes = parseInt((timer % 3600) / 60, 10);
        const seconds = parseInt(timer % 60, 10);

        displayElement.textContent =
            (hours < 10 ? "0" + hours : hours) + ":" +
            (minutes < 10 ? "0" + minutes : minutes) + ":" +
            (seconds < 10 ? "0" + seconds : seconds);

        if (--timer < 0) {
            clearInterval(timerInterval);
            // Auto submit when time's up
            alert("Time's up! Your exam will be submitted automatically.");
            document.getElementById('examForm').submit();
        }
    }, 1000);
}

document.addEventListener('DOMContentLoaded', () => {
    const timeDisplay = document.getElementById('time_left');

    if (timeDisplay) {
        // Read duration from data attribute
        const durationSec = parseInt(timeDisplay.getAttribute('data-duration'), 10);
        if (durationSec > 0) {
            startTimer(durationSec, timeDisplay);
        }
    }
});
