import { toggle, loadProfileOverlay, requestFetch, validateInput } from './app.js';

document.getElementById('profileBtn').addEventListener('click', () => toggle('profileOverlay', 'dispHidden'));
document.getElementById('model').addEventListener('input', (event) => validateInput(event, /[^a-zA-Z0-9- ]/g));
document.getElementById('licenseNumber').addEventListener('input', (event) => validateLicenseNumber(event));
document.getElementById('capacity').addEventListener('input', (event) => validateInput(event, /[^0-9]/g));
document.getElementById('rentPerDay').addEventListener('input', (event) => validateInput(event, /[^0-9]/g));
document.getElementById('postCarForm').addEventListener('submit', (event) => addCar(event));

const validateLicenseNumber = (event) => { event.target.value = event.target.value.replace(/[^a-zA-Z0-9 ]/g, '').toUpperCase(); }

const addCar = (event) => {
	event.preventDefault();
	const files = document.getElementById('imageInput').files;

	if (files.length < 1 || files.length > 5) {
		document.getElementById('errorContainer').classList.remove("dispHidden");
		document.getElementById('errorContainer').textContent = `Select 1 to 5 files.`;

		return;
	}

	let invalidFiles = [];
	Object.values(files).forEach(file => {
		if ((file.size / 1024) > 250)
			invalidFiles.push(file.name);
	});

	if (invalidFiles.length > 0) {
		document.getElementById('errorContainer').classList.remove("dispHidden");
		document.getElementById('errorContainer').textContent = `File(s) "${invalidFiles.join(", ")}" size should not exceed 250KB.`;

		return;
	}

	const formData = new FormData(event.target);

	const onFailure = (data) => {
		document.getElementById("errorContainer").classList.remove("dispHidden");
		document.getElementById("errorContainer").textContent = data.message;
	}

	const onSuccess = (data) => {
		alert(data.message);
		window.location.href = window.origin;
	}

	requestFetch('/api/Cars', 'POST', formData, onFailure, onSuccess);
}

window.document.addEventListener('DOMContentLoaded', () =>	loadProfileOverlay());