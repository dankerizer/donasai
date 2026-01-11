import { Check, Lock } from "lucide-react";
import { toast } from "sonner";
import { useSettings } from "../SettingsContext";

export default function LicenseSection() {
	const {
		licenseStatus,
		licenseKey,
		proSettings,
		isProInstalled,
	} = useSettings();

	if (!isProInstalled) return null;

	return (
		<div className="space-y-6">
			<div>
				<h3 className="text-lg font-medium text-gray-900 mb-1">
					Status Lisensi
				</h3>
				<p className="text-sm text-gray-500 mb-4">
					Kelola lisensi Donasai Pro Anda di sini.
				</p>

				<div className="p-6 border border-gray-200 rounded-lg bg-gray-50 text-center relative overflow-hidden">
					{licenseStatus === "active" ? (
						<div className="relative z-10 space-y-6">
							<div className="w-16 h-16 bg-linear-to-br from-green-400 to-emerald-600 rounded-2xl flex items-center justify-center mx-auto shadow-lg shadow-green-200">
								<Check size={32} className="text-white" strokeWidth={3} />
							</div>

							<div>
								<h3 className="text-xl font-bold text-gray-900 mb-1">
									Donasai Pro Aktif
								</h3>
								<p className="text-gray-500 text-sm">
									Terhubung dengan{" "}
									<strong>{proSettings.licenseDomain || "Domain ini"}</strong>
								</p>
								{proSettings.licenseExp && (
									<p className="text-xs text-gray-400 mt-1">
										Berlaku hingga: {proSettings.licenseExp}
									</p>
								)}
							</div>

							<div className="bg-white rounded-xl border border-gray-200 p-4 max-w-sm mx-auto shadow-sm">
								<div className="flex justify-between items-center text-sm mb-2">
									<span className="text-gray-500">Status</span>
									<span className="text-green-600 font-bold bg-green-50 px-2 py-0.5 rounded-md">
										Active
									</span>
								</div>
								<div className="flex justify-between items-center text-sm">
									<span className="text-gray-500">Versi</span>
									<span className="font-medium">1.0.0</span>
								</div>
							</div>

							<div className="pt-4 border-t border-gray-200 flex justify-center gap-4">
								<button
									type="button"
									className="text-red-600 hover:text-red-700 text-sm font-medium hover:bg-red-50 px-4 py-2 rounded-lg transition-colors"
									onClick={async () => {
										if (
											confirm(
												"Apakah Anda yakin ingin menonaktifkan lisensi ini? Fitur Pro akan dikunci.",
											)
										) {
											try {
												await fetch("/wp-json/wpd/v1/pro/deactivate", {
													method: "POST",
													headers: {
														"X-WP-Nonce": (window as any).wpdSettings?.nonce,
													},
												});
												toast.success("Lisensi berhasil dinonaktifkan.");
												window.location.reload();
											} catch (err) {
												toast.error("Gagal menonaktifkan lisensi.");
											}
										}
									}}
								>
									Nonaktifkan Lisensi
								</button>
							</div>

							{/* Hidden input for display/compatibility */}
							<input type="hidden" value={licenseKey} />
						</div>
					) : (
						<div className="space-y-4">
							<div className="mb-4">
								<div className="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
									<Lock className="text-gray-400" size={32} />
								</div>
								<h4 className="font-semibold text-gray-900">
									Belum Terhubung
								</h4>
								<p className="text-sm text-gray-500 max-w-xs mx-auto">
									Hubungkan website Anda dengan dashboard member Donasai untuk
									mengaktifkan lisensi.
								</p>
							</div>

							{proSettings.connectUrl ? (
								<a
									href={proSettings.connectUrl}
									className="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white! font-medium visited:text-white! rounded-lg hover:bg-blue-700 transition shadow-sm focus:text-blue-100!"
								>
									Hubungkan & Aktivasi
								</a>
							) : (
								<p className="text-red-500 text-sm">
									URL Aktivasi tidak ditemukan. Pastikan plugin Pro aktif.
								</p>
							)}
						</div>
					)}
				</div>
			</div>
		</div>
	);
}
