import { Input } from "/src/components/ui/Input";
import { InputMoney } from "/src/components/ui/InputMoney";
import { Label } from "/src/components/ui/Label";
import { useSettings } from "../SettingsContext";

export default function DonationSection() {
	const { formData, setFormData, licenseStatus, setShowProModal } =
		useSettings();

	return (
		<div className="space-y-6">
			<div>
				<h3 className="text-lg font-medium text-gray-900 mb-4 dark:text-gray-100">Opsi Donasi</h3>
				<div className="grid gap-4">
					<div>
						<label
							htmlFor="minAmount"
							className="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300"
						>
							Jumlah Donasi Minimum (Rp)
						</label>
						<InputMoney
							id="minAmount"
							value={formData.min_amount}
							onChange={(val) =>
								setFormData({
									...formData,
									min_amount: val,
								})
							}
						/>
					</div>
					<div>
						<Label htmlFor="presets">Preset Default (Rp)</Label>
						<Input
							type="text"
							id="presets"
							value={formData.presets}
							onChange={(e) =>
								setFormData({ ...formData, presets: e.target.value })
							}
							placeholder="50000,100000,200000"
						/>
						<p className="text-xs text-gray-500 mt-1 dark:text-gray-400">Pisahkan dengan koma.</p>
					</div>
					<div>
						<Label htmlFor="anonymousLabel">Label Anonim</Label>
						<Input
							type="text"
							id="anonymousLabel"
							value={formData.anonymous_label}
							onChange={(e) =>
								setFormData({
									...formData,
									anonymous_label: e.target.value,
								})
							}
							placeholder="Hamba Allah"
						/>
						<p className="text-xs text-gray-500 mt-1 dark:text-gray-400">
							Ditampilkan saat pengguna menyembunyikan nama mereka.
						</p>
					</div>
				</div>
			</div>
			{licenseStatus === "active" ? (
				<div className="border-t border-gray-200 pt-6 dark:border-gray-700">
					<h3 className="text-lg font-medium text-gray-900 mb-2 flex items-center gap-2 dark:text-gray-100">
						Donasi Berulang{" "}
						<span className="bg-purple-100 text-purple-700 text-xs px-2 py-0.5 rounded font-bold dark:bg-purple-900/40 dark:text-purple-300">
							PRO
						</span>
					</h3>
					<p className="text-sm text-gray-500 mb-3 dark:text-gray-400">
						Pilih interval penagihan yang tersedia untuk donatur.
					</p>
					<div className="grid grid-cols-2 gap-3 mb-6">
						{[
							{ id: "day", label: "Harian" },
							{ id: "week", label: "Mingguan" },
							{ id: "month", label: "Bulanan" },
							{ id: "year", label: "Tahunan" },
						].map((interval) => (
							<div key={interval.id} className="flex items-center space-x-2">
								<input
									type="checkbox"
									id={`interval-${interval.id}`}
									checked={(formData.recurring_intervals as string[])?.includes(
										interval.id,
									)}
									onChange={(e) => {
										const checked = e.target.checked;
										let current = [
											...((formData.recurring_intervals as string[]) || []),
										];
										if (checked) {
											if (!current.includes(interval.id))
												current.push(interval.id);
										} else {
											current = current.filter((i) => i !== interval.id);
										}
										setFormData({
											...formData,
											recurring_intervals: current,
										});
									}}
									className="h-4 w-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:checked:bg-purple-600 dark:checked:border-purple-600"
								/>
								<label
									htmlFor={`interval-${interval.id}`}
									className="text-sm text-gray-700 dark:text-gray-300"
								>
									{interval.label}
								</label>
							</div>
						))}
					</div>

					<h3 className="text-lg font-medium text-gray-900 mb-2 flex items-center gap-2 dark:text-gray-100">
						Registrasi Pengguna{" "}
						<span className="bg-purple-100 text-purple-700 text-xs px-2 py-0.5 rounded font-bold dark:bg-purple-900/40 dark:text-purple-300">
							PRO
						</span>
					</h3>
					<div className="flex items-center space-x-3">
						<input
							id="createUser"
							type="checkbox"
							checked={formData.create_user}
							onChange={(e) =>
								setFormData({
									...formData,
									create_user: e.target.checked,
								})
							}
							className="h-4 w-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:checked:bg-purple-600 dark:checked:border-purple-600"
						/>
						<label
							htmlFor="createUser"
							className="text-sm font-medium text-gray-700 dark:text-gray-300"
						>
							Otomatis buat Pengguna WordPress dari Email Donatur
						</label>
					</div>

					<h3 className="text-lg font-medium text-gray-900 mb-2 mt-6 flex items-center gap-2 dark:text-gray-100">
						Kadaluarsa Donasi Manual{" "}
						<span className="bg-purple-100 text-purple-700 text-xs px-2 py-0.5 rounded font-bold dark:bg-purple-900/40 dark:text-purple-300">
							PRO
						</span>
					</h3>
					<div className="grid gap-2">
						<label
							htmlFor="expiryHours"
							className="text-sm font-medium text-gray-700 dark:text-gray-300"
						>
							Batas Waktu Pembayaran (Jam)
						</label>
						<Input
							type="number"
							id="expiryHours"
							min={1}
							value={formData.pending_expiry_hours}
							onChange={(e) =>
								setFormData({
									...formData,
									pending_expiry_hours: parseInt(e.target.value) || 48,
								})
							}
							placeholder="48"
						/>
						<p className="text-xs text-gray-500 dark:text-gray-400">
							Donasi manual dengan status 'pending' akan dianggap kadaluarsa
							setelah melewati waktu ini.
						</p>
					</div>

					<h3 className="text-lg font-medium text-gray-900 mb-2 mt-6 flex items-center gap-2 dark:text-gray-100">
						Pengingat Email{" "}
						<span className="bg-purple-100 text-purple-700 text-xs px-2 py-0.5 rounded font-bold dark:bg-purple-900/40 dark:text-purple-300">
							PRO
						</span>
					</h3>
					<div className="space-y-4">
						<div className="flex items-center space-x-3">
							<input
								id="emailReminder"
								type="checkbox"
								checked={formData.email_reminder_enabled}
								onChange={(e) =>
									setFormData({
										...formData,
										email_reminder_enabled: e.target.checked,
									})
								}
								className="h-4 w-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600 dark:checked:bg-purple-600 dark:checked:border-purple-600"
							/>
							<label
								htmlFor="emailReminder"
								className="text-sm font-medium text-gray-700 dark:text-gray-300"
							>
								Kirim Pengingat Pembayaran Otomatis
							</label>
						</div>

						{formData.email_reminder_enabled && (
							<div className="pl-7 grid gap-2">
								<label
									htmlFor="reminderDelay"
									className="text-sm font-medium text-gray-700 dark:text-gray-300"
								>
									Kirim Setelah (Jam)
								</label>
								<Input
									type="number"
									id="reminderDelay"
									min={1}
									value={formData.email_reminder_delay}
									onChange={(e) =>
										setFormData({
											...formData,
											email_reminder_delay: parseInt(e.target.value) || 24,
										})
									}
									placeholder="24"
								/>
								<p className="text-xs text-amber-600 dark:text-amber-500">
									⚠️ Pastikan durasi ini LEBIH KECIL dari Batas Waktu Kadaluarsa
									di atas (contoh: Ingatkan setelah 24 jam, Kadaluarsa 48 jam).
								</p>
							</div>
						)}
					</div>
				</div>
			) : (
				<div className="border-t border-gray-200 pt-6 dark:border-gray-700">
					<h3 className="text-lg font-medium text-gray-900 mb-2 flex items-center gap-2 dark:text-gray-100">
						Registrasi Pengguna{" "}
						<span className="bg-purple-100 text-purple-700 text-xs px-2 py-0.5 rounded font-bold dark:bg-purple-900/40 dark:text-purple-300">
							PRO
						</span>
					</h3>
					<div className="flex items-center space-x-3 opacity-60">
						<input
							id="createUserPro"
							type="checkbox"
							checked={formData.create_user}
							onChange={() => setShowProModal(true)}
							className="h-4 w-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500 dark:bg-gray-700 dark:border-gray-600"
						/>
						<label
							htmlFor="createUserPro"
							className="text-sm font-medium text-gray-700 dark:text-gray-300"
						>
							Otomatis buat Pengguna WordPress dari Email Donatur
						</label>
					</div>
				</div>
			)}
		</div>
	);
}
