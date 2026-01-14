import { Bell, CreditCard, Link as LinkIcon, RefreshCcw } from "lucide-react";
import { toast } from "sonner";
// Use shared component to avoid redundancy
import { OrganizationForm } from "/src/components/shared/OrganizationForm";
import { Input } from "/src/components/ui/Input";
import { Label } from "/src/components/ui/Label";
import { Select } from "/src/components/ui/Select";
import type { ReceiptTemplate } from "/src/pages/receipt-template/hooks/use-receipt-template";
import { useReceiptTemplate } from "/src/pages/receipt-template/hooks/use-receipt-template";
import { useSettings } from "../SettingsContext";

// Sync Button Sub-component to handle conditional hooks
function ProSyncButton({ formData }: { formData: any }) {
	const { template, updateTemplate, isUpdating } = useReceiptTemplate();

	const handleSyncToReceipt = () => {
		if (!template) return;

		updateTemplate({
			...template,
			organization: {
				...template.organization,
				name: formData.org_name,
				email: formData.org_email,
				phone: formData.org_phone,
				address_line_1: formData.org_address,
				// Keep existing values for detailed fields that don't map directly
				address_line_2: "",
				city: "",
				postal_code: "",
				// Update logo (Receipt uses object/string)
				// The backend handles string URLs if object structure is expected, usually via sanitization
				// But ReceiptTemplate expects object or string in frontend now?
				// Interface says: logo: { url... }
				// But we updated organization form to handle strings.
				// However backend expects specific structure for template.logo
			},
			// Also sync the main logo
			logo: formData.org_logo
				? {
						url: formData.org_logo,
						attachment_id: 0,
						width: 0,
						height: 0,
					}
				: template.logo,
		});

		toast.success("Data organisasi diterapkan ke Template Kuitansi");
	};

	return (
		<button
			type="button"
			onClick={handleSyncToReceipt}
			disabled={isUpdating}
			className="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg border border-emerald-200 transition-colors"
			title="Terapkan data ke Template Kuitansi (Pro)"
		>
			<RefreshCcw size={16} className={isUpdating ? "animate-spin" : ""} />
			Terapkan ke Template Kuitansi
		</button>
	);
}

