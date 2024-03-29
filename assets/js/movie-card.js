"use strict";

import { imageBaseURL } from "./api.js";

// MOVIE CARD

export function createMovieCard(movie) {
  const { poster_path, title, release_date, vote_average, id } = movie;

  const card = document.createElement("div");
  card.classList.add("movie-card");

  card.innerHTML = `
  <figure class="poster-box card-banner">
                <img
                  src="${imageBaseURL}w342${poster_path}"
                  alt="${title}"
                  class="image-cover"
                  loading="lazy"
                />
              </figure>
              <h4 class="title">${title}</h4>
              <div class="meta-list">
                <div class="mete-item">
                  <img
                    src="./assets/images/star.png"
                    height="20"
                    width="20"
                    loading="lazy"
                    alt="rating"
                  />
                  <span class="span">${vote_average.toFixed(1)}</span>
                </div>
                <div class="card-badge">${release_date}</div>
              </div>
              <a
                href="./detail.html"
                class="card-btn"
                title="${title}"
                onclick="getMovieDetail(${id})"
              ></a>
  `;
  return card;
}
