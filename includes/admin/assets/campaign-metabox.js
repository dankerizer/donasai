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
	if (typeof donasai_packages_data === "undefined") {
		window.donasai_packages_data = [];
	}

	window.donasai_render_packages = () => {
		if (!container) return;
		container.innerHTML = "";
		donasai_packages_data.forEach((pkg, index) => {
			var row = document.createElement("div");
			row.style.marginBottom = "10px";
			row.style.display = "flex";
			row.style.gap = "10px";
			row.style.alignItems = "center";

			// Escape values to prevent XSS in innerHTML
			var safeName = pkg.name.replace(/"/g, "&quot;");
			var safePrice = pkg.price;

			row.innerHTML = `
            <input type="text" placeholder="Package Name (e.g. Sapi A)" value="${safeName}" onchange="donasai_update_package(${index}, 'name', this.value)" style="flex:2;">
            <input type="number" placeholder="Price (Rp)" value="${safePrice}" onchange="donasai_update_package(${index}, 'price', this.value)" style="flex:1;">
            <button type="button" class="button" onclick="donasai_remove_package(${index})" style="color:#b32d2e; border-color:#b32d2e;">&times;</button>
        `;
			container.appendChild(row);
		});
		updateJson();
	};

	window.donasai_add_package = () => {
		donasai_packages_data.push({ name: "", price: "" });
		donasai_render_packages();
	};

	window.donasai_remove_package = (index) => {
		donasai_packages_data.splice(index, 1);
		donasai_render_packages();
	};

	window.donasai_update_package = (index, key, value) => {
		donasai_packages_data[index][key] = value;
		updateJson();
	};

	function updateJson() {
		var jsonField = document.getElementById("donasai_packages_json");
		if (jsonField) {
			jsonField.value = JSON.stringify(donasai_packages_data);
		}
	}

	// Initial Render
	if (container) {
		donasai_render_packages();
	}
});
