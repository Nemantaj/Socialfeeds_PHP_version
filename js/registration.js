const first = document.querySelector(".first"),
	  second = document.querySelector(".second"),
	  signup = document.querySelector("#signup"),
	  login = document.querySelector("#login"),
	  refresh = document.querySelector("#register_button");


signup.addEventListener("click", () => {

	first.style.display = "none";
	second.style.display = "block";

})	  

login.addEventListener("click", () => {

	second.style.display = "none";
	first.style.display = "block";

})