document.addEventListener("DOMContentLoaded", () => {
	// Listener for Type Toggle
	var typeSelect = document.getElementById("donasai_type");
	if (typeSelect) {
		typeSelect.addEventListener("change", (e) => {
			var wrapper = document.getElementById("donasai_packages_wrapper");
			if (wrapper) {
				wrapper.style.display = e.target.value === "qurban" ? "block" : "none";
			}
		});
	}

	// Initialize Packages
	var container = document.getElementById("donasai_packages_container");

	// Ensure data exists
	if (typeof wpdPackagesData === "undefined") {
		window.wpdPackagesData = [];
	}

	window.renderPackages = () => {
		if (!container) return;
		container.innerHTML = "";
		wpdPackagesData.forEach((pkg, index) => {
			var row = document.createElement("div");
			row.style.marginBottom = "10px";
			row.style.display = "flex";
			row.style.gap = "10px";
			row.style.alignItems = "center";

			// Escape values to prevent XSS in innerHTML
			var safeName = pkg.name.replace(/"/g, "&quot;");
			var safePrice = pkg.price;

			row.innerHTML = `
            <input type="text" placeholder="Package Name (e.g. Sapi A)" value="${safeName}" onchange="updatePackage(${index}, 'name', this.value)" style="flex:2;">
            <input type="number" placeholder="Price (Rp)" value="${safePrice}" onchange="updatePackage(${index}, 'price', this.value)" style="flex:1;">
            <button type="button" class="button" onclick="removePackage(${index})" style="color:#b32d2e; border-color:#b32d2e;">&times;</button>
        `;
			container.appendChild(row);
		});
		updateJson();
	};

	window.wpdAddPackage = () => {
		wpdPackagesData.push({ name: "", price: "" });
		renderPackages();
	};

	window.removePackage = (index) => {
		wpdPackagesData.splice(index, 1);
		renderPackages();
	};

	window.updatePackage = (index, key, value) => {
		wpdPackagesData[index][key] = value;
		updateJson();
	};

	function updateJson() {
		var jsonField = document.getElementById("donasai_packages_json");
		if (jsonField) {
			jsonField.value = JSON.stringify(wpdPackagesData);
		}
	}

	// Initial Render
	if (container) {
		renderPackages();
	}
});
