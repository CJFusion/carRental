export function validateInput(event, nregex) { event.target.value = event.target.value.replace(nregex, ''); }

export async function loadProfileOverlay() {

	const isLoggedIn = await requestFetch('/api/Users/IsLoggedIn', 'GET', {}, () => { }, (data) => { return data.bool });

	const profileOverlayContents = document.createElement('div');
	profileOverlayContents.classList.add("gridRow", "profileOverlayContents");

	if (isLoggedIn) {
		profileOverlayContents.innerHTML = await loadProfileContents();
	} else {
		profileOverlayContents.innerHTML = `
		<a href="${window.origin}/home/login.html" class="btn">
			<img src="${window.origin}/assets/SVGs/enter-icon.svg" alt="Login Svg" />Login
		</a>

		${''
		// <a href="#" class="btn">
		// 	<img src="${window.origin}/assets/SVGs/user-add-svgrepo-com.svg" alt="Signup Svg" />Signup
		// </a>
		}
	`;
	}

	profileOverlayContents.querySelector("#logoutBtn")?.addEventListener('click', () => logout());
	document.getElementById("profileOverlay").appendChild(profileOverlayContents);
}

async function loadProfileContents() {
	const onSuccess = (data) => {
		const userData = Object.values(data['userId'])[0];
		return {
			"fullName": userData.fullName,
			"userType": userData.userType.toLowerCase(),
			"gender": Object(userData).hasOwnProperty('gender') ? userData.gender.toLowerCase() : 'male'
		};
	}

	const userData = await requestFetch('/api/Users/0', 'GET', {}, () => { }, onSuccess);
	const svgSrc = (userData.gender.toLowerCase !== 'female') ? 'man' : 'woman';

	const userFieldHtml = `
		<p id="userField">
			<img src="${window.origin}/assets/SVGs/${svgSrc}-svgrepo-com.svg" alt="${userData.gender} Svg" />
			${userData.fullName}
		</p>
	`;
	const addCarsHtml = ((userData.userType === 'customer') || (window.location.pathname === '/home/addCar.html')) ? '' :
		`
		<a href="${window.origin}/home/addCar.html" class="btn">
			<img src="${window.origin}/assets/SVGs/solid/car.svg" alt="Car Svg" />Add cars
		</a>
	`;
	const viewRentalsHtml = (window.location.pathname === '/home/viewRentals.html') ? '' :
		`
		<a href="${window.origin}/home/viewRentals.html" class="btn">
			<img src="${window.origin}/assets/SVGs/solid/cart-arrow-down.svg" alt="Cart Svg" />View rentals
		</a>
	`;
	const logoutBtnHtml = `
		<button id="logoutBtn">
			<img src="${window.origin}/assets/SVGs/logout-svgrepo-com.svg" alt="Logout Svg" />Logout
		</button>
	`;

	const innerHtml = userFieldHtml + addCarsHtml + viewRentalsHtml + logoutBtnHtml;
	return innerHtml;
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
		window.location.href = window.origin;
	}

	requestFetch(`/api/Users/${userType}`, 'POST', formData, onFailure, onSuccess);
}

export function passMatch(event) {
	event.preventDefault();

	const bool = (document.getElementById("password").value === document.getElementById("confirmPassword").value);
	document.getElementById("passwordError").classList.toggle('dispHidden', bool);

	return bool;
}