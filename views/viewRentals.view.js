import { toggle, loadProfileOverlay, requestFetch, handleImageLoad, changeImage } from './app.js';

window.addEventListener('resize', () => changeItemDisplay());
document.getElementById('profileBtn').addEventListener('click', () => toggle('profileOverlay', 'dispHidden'));
document.getElementById('prevItemBtn').addEventListener('click', () => changeItem(-1));
document.getElementById('nextItemBtn').addEventListener('click', () => changeItem(1));

let curDisplayCount = 0;
let curItemIndex = 0;
let maxItems = 0;

const changeItemDisplay = () => {
	const width = document.documentElement.clientWidth;
	const displayCount = width < 700 ? 1 : (width < 1000 ? 2 : 3);

	if ((curItemIndex + displayCount) > maxItems)
		curItemIndex = Math.max(0, maxItems - displayCount);

	if (displayCount !== curDisplayCount) {
		curDisplayCount = displayCount;
		const gridItems = document.getElementById("bookedCarsList").querySelectorAll(".gridItem");

		gridItems.forEach((gridItem, i) => {
			gridItem.classList.toggle('dispHidden', (i < curItemIndex) || (i >= (curItemIndex + displayCount)));
		});
	}

	updateButtonStates();
};

const updateButtonStates = () => {
	let prevButton = document.getElementById("prevItemBtn");
	let nextButton = document.getElementById("nextItemBtn");

	(curItemIndex === 0) ? prevButton.setAttribute('disabled', 'true') : prevButton.removeAttribute('disabled');
	((curItemIndex + curDisplayCount) >= maxItems) ? nextButton.setAttribute('disabled', 'true') : nextButton.removeAttribute('disabled');
}

const changeItem = (step) => {
	const newIndex = curItemIndex + step;

	if (newIndex < 0 || ((newIndex + curDisplayCount) > maxItems))
		return;

	const gridItems = document.getElementById("bookedCarsList").querySelectorAll(".gridItem");

	gridItems.forEach((gridItem, i) => {
		gridItem.classList.toggle('dispHidden', ((i < newIndex) || (i >= (newIndex + curDisplayCount))));
	});

	curItemIndex = newIndex;
	updateButtonStates();
};


const getBookingsList = async (userType) => {

	// Filler Data
	// return { "3": { "1": { "customerId": 1, "bookDate": "2023-01-01", "endDate": "2023-01-05", "customerName": "JoshWillington" }, "model": "Tesla Model S" }, "4": { "2": { "customerId": 1, "bookDate": "2023-02-01", "endDate": "2023-02-10", "customerName": "JoshWillington" }, "model": "Porsche Cayenne" }, "5": { "3": { "customerId": 1, "bookDate": "2023-03-01", "endDate": "2023-03-15", "customerName": "JoshWillington" }, "model": "Mercedes-Benz GLE" }, "6": { "4": { "customerId": 1, "bookDate": "2023-04-01", "endDate": "2023-04-20", "customerName": "JoshWillington" }, "model": "BMW X5" }, "7": { "5": { "customerId": 1, "bookDate": "2023-05-01", "endDate": "2023-05-25", "customerName": "JoshWillington" }, "model": "Audi Q7" } };

	const onFailure = (data) => {
		document.getElementById("errorContainer").classList.remove('dispHidden');
		document.getElementById("errorContainer").textContent = data.message;
	}

	const onSuccess = (data) => {
		return data;
	}

	userType = userType.charAt(0).toUpperCase() + userType.slice(1);
	return await requestFetch(`/api/Bookings/${userType}`, 'GET', {}, onFailure, onSuccess);
}

