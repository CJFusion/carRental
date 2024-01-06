function openOverlay() {
	document.getElementById("overlay").style.display = "flex";
}

function closeOverlay() {
	document.getElementById("overlay").style.display = "none";
}

function onInput(event, nregex) { event.target.value = event.target.value.replace(nregex, ''); }

function validateInput(event, nregex) {
	event.target.value = event.target.value.replace(nregex, '');
	if (event.target.name === 'licenseNumber')
		event.target.value = event.target.value.replace(/[a-z]/g, (match) => match.toUpperCase());
}

function sleep(delay) { new Promise((resolve) => setTimeout(resolve, delay)); }

function openProfileOverlay() {
	if (document.getElementById("profileOverlay").style.display == "none")
		document.getElementById("profileOverlay").style.display = "flex";
	else
		document.getElementById("profileOverlay").style.display = "none";
}

function selAccount(accountType) {
	if (accountType === 'customer') {
		// redirectToRegistrationPage(accountType) 
		window.location.href = 'signUp/registerCustomer.html';
	} else if (accountType === 'rentalAgency') {
		// redirectToRegistrationPage(accountType)
		window.location.href = 'signUp/registerAgency.html';
	}
	// alert("Selected account type: " + accountType);
	// closeOverlay(); // Close the overlay after selection (You can perform further actions here)
}

function debugFetch(uri, requestMethod, formData, onFailure, onSuccess) {
	let fetchOptions = { method: requestMethod, headers: { 'X-Requested-With': 'fetch' } };
	if (requestMethod.toUpperCase() !== 'GET')
		fetchOptions.body = formData;

	return fetch(uri, fetchOptions)
		.then(async response => {
			if (response.redirected)
				window.location.href = response.url;
			let res = await response.text();
			try {
				res = JSON.parse(res);
			} catch (error) {
				throw new Error(`\nError parsing JSON: \n ${error} \n\n Source: ${res}`);
			}

			return res;
		}).then(data => {
			console.table(data);
			if (data.hasOwnProperty('error'))
				return onFailure(data);
			return onSuccess(data);
		}).catch(error => {
			console.error('Fetch API -', error);
		});
}

function requestFetch(uri, requestMethod, formData, onFailure, onSuccess) {
	let fetchOptions = { method: requestMethod, headers: { 'X-Requested-With': 'fetch' } };
	if (requestMethod.toUpperCase() !== 'GET')
		fetchOptions.body = formData;

	return fetch(uri, fetchOptions)
		.then(response => {
			if (response.redirected)
				window.location.href = response.url;
			return response.json();
		}).then(data => {
			if (!data.hasOwnProperty('error'))
				return onSuccess(data);
			else
				return onFailure(data);
		});
}

function loginAuth(event) {
	event.preventDefault();

	var formData = new FormData(event.target);

	const onFailure = (data) => {
		document.getElementById("loginError").style.display = 'flex';
		document.getElementById("loginError").textContent = data.message;
	}

	const onSuccess = (data) => {
		window.location.href = 'home/rentCar.html';
	}

	requestFetch('/api/login.php', 'POST', formData, onFailure, onSuccess);
}

const createAccAuth = (event, element) => {
	let userType = 'Customer';
	if (element !== null)
		userType = 'Agency';

	let formData = new FormData(event.target);

	const onFailure = (data) => {
		document.getElementById("registerError").style.display = 'flex';
		document.getElementById("registerError").textContent = data.message;
	}

	const onSuccess = (data) => {
		window.location.href = '../home/rentCar.html';
	}

	requestFetch(`/api/Users/${userType}`, 'POST', formData, onFailure, onSuccess);
}

function passMatch(event) {
	event.preventDefault();
	let property = 'none';
	if (!(document.getElementById("password").value === document.getElementById("confirmPassword").value))
		property = 'flex';
	document.getElementById("passwordError").style.display = property;

	if (!event.submitter)
		return;

	if (property !== 'none') {
		document.getElementById("confirmPassword").focus();
		return;
	}

	const element = document.getElementById('agencyName');
	createAccAuth(event, element);
}