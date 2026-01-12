import { Lock } from "lucide-react";

export function ActivationLock() {
	const proSettings = (window as any).wpdProSettings || {};

	return (
		<div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-white/50 backdrop-blur-xl animate-in fade-in duration-500">
			<div className="bg-white rounded-2xl shadow-2xl border border-gray-100 max-w-md w-full overflow-hidden p-8 text-center ring-1 ring-black/5 relative">
				{/* Decorative Background Blob */}
				<div className="absolute top-0 left-1/2 -translate-x-1/2 w-full h-32 bg-linear-to-b from-blue-50 to-transparent -z-10" />

				<div className="w-20 h-20 bg-blue-600 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-blue-200 shadow-xl rotate-3 transition-transform hover:rotate-6">
					<Lock className="text-white" size={40} strokeWidth={2.5} />
				</div>

				<h2 className="text-2xl font-bold text-gray-900 mb-2 tracking-tight">
					Aktivasi Donasai Pro
				</h2>

				<p className="text-gray-500 mb-8 leading-relaxed text-[15px]">
					Plugin <strong>Donasai Pro</strong> telah terpasang. Harap hubungkan
					lisensi Anda untuk membuka semua fitur premium.
				</p>

				<div className="space-y-3">
					{proSettings.connectUrl ? (
						<a
							href={proSettings.connectUrl}
							className="block w-full py-3.5 px-6 bg-blue-600 hover:bg-blue-700 text-white! visited:text-white! font-bold rounded-xl transition-all shadow-lg shadow-blue-600/20 hover:shadow-blue-600/30 active:scale-95"
						>
							Hubungkan & Aktivasi
						</a>
					) : (
						<div className="p-4 bg-red-50 text-red-600 rounded-xl text-sm border border-red-100 mb-4">
							<p className="font-semibold">Error Konfigurasi</p>
							<p>URL Aktivasi tidak ditemukan.</p>
						</div>
					)}

					<a
						href="/wp-admin/plugins.php"
						className="block w-full py-3.5 px-6 bg-white hover:bg-gray-50 text-gray-600 font-medium rounded-xl border border-gray-200 transition-colors"
					>
						Nonaktifkan Plugin
					</a>
				</div>

				<p className="mt-8 text-xs text-gray-400">
					Masalah dengan lisensi?{" "}
					<a
						href="https://donasai.com/contact"
						className="underline hover:text-gray-600"
					>
						Hubungi Dukungan
					</a>
				</p>
			</div>
		</div>
	);
}
