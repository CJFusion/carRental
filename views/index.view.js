import { toggle, loadProfileOverlay, requestFetch, handleImageLoad, changeImage } from './app.js';

document.getElementById('profileBtn').addEventListener('click', () => toggle('profileOverlay', 'dispHidden'));

const addBooking = async (event) => {
	event.preventDefault();
	const isLoggedIn = await requestFetch('/api/Users/IsLoggedIn', 'GET', {}, () => { }, (data) => { return data.bool });
	if (!isLoggedIn) {
		alert("You are currently not logged in as a customer.")
		window.location.href = window.origin + '/home/login.html';
	}

	let formData = new FormData(event.target);

	const onFailure = (data) => {
		alert(data.message);
	}

	const onSuccess = (data) => {
		alert(`Booked car successfully.`);
		event.target.reset();
		event.target.querySelector(".totalCost").textContent = "0.00";
	}

	requestFetch('/api/Bookings', 'POST', formData, onFailure, onSuccess);
}

const getCarList = async () => {

	//Filler Database Fetched Data
	// return { "agencyId": { "2": { "carId": { "3": { "model": "Tesla Model S", "licenseNumber": "MH 01 AB 1234", "capacity": "5", "rentPerDay": "600.00", "imageUrl": ["/uploads/userId_2/teslaS_1.jpg", "/uploads/userId_2/teslaS_2.jpg", "/uploads/userId_2/teslaS_3.jpg", "/uploads/userId_2/teslaS_4.jpg"], "message": "All image links retrieved successfully" }, "4": { "model": "Porsche Cayenne", "licenseNumber": "DL 02 CD 5678", "capacity": "4", "rentPerDay": "550.00", "imageUrl": ["/uploads/userId_2/porscheCayenne_1.jpg", "/uploads/userId_2/porscheCayenne_2.jpg", "/uploads/userId_2/porscheCayenne_3.jpg", "/uploads/userId_2/porscheCayenne_4.jpg"], "message": "All image links retrieved successfully" }, "5": { "model": "Mercedes-Benz GLE", "licenseNumber": "KA 05 EF 9012", "capacity": "5", "rentPerDay": "700.00", "imageUrl": ["/uploads/userId_2/MercedesBenzGle_1.jpg", "/uploads/userId_2/MercedesBenzGle_2.jpg", "/uploads/userId_2/MercedesBenzGle_3.jpg", "/uploads/userId_2/MercedesBenzGle_4.jpg"], "message": "All image links retrieved successfully" }, "6": { "model": "BMW X5", "licenseNumber": "TN 06 GH 3456", "capacity": "4", "rentPerDay": "620.00", "imageUrl": ["/uploads/userId_2/bmwX5_1.jpg", "/uploads/userId_2/bmwX5_2.jpg", "/uploads/userId_2/bmwX5_3.jpg", "/uploads/userId_2/bmwX5_4.jpg"], "message": "All image links retrieved successfully" }, "7": { "model": "Audi Q7", "licenseNumber": "UP 07 IJ 7890", "capacity": "5", "rentPerDay": "580.00", "imageUrl": ["/uploads/userId_2/audiQ7_1.jpg", "/uploads/userId_2/audiQ7_2.jpg", "/uploads/userId_2/audiQ7_3.jpg", "/uploads/userId_2/audiQ7_4.jpg"], "message": "All image links retrieved successfully" } } }, "11": { "carId": { "8": { "model": "Range Rover", "licenseNumber": "GJ 08 KL 1234", "capacity": "4", "rentPerDay": "800.00", "imageUrl": ["/uploads/userId_11/rangeRover_1.jpg", "/uploads/userId_11/rangeRover_2.jpg", "/uploads/userId_11/rangeRover_3.jpg", "/uploads/userId_11/rangeRover_4.jpg"], "message": "All image links retrieved successfully" }, "9": { "model": "Bentley Bentayga", "licenseNumber": "MH 09 MN 5678", "capacity": "4", "rentPerDay": "900.00", "imageUrl": ["/uploads/userId_11/bentleyBentayga_1.jpg", "/uploads/userId_11/bentleyBentayga_2.jpg", "/uploads/userId_11/bentleyBentayga_3.jpg", "/uploads/userId_11/bentleyBentayga_4.jpg"], "message": "All image links retrieved successfully" }, "10": { "model": "Rolls-Royce Cullinan", "licenseNumber": "UP 10 OP 9012", "capacity": "4", "rentPerDay": "850.00", "imageUrl": ["/uploads/userId_11/rollsRoyceCullinan_1.jpg", "/uploads/userId_11/rollsRoyceCullinan_2.jpg", "/uploads/userId_11/rollsRoyceCullinan_3.jpg", "/uploads/userId_11/rollsRoyceCullinan_4.jpg"], "message": "All image links retrieved successfully" }, "11": { "model": "Ferrari Portofino", "licenseNumber": "DL 11 QR 3456", "capacity": "4", "rentPerDay": "1000.00", "imageUrl": ["/uploads/userId_11/ferrariPortofino_1.jpg", "/uploads/userId_11/ferrariPortofino_2.jpg", "/uploads/userId_11/ferrariPortofino_3.jpg", "/uploads/userId_11/ferrariPortofino_4.jpg"], "message": "All image links retrieved successfully" }, "12": { "model": "Lamborghini Urus", "licenseNumber": "TN 12 ST 7890", "capacity": "4", "rentPerDay": "950.00", "imageUrl": ["/uploads/userId_11/lamborghiniUrus_1.jpg", "/uploads/userId_11/lamborghiniUrus_2.jpg", "/uploads/userId_11/lamborghiniUrus_3.jpg", "/uploads/userId_11/lamborghiniUrus_4.jpg"], "message": "All image links retrieved successfully" } } }, "13": { "carId": { "13": { "model": "Maserati Levante", "licenseNumber": "KA 13 UV 1234", "capacity": "5", "rentPerDay": "750.00", "imageUrl": ["/uploads/userId_13/maseratiLevante_1.jpg", "/uploads/userId_13/maseratiLevante_2.jpg", "/uploads/userId_13/maseratiLevante_3.jpg", "/uploads/userId_13/maseratiLevante_4.jpg"], "message": "All image links retrieved successfully" }, "14": { "model": "Aston Martin DBX", "licenseNumber": "MH 14 WX 5678", "capacity": "4", "rentPerDay": "700.00", "imageUrl": ["/uploads/userId_13/astonMartinDbx_1.jpg", "/uploads/userId_13/astonMartinDbx_2.jpg", "/uploads/userId_13/astonMartinDbx_3.jpg", "/uploads/userId_13/astonMartinDbx_4.jpg"], "message": "All image links retrieved successfully" }, "15": { "model": "McLaren GT", "licenseNumber": "GJ 15 YZ 9012", "capacity": "5", "rentPerDay": "820.00", "imageUrl": ["/uploads/userId_13/mclarenGt_1.jpg", "/uploads/userId_13/mclarenGt_2.jpg", "/uploads/userId_13/mclarenGt_3.jpg", "/uploads/userId_13/mclarenGt_4.jpg"], "message": "All image links retrieved successfully" }, "16": { "model": "Bugatti Chiron", "licenseNumber": "DL 16 AB 3456", "capacity": "5", "rentPerDay": "1500.00", "imageUrl": ["/uploads/userId_13/bugattiChiron_1.jpg", "/uploads/userId_13/bugattiChiron_2.jpg", "/uploads/userId_13/bugattiChiron_3.jpg", "/uploads/userId_13/bugattiChiron_4.jpg"], "message": "All image links retrieved successfully" }, "17": { "model": "Porsche 911", "licenseNumber": "TN 17 CD 7890", "capacity": "5", "rentPerDay": "1200.00", "imageUrl": ["/uploads/userId_13/porsche911_1.jpg", "/uploads/userId_13/porsche911_2.jpg", "/uploads/userId_13/porsche911_3.jpg", "/uploads/userId_13/porsche911_4.jpg"], "message": "All image links retrieved successfully" } } } } }

	const onFailure = (data) => {
		document.getElementById('errorContainer').classList.remove('dispHidden');
		document.getElementById("errorContainer").textContent = data.message;
	}

	const onSuccess = (data) => {
		return data.availableCars;
	}

	return await requestFetch('/api/Cars', 'GET', {}, onFailure, onSuccess);
}

