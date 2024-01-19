export function validateInput(event, nregex) { event.target.value = event.target.value.replace(nregex, ''); }

export async function loadProfileOverlay(callBack = null) {

	const onSuccess = (data) => {
		let userData = Object.values(data['userId'])[0];
		return {
			"fullName": userData.fullName,
			"userType": userData.userType,
			"gender": Object(userData).hasOwnProperty('gender') ? userData.gender : 'Male'
		};
	}

	const userData = await requestFetch('/api/Users/0', 'GET', {}, () => { }, onSuccess);
	displayUser(userData.fullName, userData.gender, userData.userType);

	if (callBack && typeof callBack === 'function')
		callBack();
}

async function displayUser(fullName, gender, userType) {
	if (userType.toLowerCase() === "customer")
		document.querySelectorAll(".agencyBtn").forEach(element => element.remove());

	const userField = document.getElementById("userField");
	const svgSrc = gender !== 'Female' ? 'man' : 'woman';

	userField.innerHTML = `
		<img src="${window.origin}/assets/SVGs/${svgSrc}-svgrepo-com.svg" alt="${gender} Svg" />
		${fullName}
	`;
}

export function toggle(elementId, className) {
	const element = document.getElementById(elementId);
	if (element)
		element.classList.toggle(className);
}

export function handleImageLoad(elementId) {
	document.getElementById("loadingContainer_" + elementId).classList.add('dispHidden');
	document.getElementById("item_" + elementId).classList.remove('opacityHidden');
}

export function changeImage(step, elementId) {
	const displayedImage = document.getElementById("item_" + elementId);

	let currentIndex = parseInt(displayedImage.getAttribute('data-index'));
	const imageUrls = JSON.parse(displayedImage.getAttribute('data-src'));

	currentIndex += step;

	if (currentIndex < 0)
		currentIndex = imageUrls.length - 1;
	else if (currentIndex >= imageUrls.length)
		currentIndex = 0;

	document.getElementById("loadingContainer_" + elementId).classList.remove('dispHidden');
	displayedImage.classList.add('opacityHidden');

	displayedImage.src = "" + imageUrls[currentIndex];
	displayedImage.setAttribute('data-index', currentIndex);
}

export async function debugFetch(uri, requestMethod, formData, onFailure, onSuccess) {
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

export async function requestFetch(uri, requestMethod, formData, onFailure, onSuccess) {
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
			return onFailure(data);
		});
}

export function logout() {
	requestFetch('/api/logout', 'GET', {}, () => { }, () => { });
}

export function createAccAuth(event) {
	if (!passMatch(event)) {
		document.getElementById("confirmPassword").focus();
		return;
	}

	let userType = 'Customer';
	if (document.getElementById('agencyName'))
		userType = 'Agency';

	const formData = new FormData(event.target);

	const onFailure = (data) => {
		document.getElementById("errorContainer").classList.remove('dispHidden');
		document.getElementById("errorContainer").textContent = data.message;
	}

	const onSuccess = (data) => {
		alert(data.message + "\nRedirecting to main page...");
		window.location.href = window.origin + '/home/rentCar.html';
	}

	requestFetch(`/api/Users/${userType}`, 'POST', formData, onFailure, onSuccess);
}

export function passMatch(event) {
	event.preventDefault();

	const bool = (document.getElementById("password").value === document.getElementById("confirmPassword").value);
	document.getElementById("passwordError").classList.toggle('dispHidden', bool);

	return bool;
}