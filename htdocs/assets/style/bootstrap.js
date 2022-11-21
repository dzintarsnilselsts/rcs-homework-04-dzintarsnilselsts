const button = document.querySelector("button");
const buttonWidth = 100;

const maxWidth = window.innerWidth - buttonWidth;

button.addEventListener("mouseover", function() {
  button.style.left = Math.floor(Math.random() * (maxWidth - 100)) + "px";
})

button.addEventListener("mouseenter", function () {
  button.innerHTML = "NOPE";
  button.style.width = "100" + "px";
});

button.addEventListener("mouseout", function () {
  button.innerHTML = "YEET";
  button.style.width = "100" + "px";
});

button.addEventListener("click", function () {
  button.innerHTML = "grow a pair, be a man";
  button.style.width = "200" + "px";
});
