import { X, Check } from "lucide-react";
import { useSettings } from "./SettingsContext";

export default function ProModal() {
	const { showProModal, setShowProModal } = useSettings();

	if (!showProModal) return null;

	const features = [
		"Payment Gateway Otomatis (Midtrans, Xendit, Tripay)",
		"Donasi Berulang (Recurring)",
		// "Notifikasi WhatsApp (Fonnte/Watzap)",
		"Manajemen Multi-Akun Bank",
		"Tracking Pixel (Facebook, TikTok, GA4)",
		"Badge Donatur Verifikasi",
		"Prioritas Dukungan Teknis",
	];

	return (
		<div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm animate-in fade-in duration-200">
			<div className="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden relative animate-in zoom-in-95 duration-200 ring-1 ring-black/5">
				{/* Header */}
				<div className="bg-linear-to-br from-emerald-600 to-teal-600 p-6 text-white relative overflow-hidden">
					<div className="absolute top-0 right-0 p-4 opacity-10">
						<svg
							width="120"
							height="120"
							viewBox="0 0 24 24"
							fill="currentColor"
						>
							<path d="M2.5 12c0-4.478 3.522-8.5 8.5-8.5s9.5 3.522 9.5 8.5-3.522 8.5-8.5 8.5-9.5-3.522-9.5-8.5zm8.5-6.5c-3.584 0-6.5 2.916-6.5 6.5s2.916 6.5 6.5 6.5 6.5-2.916 6.5-6.5-2.916-6.5-6.5-6.5z" />
						</svg>
					</div>
					<h2 className="text-2xl font-bold relative z-10">Donasai Pro</h2>
					<p className="text-emerald-100 relative z-10 text-sm mt-1">
						Aktifkan fitur powerful untuk maksimalkan donasi.
					</p>
					<button
						type="button"
						onClick={() => setShowProModal(false)}
						className="absolute top-4 right-4 text-white/70 hover:text-white p-1 hover:bg-white/10 rounded-full transition"
					>
						<X size={20} />
					</button>
				</div>

				{/* Body */}
				<div className="p-6">
					<div className="space-y-3 mb-6">
						{features.map((feature, idx) => (
							// biome-ignore lint/suspicious/noArrayIndexKey: <explanation>
							<div key={idx} className="flex items-start gap-3">
								<div className="p-0.5 bg-emerald-100 text-emerald-600 rounded-full dark:bg-emerald-900/30 dark:text-emerald-400 mt-0.5">
									<Check size={14} />
								</div>
								<span className="text-gray-700 dark:text-gray-300 text-sm">
									{feature}
								</span>
							</div>
						))}
					</div>

					<div className="flex flex-col gap-3">
						<a
							href="https://donasai.com/pricing"
							target="_blank"
							rel="noreferrer"
							className="block w-full text-center py-3 bg-emerald-600 hover:bg-emerald-700 text-white! font-bold rounded-xl transition shadow-lg  dark:shadow-emerald-900/20"
						>
							Dapatkan Lisensi Pro
						</a>
						<p className="text-xs text-center text-gray-500 dark:text-gray-400">
							Sudah punya lisensi? Masuk ke menu{" "}
							<button
								type="button"
								onClick={() => {
									setShowProModal(false);
									const url = new URL(window.location.href);
									url.searchParams.set("tab", "license");
									window.history.pushState({}, "", url.toString());
									window.location.reload(); // Simple reload to switch tab for now
								}}
								className="text-emerald-600 hover:underline font-medium"
							>
								Lisensi
							</button>{" "}
							untuk aktivasi.
						</p>
					</div>
				</div>
			</div>
		</div>
	);
}