// Function to display booked cars and associated customers
const displayBookedCars = async () => {
	let userType = await requestFetch('/api/Users/0', 'GET', {}, () => { }, (data) => { return Object.values(data['userId'])[0].userType });
	let bookingsList = await getBookingsList(userType.toLowerCase());

	document.getElementById('loadingContainer').classList.add('dispHidden');
	if (bookingsList === null || bookingsList === undefined || bookingsList === '') {
		updateButtonStates();
		return;
	}

	let bookedCarsListDiv = document.getElementById("bookedCarsList");
	bookedCarsListDiv.innerHTML = ""; // Clear previous content

	const svgLoader = `
		<svg class="carSvg" width="160" height="50" xmlns="http://www.w3.org/2000/svg">
			<g transform="translate(2 1)" stroke="#002742" fill="none" fill-rule="evenodd" stroke-linecap="round"
				stroke-linejoin="round">
				<path class="carSvg__body"
					d="m52 12zM73.86 10c.59 1.27 1.06 2.69 1.42 4.23.82 2.57-.39 3.11-3.19 2.06-2.06-1.23-4.12-2.47-6.18-3.7-1.05-.74-1.55-1.47-1.38-2.19.34-1.42 3.08-2.16 5.33-2.6C72.72 7.23 72.52 7.11 73.86 10zM106.77 17.5c-2.77-2.97-5.97-4.9-9.67-6.76-8.1-4.08-13.06-3.58-21.66-3.58l2.89 7.5c1.21 1.6 2.58 2.73 4.66 2.84H106.77L106.77 17.5zM112.09 15.3c-3.52-2.19-7.35-4.15-11.59-5.82-12.91-5.09-22.78-6-36.32-1.9-4.08 1.55-8.16 3.1-12.24 4.06-4.03.96-21.48 1.88-21.91 4.81l4.31 5.15c-1.57 1.36-2.85 3.03-3.32 5.64.13 1.61.57 2.96 1.33 4.04 1.29 1.85 5.07 3.76 7.11 2.67.65-.35 1.02-1.05 1.01-2.24-1.258-21.366 29.831-21.132 26.62 2.82H117.43C109.209 10.906 147.227 12.076 140.81 33.7 143.29 36.38 149.14 32.34 152.91 23.75c-1.03-1.02-2.16-1.99-3.42-2.89.06-.05-.06.19.15-.17.21-.36-.51-1.87-1.99-2.74C139.89 13.4 121.18 13.52 112.09 15.3L112.09 15.3z"
					stroke-width="2" />
				<path class="carSvg__wheel--left"
					d="M53.49 19.57c-5.93 0-10.73 4.8-10.73 10.73 0 5.93 4.8 10.73 10.73 10.73s10.73-4.8 10.73-10.73C64.22 24.37 59.42 19.57 53.49 19.57L53.49 19.57zM53.49 25.31c-2.75 0-4.99 2.23-4.99 4.99 0 2.75 2.23 4.99 4.99 4.99 2.75 0 4.99-2.23 4.99-4.99C58.48 27.54 56.25 25.31 53.49 25.31L53.49 25.31z"
					stroke-width="3" />
				<path class="carSvg__wheel--right"
					d="M129.05 25.31c-2.75 0-4.99 2.23-4.99 4.99 0 2.75 2.23 4.99 4.99 4.99 2.75 0 4.99-2.23 4.99-4.99C134.04 27.54 131.81 25.31 129.05 25.31L129.05 25.31zM129.05 19.57c-5.93 0-10.73 4.8-10.73 10.73 0 5.93 4.8 10.73 10.73 10.73s10.73-4.8 10.73-10.73C139.78 24.37 134.98 19.57 129.05 19.57L129.05 19.57z"
					stroke-width="3" />
				<path class="carSvg__line carSvg__line--top" d="M22.5 16.5H2.475" stroke-width="3" />
				<path class="carSvg__line carSvg__line--middle" d="M20.5 23.5H.4755" stroke-width="3" />
				<path class="carSvg__line carSvg__line--bottom" d="M25.5 9.5h-19" stroke-width="3" />
			</g>
		</svg>
	`;

	maxItems = Object.keys(bookingsList.carId).length;
	Object.entries(bookingsList.carId).forEach(([key, bookedCar]) => {
		let sources = [];
		Object.values(bookedCar.imageUrl).forEach((filePath) => { sources.push(filePath) });

		let carDetails = document.createElement("div");
		carDetails.classList.add("gridItem");

		carDetails.innerHTML = `
				<div class="productImageDiv">
					<button class="prevBtn">❮</button>
					<div class="loader" id="loadingContainer_${key}">
						${svgLoader}
					</div>
					<img id="item_${key}" class="opacityHidden" loading="lazy" src="${sources[0]}" data-index="0" data-src='${JSON.stringify(sources)}' alt="${bookedCar.model} image" />
					<button class="nextBtn">❯</button>
				</div>
				<h3>${bookedCar.model}</h3>
				<div>
					<p>License:</p><p>${bookedCar.licenseNumber}</p>
				</div>
				<div>
					<p>Capacity:</p><p>${bookedCar.capacity}</p>
				</div>
				<p><i><img src="../assets/SVGs/rupee-sign-svgrepo-com.svg" alt="Rupee" />/day</i> ${bookedCar.rentPerDay} </p>
				<div><h3>${userType == 'customer' ? "My Timelines" : "Customers"}</h3></div>
		`;

		carDetails.querySelector('.prevBtn').addEventListener('click', () => changeImage(-1, key));
		carDetails.querySelector('.nextBtn').addEventListener('click', () => changeImage(1, key));
		carDetails.querySelector(`#item_${key}`).addEventListener('load', () => handleImageLoad(key));

		let bookedUsers = document.createElement("ul");
		bookedUsers.classList.add("customerList");
		bookedUsers.innerHTML = `
			${Object.values(bookedCar.bookingId)
				.map(car => `
					<li>
						<p>${userType == 'customer' ? "Booked" : car.customerName}</p>
						<p>From:</p>
						<p>${car.bookDate.split('-').reverse().join('-')}</p>
						<p>To:</p>
						<p>${car.endDate.split('-').reverse().join('-')}</p>
					</li>`).join('\n')}
				`;

		carDetails.appendChild(bookedUsers);
		bookedCarsListDiv.appendChild(carDetails);
	});

	changeItemDisplay();
}

window.document.addEventListener('DOMContentLoaded', () => loadProfileOverlay());
window.addEventListener('load', () => displayBookedCars());