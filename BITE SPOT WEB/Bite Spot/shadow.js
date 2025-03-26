window.addEventListener("scroll", function () {
  const nav = document.querySelector("nav ul");

  if (window.scrollY > 10) {
    nav.classList.add("shadow"); // Add shadow when scrolled past 10px
  } else {
    nav.classList.remove("shadow"); // Remove shadow when at the top
  }
});

/* I CREATE THIS FOR SEARCH FUNCTION AND SUGGESTION*/
document.addEventListener("DOMContentLoaded", function () {
  const searchInput = document.querySelector(".search input");
  const searchButton = document.querySelector(".search button");
  const searchContainer = document.querySelector(".search");
  const restaurants = document.querySelectorAll(".restaurant");

  // Create a suggestion box for restaurant names
  const suggestionBox = document.createElement("ul");
  suggestionBox.classList.add("suggestion-box");
  searchContainer.appendChild(suggestionBox);

  // Create a suggestion box for locations
  const locationBox = document.createElement("ul");
  locationBox.classList.add("suggestion-box");
  searchContainer.appendChild(locationBox);

  // Store restaurant names, locations, and tags separately
  const restaurantNames = [];
  const restaurantLocations = new Set(); // Use a Set to avoid duplicate locations
  const restaurantTags = new Set();

  restaurants.forEach((restaurant) => {
    const name = restaurant.querySelector("h3").innerText;
    const location = restaurant.getAttribute("data-location") || "Unknown";
    const tags = restaurant.getAttribute("data-tags")?.toLowerCase().split(" ") || [];

    restaurantNames.push(name);
    restaurantLocations.add(location);
    tags.forEach(tag => restaurantTags.add(tag));
  });

  function showRestaurantSuggestions() {
    const filter = searchInput.value.toLowerCase().trim();
    suggestionBox.innerHTML = "";
    locationBox.innerHTML = "";

    if (filter.length === 0) {
      suggestionBox.style.display = "none";
      locationBox.style.display = "none";
      return;
    }

    // Show only restaurant names that START with the input
    const filteredRestaurants = restaurantNames.filter(
      (name) => name.toLowerCase().startsWith(filter)
    );

    if (filteredRestaurants.length === 0) {
      suggestionBox.style.display = "none";
    } else {
      filteredRestaurants.forEach((name) => {
        const li = document.createElement("li");
        li.textContent = name;
        li.addEventListener("click", function () {
          searchInput.value = name;
          suggestionBox.innerHTML = "";
          searchRestaurants();
        });
        suggestionBox.appendChild(li);
      });
      suggestionBox.style.display = "block";
    }
  }

  function showLocationSuggestions() {
    const filter = searchInput.value.toLowerCase().trim();
    suggestionBox.innerHTML = "";
    locationBox.innerHTML = "";

    if (filter.length === 0) {
      suggestionBox.style.display = "none";
      locationBox.style.display = "none";
      return;
    }

    // Show only locations that START with the input
    const filteredLocations = Array.from(restaurantLocations).filter(
      (location) => location.toLowerCase().includes(filter)
    );
    

    if (filteredLocations.length === 0) {
      locationBox.style.display = "none";
    } else {
      filteredLocations.forEach((location) => {
        const li = document.createElement("li");
        li.textContent = location;
        li.addEventListener("click", function () {
          searchInput.value = location;
          locationBox.innerHTML = "";
          searchRestaurants();
        });
        locationBox.appendChild(li);
      });
      locationBox.style.display = "block";
    }
  }

  function searchRestaurants() {
    const filter = searchInput.value.toLowerCase().trim();
    let found = false;

    restaurants.forEach((restaurant) => {
      const name = restaurant.querySelector("h3").innerText.toLowerCase();
      const location = restaurant.getAttribute("data-location")?.toLowerCase() || "";
      const tags = restaurant.getAttribute("data-tags")?.toLowerCase() || "";

      // Show restaurants that match name, location, or tags
      if (name.startsWith(filter) || location.startsWith(filter) || tags.includes(filter)) {
        restaurant.style.display = "block";
        found = true;
      } else {
        restaurant.style.display = "none";
      }
    });

    suggestionBox.style.display = "none";
    locationBox.style.display = "none";

    if (!found) {
      alert("No matching restaurants found.");
    }
  }

  searchInput.addEventListener("input", function () {
    const value = searchInput.value.trim().toLowerCase();
    
    if (value.length === 0) {
      suggestionBox.style.display = "none";
      locationBox.style.display = "none";
      return;
    }
  
    let hasNameMatches = restaurantNames.some((name) => name.toLowerCase().startsWith(value));
    let hasLocationMatches = Array.from(restaurantLocations).some((location) => location.toLowerCase().startsWith(value));
  
    if (hasNameMatches) {
      showRestaurantSuggestions();
    } else {
      suggestionBox.style.display = "none";
    }
  
    if (hasLocationMatches) {
      showLocationSuggestions();
    } else {
      locationBox.style.display = "none";
    }
  });
  
  

  searchButton.addEventListener("click", searchRestaurants);

  searchInput.addEventListener("keypress", function (event) {
    if (event.key === "Enter") {
      searchRestaurants();
    }
  });

  document.addEventListener("click", function (e) {
    if (!searchContainer.contains(e.target)) {
      suggestionBox.style.display = "none";
      locationBox.style.display = "none";
    }
  });
});
