/* biome-ignore-all lint/a11y/useSemanticElements: Pro feature cards use divs with role=button for conditional interactivity */
import clsx from "clsx";
import { Lock, RotateCcw } from "lucide-react";
import { Input } from "/src/components/ui/Input";
import { Label } from "/src/components/ui/Label";
import { Select } from "/src/components/ui/Select";
import { useSettings } from "../SettingsContext";

export default function AppearanceSection() {
	const { formData, setFormData, licenseStatus, setShowProModal } =
		useSettings();

	const isProActive = ["active", "pro"].includes(licenseStatus);

	const handleResetAppearance = () => {
		if (window.confirm("Kembalikan pengaturan tampilan ke default?")) {
			setFormData({
				...formData,
				brand_color: "#059669",
				button_color: "#ec4899",
				container_width: "1100px",
				border_radius: "12px",
				campaign_layout: "sidebar-right",
				sidebar_count: 5,
				donor_per_page: 10,
			});
		}
	};

	return (
		<div className="space-y-8">
			<div>
				<div className="flex items-center justify-between mb-4">
					<h3 className="text-lg font-medium text-gray-900 dark:text-gray-100">
						Tampilan & Layout
					</h3>
					<button
						type="button"
						onClick={handleResetAppearance}
						className="text-xs flex items-center gap-1.5 text-gray-500 hover:text-red-600 transition-colors dark:text-gray-400 dark:hover:text-red-400"
					>
						<RotateCcw size={12} />
						Reset Default
					</button>
				</div>
				<div className="grid grid-cols-1 md:grid-cols-2 gap-6">
					<div>
						<Label htmlFor="brand_color" className="mb-2">
							Warna Merek Utama
						</Label>
						<div className="flex items-center gap-3">
							<Input
								type="color"
								id="brand_color"
								value={formData.brand_color || "#059669"}
								onChange={(e) =>
									setFormData({
										...formData,
										brand_color: e.target.value,
									})
								}
								className="h-10 w-20 p-1 cursor-pointer"
							/>
							<span className="text-sm text-gray-500 font-mono uppercase dark:text-gray-400">
								{formData.brand_color}
							</span>
						</div>
						<p className="text-xs text-gray-500 mt-2 dark:text-gray-400">
							Digunakan untuk lencana, jumlah, dan bilah kemajuan.
						</p>
					</div>
					<div>
						<Label htmlFor="button_color" className="mb-2">
							Warna Tombol
						</Label>
						<div className="flex items-center gap-3">
							<Input
								type="color"
								id="button_color"
								value={formData.button_color || "#ec4899"}
								onChange={(e) =>
									setFormData({
										...formData,
										button_color: e.target.value,
									})
								}
								className="h-10 w-20 p-1 cursor-pointer"
							/>
							<span className="text-sm text-gray-500 font-mono uppercase dark:text-gray-400">
								{formData.button_color}
							</span>
						</div>
						<p className="text-xs text-gray-500 mt-2 dark:text-gray-400">
							Tombol CTA utama (Donasi, Kirim).
						</p>
					</div>
					<div>
						<Label htmlFor="container_width">Lebar Kontainer</Label>
						<Input
							type="text"
							id="container_width"
							value={formData.container_width || "1100px"}
							onChange={(e) =>
								setFormData({
									...formData,
									container_width: e.target.value,
								})
							}
							placeholder="1100px"
						/>
						<p className="text-xs text-gray-500 mt-2 dark:text-gray-400">
							Lebar maksimal halaman campaign.
						</p>
					</div>
					<div>
						<Label htmlFor="border_radius">Border Radius</Label>
						<Input
							type="text"
							id="border_radius"
							value={formData.border_radius || "12px"}
							onChange={(e) =>
								setFormData({
									...formData,
									border_radius: e.target.value,
								})
							}
							placeholder="12px"
						/>
						<p className="text-xs text-gray-500 mt-2 dark:text-gray-400">
							Kelengkungan sudut (card, tombol, input).
						</p>
					</div>
					<div className="md:col-span-2">
						<label
							htmlFor="campaign_layout"
							className="block text-sm font-medium text-gray-700 mb-3 dark:text-gray-300"
						>
							Layout Halaman Campaign
						</label>
						<div className="grid grid-cols-3 gap-4">
							{/* Sidebar Right */}
							<button
								type="button"
								id="campaign_layout"
								className={clsx(
									"w-full text-left border-2 rounded-xl p-3 cursor-pointer transition-all hover:border-blue-300",
									formData.campaign_layout === "sidebar-right"
										? "border-blue-600 bg-blue-50 ring-1 ring-blue-600 dark:bg-blue-900/20 dark:border-blue-500"
										: "border-gray-200 bg-white dark:bg-gray-800 dark:border-gray-700 dark:hover:border-gray-600",
								)}
								onClick={() =>
									setFormData({
										...formData,
										campaign_layout: "sidebar-right",
									})
								}
							>
								<div className="aspect-video bg-gray-100 rounded mb-2 flex gap-1 p-1 dark:bg-gray-700">
									<div className="bg-gray-300 h-full w-2/3 rounded-sm dark:bg-gray-600"></div>
									<div className="bg-blue-200 h-full w-1/3 rounded-sm dark:bg-blue-900"></div>
								</div>
								<div className="text-xs font-medium text-center text-gray-700 dark:text-gray-300">
									Sidebar Kanan
								</div>
							</button>

							{/* Sidebar Left */}
							<button
								type="button"
								className={clsx(
									"w-full text-left border-2 rounded-xl p-3 cursor-pointer transition-all hover:border-blue-300",
									formData.campaign_layout === "sidebar-left"
										? "border-blue-600 bg-blue-50 ring-1 ring-blue-600 dark:bg-blue-900/20 dark:border-blue-500"
										: "border-gray-200 bg-white dark:bg-gray-800 dark:border-gray-700 dark:hover:border-gray-600",
								)}
								onClick={() =>
									setFormData({
										...formData,
										campaign_layout: "sidebar-left",
									})
								}
							>
								<div className="aspect-video bg-gray-100 rounded mb-2 flex gap-1 p-1 dark:bg-gray-700">
									<div className="bg-blue-200 h-full w-1/3 rounded-sm dark:bg-blue-900"></div>
									<div className="bg-gray-300 h-full w-2/3 rounded-sm dark:bg-gray-600"></div>
								</div>
								<div className="text-xs font-medium text-center text-gray-700 dark:text-gray-300">
									Sidebar Kiri
								</div>
							</button>

							{/* Full Width */}
							<button
								type="button"
								className={clsx(
									"w-full text-left border-2 rounded-xl p-3 cursor-pointer transition-all hover:border-blue-300",
									formData.campaign_layout === "full-width"
										? "border-blue-600 bg-blue-50 ring-1 ring-blue-600 dark:bg-blue-900/20 dark:border-blue-500"
										: "border-gray-200 bg-white dark:bg-gray-800 dark:border-gray-700 dark:hover:border-gray-600",
								)}
								onClick={() =>
									setFormData({
										...formData,
										campaign_layout: "full-width",
									})
								}
							>
								<div className="aspect-video bg-gray-100 rounded mb-2 w-full p-1 dark:bg-gray-700">
									<div className="bg-gray-300 h-full w-full rounded-sm dark:bg-gray-600"></div>
								</div>
								<div className="text-xs font-medium text-center text-gray-700 dark:text-gray-300">
									Full Width
								</div>
							</button>
						</div>
					</div>
				</div>

				{/* Donor Limits */}
				<div className="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
					<div>
						<Label htmlFor="sidebar_count">Jumlah Donatur di Sidebar</Label>
						<Input
							type="number"
							id="sidebar_count"
							value={formData.sidebar_count}
							onChange={(e) =>
								setFormData({
									...formData,
									sidebar_count: Number.parseInt(e.target.value) || 5,
								})
							}
							min={1}
							max={20}
							className="mt-1"
						/>
						<p className="text-xs text-gray-500 mt-2 dark:text-gray-400">
							Jumlah donatur terakhir yang ditampilkan di sidebar.
						</p>
					</div>
					<div>
						<Label htmlFor="donor_per_page">Jumlah Donatur per Halaman</Label>
						<Input
							type="number"
							id="donor_per_page"
							value={formData.donor_per_page}
							onChange={(e) =>
								setFormData({
									...formData,
									donor_per_page: Number.parseInt(e.target.value) || 10,
								})
							}
							min={1}
							max={50}
							className="mt-1"
						/>
						<p className="text-xs text-gray-500 mt-2 dark:text-gray-400">
							Jumlah donatur yang dimuat per klik "Muat Lebih Banyak".
						</p>
					</div>
				</div>
			</div>

			{/* Pro Teasers */}
			<div className="border-t border-gray-200 pt-6 dark:border-gray-700">
				<h3 className="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2 dark:text-gray-100">
					Gaya Lanjutan{" "}
					<span className="bg-purple-100 text-purple-700 text-xs px-2 py-0.5 rounded font-bold dark:bg-purple-900/40 dark:text-purple-300">
						PRO
					</span>
				</h3>
				<div className="grid gap-4 md:grid-cols-2">
					{/* Typography */}
					<div
						role="button"
						tabIndex={0}
						className={clsx(
							"border border-gray-200 rounded-lg p-4 bg-gray-50 relative dark:bg-gray-800/50 dark:border-gray-700",
							!isProActive ? "opacity-60 cursor-pointer" : "",
						)}
						onClick={() => !isProActive && setShowProModal(true)}
						onKeyDown={(e) => {
							if (e.key === "Enter" || e.key === " ") {
								e.preventDefault();
								!isProActive && setShowProModal(true);
							}
						}}
					>
						<div className="flex justify-between items-start mb-2">
							<div className="font-medium text-gray-900 dark:text-gray-100">
								Tipografi
							</div>
							{!isProActive ? (
								<Lock size={14} className="text-gray-400" />
							) : (
								<span className="text-xs font-bold text-green-600 bg-green-100 px-2 rounded">
									Active
								</span>
							)}
						</div>

						{isProActive ? (
							<div className="space-y-3 mt-3">
								<div>
									<Label htmlFor="font_family" className="text-xs mb-1">
										Font Utama
									</Label>
									<Select
										id="font_family"
										value={formData.font_family || "Inter"}
										onChange={(e) =>
											setFormData({
												...formData,
												font_family: e.target.value,
											})
										}
										className="text-sm"
									>
										<option value="Inter">Inter (Default)</option>
										<option value="Roboto">Roboto</option>
										<option value="Open Sans">Open Sans</option>
										<option value="Poppins">Poppins</option>
										<option value="Lato">Lato</option>
									</Select>
								</div>
								<div>
									<Label htmlFor="font_size" className="text-xs mb-1">
										Ukuran Font Dasar
									</Label>
									<div className="flex items-center gap-2">
										<Input
											type="text"
											id="font_size"
											value={formData.font_size || "16px"}
											onChange={(e) =>
												setFormData({
													...formData,
													font_size: e.target.value,
												})
											}
											className="w-20 text-sm p-2"
										/>
										<span className="text-xs text-gray-500">px/rem</span>
									</div>
								</div>
							</div>
						) : (
							<p className="text-sm text-gray-500 dark:text-gray-400">
								Font Google kustom dan kontrol ukuran.
							</p>
						)}
					</div>

					{/* Dark Mode */}
					<div
						role="button"
						tabIndex={0}
						className={clsx(
							"border border-gray-200 rounded-lg p-4 bg-gray-50 relative dark:bg-gray-800/50 dark:border-gray-700",
							!isProActive ? "opacity-60 cursor-pointer" : "",
						)}
						onClick={() => !isProActive && setShowProModal(true)}
						onKeyDown={(e) => {
							if (e.key === "Enter" || e.key === " ") {
								e.preventDefault();
								!isProActive && setShowProModal(true);
							}
						}}
					>
						<div className="flex justify-between items-start mb-2">
							<div className="font-medium text-gray-900 dark:text-gray-100">
								Mode Gelap
							</div>
							{!isProActive ? (
								<Lock size={14} className="text-gray-400" />
							) : (
								<span className="text-xs font-bold text-green-600 bg-green-100 px-2 rounded">
									Active
								</span>
							)}
						</div>

						{isProActive ? (
							<div className="mt-3">
								<div className="flex items-center gap-3">
									<label className="relative inline-flex items-center cursor-pointer">
										<input
											type="checkbox"
											className="sr-only peer"
											checked={formData.dark_mode}
											onChange={(e) =>
												setFormData({
													...formData,
													dark_mode: e.target.checked,
												})
											}
										/>
										<div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
									</label>
									<span className="text-sm text-gray-600 dark:text-gray-300">
										{formData.dark_mode ? "Aktif" : "Nonaktif"}
									</span>
								</div>
								<p className="text-xs text-gray-500 mt-2 dark:text-gray-400">
									Otomatis menyesuaikan warna background dan teks.
								</p>
							</div>
						) : (
							<p className="text-sm text-gray-500 dark:text-gray-400">
								Aktifkan dukungan mode gelap di seluruh situs.
							</p>
						)}
					</div>

					{/* Donation Form Layout (Pro Only) */}
					<div
						role="button"
						tabIndex={0}
						className={clsx(
							"border border-gray-200 rounded-lg p-4 bg-gray-50 relative md:col-span-2 dark:bg-gray-800/50 dark:border-gray-700",
							!isProActive ? "opacity-60 cursor-pointer" : "",
						)}
						onClick={() => !isProActive && setShowProModal(true)}
						onKeyDown={(e) => {
							if (e.key === "Enter" || e.key === " ") {
								e.preventDefault();
								!isProActive && setShowProModal(true);
							}
						}}
					>
						<div className="flex justify-between items-start mb-4">
							<div className="font-medium text-gray-900 dark:text-gray-100">
								Layout Formulir Donasi
							</div>
							{!isProActive ? (
								<Lock size={14} className="text-gray-400" />
							) : (
								<span className="text-xs font-bold text-green-600 bg-green-100 px-2 rounded">
									Active
								</span>
							)}
						</div>

						{isProActive ? (
							<div className="grid grid-cols-2 gap-4">
								{/* Default */}
								<button
									type="button"
									className={clsx(
										"w-full text-left border-2 rounded-xl p-3 cursor-pointer transition-all hover:border-blue-300",
										formData.donation_layout === "default"
											? "border-blue-600 bg-blue-50 ring-1 ring-blue-600 dark:bg-blue-900/20 dark:border-blue-500"
											: "border-gray-200 bg-white dark:bg-gray-800 dark:border-gray-700 dark:hover:border-gray-600",
									)}
									onClick={() =>
										setFormData({
											...formData,
											donation_layout: "default",
										})
									}
								>
									<div className="aspect-video bg-gray-100 rounded mb-2 flex flex-col items-center justify-center p-2 dark:bg-gray-700">
										<div className="bg-white w-2/3 h-full rounded shadow-sm border border-gray-200 dark:bg-gray-600 dark:border-gray-500"></div>
									</div>
									<div className="text-xs font-medium text-center text-gray-700 dark:text-gray-300">
										Tunggal (Default)
									</div>
								</button>

								{/* Split */}
								<button
									type="button"
									className={clsx(
										"w-full text-left border-2 rounded-xl p-3 cursor-pointer transition-all hover:border-blue-300",
										formData.donation_layout === "split"
											? "border-blue-600 bg-blue-50 ring-1 ring-blue-600 dark:bg-blue-900/20 dark:border-blue-500"
											: "border-gray-200 bg-white dark:bg-gray-800 dark:border-gray-700 dark:hover:border-gray-600",
									)}
									onClick={() =>
										setFormData({
											...formData,
											donation_layout: "split",
										})
									}
								>
									<div className="aspect-video bg-gray-100 rounded mb-2 flex gap-1 p-1 dark:bg-gray-700">
										<div className="bg-blue-100 h-full w-1/2 rounded-sm border border-blue-200 dark:bg-blue-900 dark:border-blue-700"></div>
										<div className="bg-white h-full w-1/2 rounded-sm border border-gray-200 dark:bg-gray-600 dark:border-gray-500"></div>
									</div>
									<div className="text-xs font-medium text-center text-gray-700 dark:text-gray-300">
										Split (Kiri Info, Kanan Form)
									</div>
								</button>
							</div>
						) : (
							<p className="text-sm text-gray-500 dark:text-gray-400">
								Pilihan tata letak untuk formulir donasi.
							</p>
						)}
					</div>

					{/* Hero Style (Pro Only) */}
					<div
						role="button"
						tabIndex={0}
						className={clsx(
							"border border-gray-200 rounded-lg p-4 bg-gray-50 relative md:col-span-2 dark:bg-gray-800/50 dark:border-gray-700",
							!isProActive ? "opacity-60 cursor-pointer" : "",
						)}
						onClick={() => !isProActive && setShowProModal(true)}
						onKeyDown={(e) => {
							if (e.key === "Enter" || e.key === " ") {
								e.preventDefault();
								!isProActive && setShowProModal(true);
							}
						}}
					>
						<div className="flex justify-between items-start mb-4">
							<div className="font-medium text-gray-900 dark:text-gray-100">
								Gaya Hero Section
							</div>
							{!isProActive ? (
								<Lock size={14} className="text-gray-400" />
							) : (
								<span className="text-xs font-bold text-green-600 bg-green-100 px-2 rounded">
									Active
								</span>
							)}
						</div>

						{isProActive ? (
							<div className="grid grid-cols-3 gap-4">
								{/* Standard */}
								<button
									type="button"
									className={clsx(
										"w-full text-left border-2 rounded-xl p-3 cursor-pointer transition-all hover:border-blue-300",
										formData.hero_style === "standard"
											? "border-blue-600 bg-blue-50 ring-1 ring-blue-600"
											: "border-gray-200 bg-white",
									)}
									onClick={() =>
										setFormData({
											...formData,
											hero_style: "standard",
										})
									}
								>
									<div className="aspect-video bg-gray-100 rounded mb-2 flex flex-col items-center justify-center p-2 gap-1">
										<div className="bg-gray-300 w-full h-1/2 rounded-sm"></div>
										<div className="bg-gray-400 w-3/4 h-2 rounded-sm"></div>
									</div>
									<div className="text-xs font-medium text-center text-gray-700">
										Standard
									</div>
								</button>

								{/* Wide */}
								<button
									type="button"
									className={clsx(
										"w-full text-left border-2 rounded-xl p-3 cursor-pointer transition-all hover:border-blue-300",
										formData.hero_style === "wide"
											? "border-blue-600 bg-blue-50 ring-1 ring-blue-600"
											: "border-gray-200 bg-white",
									)}
									onClick={() =>
										setFormData({
											...formData,
											hero_style: "wide",
										})
									}
								>
									<div className="aspect-video bg-gray-100 rounded mb-2 flex flex-col gap-1 p-1">
										<div className="bg-gray-300 w-full h-2/3 rounded-sm"></div>
										<div className="bg-gray-400 w-1/2 h-2 rounded-sm ml-1"></div>
									</div>
									<div className="text-xs font-medium text-center text-gray-700">
										Wide (Lebar Penuh)
									</div>
								</button>

								{/* Overlay */}
								<button
									type="button"
									className={clsx(
										"w-full text-left border-2 rounded-xl p-3 cursor-pointer transition-all hover:border-blue-300",
										formData.hero_style === "overlay"
											? "border-blue-600 bg-blue-50 ring-1 ring-blue-600"
											: "border-gray-200 bg-white",
									)}
									onClick={() =>
										setFormData({
											...formData,
											hero_style: "overlay",
										})
									}
								>
									<div className="aspect-video bg-gray-300 rounded mb-2 flex items-center justify-center relative overflow-hidden">
										<div className="absolute inset-0 bg-black/30"></div>
										<div className="relative bg-white w-2/3 h-2 rounded-sm"></div>
									</div>
									<div className="text-xs font-medium text-center text-gray-700">
										Overlay (Teks diatas Gambar)
									</div>
								</button>
							</div>
						) : (
							<p className="text-sm text-gray-500">
								Pilihan gaya tampilan gambar utama (cover) campaign.
							</p>
						)}
					</div>

					{/* Feature Controls (Pro Only) */}
					<div
						role="button"
						tabIndex={0}
						className={clsx(
							"border border-gray-200 rounded-lg p-4 bg-gray-50 relative md:col-span-2",
							!isProActive ? "opacity-60 cursor-pointer" : "",
						)}
						onClick={() => !isProActive && setShowProModal(true)}
						onKeyDown={(e) => {
							if (e.key === "Enter" || e.key === " ") {
								e.preventDefault();
								!isProActive && setShowProModal(true);
							}
						}}
					>
						<div className="flex justify-between items-start mb-4">
							<div className="font-medium text-gray-900">
								Kontrol Fitur Halaman
							</div>
							{!isProActive ? (
								<Lock size={14} className="text-gray-400" />
							) : (
								<span className="text-xs font-bold text-green-600 bg-green-100 px-2 rounded">
									Active
								</span>
							)}
						</div>

						{isProActive ? (
							<div className="grid grid-cols-1 md:grid-cols-2 gap-4">
								<div className="flex items-center gap-3">
									<label className="relative inline-flex items-center cursor-pointer">
										<input
											type="checkbox"
											className="sr-only peer"
											checked={formData.show_countdown}
											onChange={(e) =>
												setFormData({
													...formData,
													show_countdown: e.target.checked,
												})
											}
										/>
										<div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
									</label>
									<span className="text-sm text-gray-700">
										Tampilkan Countdown Timer
									</span>
								</div>

								<div className="flex items-center gap-3">
									<label className="relative inline-flex items-center cursor-pointer">
										<input
											type="checkbox"
											className="sr-only peer"
											checked={formData.show_prayer_tab}
											onChange={(e) =>
												setFormData({
													...formData,
													show_prayer_tab: e.target.checked,
												})
											}
										/>
										<div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
									</label>
									<span className="text-sm text-gray-700">
										Tampilkan Tab Doa
									</span>
								</div>

								<div className="flex items-center gap-3">
									<label className="relative inline-flex items-center cursor-pointer">
										<input
											type="checkbox"
											className="sr-only peer"
											checked={formData.show_updates_tab}
											onChange={(e) =>
												setFormData({
													...formData,
													show_updates_tab: e.target.checked,
												})
											}
										/>
										<div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
									</label>
									<span className="text-sm text-gray-700">
										Tampilkan Kabar Terbaru
									</span>
								</div>

								<div className="flex items-center gap-3">
									<label className="relative inline-flex items-center cursor-pointer">
										<input
											type="checkbox"
											className="sr-only peer"
											checked={formData.show_donor_list}
											onChange={(e) =>
												setFormData({
													...formData,
													show_donor_list: e.target.checked,
												})
											}
										/>
										<div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
									</label>
									<span className="text-sm text-gray-700">
										Tampilkan List Donatur Sidebar
									</span>
								</div>

								<div className="flex items-center gap-3">
									<label className="relative inline-flex items-center cursor-pointer">
										<input
											type="checkbox"
											className="sr-only peer"
											checked={formData.show_leaderboard}
											onChange={(e) =>
												setFormData({
													...formData,
													show_leaderboard: e.target.checked,
												})
											}
										/>
										<div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
									</label>
									<span className="text-sm text-gray-700">
										Tampilkan Leaderboard Fundraiser
									</span>
								</div>
							</div>
						) : (
							<p className="text-sm text-gray-500">
								Aktifkan/nonaktifkan elemen seperti Countdown, Doa, dan List
								Donatur.
							</p>
						)}
					</div>

					{/* Social Proof Popup (Pro Only) */}
					<div
						role="button"
						tabIndex={0}
						className={clsx(
							"border border-gray-200 rounded-lg p-4 bg-gray-50 relative md:col-span-2",
							!isProActive ? "opacity-60 cursor-pointer" : "",
						)}
						onClick={() => !isProActive && setShowProModal(true)}
						onKeyDown={(e) => {
							if (e.key === "Enter" || e.key === " ") {
								e.preventDefault();
								!isProActive && setShowProModal(true);
							}
						}}
					>
						<div className="flex justify-between items-start mb-4">
							<div>
								<div className="font-medium text-gray-900">
									Social Proof Popup
								</div>
								<p className="text-xs text-gray-500 mt-1">
									Tampilkan notifikasi "Ahmad baru saja berdonasi..." untuk
									meningkatkan konversi.
								</p>
							</div>
							{!isProActive ? (
								<Lock size={14} className="text-gray-400" />
							) : (
								<span className="text-xs font-bold text-green-600 bg-green-100 px-2 rounded">
									Active
								</span>
							)}
						</div>

						{isProActive ? (
							<div className="space-y-4">
								{/* Enable Toggle */}
								<div className="flex items-center gap-3">
									<label className="relative inline-flex items-center cursor-pointer">
										<input
											type="checkbox"
											className="sr-only peer"
											checked={formData.social_proof_enabled}
											onChange={(e) =>
												setFormData({
													...formData,
													social_proof_enabled: e.target.checked,
												})
											}
										/>
										<div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
									</label>
									<span className="text-sm text-gray-700">
										{formData.social_proof_enabled ? "Aktif" : "Nonaktif"}
									</span>
								</div>

								{formData.social_proof_enabled && (
									<div className="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-gray-200">
										{/* Position */}
										<div>
											<label className="block text-sm font-medium text-gray-700 mb-1">
												Posisi Popup
											</label>
											<select
												className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
												value={formData.social_proof_position}
												onChange={(e) =>
													setFormData({
														...formData,
														social_proof_position: e.target.value,
													})
												}
											>
												<option value="bottom-left">Kiri Bawah</option>
												<option value="bottom-right">Kanan Bawah</option>
											</select>
										</div>

										{/* Interval */}
										<div>
											<label className="block text-sm font-medium text-gray-700 mb-1">
												Interval (detik)
											</label>
											<input
												type="number"
												className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
												value={formData.social_proof_interval}
												onChange={(e) =>
													setFormData({
														...formData,
														social_proof_interval:
															Number.parseInt(e.target.value) || 10,
													})
												}
												min={5}
												max={60}
											/>
											<p className="text-xs text-gray-500 mt-1">
												Jeda antar notifikasi
											</p>
										</div>

										{/* Duration */}
										<div>
											<label className="block text-sm font-medium text-gray-700 mb-1">
												Durasi Tampil (detik)
											</label>
											<input
												type="number"
												className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
												value={formData.social_proof_duration}
												onChange={(e) =>
													setFormData({
														...formData,
														social_proof_duration:
															Number.parseInt(e.target.value) || 5,
													})
												}
												min={3}
												max={15}
											/>
										</div>

										{/* Limit */}
										<div>
											<label className="block text-sm font-medium text-gray-700 mb-1">
												Jumlah Donasi Dirotasi
											</label>
											<input
												type="number"
												className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
												value={formData.social_proof_limit}
												onChange={(e) =>
													setFormData({
														...formData,
														social_proof_limit:
															Number.parseInt(e.target.value) || 10,
													})
												}
												min={5}
												max={50}
											/>
										</div>

										{/* Preview Button */}
										<div className="md:col-span-2">
											<button
												type="button"
												onClick={() => {
													// Show preview popup
													const existingPreview = document.getElementById(
														"donasai-sp-admin-preview",
													);
													if (existingPreview) existingPreview.remove();

													const previewHtml = `
														<div id="donasai-sp-admin-preview" style="
															position: fixed;
															bottom: 20px;
															${formData.social_proof_position === "bottom-right" ? "right: 20px;" : "left: 20px;"}
															background: #fff;
															border-radius: 12px;
															box-shadow: 0 10px 40px rgba(0,0,0,0.15);
															padding: 16px 20px;
															max-width: 340px;
															z-index: 99999;
															animation: donasai-sp-slide-in 0.3s ease-out;
														">
															<style>
																@keyframes donasai-sp-slide-in {
																	from { opacity: 0; transform: translateY(20px); }
																	to { opacity: 1; transform: translateY(0); }
																}
															</style>
															<button onclick="this.parentElement.remove()" style="
																position: absolute;
																top: 8px;
																right: 10px;
																background: none;
																border: none;
																font-size: 16px;
																color: #999;
																cursor: pointer;
															">&times;</button>
															<div style="display: flex; align-items: flex-start; gap: 12px;">
																<div style="
																	width: 44px;
																	height: 44px;
																	background: linear-gradient(135deg, ${formData.brand_color || "#059669"}, ${formData.brand_color || "#059669"}88);
																	border-radius: 50%;
																	display: flex;
																	align-items: center;
																	justify-content: center;
																	color: #fff;
																	font-weight: 700;
																	font-size: 18px;
																">A</div>
																<div>
																	<div style="font-weight: 600; color: #1f2937; font-size: 14px;">Ahmad Hidayat</div>
																	<div style="font-size: 13px; color: #4b5563;">
																		baru saja berdonasi <strong style="color: ${formData.brand_color || "#059669"}">Rp 100.000</strong>
																	</div>
																	<div style="font-size: 11px; color: #9ca3af; margin-top: 4px;">5 menit yang lalu</div>
																</div>
															</div>
														</div>
													`;
													document.body.insertAdjacentHTML(
														"beforeend",
														previewHtml,
													);

													// Auto remove after duration
													setTimeout(
														() => {
															const preview = document.getElementById(
																"donasai-sp-admin-preview",
															);
															if (preview) preview.remove();
														},
														(formData.social_proof_duration || 5) * 1000,
													);
												}}
												className="w-full py-2 px-4 bg-linear-to-r from-purple-600 to-indigo-600 text-white rounded-lg font-medium text-sm hover:from-purple-700 hover:to-indigo-700 transition-all flex items-center justify-center gap-2"
											>
												<svg
													className="w-4 h-4"
													fill="none"
													stroke="currentColor"
													viewBox="0 0 24 24"
												>
													<path
														strokeLinecap="round"
														strokeLinejoin="round"
														strokeWidth={2}
														d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
													/>
													<path
														strokeLinecap="round"
														strokeLinejoin="round"
														strokeWidth={2}
														d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
													/>
												</svg>
												Lihat Preview
											</button>
										</div>
									</div>
								)}
							</div>
						) : (
							<p className="text-sm text-gray-500">
								Tampilkan popup "X baru saja berdonasi..." untuk meningkatkan
								social proof.
							</p>
						)}
					</div>
				</div>
			</div>
		</div>
	);
}
