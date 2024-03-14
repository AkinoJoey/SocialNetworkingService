import { Dropdown } from "flowbite";

function setupDropDowns(dropdownContainers) {
	dropdownContainers.forEach(function (dropdownContainer) {
		let dropdownBtn = dropdownContainer.querySelector(".dropdown-btn");
		let dropdownMenu = dropdownContainer.querySelector(".dropdown-menu");
		let dropdown = new Dropdown(dropdownMenu, dropdownBtn);
		dropdown.hide();
	});
}



export { setupDropDowns }