import { validateInput, passMatch, createAccAuth } from '../assets/app.js';

document.getElementById('username').addEventListener('input', (event) => validateInput(event, /[^a-zA-Z0-9]/g));

document.getElementById('password').addEventListener('input', (event) => passMatch(event));
document.getElementById('confirmPassword').addEventListener('input', (event) => passMatch(event));

document.getElementById('agencyName')?.addEventListener('input', (event) => validateInput(event, /[^a-zA-Z. ]/g));
document.getElementById('fullName').addEventListener('input', (event) => validateInput(event, /[^a-zA-Z ]/g));
document.getElementById('phone').addEventListener('input', (event) => validateInput(event, /[^0-9]/g));
document.getElementById('addressState').addEventListener('input', (event) => validateInput(event, /[^a-zA-Z]/g));

document.getElementById('signUpForm').addEventListener('submit', (event) => createAccAuth(event));