import React from "react";
import ReactDOM from "react-dom/client";
import App from "./App.tsx";
import "./index.css";

const rootElement = document.getElementById("wpd-admin-app");
if (rootElement) {
	ReactDOM.createRoot(rootElement).render(
		<React.StrictMode>
			<App />
		</React.StrictMode>,
	);
}
