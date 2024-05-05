"use strict";

const api_key = "YOUR_TMDB_API_KEY";
const imageBaseURL = "http://image.tmdb.org/t/p/";

// Fetch data from server using 'url' and pass the result in JSON data to a 'callback' function along with optional parameter if has 'optionalParam'

const fetchDataFromServer = function (url, callback, optionalParam) {
  fetch(url)
    .then((response) => response.json())
    .then((data) => callback(data, optionalParam));
};

export { imageBaseURL, api_key, fetchDataFromServer };
