import { Save } from "lucide-react";
// Import shared components
import { LogoUploader } from "@/components/shared/LogoUploader";
import { OrganizationForm } from "@/components/shared/OrganizationForm";
import { Checkbox } from "@/components/ui/Checkbox";
import { Input } from "@/components/ui/Input";
import { Label } from "@/components/ui/Label";
import { Radio } from "@/components/ui/Radio";
import { FooterEditor } from "./footer-editor";
import type { ReceiptTemplate } from "./hooks/use-receipt-template";

interface CustomizationFormProps {
	template: ReceiptTemplate | undefined;
	onChange: (template: ReceiptTemplate) => void;
	onSave: () => void;
	isSaving: boolean;
}

export function CustomizationForm({
	template,
	onChange,
	onSave,
	isSaving,
}: CustomizationFormProps) {
	if (!template) {
		return (
			<div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-8">
				<div className="animate-pulse space-y-4">
					<div className="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4"></div>
					<div className="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
				</div>
			</div>
		);
	}

	const handleLogoChange = (logo: ReceiptTemplate["logo"]) => {
		onChange({ ...template, logo });
	};

	const handleOrganizationChange = (
		organization: ReceiptTemplate["organization"],
	) => {
		onChange({ ...template, organization });
	};

	const handleFooterChange = (footer: ReceiptTemplate["footer"]) => {
		onChange({ ...template, footer });
	};

	const handleAdvancedChange = (
		field: keyof ReceiptTemplate["advanced"],
		value: string | boolean,
	) => {
		onChange({
			...template,
			advanced: {
				...template.advanced,
				[field]: value,
			},
		});
	};

	return (
		<div className="space-y-6">
			{/* Logo Section */}
			<div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
				<LogoUploader value={template.logo} onChange={handleLogoChange} />
			</div>

			{/* Organization Info Section */}
			<div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
				<div>
					<h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-1">
						🏢 Informasi Organisasi
					</h3>
					<p className="text-sm text-gray-500 dark:text-gray-400 mb-6">
						Informasi ini akan muncul di semua kuitansi donasi
					</p>
				</div>
				<OrganizationForm
					data={template.organization}
					onChange={(organization)=>handleOrganizationChange(organization)}
					mode="detailed"
					showLogo={false} // Logo is separate above
				/>
			</div>

			{/* Footer Section */}
			<div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
				<FooterEditor footer={template.footer} onChange={handleFooterChange} />
			</div>

			{/* Advanced Options */}
			<div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
				<div className="space-y-6">
					<div>
						<h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-1">
							⚙️ Pengaturan Lanjutan
						</h3>
						<p className="text-sm text-gray-500 dark:text-gray-400">
							Konfigurasi format kuitansi dan pengiriman
						</p>
					</div>

					<div className="space-y-4">
						{/* Receipt Format */}
						<div>
							<Label>Format Kuitansi</Label>
							<div className="space-y-3 mt-3">
								<label htmlFor="format-html" className="flex items-start gap-3 p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer hover:border-emerald-300 dark:hover:border-emerald-600 transition-colors has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50 dark:has-[:checked]:bg-emerald-950">
									<div className="mt-0.5">
										<Radio
											name="format"
											value="html"
											checked={template.advanced.format === "html"}
											onChange={(e) =>
												handleAdvancedChange("format", e.target.value)
											}
										/>
									</div>
									<div className="flex-1">
										<span className="text-sm font-medium text-gray-700 dark:text-gray-300">
											Link HTML (Rekomendasi)
										</span>
										<p className="text-xs text-gray-500 dark:text-gray-400 mt-1">
											Kirim email dengan link untuk melihat/print kuitansi
											(lebih cepat, ukuran email kecil)
										</p>
									</div>
								</label>

								<label htmlFor="format-pdf" className="flex items-start gap-3 p-4 border-2 border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer hover:border-emerald-300 dark:hover:border-emerald-600 transition-colors has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50 dark:has-[:checked]:bg-emerald-950">
									<div className="mt-0.5">
										<Radio
											name="format"
											value="pdf"
											checked={template.advanced.format === "pdf"}
											onChange={(e) =>
												handleAdvancedChange("format", e.target.value)
											}
										/>
									</div>
									<div className="flex-1">
										<span className="text-sm font-medium text-gray-700 dark:text-gray-300">
											File PDF Attachment
										</span>
										<p className="text-xs text-gray-500 dark:text-gray-400 mt-1">
											Generate dan lampirkan file PDF (membutuhkan library PDF,
											mungkin lebih lambat)
										</p>
									</div>
								</label>
							</div>
						</div>

						{/* Checkboxes */}
						<div className="space-y-3 pt-4 border-t border-gray-200 dark:border-gray-700">
							<div className="flex items-start gap-3 group">
								<Checkbox
									id="auto_send_email"
									checked={template.advanced.auto_send_email}
									onChange={(e) =>
										handleAdvancedChange("auto_send_email", e.target.checked)
									}
									className="mt-0.5"
								/>
								<label
									htmlFor="auto_send_email"
									className="cursor-pointer flex-1"
								>
									<span className="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">
										Kirim kuitansi otomatis via email
									</span>
									<p className="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
										Otomatis kirim kuitansi ke donor setelah pembayaran selesai
									</p>
								</label>
							</div>

							<div className="flex items-start gap-3 group">
								<Checkbox
									id="include_serial_number"
									checked={template.advanced.include_serial_number}
									onChange={(e) =>
										handleAdvancedChange(
											"include_serial_number",
											e.target.checked,
										)
									}
									className="mt-0.5"
								/>
								<label
									htmlFor="include_serial_number"
									className="cursor-pointer flex-1"
								>
									<span className="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">
										Sertakan nomor seri
									</span>
									<p className="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
										Tambahkan nomor unik kuitansi untuk tracking dan verifikasi
									</p>
								</label>
							</div>
						</div>

						{/* Header Color */}
						<div className="pt-4 border-t border-gray-200 dark:border-gray-700">
							<Label htmlFor="header-color">Warna Header</Label>
							<div className="flex items-center gap-3 mt-2">
								<input
									type="color"
									id="header-color"
									value={template.advanced.header_color}
									onChange={(e) =>
										handleAdvancedChange("header_color", e.target.value)
									}
									className="w-12 h-12 rounded-lg border-2 border-gray-300 dark:border-gray-600 cursor-pointer"
								/>
								<Input
									type="text"
									value={template.advanced.header_color}
									onChange={(e) =>
										handleAdvancedChange("header_color", e.target.value)
									}
									placeholder="#059669"
									className="flex-1"
								/>
							</div>
							<p className="text-xs text-gray-500 dark:text-gray-400 mt-2">
								Warna ini akan digunakan untuk header dan aksen di kuitansi
							</p>
						</div>
					</div>
				</div>
			</div>

			{/* Save Button - Sticky Footer */}
			<div className="sticky bottom-0 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-lg z-10">
				<div className="flex items-center justify-between">
					<p className="text-sm text-gray-600 dark:text-gray-400">
						Pastikan untuk menyimpan perubahan Anda
					</p>
					<button
					type="button"
						onClick={onSave}
						disabled={isSaving}
						className="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 shadow-sm hover:shadow-md"
					>
						{isSaving ? (
							<>
								<div className="animate-spin rounded-full h-4 w-4 border-2 border-white/30 border-t-white"></div>
								Menyimpan...
							</>
						) : (
							<>
								<Save size={18} />
								Simpan Perubahan
							</>
						)}
					</button>
				</div>
			</div>
		</div>
	);
}
