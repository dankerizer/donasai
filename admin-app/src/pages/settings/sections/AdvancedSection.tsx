import { toast } from "sonner";
import { useSettings } from "../SettingsContext";

export default function AdvancedSection() {
	const { formData, setFormData } = useSettings();

	return (
		<div className="space-y-6">
			<div>
				<h3 className="text-lg font-medium text-gray-900 mb-4 dark:text-gray-100">
					Ekspor & Impor Pengaturan
				</h3>

				{/* Export Settings */}
				<div className="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-4 dark:bg-gray-800/50 dark:border-gray-700">
					<h4 className="font-medium text-gray-800 mb-2 dark:text-gray-200">
						Ekspor Pengaturan
					</h4>
					<p className="text-sm text-gray-600 mb-3 dark:text-gray-400">
						Unduh semua pengaturan plugin Anda sebagai file JSON. Gunakan untuk
						backup atau memindahkan ke situs lain.
					</p>
					<button
						type="button"
						onClick={() => {
							const settings = {
								general: {
									campaign_slug: formData.campaign_slug,
									payment_slug: formData.payment_slug,
									remove_branding: formData.remove_branding,
									confirmation_page: formData.confirmation_page,
									// Webhooks
									webhook_url: formData.webhook_url,
									webhook_secret: formData.webhook_secret,
									webhook_enabled: formData.webhook_enabled,
								},
								donation: {
									min_amount: formData.min_amount,
									presets: formData.presets,
									anonymous_label: formData.anonymous_label,
									create_user: formData.create_user,
								},
								appearance: {
									brand_color: formData.brand_color,
									button_color: formData.button_color,
									container_width: formData.container_width,
									border_radius: formData.border_radius,
									campaign_layout: formData.campaign_layout,
									font_family: formData.font_family,
									font_size: formData.font_size,
									dark_mode: formData.dark_mode,
									donation_layout: formData.donation_layout,
								},
								bank: {
									bank_name: formData.bank_name,
									account_number: formData.account_number,
									account_name: formData.account_name,
								},
								organization: {
									org_name: formData.org_name,
									org_address: formData.org_address,
									org_phone: formData.org_phone,
									org_email: formData.org_email,
								},
								notifications: {
									opt_in_email: formData.opt_in_email,
									opt_in_whatsapp: formData.opt_in_whatsapp,
								},
								exported_at: new Date().toISOString(),
								version: "1.0",
							};
							const blob = new Blob([JSON.stringify(settings, null, 2)], {
								type: "application/json",
							});
							const url = URL.createObjectURL(blob);
							const a = document.createElement("a");
							a.href = url;
							a.download = `donasai-settings-${
								new Date().toISOString().split("T")[0]
							}.json`;
							a.click();
							URL.revokeObjectURL(url);
							toast.success("Ekspor berhasil!", {
								description: "File pengaturan telah diunduh.",
							});
						}}
						className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm"
					>
						📥 Ekspor Pengaturan
					</button>
				</div>

				{/* Import Settings */}
				<div className="bg-gray-50 p-4 rounded-lg border border-gray-200 dark:bg-gray-800/50 dark:border-gray-700">
					<h4 className="font-medium text-gray-800 mb-2 dark:text-gray-200">
						Impor Pengaturan
					</h4>
					<p className="text-sm text-gray-600 mb-3 dark:text-gray-400">
						Muat pengaturan dari file JSON yang telah diekspor sebelumnya.
					</p>
					<input
						type="file"
						accept=".json"
						onChange={(e) => {
							const file = e.target.files?.[0];
							if (!file) return;
							const reader = new FileReader();
							reader.onload = (event) => {
								try {
									const imported = JSON.parse(event.target?.result as string);
									setFormData((prev) => ({
										...prev,
										campaign_slug:
											imported.general?.campaign_slug || prev.campaign_slug,
										payment_slug:
											imported.general?.payment_slug || prev.payment_slug,
										remove_branding:
											imported.general?.remove_branding ?? prev.remove_branding,
										confirmation_page:
											imported.general?.confirmation_page ||
											prev.confirmation_page,
										webhook_url:
											imported.general?.webhook_url || prev.webhook_url,
										webhook_secret:
											imported.general?.webhook_secret || prev.webhook_secret,
										webhook_enabled:
											imported.general?.webhook_enabled ?? prev.webhook_enabled,
										min_amount:
											imported.donation?.min_amount || prev.min_amount,
										presets: imported.donation?.presets || prev.presets,
										anonymous_label:
											imported.donation?.anonymous_label ||
											prev.anonymous_label,
										create_user:
											imported.donation?.create_user ?? prev.create_user,
										brand_color:
											imported.appearance?.brand_color || prev.brand_color,
										button_color:
											imported.appearance?.button_color || prev.button_color,
										container_width:
											imported.appearance?.container_width ||
											prev.container_width,
										border_radius:
											imported.appearance?.border_radius || prev.border_radius,
										campaign_layout:
											imported.appearance?.campaign_layout ||
											prev.campaign_layout,
										font_family:
											imported.appearance?.font_family || prev.font_family,
										font_size: imported.appearance?.font_size || prev.font_size,
										dark_mode: imported.appearance?.dark_mode ?? prev.dark_mode,
										donation_layout:
											imported.appearance?.donation_layout ||
											prev.donation_layout,
										bank_name: imported.bank?.bank_name || prev.bank_name,
										account_number:
											imported.bank?.account_number || prev.account_number,
										account_name:
											imported.bank?.account_name || prev.account_name,
										org_name: imported.organization?.org_name || prev.org_name,
										org_address:
											imported.organization?.org_address || prev.org_address,
										org_phone:
											imported.organization?.org_phone || prev.org_phone,
										org_email:
											imported.organization?.org_email || prev.org_email,
										opt_in_email:
											imported.notifications?.opt_in_email || prev.opt_in_email,
										opt_in_whatsapp:
											imported.notifications?.opt_in_whatsapp ||
											prev.opt_in_whatsapp,
									}));
									toast.success("Pengaturan berhasil diimpor!", {
										description:
											'Klik "Simpan Perubahan" untuk menyimpan ke database.',
									});
								} catch {
									toast.error("Gagal membaca file", {
										description: "Pastikan file JSON valid dan sesuai format.",
									});
								}
							};
							reader.readAsText(file);
						}}
						className="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
					/>
				</div>
			</div>

			{/* Danger Zone */}
			<div className="border-t border-gray-200 pt-6 dark:border-gray-700">
				<h3 className="text-lg font-bold text-red-700 mb-2 dark:text-red-400">
					Zona Bahaya: Pengaturan Uninstall
				</h3>
				<div className="space-y-3 bg-red-50 p-4 rounded-lg border border-red-100 dark:bg-red-900/10 dark:border-red-900/20">
					<div className="flex items-start space-x-3">
						<input
							type="checkbox"
							id="delete_on_uninstall_settings"
							checked={formData.delete_on_uninstall_settings}
							onChange={(e) =>
								setFormData({
									...formData,
									delete_on_uninstall_settings: e.target.checked,
								})
							}
							className="h-4 w-4 text-red-600 border-red-300 rounded focus:ring-red-500 mt-0.5"
						/>
						<div className="mt-[-2px]">
							<label
								htmlFor="delete_on_uninstall_settings"
								className="text-sm font-medium text-gray-800 dark:text-gray-200"
							>
								Hapus Semua Pengaturan
							</label>
							<p className="text-xs text-gray-600 dark:text-gray-400">
								Jika dicentang, semua opsi pengaturan plugin akan dihapus dari
								database ketika plugin di-uninstall.
							</p>
						</div>
					</div>
					<div className="flex items-start space-x-3">
						<input
							type="checkbox"
							id="delete_on_uninstall_tables"
							checked={formData.delete_on_uninstall_tables}
							onChange={(e) =>
								setFormData({
									...formData,
									delete_on_uninstall_tables: e.target.checked,
								})
							}
							className="h-4 w-4 text-red-600 border-red-300 rounded focus:ring-red-500 mt-0.5"
						/>
						<div className="mt-[-2px]">
							<label
								htmlFor="delete_on_uninstall_tables"
								className="text-sm font-medium text-gray-800 dark:text-gray-200"
							>
								Hapus Tabel Database
							</label>
							<p className="text-xs text-gray-600 dark:text-gray-400">
								Jika dicentang, tabel donasi dan kampanye akan{" "}
								<b>DIHAPUS PERMANEN</b> ketika plugin di-uninstall.
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	);
}