export default function GeneralSection() {
	const { formData, setFormData, pages, isProInstalled } = useSettings();

	const handleOrgChange = (newOrgData: any) => {
		setFormData({
			...formData,
			org_name: newOrgData.name,
			org_email: newOrgData.email,
			org_phone: newOrgData.phone,
			org_address: newOrgData.address,
			org_logo:
				typeof newOrgData.logo === "string"
					? newOrgData.logo
					: newOrgData.logo?.url || "",
			// Note: Website is not in General Settings schema yet, but component handles it gracefully (ignored)
		});
	};

	// Map formData to OrganizationData format
	const orgData = {
		name: formData.org_name,
		email: formData.org_email,
		phone: formData.org_phone,
		address: formData.org_address,
		logo: formData.org_logo,
		website: "", // Not used in General
	} as unknown as ReceiptTemplate["organization"];

	return (
		<div className="space-y-8">
			<div>
				<div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
					<div>
						<h3 className="text-lg font-medium text-gray-900 mb-1 dark:text-gray-100">
							Detail Organisasi
						</h3>
						<p className="text-sm text-gray-500 dark:text-gray-400">
							Informasi ini akan muncul pada kuitansi donasi.
						</p>
					</div>
					{isProInstalled && <ProSyncButton formData={formData} />}
				</div>

				{/* Use Shared Component */}
				<OrganizationForm
					data={orgData}
					onChange={handleOrgChange}
					mode="simple"
					showLogo={true}
				/>
			</div>

			<div className="border-t border-gray-200 pt-6 dark:border-gray-700">
				<div className="flex items-center gap-3 mb-6">
					<div className="p-2 bg-blue-50 rounded-lg text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
						<LinkIcon size={20} />
					</div>
					<div>
						<h3 className="text-lg font-medium text-gray-900 dark:text-gray-100">
							Pengaturan Permalink
						</h3>
					</div>
				</div>

				<div className="grid grid-cols-1 gap-6 mb-6">
					<div className="bg-gray-50 p-5 rounded-xl border border-gray-200 dark:bg-gray-800/50 dark:border-gray-700">
						<div className="flex items-center gap-2 mb-4">
							<div className="p-1.5 bg-blue-100 text-blue-600 rounded dark:bg-blue-900/50 dark:text-blue-400">
								<LinkIcon size={16} />
							</div>
							<h4 className="text-sm font-semibold text-gray-900 dark:text-gray-100">
								Struktur URL Kampanye
							</h4>
						</div>

						<div className="flex flex-col sm:flex-row sm:items-center gap-3">
							<div className="flex-1 relative flex items-center max-w-full">
								<div className="flex items-center w-full rounded-lg border border-gray-300 bg-white shadow-sm overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500 dark:bg-gray-900 dark:border-gray-600">
									<span className="pl-3 pr-1 py-2.5 text-gray-500 text-sm bg-gray-50 border-r border-gray-200 select-none whitespace-nowrap dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700">
										{window.location.host}/
									</span>
									<Input
										type="text"
										className="flex-1 min-w-[100px] border-none! focus:ring-0! max-w-[200px]! shadow-none! rounded-none! text-sm text-gray-900 font-semibold px-3 py-2.5 placeholder-gray-400 dark:bg-transparent! dark:text-gray-100"
										value={formData.campaign_slug}
										onChange={(e) =>
											setFormData({
												...formData,
												campaign_slug: e.target.value,
											})
										}
										placeholder="campaign"
									/>
									<span className="pl-1 pr-3 py-2.5 text-gray-400 text-sm bg-white select-none whitespace-nowrap dark:bg-gray-900 dark:text-gray-500">
										/ nama-kampanye
									</span>
								</div>
							</div>
						</div>
						<p className="text-xs text-gray-500 mt-2">
							Slug ini akan menjadi awalan untuk semua halaman kampanye donasi
							Anda.
						</p>
					</div>

					<div className="bg-gray-50 p-5 rounded-xl border border-gray-200 dark:bg-gray-800/50 dark:border-gray-700">
						<div className="flex items-center gap-2 mb-4">
							<div className="p-1.5 bg-green-100 text-green-600 rounded dark:bg-green-900/40 dark:text-green-400">
								<CreditCard size={16} />
							</div>
							<h4 className="text-sm font-semibold text-gray-900 dark:text-gray-100">
								Struktur URL Pembayaran
							</h4>
						</div>

						<div className="flex flex-col sm:flex-row sm:items-center gap-3">
							<div className="flex-1 relative flex items-center max-w-full">
								<div className="flex items-center w-full rounded-lg border border-gray-300 bg-white shadow-sm overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500 dark:bg-gray-900 dark:border-gray-600">
									<span className="pl-3 pr-1 py-2.5 text-gray-500 text-sm bg-gray-50 border-r border-gray-200 select-none whitespace-nowrap max-w-[180px] overflow-hidden text-ellipsis dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700">
										.../{formData.campaign_slug}/nama-campaign
									</span>
									<Input
										type="text"
										className="flex-1! min-w-[100px]! border-none! focus:ring-0! shadow-none! rounded-none! text-sm text-gray-900 font-semibold px-3 py-2.5 placeholder-gray-400 dark:bg-transparent! dark:text-gray-100"
										value={formData.payment_slug}
										onChange={(e) =>
											setFormData({
												...formData,
												payment_slug: e.target.value,
											})
										}
										placeholder="pay"
									/>
								</div>
							</div>
						</div>
						<p className="text-xs text-gray-500 mt-2">
							Halaman tempat donatur mengisi nominal dan memilih metode
							pembayaran.
						</p>
					</div>
				</div>

				<div className="bg-amber-50 border border-amber-200 rounded-lg p-4 flex gap-3 text-amber-800 dark:bg-amber-900/20 dark:border-amber-800 dark:text-amber-400">
					<Bell size={18} className="shrink-0 mt-0.5" />
					<div className="text-sm">
						<p className="font-semibold mb-1">
							Penting: Simpan Permalink WordPress
						</p>
						<p>
							Setelah mengubah slug di atas, Anda <u>wajib</u> masuk ke menu{" "}
							<strong>Settings &gt; Permalinks</strong> di dashboard WordPress
							dan klik "Save Changes" agar URL baru dapat diakses.
						</p>
					</div>
				</div>
			</div>

			<div className="border-t border-gray-200 pt-6 dark:border-gray-700">
				<h3 className="text-lg font-medium text-gray-900 mb-4 dark:text-gray-100">
					Pengaturan Konfirmasi
				</h3>
				<div className="grid grid-cols-1 md:grid-cols-2 gap-4">
					<div>
						<Label htmlFor="confirmationPage">Halaman Konfirmasi</Label>
						<Select
							id="confirmationPage"
							value={formData.confirmation_page || ""}
							onChange={(e) =>
								setFormData({
									...formData,
									confirmation_page: e.target.value,
								})
							}
						>
							<option value="">-- Pilih Halaman --</option>
							{pages.map((page: any) => (
								<option key={page.id} value={page.id}>
									{page.title}
								</option>
							))}
						</Select>
						<p className="mt-1 text-xs text-gray-500">
							Halaman ini harus berisi shortcode{" "}
							<code>[wpd_confirmation_form]</code>.
						</p>
					</div>
				</div>
			</div>

			{/* Tracking Pixels */}
			<div className="border-t border-gray-200 pt-6 dark:border-gray-700">
				<div className="flex items-center gap-3 mb-4">
					<div className="p-2 bg-indigo-50 rounded-lg text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400">
						<svg
							width="20"
							height="20"
							viewBox="0 0 24 24"
							fill="none"
							stroke="currentColor"
							strokeWidth="2"
							strokeLinecap="round"
							strokeLinejoin="round"
						>
							<path d="M2 12h20" />
							<path d="M2 12l5-5" />
							<path d="M2 12l5 5" />
							<circle cx="12" cy="12" r="3" />
						</svg>
					</div>
					<div>
						<h3 className="text-lg font-medium text-gray-900 dark:text-gray-100">
							Tracking Pixels Global
						</h3>
						<p className="text-sm text-gray-500 dark:text-gray-400">
							Pengaturan ini akan diterapkan ke semua kampanye secara otomatis jika tidak ada pengaturan spesifik di kampanye tersebut.
						</p>
					</div>
				</div>

				<div className="grid grid-cols-1 md:grid-cols-3 gap-6">
					<div>
						<Label htmlFor="pixelFb">Facebook Pixel ID</Label>
						<Input
							id="pixelFb"
							value={formData.pixel_fb || ""}
							onChange={(e) =>
								setFormData({ ...formData, pixel_fb: e.target.value })
							}
							placeholder="1234567890"
							className="mt-1"
						/>
					</div>
					<div>
						<Label htmlFor="pixelTiktok">TikTok Pixel ID</Label>
						<Input
							id="pixelTiktok"
							value={formData.pixel_tiktok || ""}
							onChange={(e) =>
								setFormData({ ...formData, pixel_tiktok: e.target.value })
							}
							placeholder="CXXXXXXXXXXXX"
							className="mt-1"
						/>
					</div>
					<div>
						<Label htmlFor="pixelGa4">GA4 Measurement ID</Label>
						<Input
							id="pixelGa4"
							value={formData.pixel_ga4 || ""}
							onChange={(e) =>
								setFormData({ ...formData, pixel_ga4: e.target.value })
							}
							placeholder="G-XXXXXXXXXX"
							className="mt-1"
						/>
					</div>
				</div>
				<div className="mt-4 p-3 bg-blue-50 border border-blue-100 rounded-lg text-sm text-blue-700 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-300">
					<p>
						<strong>Catatan:</strong> Pixel yang diisi di sini akan berfungsi sebagai fallback. Jika Anda mengisi Pixel ID di pengaturan spesifik kampanye, maka Pixel ID di kampanye tersebut yang akan digunakan (menggantikan yang global).
					</p>
				</div>
			</div>

			<div className="border-t border-gray-200 pt-6 dark:border-gray-700">
				<h3 className="text-lg font-medium text-gray-900 mb-2 flex items-center gap-2 dark:text-gray-100">
					Branding
				</h3>
				<div className="flex items-center space-x-3">
					<input
						id="removeBranding"
						type="checkbox"
						checked={formData.remove_branding}
						onChange={(e) =>
							setFormData({
								...formData,
								remove_branding: e.target.checked,
							})
						}
						className="h-4 w-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
					/>
					<label
						htmlFor="removeBranding"
						className="text-sm font-medium text-gray-700 dark:text-gray-300"
					>
						Hapus Branding "Powered by Donasai"
					</label>
				</div>
				<p className="text-xs text-gray-500 mt-1 ml-7">
					Opsi untuk menyembunyikan "Powered by Donasai" di footer form dan
					kuitansi.
				</p>
			</div>
		</div>
	);
}
