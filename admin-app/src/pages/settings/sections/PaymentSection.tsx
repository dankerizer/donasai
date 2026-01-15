/** biome-ignore-all lint/a11y/useKeyWithClickEvents: <explanation> */

import { Crown, Plus, Trash } from "lucide-react";
import { Input } from "/src/components/ui/Input";
import { Label } from "/src/components/ui/Label";
import { useSettings } from "../SettingsContext";
import FeeCoverageSection from "./FeeCoverageSection";

export default function PaymentSection() {
	const {
		formData,
		setFormData,
		licenseStatus,
		addAccount,
		removeAccount,
		updateAccount,
		setShowProModal,
	} = useSettings();

	return (
		<div className="space-y-8">
			<div>
				<h3 className="text-lg font-medium text-gray-900 mb-4 dark:text-gray-100">
					Transfer Bank (Manual)
				</h3>

				{licenseStatus === "active" ? (
					<div className="space-y-4">
						<div className="bg-purple-50 p-4 rounded-lg border border-purple-200 dark:bg-purple-900/20 dark:border-purple-800">
							<div className="flex justify-between items-center mb-3">
								<h4 className="text-sm font-bold text-purple-800 flex! items-center gap-2 dark:text-purple-300">
									<Crown size={14} /> Multi-Akun Bank (Pro)
								</h4>
								<button
									type="button"
									onClick={addAccount}
									className="text-xs flex items-center gap-1 bg-purple-600 text-white px-2 py-1 rounded hover:bg-purple-700"
								>
									<Plus size={12} /> Tambah Rekening
								</button>
							</div>

							{formData.pro_accounts.length === 0 && (
								<p className="text-sm text-gray-500 italic dark:text-gray-400">
									Belum ada rekening yang ditambahkan.
								</p>
							)}

							<div className="space-y-3">
								{formData.pro_accounts.map((acc, idx) => (
									<div
										key={acc.id || idx}
										className="bg-white p-3 rounded border border-gray-200 shadow-sm relative group dark:bg-gray-800 dark:border-gray-700"
									>
										<div className="grid md:grid-cols-3 gap-2 mb-2">
											<Input
												type="text"
												placeholder="Bank (e.g BCA)"
												value={acc.bank_name}
												className="text-xs py-1"
												onChange={(e) =>
													updateAccount(idx, "bank_name", e.target.value)
												}
											/>
											<Input
												type="text"
												placeholder="No. Rekening"
												value={acc.account_number}
												className="text-xs py-1"
												onChange={(e) =>
													updateAccount(idx, "account_number", e.target.value)
												}
											/>
											<Input
												type="text"
												placeholder="Atas Nama"
												value={acc.account_name}
												className="text-xs py-1"
												onChange={(e) =>
													updateAccount(idx, "account_name", e.target.value)
												}
											/>
										</div>
										<div className="flex justify-between items-center">
											<label className="flex items-center gap-2 text-xs text-gray-600 cursor-pointer dark:text-gray-400">
												<input
													type="checkbox"
													checked={acc.is_default}
													onChange={(e) =>
														updateAccount(idx, "is_default", e.target.checked)
													}
													className="rounded text-purple-600 focus:ring-purple-500"
												/>
												Default
											</label>
											<button
												type="button"
												onClick={() => removeAccount(idx)}
												className="text-red-500 hover:text-red-700 p-1"
											>
												<Trash size={14} />
											</button>
										</div>
									</div>
								))}
							</div>
						</div>

						<div className="opacity-50 pointer-events-none">
							<p className="text-xs text-gray-500 mb-2 dark:text-gray-400">
								Pengaturan Fallback (Gratis):
							</p>
							<div className="grid grid-cols-2 gap-4">
								<div>
									<Label htmlFor="bankName" className="text-xs mb-1">
										Nama Bank
									</Label>
									<Input
										type="text"
										id="bankName"
										className="bg-gray-100 dark:bg-gray-800"
										value={formData.bank_name}
										readOnly
									/>
								</div>
								<div>
									<Label htmlFor="accountNumber" className="text-xs mb-1">
										Nomor Rekening
									</Label>
									<Input
										type="text"
										id="accountNumber"
										className="bg-gray-100 dark:bg-gray-800"
										value={formData.account_number}
										readOnly
									/>
								</div>
							</div>
						</div>
					</div>
				) : (
					<div className="grid gap-4">
						<div className="grid grid-cols-2 gap-4">
							<div>
								<Label htmlFor="bankName">Nama Bank</Label>
								<Input
									type="text"
									id="bankName"
									value={formData.bank_name}
									onChange={(e) =>
										setFormData({
											...formData,
											bank_name: e.target.value,
										})
									}
									placeholder="Contoh: BCA"
								/>
							</div>
							<div>
								<Label htmlFor="accountNumber">Nomor Rekening</Label>
								<Input
									type="text"
									id="accountNumber"
									value={formData.account_number}
									onChange={(e) =>
										setFormData({
											...formData,
											account_number: e.target.value,
										})
									}
								/>
							</div>
						</div>
						<div>
							<Label htmlFor="accountName">Nama Pemilik Rekening</Label>
							<Input
								type="text"
								id="accountName"
								value={formData.account_name}
								onChange={(e) =>
									setFormData({
										...formData,
										account_name: e.target.value,
									})
								}
							/>
						</div>

						{/* Pro Teaser */}
						<button
							type="button"
							id="proTeaser"
							className="w-full bg-gray-50 border border-gray-200 rounded-lg p-3 flex justify-between items-center cursor-pointer hover:bg-gray-100 dark:bg-gray-800/50 dark:border-gray-700 dark:hover:bg-gray-800"
							onClick={() => setShowProModal(true)}
						>
							<div className="flex items-center gap-2">
								<Crown size={14} className="text-purple-600" />
								<span className="text-sm font-medium text-gray-600 dark:text-gray-300">
									Butuh lebih dari satu rekening bank?
								</span>
							</div>
							<span className="text-xs text-purple-600 font-bold">
								Upgrade Pro
							</span>
						</button>
					</div>
				)}
			</div>

			{licenseStatus === "active" ? (
				<>
					<div className="border-t border-gray-200 pt-6 dark:border-gray-700">
						<h3 className="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2 dark:text-gray-100">
							Gerbang Pembayaran Midtrans
						</h3>
						<div className="space-y-4">
							<div className="flex items-center space-x-3">
								<input
									type="checkbox"
									id="midtrans_enabled"
									checked={formData.midtrans_enabled}
									onChange={(e) =>
										setFormData((prev) => ({
											...prev,
											midtrans_enabled: e.target.checked,
										}))
									}
									className="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
								/>
								<label
									htmlFor="midtrans_enabled"
									className="text-sm font-medium text-gray-700 dark:text-gray-300"
								>
									Aktifkan Midtrans
								</label>
							</div>

							{formData.midtrans_enabled && (
								<div className="p-4 bg-gray-50 rounded-lg border border-gray-200 space-y-4 dark:bg-gray-800/50 dark:border-gray-700">
									<div className="flex items-center space-x-3">
										<input
											type="checkbox"
											id="midtrans_production"
											checked={formData.midtrans_production}
											onChange={(e) =>
												setFormData((prev) => ({
													...prev,
													midtrans_production: e.target.checked,
												}))
											}
											className="h-4 w-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
										/>
										<label
											htmlFor="midtrans_production"
											className="text-sm font-medium text-purple-900"
										>
											Mode Produksi
										</label>
									</div>

									<div className="grid md:grid-cols-2 gap-4">
										<div>
											<Label
												htmlFor="midtrans_server_key"
												className="text-xs text-gray-700 mb-1 dark:text-gray-300"
											>
												Server Key
											</Label>
											<Input
												id="midtrans_server_key"
												type="password"
												className="font-mono text-xs bg-white border-gray-300 dark:bg-gray-900 dark:border-gray-700"
												value={formData.midtrans_server_key}
												onChange={(e) =>
													setFormData({
														...formData,
														midtrans_server_key: e.target.value,
													})
												}
												placeholder="Midtrans Server Key..."
											/>
										</div>
										<div>
											<Label
												htmlFor="pro_midtrans_client_key"
												className="text-xs text-gray-700 mb-1 dark:text-gray-300"
											>
												Client Key
											</Label>
											<Input
												id="pro_midtrans_client_key"
												type="text"
												className="font-mono text-xs bg-white border-gray-300 dark:bg-gray-900 dark:border-gray-700"
												value={formData.pro_midtrans_client_key}
												onChange={(e) =>
													setFormData({
														...formData,
														pro_midtrans_client_key: e.target.value,
													})
												}
												placeholder="Midtrans Client Key..."
											/>
										</div>
									</div>
								</div>
							)}
						</div>
					</div>

					<div className="border-t border-gray-200 pt-6 dark:border-gray-700">
						<h3 className="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2 dark:text-gray-100">
							Xendit Payment Gateway
						</h3>
						<div className="space-y-4">
							<div className="p-4 bg-gray-50 rounded-lg border border-gray-200 space-y-4 dark:bg-gray-800/50 dark:border-gray-700">
								<p className="text-sm text-gray-600 mb-2 dark:text-gray-400">
									Terima pembayaran via VA, E-Wallet (OVO, DANA, LinkAja), dan
									QRIS menggunakan Xendit.
								</p>
								<div>
									<Label
										htmlFor="pro_xendit_api_key"
										className="text-xs text-gray-700 mb-1 dark:text-gray-300"
									>
										Xendit Secret API Key
									</Label>
									<Input
										id="pro_xendit_api_key"
										type="password"
										className="font-mono text-xs bg-white border-gray-300 dark:bg-gray-900 dark:border-gray-700"
										value={formData.pro_xendit_api_key}
										onChange={(e) =>
											setFormData({
												...formData,
												pro_xendit_api_key: e.target.value,
											})
										}
										placeholder="xnd_..."
									/>
									<p className="text-[10px] text-gray-400 mt-1">
										Dapatkan ID di Dashboard Xendit &gt; Settings &gt; API Keys.
									</p>
								</div>
							</div>
						</div>
					</div>

					<div className="border-t border-gray-200 pt-6 dark:border-gray-700">
						<h3 className="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2 dark:text-gray-100">
							Tripay Payment Gateway
						</h3>
						<div className="space-y-4">
							<div className="p-4 bg-gray-50 rounded-lg border border-gray-200 space-y-4 dark:bg-gray-800/50 dark:border-gray-700">
								<div className="flex items-center space-x-3">
									<input
										type="checkbox"
										id="tripay_production"
										checked={formData.pro_tripay_is_production}
										onChange={(e) =>
											setFormData((prev) => ({
												...prev,
												pro_tripay_is_production: e.target.checked,
											}))
										}
										className="h-4 w-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:checked:bg-purple-600"
									/>
									<label
										htmlFor="tripay_production"
										className="text-sm font-medium text-purple-900 dark:text-purple-300"
									>
										Mode Produksi
									</label>
								</div>

								<div className="grid md:grid-cols-2 gap-4">
									<div>
										<Label
											htmlFor="pro_tripay_api_key"
											className="text-xs text-gray-700 mb-1 dark:text-gray-300"
										>
											API Key
										</Label>
										<Input
											id="pro_tripay_api_key"
											type="password"
											className="font-mono text-xs bg-white border-gray-300 dark:bg-gray-900 dark:border-gray-700"
											value={formData.pro_tripay_api_key}
											onChange={(e) =>
												setFormData({
													...formData,
													pro_tripay_api_key: e.target.value,
												})
											}
											placeholder="Tripay API Key..."
										/>
									</div>
									<div>
										<Label
											htmlFor="pro_tripay_private_key"
											className="text-xs text-gray-700 mb-1 dark:text-gray-300"
										>
											Private Key
										</Label>
										<Input
											id="pro_tripay_private_key"
											type="password"
											className="font-mono text-xs bg-white border-gray-300 dark:bg-gray-900 dark:border-gray-700"
											value={formData.pro_tripay_private_key}
											onChange={(e) =>
												setFormData({
													...formData,
													pro_tripay_private_key: e.target.value,
												})
											}
											placeholder="Tripay Private Key..."
										/>
									</div>
								</div>
								<div>
									<Label
										htmlFor="pro_tripay_merchant_code"
										className="text-xs text-gray-700 mb-1 dark:text-gray-300"
									>
										Merchant Code
									</Label>
									<Input
										id="pro_tripay_merchant_code"
										type="text"
										className="font-mono text-xs bg-white border-gray-300 dark:bg-gray-900 dark:border-gray-700"
										value={formData.pro_tripay_merchant_code}
										onChange={(e) =>
											setFormData({
												...formData,
												pro_tripay_merchant_code: e.target.value,
											})
										}
										placeholder="e.g. T12345"
									/>
								</div>
							</div>
						</div>
					</div>
				</>
			) : (
				<div className="border-t border-gray-200 pt-6 dark:border-gray-700">
					<div className="bg-linear-to-br from-emerald-50 to-teal-50 border border-emerald-100 rounded-xl p-6 relative overflow-hidden dark:from-emerald-900/10 dark:to-teal-900/10 dark:border-emerald-800">
						<div className="absolute top-0 right-0 p-4 opacity-5">
							<Crown size={180} />
						</div>

						<div className="relative z-10">
							<div className="flex items-center gap-3 mb-4">
								<div className="p-2 bg-emerald-600 text-white rounded-lg shadow-sm">
									<Crown size={24} />
								</div>
								<div>
									<h3 className="text-xl font-bold text-gray-900 dark:text-gray-100">
										Otomatisasi Pembayaran
									</h3>
									<p className="text-sm text-gray-600 dark:text-gray-400">
										Aktifkan lisensi Pro untuk menggunakan payment gateway otomatis.
									</p>
								</div>
							</div>

							<div className="grid md:grid-cols-3 gap-4 mb-6">
								<div className="bg-white/60 p-3 rounded-lg border border-emerald-100 dark:bg-gray-800/50 dark:border-gray-700">
									<h4 className="font-semibold text-gray-900 mb-1 flex items-center gap-2 dark:text-gray-100">
										<span className="w-2 h-2 rounded-full bg-blue-500" />
										Midtrans
									</h4>
									<p className="text-xs text-gray-500 dark:text-gray-400">
										GoPay, OVO, ShopeePay, VA (BCA, Mandiri, BNI, BRI)
									</p>
								</div>
								<div className="bg-white/60 p-3 rounded-lg border border-emerald-100 dark:bg-gray-800/50 dark:border-gray-700">
									<h4 className="font-semibold text-gray-900 mb-1 flex items-center gap-2 dark:text-gray-100">
										<span className="w-2 h-2 rounded-full bg-cyan-500" />
										Xendit
									</h4>
									<p className="text-xs text-gray-500 dark:text-gray-400">
										VA All Banks, QRIS, E-Wallet, Retail Outlets
									</p>
								</div>
								<div className="bg-white/60 p-3 rounded-lg border border-emerald-100 dark:bg-gray-800/50 dark:border-gray-700">
									<h4 className="font-semibold text-gray-900 mb-1 flex items-center gap-2 dark:text-gray-100">
										<span className="w-2 h-2 rounded-full bg-orange-500" />
										Tripay
									</h4>
									<p className="text-xs text-gray-500 dark:text-gray-400">
										Biaya murah, Indomaret, Alfamart, QRIS
									</p>
								</div>
							</div>

							<button
								type="button"
								onClick={() => setShowProModal(true)}
								className="w-full sm:w-auto px-6 py-2.5 bg-emerald-600 text-white font-medium rounded-lg hover:bg-emerald-700 transition shadow-sm flex items-center justify-center gap-2"
							>
								<Crown size={16} />
								Upgrade ke Donasai Pro
							</button>
						</div>
					</div>
				</div>
			)}

			<div className="border-t border-gray-200 pt-6 dark:border-gray-700">
				<FeeCoverageSection />
			</div>
		</div>
	);
}
