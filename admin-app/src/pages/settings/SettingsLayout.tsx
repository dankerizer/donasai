import clsx from "clsx";
import {
	Bell,
	Building,
	Check,
	CreditCard,
	Crown,
	Heart,
	Lock,
	Paintbrush,
	Palette,
	Star,
} from "lucide-react";
import { useEffect, useState } from "react";
import { useSettings } from "./SettingsContext";
import ProModal from "./ProModal";
import AdvancedSection from "./sections/AdvancedSection";
import AppearanceSection from "./sections/AppearanceSection";
import DonationSection from "./sections/DonationSection";
// Section Components
import GeneralSection from "./sections/GeneralSection";
import LicenseSection from "./sections/LicenseSection";
import NotificationSection from "./sections/NotificationSection";
import PaymentSection from "./sections/PaymentSection";

export default function SettingsLayout() {
	const {
		isProInstalled,
		licenseStatus,
		proSettings,
		isLoading,
		saveSettings,
		setShowProModal,
		isSaving,
	} = useSettings();

	// Get initial tab from URL
	const searchParams = new URLSearchParams(window.location.search);
	const initialTab = searchParams.get("tab") || "general";
	const [activeTab, setActiveTab] = useState(initialTab);

	// Sync activeTab with URL
	// biome-ignore lint/correctness/useExhaustiveDependencies: <explanation>
	useEffect(() => {
		const params = new URLSearchParams(window.location.search);
		const currentTab = params.get("tab");
		if (currentTab && currentTab !== activeTab) {
			setActiveTab(currentTab);
		}
	}, []);

	const handleTabChange = (tabId: string) => {
		if (tabId === "editor") {
			window.location.hash = "#/editor";
			return;
		}

		const params = new URLSearchParams(window.location.search);
		params.set("tab", tabId);
		window.history.pushState(
			{},
			"",
			`${window.location.pathname}?${params.toString()}`,
		);
		setActiveTab(tabId);
	};

	const tabs = [
		{ id: "general", label: "General & Org", icon: Building },
		{ id: "donation", label: "Donation Settings", icon: Heart },
		{ id: "payment", label: "Payment", icon: CreditCard },
		{ id: "notifications", label: "Notifications", icon: Bell },
		{ id: "appearance", label: "Appearance", icon: Palette },
		{ id: "advanced", label: "Advanced", icon: Star },
		{ id: "editor", label: "Donasai Editor", icon: Paintbrush },
		{ id: "license", label: "License", icon: Lock },
	].filter(
		(tab) => (tab.id !== "license" && tab.id !== "editor") || isProInstalled,
	);

	const showActivationLock = isProInstalled && licenseStatus !== "active";

	if (isLoading) {
		return (
			<div className="flex items-center justify-center min-h-[400px]">
				<div className="animate-spin rounded-full h-8 w-8 border-b-2 border-emerald-600" />
			</div>
		);
	}

	return (
		<div className="space-y-6 relative">
			{/* Activation Lock Modal - Reusing the component if available or custom logic */}
			{/* The original code had a custom inline modal. We can extract it or keep it. 
                For now, I'll use the ActivationLock component if it matches, 
                but looking at the original code, it had a CUSTOM modal implementation inside Settings.tsx.
                I will stick to the original implementation for now to ensure exact parity, 
                or if ActivationLock component is the same, use it. 
                Ref: line 373 in Settings.tsx. It looks like a custom modal.
            */}
			{showActivationLock && (
				<div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-white/50 dark:bg-black/50 backdrop-blur-xl animate-in fade-in duration-500">
					<div className="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-800 max-w-md w-full overflow-hidden p-8 text-center ring-1 ring-black/5 relative">
						{/* Decorative Background Blob */}
						<div className="absolute top-0 left-1/2 -translate-x-1/2 w-full h-32 bg-linear-to-b from-blue-50 to-transparent -z-10"></div>

						<div className="w-20 h-20 bg-blue-600 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-blue-200 shadow-xl rotate-3 transition-transform hover:rotate-6">
							<Lock className="text-white" size={40} strokeWidth={2.5} />
						</div>

						<h2 className="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2 tracking-tight">
							Aktivasi Donasai Pro
						</h2>

						<p className="text-gray-500 dark:text-gray-400 mb-8 leading-relaxed text-[15px]">
							Plugin <strong>Donasai Pro</strong> telah terpasang. Harap
							hubungkan lisensi Anda untuk membuka semua fitur premium.
						</p>

						<div className="space-y-3">
							{proSettings.connectUrl ? (
								<a
									href={proSettings.connectUrl as string}
									className="block w-full py-3.5 px-6 bg-blue-600 hover:bg-blue-700 text-white! font-bold rounded-xl transition-all shadow-lg shadow-blue-600/20 hover:shadow-blue-600/30 active:scale-95 focus:text-blue-100!"
								>
									Hubungkan & Aktivasi
								</a>
							) : (
								<div className="p-4 bg-red-50 text-red-600 rounded-xl text-sm border border-red-100 mb-4 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800">
									<p className="font-semibold">Error Konfigurasi</p>
									<p>URL Aktivasi tidak ditemukan.</p>
								</div>
							)}

							<a
								href="/wp-admin/plugins.php"
								className="block w-full py-3.5 px-6 bg-white hover:bg-gray-50 text-gray-600 font-medium rounded-xl border border-gray-200 transition-colors dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-700"
							>
								Nonaktifkan Plugin
							</a>
						</div>

						<p className="mt-8 text-xs text-gray-400 dark:text-gray-500">
							Masalah dengan lisensi?{" "}
							<a
								href="https://donasai.com/contact"
								className="underline hover:text-gray-600 dark:hover:text-gray-300"
							>
								Hubungi Dukungan
							</a>
						</p>
					</div>
				</div>
			)}

			<ProModal />

			<div className="flex justify-between items-center">
				<h2 className="text-2xl font-bold text-gray-800 dark:text-gray-100">Pengaturan</h2>
			</div>

			{/* Pro Banner */}
			{licenseStatus !== "active" ? (
				<div className="bg-linear-to-r from-emerald-50 to-teal-50 border border-emerald-100 rounded-xl p-4 flex flex-col md:flex-row items-center justify-between gap-4 dark:from-emerald-900/20 dark:to-teal-900/20 dark:border-emerald-800">
					<div className="flex flex-col md:flex-row items-center gap-4 md:gap-8 w-full md:w-auto">
						<div className="flex items-center gap-3 w-full md:w-auto">
							<div className="p-2 bg-emerald-100 text-emerald-600 rounded-lg shrink-0 dark:bg-emerald-800/50 dark:text-emerald-400">
								<Crown size={20} />
							</div>
							<div>
								<h3 className="font-bold text-gray-900 text-sm dark:text-gray-100">
									Upgrade ke Pro
								</h3>
								<p className="text-xs text-gray-500 dark:text-gray-400">
									Buka semua fitur premium
								</p>
							</div>
						</div>

						<div className="flex flex-col sm:flex-row gap-x-6 gap-y-2 w-full md:w-auto pl-12 md:pl-0">
							<div className="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
								<Check size={14} className="text-emerald-500" />
								<span>Payment Gateway Auto</span>
							</div>
							<div className="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
								<Check size={14} className="text-emerald-500" />
								<span>Donasi Berulang</span>
							</div>
							<div className="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
								<Check size={14} className="text-emerald-500" />
								<span>Visual Editor</span>
							</div>
						</div>
					</div>

					<button
						type="button"
						onClick={() => setShowProModal(true)}
						className="w-full md:w-auto px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition shadow-sm whitespace-nowrap shrink-0"
					>
						Lihat Detail
					</button>
				</div>
			) : (
				<div className="bg-green-100 border border-green-200 rounded-xl p-4 text-green-800 flex items-center gap-3 dark:bg-green-900/20 dark:border-green-800 dark:text-green-300">
					<div className="p-2 bg-green-200 rounded-full dark:bg-green-800/50">
						<Check size={20} />
					</div>
					<div>
						<h3 className="font-bold">Donasai Pro Aktif</h3>
						<p className="text-sm opacity-90">
							Terima kasih telah mendukung pengembangan plugin ini!
						</p>
					</div>
				</div>
			)}

			<div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden min-h-[500px] flex flex-col md:flex-row dark:bg-gray-800 dark:border-gray-700">
				{/* Sidebar Tabs */}
				<div className="w-full md:w-64 bg-gray-50 border-b md:border-b-0 md:border-r border-gray-200 p-2 md:p-4 space-x-2 md:space-x-0 md:space-y-2 flex flex-row md:flex-col overflow-x-auto md:overflow-visible dark:bg-gray-900/50 dark:border-gray-700 scrollbar-hide">
					{tabs.map((tab) => {
						const Icon = tab.icon;
						return (
							<button
								type="button"
								key={tab.id}
								onClick={() => handleTabChange(tab.id)}
								className={clsx(
									"shrink-0 w-auto md:w-full flex items-center gap-2 md:gap-3 px-3 py-2 md:px-4 md:py-3 rounded-lg text-sm font-medium transition-colors text-left whitespace-nowrap",
									activeTab === tab.id
										? "bg-white text-blue-600 shadow-sm border border-gray-200 dark:bg-gray-800! dark:text-blue-400! dark:border-gray-700"
										: "text-gray-600 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400! dark:hover:bg-gray-800 dark:hover:text-gray-200",
								)}
							>
								<Icon size={18} />
								<span className="sm:hidden! md:hidden">
                                     {tab.label === "General & Org"
                                         ? "Umum"
                                         : tab.label}
                                </span>
                                <span className="hidden! md:inline!">
									{tab.label === "General & Org"
										? "Umum & Organisasi"
										: tab.label === "Donation Settings"
											? "Pengaturan Donasi"
											: tab.label === "Payment"
												? "Pembayaran"
											: tab.label === "Notifications"
												? "Notifikasi"
											: tab.label === "Appearance"
												? "Tampilan"
											: tab.label === "License"
												? "Lisensi"
											: tab.label === "Receipt Template"
												? "Template Kuitansi"
											: tab.label === "Email Template"
												? "Template Email"
											: tab.label}
                                </span>
							</button>
						);
					})}
				</div>

				{/* Content Area */}
				<div className="flex-1 p-4 md:p-8 overflow-hidden">
					<form onSubmit={saveSettings} className="max-w-2xl space-y-6">
						{activeTab === "general" && <GeneralSection />}
						{activeTab === "donation" && <DonationSection />}
						{activeTab === "payment" && <PaymentSection />}
						{activeTab === "notifications" && <NotificationSection />}
						{activeTab === "appearance" && <AppearanceSection />}
						{activeTab === "advanced" && <AdvancedSection />}
						{activeTab === "license" && <LicenseSection />}

						<div className="flex justify-end pt-6 border-t border-gray-100 dark:border-gray-700">
							<button
								type="submit"
								disabled={isLoading || isSaving}
								className="bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-6 py-2.5 rounded-lg shadow-sm hover:shadow-md transition-all flex items-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed"
							>
								{isSaving ? (
									<>
										<div className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" />
										<span>Menyimpan...</span>
									</>
								) : (
									<>
										<Check size={18} />
										<span>Simpan Perubahan</span>
									</>
								)}
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	);
}
