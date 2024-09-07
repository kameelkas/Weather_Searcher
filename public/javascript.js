//Getting access to background and button elements
let page = document.getElementById("container");
let button1 = document.getElementById("submitButton");
let button2 = document.getElementById("lightDarkButton");

document.getElementById("lightDarkButton").onclick = function () {
  // function to toggle between 'light' and 'dark' mode
  page.classList.toggle("dark");
  page.classList.toggle("light");
  button1.classList.toggle("buttons");
  button1.classList.toggle("buttonsDark");
  button2.classList.toggle("buttons");
  button2.classList.toggle("buttonsDark");
  // localStorage to keep track of which background is currently on screen
  localStorage.setItem(
    "backgroundState",
    page.classList.contains("dark") ? "dark" : "light"
  );
};

/*since the page reloads on the form submit, when it reloads
the default background is applied (which is the 'light' mode background), 
so if the user was on the 'dark' mode background, this function 
ensures that when the page reloads, the 'dark' mode background is
 what shows on the page reload*/
document.addEventListener("DOMContentLoaded", function () {
  let page = document.getElementById("container");
  let backgroundState = localStorage.getItem("backgroundState");

  if (backgroundState === "dark") {
    page.classList.toggle("dark");
    page.classList.toggle("light");
    button1.classList.toggle("buttons");
    button1.classList.toggle("buttonsDark");
    button2.classList.toggle("buttons");
    button2.classList.toggle("buttonsDark");
  }
});
