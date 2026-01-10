import { Input } from "@/components/ui/Input";
import { Label } from "@/components/ui/Label";
import { Select } from "@/components/ui/Select";
import { Textarea } from "@/components/ui/Textarea";
import {
    Bell,
    CreditCard,
    Image,
    Link as LinkIcon,
    Trash2,
    Upload,
} from "lucide-react";
import { useSettings } from "../SettingsContext";

export default function GeneralSection() {
	const { formData, setFormData, pages } = useSettings();

	return (
		<div className="space-y-8">
			<div>
				<h3 className="text-lg font-medium text-gray-900 mb-1">
					Detail Organisasi
				</h3>
				<p className="text-sm text-gray-500 mb-4">
					Informasi ini akan muncul pada kuitansi donasi.
				</p>

				<div className="grid gap-4">
					<div>
						<Label htmlFor="orgName">Nama Organisasi</Label>
						<Input
							type="text"
							id="orgName"
							value={formData.org_name}
							onChange={(e) =>
								setFormData({ ...formData, org_name: e.target.value })
							}
							placeholder="Contoh: Yayasan Amal Bhakti"
						/>
					</div>
					<div>
						<Label htmlFor="orgAddress">Alamat</Label>
						<Textarea
							id="orgAddress"
							rows={3}
							value={formData.org_address}
							onChange={(e) =>
								setFormData({ ...formData, org_address: e.target.value })
							}
							placeholder="Alamat lengkap..."
						/>
					</div>
					<div className="grid grid-cols-2 gap-4">
						<div>
							<Label htmlFor="orgEmail">Email</Label>
							<Input
								type="email"
								value={formData.org_email}
								onChange={(e) =>
									setFormData({
										...formData,
										org_email: e.target.value,
									})
								}
							/>
						</div>
						<div>
							<Label htmlFor="orgPhone">Telepon / WhatsApp</Label>
							<Input
								type="text"
								value={formData.org_phone}
								onChange={(e) =>
									setFormData({
										...formData,
										org_phone: e.target.value,
									})
								}
							/>
						</div>
					</div>
					<div>
						<Label htmlFor="orgLogo">Logo URL</Label>
						<div className="flex items-start gap-4">
							<div className="relative group shrink-0">
								{formData.org_logo ? (
									<div className="w-24 h-24 rounded-lg border border-gray-200 overflow-hidden bg-gray-50 relative">
										<img
											src={formData.org_logo}
											alt="Logo"
											className="w-full h-full object-contain"
										/>
										<button
											type="button"
											onClick={() => setFormData({ ...formData, org_logo: "" })}
											className="absolute top-1 right-1 p-1 bg-red-500 text-white rounded-full opacity-0 group-hover:opacity-100 transition-opacity"
											title="Hapus Logo"
										>
											<Trash2 size={12} />
										</button>
									</div>
								) : (
									<div className="w-24 h-24 rounded-lg border border-dashed border-gray-300 bg-gray-50 flex flex-col items-center justify-center text-gray-400">
										<Image size={24} className="mb-1" />
										<span className="text-[10px]">No Logo</span>
									</div>
								)}
							</div>

							<div className="flex-1">
								<div className="flex gap-2 mb-2">
									<Input
										type="text"
										id="orgLogo"
										className="flex-1 bg-gray-50"
										value={formData.org_logo}
										readOnly
										placeholder="URL Logo..."
									/>
									<button
										type="button"
										onClick={() => {
											// @ts-expect-error - WP Media
											const frame = wp.media({
												title: "Pilih Logo Organisasi",
												button: {
													text: "Gunakan Logo Ini",
												},
												multiple: false,
											});

											frame.on("select", () => {
												const attachment = frame
													.state()
													.get("selection")
													.first()
													.toJSON();
												setFormData({
													...formData,
													org_logo: attachment.url,
												});
											});

											frame.open();
										}}
										className="px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 flex items-center gap-2"
									>
										<Upload size={16} />
										Upload
									</button>
								</div>
								<p className="text-xs text-gray-500">
									Format: PNG, JPG, atau WebP. Disarankan ukuran persegi (1:1)
									minimal 512x512px.
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div className="border-t border-gray-200 pt-6">
				<div className="flex items-center gap-3 mb-6">
					<div className="p-2 bg-blue-50 rounded-lg text-blue-600">
						<LinkIcon size={20} />
					</div>
					<div>
						<h3 className="text-lg font-medium text-gray-900">
							Pengaturan Permalink
						</h3>
					</div>
				</div>

				<div className="grid grid-cols-1 gap-6 mb-6">
					<div className="bg-gray-50 p-5 rounded-xl border border-gray-200">
						<div className="flex items-center gap-2 mb-4">
							<div className="p-1.5 bg-blue-100 text-blue-600 rounded">
								<LinkIcon size={16} />
							</div>
							<h4 className="text-sm font-semibold text-gray-900">
								Struktur URL Kampanye
							</h4>
						</div>

						<div className="flex flex-col sm:flex-row sm:items-center gap-3">
							<div className="flex-1 relative flex items-center max-w-full">
								<div className="flex items-center w-full rounded-lg border border-gray-300 bg-white shadow-sm overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500">
									<span className="pl-3 pr-1 py-2.5 text-gray-500 text-sm bg-gray-50 border-r border-gray-200 select-none whitespace-nowrap">
										{window.location.host}/
									</span>
									<Input
										type="text"
										className="flex-1 min-w-[100px] border-none! focus:ring-0! max-w-[200px]! shadow-none! rounded-none! text-sm text-gray-900 font-semibold px-3 py-2.5 placeholder-gray-400"
										value={formData.campaign_slug}
										onChange={(e) =>
											setFormData({
												...formData,
												campaign_slug: e.target.value,
											})
										}
										placeholder="campaign"
									/>
									<span className="pl-1 pr-3 py-2.5 text-gray-400 text-sm bg-white select-none whitespace-nowrap">
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

					<div className="bg-gray-50 p-5 rounded-xl border border-gray-200">
						<div className="flex items-center gap-2 mb-4">
							<div className="p-1.5 bg-green-100 text-green-600 rounded">
								<CreditCard size={16} />
							</div>
							<h4 className="text-sm font-semibold text-gray-900">
								Struktur URL Pembayaran
							</h4>
						</div>

						<div className="flex flex-col sm:flex-row sm:items-center gap-3">
							<div className="flex-1 relative flex items-center max-w-full">
								<div className="flex items-center w-full rounded-lg border border-gray-300 bg-white shadow-sm overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500">
									<span className="pl-3 pr-1 py-2.5 text-gray-500 text-sm bg-gray-50 border-r border-gray-200 select-none whitespace-nowrap max-w-[180px] overflow-hidden text-ellipsis">
										.../{formData.campaign_slug}/nama-campaign
									</span>
									<Input
										type="text"
										className="flex-1! min-w-[100px]! border-none! focus:ring-0! shadow-none! rounded-none! text-sm text-gray-900 font-semibold px-3 py-2.5 placeholder-gray-400"
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

				<div className="bg-amber-50 border border-amber-200 rounded-lg p-4 flex gap-3 text-amber-800">
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

			<div className="border-t border-gray-200 pt-6">
				<h3 className="text-lg font-medium text-gray-900 mb-4">
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

			<div className="border-t border-gray-200 pt-6">
				<h3 className="text-lg font-medium text-gray-900 mb-2 flex items-center gap-2">
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
						className="text-sm font-medium text-gray-700"
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
