import { QueryClient, QueryClientProvider } from "@tanstack/react-query";

const queryClient = new QueryClient();

import { ReactQueryDevtools } from "@tanstack/react-query-devtools";
import clsx from "clsx";
import {
	CheckCircle,
	Heart,
	LayoutDashboard,
	Settings as SettingsIcon,
	Users,
} from "lucide-react";
import { useEffect } from "react";
import {
	HashRouter,
	Link,
	Route,
	Routes,
	useLocation,
	useNavigate,
} from "react-router-dom";
import { Toaster } from "sonner";
import LogoIcon from "./assets/logo";
import { ActivationLock } from "./components/ActivationLock";
import Confirmations from "./pages/Confirmations";
import Dashboard from "./pages/Dashboard";
import DonationsPage from "./pages/DonationsPage";
import FundraisersPage from "./pages/FundraisersPage";
import ReceiptTemplatePage from "./pages/receipt-template";
import EmailTemplatePage from "./pages/email-template";
import EditorPage from "./pages/editor";
import SettingsPage from "./pages/settings";

// ... imports

function App() {
	const isPro = (window as any).wpdSettings?.isPro;
	const proSettings = (window as any).wpdProSettings || {};
	const isLicenseActive =
		proSettings.licenseStatus === "active" ||
		proSettings.licenseStatus === "valid";

	// Show Lock if Pro is installed but License is NOT active
	const showLock = isPro && !isLicenseActive;

	return (
		<QueryClientProvider client={queryClient}>
			<HashRouter>
				<Routes>
					{/* Editor renders full-screen, outside normal layout */}
					<Route path="/editor" element={<EditorPage />} />

					{/* All other routes use AppLayout */}
					<Route
						path="*"
						element={
							<div className="relative">
								{showLock && <ActivationLock />}
								<AppLayout />
							</div>
						}
					/>
				</Routes>
				<ReactQueryDevtools initialIsOpen={false} />
			</HashRouter>
			<Toaster position="top-right" richColors closeButton />
		</QueryClientProvider>
	);
}

function AppLayout() {
	const location = useLocation();
	const navigate = useNavigate();

	// biome-ignore lint/correctness/useExhaustiveDependencies: <explanation>
	useEffect(() => {
		const settings = (window as any).wpdSettings;
		// Check if we have an initial path and we are currently at root (default)
		// We also check if the hash is empty to avoid overriding direct bookmarks
		if (
			settings?.initialPath &&
			(location.pathname === "/" || location.pathname === "")
		) {
			if (settings.initialPath !== "/") {
				navigate(settings.initialPath, { replace: true });
			}
		}
	}, [location.pathname]);

	const navItems = [
		{ label: "Dasbor", path: "/", icon: LayoutDashboard },
		{ label: "Donasi", path: "/donations", icon: Heart },
		{ label: "Konfirmasi", path: "/confirmations", icon: CheckCircle },
		{ label: "Penggalang Dana", path: "/fundraisers", icon: Users },
		{ label: "Pengaturan", path: "/settings", icon: SettingsIcon },
	];

	return (
		<div className="min-h-screen bg-gray-50 font-sans">
			{/* Top Navbar */}
			<header className="bg-white border-b border-gray-200 sticky top-[32px] z-40 shadow-sm">
				<div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
					<div className="flex justify-between h-16">
						{/* Logo Section */}
						<div className="flex items-center">
							<div className="shrink-0 flex items-center gap-2">
								<div className="">
									<LogoIcon className="size-10 fill-current text-emerald-600" />
								</div>
								<h1 className="text-lg font-bold text-gray-800 tracking-tight">
									Donasai
								</h1>
							</div>
						</div>

						{/* Navigation Menu */}
						<div className="flex items-center space-x-1">
							{navItems.map((item) => {
								const Icon = item.icon;
								const isActive = location.pathname === item.path;
								return (
									<Link
										key={item.path}
										to={item.path}
										className={clsx(
											"flex items-center gap-2 px-4 py-2 rounded-md text-sm font-medium transition-colors",
											isActive
												? "bg-emerald-50 text-emerald-700"
												: "text-gray-600 hover:bg-gray-50 hover:text-gray-900",
										)}
									>
										<Icon size={18} />
										{item.label}
									</Link>
								);
							})}
						</div>
					</div>
				</div>
			</header>

			{/* Main Content */}
			<main className="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
				<Routes>
					<Route path="/" element={<Dashboard />} />
					<Route path="/donations" element={<DonationsPage />} />
					<Route path="/confirmations" element={<Confirmations />} />
					<Route path="/fundraisers" element={<FundraisersPage />} />
					<Route path="/settings" element={<SettingsPage />} />
					<Route path="/receipt-template" element={<ReceiptTemplatePage />} />
					<Route path="/email-template" element={<EmailTemplatePage />} />
				</Routes>
			</main>
		</div>
	);
}

export default App;
