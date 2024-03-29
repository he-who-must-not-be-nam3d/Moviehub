"use strict";
import { sidebar } from "./sidebar.js";
import { api_key, fetchDataFromServer } from "./api.js";
import { createMovieCard } from "./movie-card.js";
import { search } from "./search.js";

// Retrieve combined data from local storage
const combinedData = JSON.parse(localStorage.getItem("genreCombinedData"));

// Define a function to get a random genre ID and name from the combinedData array
function getRandomGenre(combinedData) {
  // Check if combinedData is not empty
  if (combinedData.length > 0) {
    // Randomly select an index from the combinedData array
    const randomIndex = Math.floor(Math.random() * combinedData.length);

    // Get the random genre data at the randomly selected index
    const randomGenre = combinedData[randomIndex];

    // Return the random genre data
    return randomGenre;
  } else {
    // If combinedData is empty, return null
    return null;
  }
}

// Call the getRandomGenre function with the combinedData array
const randomGenre = getRandomGenre(combinedData);

const genreName = randomGenre.name;
// Use randomGenre.id for genreId
const randomGenreId = randomGenre.id;
const urlParam = `with_genres=${randomGenreId}`;

const pageContent = document.querySelector("[page-content]");

let currentPage = 1;
let totalPages = 0;

fetchDataFromServer(
  `https://api.themoviedb.org/3/discover/movie?api_key=${api_key}&include_adult=false&include_video=false&page=${currentPage}&sort_by=popularity.desc&${urlParam}`,
  function ({ results: movieList, total_pages }) {
    totalPages = total_pages;

    document.title = `${genreName} Movies - Moviehub`;

    const movieListElem = document.createElement("section");
    movieListElem.classList.add("movie-list", "genre-list");
    movieListElem.ariaLabel = `${genreName} Movies`;

    movieListElem.innerHTML = `
    <div class="title-wrapper">
          <h1 class="heading">Top Picks For You</h1>
          </div>
        </div>

        <div class="grid-list"></div>
        <button class="btn load-more" load-more>Load More</button>
    `;

    // add movie based on fetched data

    for (const movie of movieList) {
      const movieCard = createMovieCard(movie);

      movieListElem.querySelector(".grid-list").appendChild(movieCard);
    }

    pageContent.appendChild(movieListElem);

    //   LOAD MORE BUTTON FUNCTIONALITY
    document
      .querySelector("[load-more]")
      .addEventListener("click", function () {
        if (currentPage >= totalPages) {
          this.style.display = "none";
          return;
        }
        currentPage++;
        this.classList.add("loading");

        fetchDataFromServer(
          `https://api.themoviedb.org/3/discover/movie?api_key=${api_key}&include_adult=false&include_video=false&page=${currentPage}&sort_by=popularity.desc&${urlParam}`,
          ({ results: movieList }) => {
            this.classList.remove("loading");

            for (const movie of movieList) {
              const movieCard = createMovieCard(movie);

              movieListElem.querySelector(".grid-list").appendChild(movieCard);
            }
          }
        );
      });
  }
);

search();
sidebar();
