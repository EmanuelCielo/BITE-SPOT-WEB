window.addEventListener("resize", function() {
    const navImage = document.getElementById("navigation-image");
    if (window.innerWidth < screen.width) {
        navImage.style.display = "block"; // Show when not fullscreen
    } else {
        navImage.style.display = "none"; // Hide in fullscreen
    }
});
