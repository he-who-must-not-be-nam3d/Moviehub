"use strict";

import { api_key, fetchDataFromServer } from "./api.js";

// Fetch all genres and change genre format
const genreList = {};

fetchDataFromServer(
  `https://api.themoviedb.org/3/genre/movie/list?api_key=${api_key}`,
  function ({ genres }) {
    for (const { id, name } of genres) {
      genreList[id] = name;
    }

    // Call the function to display genres to the user
    displayGenreOptions();
  }
);

function displayGenreOptions() {
  const genreOptionsContainer = document.getElementById("genreOptions");

  let genreOptionsHTML = "";

  // Display genre options to the user
  for (const id in genreList) {
    genreOptionsHTML += `
      <div class="genres">
        <input type="checkbox" name="genres[]" value="${id}" id="genre-${id}">
        <label for="genre-${id}">${genreList[id]}</label>
      </div>
    `;
  }

  // Append the generated HTML to the container
  genreOptionsContainer.innerHTML = genreOptionsHTML;
}

// Event listener for form submission
document.getElementById("genreForm").addEventListener("submit", handleSubmit);

// Function to handle form submission
function handleSubmit(event) {
  event.preventDefault();

  // Get selected genres
  const selectedGenres = Array.from(
    document.querySelectorAll('input[name="genres[]"]:checked')
  ).map((checkbox) => checkbox.value);

  // Send selected genres to PHP script
  sendGenresToPHP(selectedGenres);
}

/// Function to send selected genres to PHP script
function sendGenresToPHP(selectedGenres) {
  console.log("Selected genres:", selectedGenres); // Log selected genres
  // Send an AJAX request to PHP script
  const xhr = new XMLHttpRequest();
  xhr.open("POST", "./assets/php/insert_genres.php", true);
  xhr.setRequestHeader("Content-Type", "application/json");
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4) {
      console.log("Response status code:", xhr.status);
      if (xhr.status === 200) {
        window.location.href = "/moviehub/home.html";
      } else {
        console.log("Error:", xhr.status); // Log any error status
      }
    }
  };

  // Send selected genres as JSON to PHP script
  xhr.send(JSON.stringify({ genres: selectedGenres }));
}
