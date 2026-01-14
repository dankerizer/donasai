import { QueryClient, QueryClientProvider } from "@tanstack/react-query";

const queryClient = new QueryClient();

import { ReactQueryDevtools } from "@tanstack/react-query-devtools";
import clsx from "clsx";
import {
	CheckCircle,
	Heart,
	LayoutDashboard,
	Menu,
	Settings as SettingsIcon,
	Users,
	X,
} from "lucide-react";
import { useEffect, useState } from "react";
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
import { ThemeProvider } from "./components/ThemeProvider";
import { ThemeToggle } from "./components/ThemeToggle";
import Confirmations from "./pages/Confirmations";
import Dashboard from "./pages/Dashboard";
import DonationsPage from "./pages/DonationsPage";
import EditorPage from "./pages/editor";
import EmailTemplatePage from "./pages/email-template";
import FundraisersPage from "./pages/FundraisersPage";
import ReceiptTemplatePage from "./pages/receipt-template";
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
			<ThemeProvider defaultTheme="light" storageKey="donasai-theme">
				<HashRouter>
					<Routes>
						{/* Editor renders full-screen, outside normal layout */}
						<Route path="/editor" element={<EditorPage />} />

						{/* All other routes use AppLayout */}
						<Route
							path="*"
							element={
								<div className="relative min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors">
									{showLock && <ActivationLock />}
									<AppLayout />
								</div>
							}
						/>
					</Routes>
					<ReactQueryDevtools initialIsOpen={false} />
				</HashRouter>
				<Toaster position="top-right" richColors closeButton />
			</ThemeProvider>
		</QueryClientProvider>
	);
}

function AppLayout() {
	const location = useLocation();
	const navigate = useNavigate();
	const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);

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

	// Close mobile menu on route change
	useEffect(() => {
		setIsMobileMenuOpen(false);
	}, [location.pathname]);

	const navItems = [
		{ label: "Dasbor", path: "/", icon: LayoutDashboard },
		{ label: "Donasi", path: "/donations", icon: Heart },
		{ label: "Konfirmasi", path: "/confirmations", icon: CheckCircle },
		{ label: "Penggalang Dana", path: "/fundraisers", icon: Users },
		{ label: "Pengaturan", path: "/settings", icon: SettingsIcon },
	];

	return (
		<div className="min-h-screen font-sans text-gray-900 dark:text-gray-100">
			{/* Top Navbar */}
			<header className="bg-white dark:bg-gray-950 border-b border-gray-200 dark:border-gray-800 sticky top-[32px] z-40 shadow-sm transition-colors">
				<div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
					<div className="flex justify-between h-16 items-center">
						{/* Left: Logo & Mobile Menu Toggle */}
						<div className="flex items-center gap-3">
							{/* Mobile Menu Button */}
							<button
								type="button"
								className="lg:hidden p-2 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-md"
								onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
								aria-label="Toggle menu"
							>
								{isMobileMenuOpen ? <X size={20} /> : <Menu size={20} />}
							</button>

							{/* Logo */}
							<div className="shrink-0 flex items-center gap-2">
								<div className="">
									<LogoIcon className="size-8 md:size-10 fill-current text-emerald-600" />
								</div>
								<h1 className="text-lg font-bold text-gray-800 dark:text-white tracking-tight my-0! py-0! flex items-center">
									<span>Donasai</span>
									<span className="text-xs text-gray-500 dark:text-gray-400 inline-block relative wpd ml-1">
										v01
									</span>
								</h1>
							</div>
						</div>

						{/* Desktop Navigation Menu */}
						<div className="hidden! md:flex! items-center space-x-1">
							{navItems.map((item) => {
								const Icon = item.icon;
								const isActive = location.pathname === item.path;
								return (
									<Link
										key={item.path}
										to={item.path}
										className={clsx(
											"flex items-center gap-2 px-3 py-2 rounded-md text-sm font-medium transition-colors",
											isActive
												? "bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400"
												: "text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-900 hover:text-gray-900 dark:hover:text-gray-200",
										)}
									>
										<Icon size={18} />
										{item.label}
									</Link>
								);
							})}

							<div className="pl-2 ml-2 border-l border-gray-200 dark:border-gray-800">
								<ThemeToggle />
							</div>
						</div>

						{/* Right: Theme Toggle (Mobile) */}
						<div className="flex lg:hidden items-center">
							<ThemeToggle />
						</div>
					</div>
				</div>

				{/* Mobile Navigation Menu (Dropdown) */}
				{isMobileMenuOpen && (
					<div className="lg:hidden border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-950 animate-in slide-in-from-top-2 duration-200">
						<div className="px-2 pt-2 pb-3 space-y-1 sm:px-3">
							{navItems.map((item) => {
								const Icon = item.icon;
								const isActive = location.pathname === item.path;
								return (
									<Link
										key={item.path}
										to={item.path}
										className={clsx(
											"flex items-center gap-3 px-3 py-3 rounded-md text-base font-medium transition-colors",
											isActive
												? "bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400"
												: "text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-900 hover:text-gray-900 dark:hover:text-gray-200",
										)}
									>
										<Icon size={20} />
										{item.label}
									</Link>
								);
							})}
						</div>
					</div>
				)}
			</header>

			{/* Main Content */}
			<main className="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
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
