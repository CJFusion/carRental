import { toggle, requestFetch } from '../assets/app.js';

document.getElementById('overlayOpenBtn').addEventListener('click', () => toggle('overlay', 'dispHidden'));
document.getElementById('overlayCloseBtn').addEventListener('click', () => toggle('overlay', 'dispHidden'));
document.getElementById('loginForm').addEventListener('submit', (event) => loginAuth(event));

const loginAuth = (event) => {
	event.preventDefault();

	var formData = new FormData(event.target);

	const onFailure = (data) => {
		const errorContainer = document.getElementById("errorContainer");
		errorContainer.classList.remove('dispHidden');
		errorContainer.textContent = data.message;
	}

	const onSuccess = (data) => {
		// TODO: create /api/redirect/RentPage
		window.location.href = 'home/rentCar.html';
	}

	requestFetch('/api/login.php', 'POST', formData, onFailure, onSuccess);
}