// Function to display available cars
const displayCars = async () => {
	let getList = await getCarList();
	let userType = await requestFetch('/api/Users/0', 'GET', {}, () => { }, (data) => {
		if (!Object(data).hasOwnProperty('userId'))
			return "none";
		return Object.values(data['userId'])[0].userType
	});

	document.getElementById('loadingContainer').classList.add('dispHidden');
	if (!getList)
		return;

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

	const carListDiv = document.getElementById("carList");
	carListDiv.innerHTML = ""; // Clear previous content

	Object.values(getList.agencyId).forEach(function (agency) {
		Object.entries(agency.carId).forEach(([key, car]) => {
			let sources = [];
			Object.values(car.imageUrl).forEach((filePath) => { sources.push(filePath) });

			const carDetails = document.createElement("div");
			carDetails.classList.add("gridItem");

			carDetails.innerHTML = `
				<div class="productImageDiv">
					<button class="prevBtn">❮</button>
					<div class="loader" id="loadingContainer_${key}">
						${svgLoader}
					</div>
					<img id="item_${key}" class="opacityHidden" loading="lazy" src="${sources[0]}" data-index="0" data-src='${JSON.stringify(sources)}' alt="${car.model} image" />
					<button class="nextBtn">❯</button>
				</div>
				<h3>${car.model}</h3>
				<div>
					<p>License:</p><p>${car.licenseNumber}</p>
				</div>
				<div>
					<p>Capacity:</p><p>${car.capacity}</p>
				</div>
				<p><i><img src="../assets/SVGs/rupee-sign-svgrepo-com.svg" alt="Rupee" />/day</i> ${car.rentPerDay} </p>
			`;

			carDetails.querySelector('.prevBtn').addEventListener('click', () => changeImage(-1, key));
			carDetails.querySelector('.nextBtn').addEventListener('click', () => changeImage(1, key));
			carDetails.querySelector(`#item_${key}`).addEventListener('load', () => handleImageLoad(key));

			if (userType.toLowerCase() !== "agency")
				carDetails.appendChild(createBookForm(key, car.rentPerDay));
			carListDiv.appendChild(carDetails);
		})
	});
}

