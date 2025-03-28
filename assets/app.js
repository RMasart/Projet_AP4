import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

// Page Profil
document.getElementById("info-toggle").addEventListener("click", function (event) {
    event.preventDefault();
    toggleSection("info-content", "other-info-content");
    updateActiveButton("info-toggle", "other-info-toggle");
});

document.getElementById("other-info-toggle").addEventListener("click", function (event) {
    event.preventDefault();
    toggleSection("other-info-content", "info-content");
    updateActiveButton("other-info-toggle", "info-toggle");
});

// Toggle visibility of sections
function toggleSection(showId, hideId) {
    document.getElementById(showId).classList.remove("d-none");
    document.getElementById(hideId).classList.add("d-none");
}

// Update active button
function updateActiveButton(activeButtonId, inactiveButtonId) {
    document.getElementById(activeButtonId).classList.add("active-btn");
    document.getElementById(inactiveButtonId).classList.remove("active-btn");
}

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');
