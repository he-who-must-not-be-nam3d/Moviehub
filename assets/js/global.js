"use strict";

// Add event on multiple elements

const addEventOnElements = function (elements, eventType, callback) {
  for (const elem of elements) elem.addEventListener(eventType, callback);
};

// Toggle search for small devices

const searchBox = document.querySelector("[search-box]");
const searchTogglers = document.querySelectorAll("[search-toggler]");

addEventOnElements(searchTogglers, "click", function () {
  searchBox.classList.toggle("active");
});

// STORE MOVIE ID IN LOCALSTORAGE WHEN CLICKED ON MOVIE CARD

const getMovieDetail = function (movieId) {
  window.localStorage.setItem("movieId", String(movieId));
  return movieId;
};

const getMovieList = function (urlParam, genreName) {
  window.localStorage.setItem("urlParam", urlParam);
  window.localStorage.setItem("genreName", genreName);
};