const createBookForm = (key, rentPerDay) => {
	let bookForm = document.createElement("form");
	bookForm.classList.add("bookForm");

	bookForm.innerHTML = `
		<input type="number" id="daysInput_${key}" class="input" min="1" max="31" name="daysBooked" title="1 month booking max" placeholder="Booking duration in days" required>
		<span class="tip"><div>Set the rental period in days</div>[Max 31]</span>

		<input type="date" class="input" name="bookDate" placeholder="Reservation date" required>
		<span class="tip">Set a reservation date</span>

		<input type="hidden" name="carId" value = "${key}">
		<div class="actions">
			<input type="submit" class="book btn" value="Book">
			<p class="book">
				<img src="../assets/SVGs/rupee-sign-svgrepo-com.svg" alt="Rupee" />
				<span class="totalCost">0.00</span>
			</p>
		</div>
	`;

	bookForm.querySelector(`#daysInput_${key}`).addEventListener('input', (event) => {
		bookForm.querySelector(".totalCost").textContent = (rentPerDay * event.target.value).toFixed(2);
	});
	bookForm.addEventListener('submit', (event) => addBooking(event));

	return bookForm;
}


window.document.addEventListener('DOMContentLoaded', () => loadProfileOverlay());

window.addEventListener('load', () => displayCars());
