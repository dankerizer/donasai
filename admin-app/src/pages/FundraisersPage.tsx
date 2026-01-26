import { useQuery } from "@tanstack/react-query";
import { Lock, ShieldCheck, TrendingUp, Users } from "lucide-react";

interface Fundraiser {
	id: number;
	user_id: number;
	campaign_id: number;
	referral_code: string;
	total_donations: number;
	donation_count: number;
	created_at: string;
}

export default function FundraisersPage() {
	const isPro = (window as any).wpdSettings?.isPro;

	const { data: fundraisers, isLoading } = useQuery({
		queryKey: ["fundraisers"],
		queryFn: async () => {
			const response = await fetch("/wp-json/donasai/v1/fundraisers", {
				headers: { "X-WP-Nonce": (window as any).wpdSettings?.nonce },
			});
			if (!response.ok) return [];
			return response.json();
		},
		enabled: !!isPro,
	});

	if (!isPro) {
		return (
			<div className="space-y-6">
				<div className="flex justify-between items-center">
					<h2 className="text-2xl font-bold text-gray-800 dark:text-gray-100">
						Penggalang Dana
					</h2>
				</div>

				<div className="relative overflow-hidden bg-white rounded-2xl shadow-lg border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
					{/* Decorative Background Elements */}
					<div className="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-emerald-50 rounded-full blur-3xl opacity-50 pointer-events-none" />
					<div className="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 bg-teal-50 rounded-full blur-3xl opacity-50 pointer-events-none" />

					<div className="relative z-10 flex flex-col md:flex-row items-center p-8 md:p-12 gap-10">
						{/* Left Content */}
						<div className="flex-1 text-center md:text-left space-y-6">
							<div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold uppercase tracking-wide dark:bg-emerald-900/30 dark:text-emerald-400">
								<Lock size={12} /> Fitur Premium
							</div>
							<h3 className="text-3xl! md:text-4xl! font-extrabold text-gray-900 tracking-tight dark:text-gray-100">
								Lipat Gandakan Donasi dengan{" "}
								<span className="text-emerald-600">Fundraiser</span>
							</h3>
							<p className="text-lg! text-gray-600! leading-relaxed dark:text-gray-300!">
								Fitur Peer-to-Peer Fundraising memungkinkan relawan, komunitas,
								dan influencer Anda untuk membuat halaman kampanye mereka
								sendiri.
								<br className="hidden md:inline" /> Perluas jangkauan dan
								dapatkan donatur baru secara organik.
							</p>

							<div className="flex flex-col sm:flex-row gap-4 justify-center md:justify-start pt-2">
								<a
									href="https://donasai.com/pricing"
									target="_blank"
									rel="noopener noreferrer"
									className="inline-flex items-center justify-center gap-2 px-8 py-4 bg-emerald-600 text-white! font-bold rounded-xl hover:bg-emerald-700   hover:-translate-y-1 transition-all"
								>
									Upgrade ke Donasai Pro
								</a>
								<a
									href="https://donasai.com/docs/fundraiser"
									target="_blank"
									rel="noopener noreferrer"
									className="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white text-gray-700 font-bold rounded-xl border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all dark:bg-gray-800 dark:text-gray-200 dark:border-gray-700 dark:hover:bg-gray-700"
								>
									Pelajari Selengkapnya
								</a>
							</div>
						</div>

						{/* Right Visual/Feature Grid */}
						<div className="flex-1 w-full max-w-md">
							<div className="bg-gray-50/80 backdrop-blur-sm rounded-2xl p-6 border border-gray-100 space-y-6 shadow-sm dark:bg-gray-800/50 dark:border-gray-700">
								<div className="flex gap-4">
									<div className="p-3 bg-blue-100 text-blue-600 rounded-lg h-fit">
										<Users size={24} />
									</div>
									<div>
										<h4 className="font-bold text-gray-900 my-0! dark:text-gray-100">
											Halaman Unik Relawan
										</h4>
										<p className="text-sm text-gray-500 mt-1! dark:text-gray-400">
											Setiap fundraiser mendapat link referal unik untuk melacak
											donasi yang mereka bawa.
										</p>
									</div>
								</div>
								<div className="flex gap-4">
									<div className="p-3 bg-purple-100 text-purple-600 rounded-lg h-fit">
										<TrendingUp size={24} />
									</div>
									<div>
										<h4 className="font-bold text-gray-900 my-0! dark:text-gray-100">
											Leaderboard & Gamifikasi
										</h4>
										<p className="text-sm text-gray-500 mt-1! dark:text-gray-400">
											Otomatis tampilkan donatur terbanyak untuk memicu
											kompetisi kebaikan.
										</p>
									</div>
								</div>
								<div className="flex gap-4">
									<div className="p-3 bg-orange-100 text-orange-600 rounded-lg h-fit">
										<ShieldCheck size={24} />
									</div>
									<div>
										<h4 className="font-bold text-gray-900 my-0! dark:text-gray-100">
											Kendali Penuh
										</h4>
										<p className="text-sm text-gray-500 mt-1! dark:text-gray-400">
											Setujui atau tolak pendaftar fundraiser dan pantau
											performa mereka di dashboard.
										</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		);
	}

	return (
		<div className="space-y-6">
			<div className="flex justify-between items-center">
				<h2 className="text-2xl font-bold text-gray-800 dark:text-gray-100">
					Penggalang Dana
				</h2>
				{/* Future: Add Invite/Create Button */}
			</div>

			<div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden dark:bg-gray-900 dark:border-gray-800">
				<table className="w-full text-left text-sm text-gray-600 dark:text-gray-400">
					<thead className="bg-gray-50 border-b border-gray-200 font-medium text-gray-900 dark:bg-gray-800/50 dark:border-gray-800 dark:text-gray-200">
						<tr>
							<th className="px-6 py-4">ID</th>
							<th className="px-6 py-4">Kode (Pengguna)</th>
							<th className="px-6 py-4">ID Kampanye</th>
							<th className="px-6 py-4 text-right">Donasi Terkumpul</th>
							<th className="px-6 py-4 text-right">Jumlah</th>
							<th className="px-6 py-4">Bergabung Pada</th>
						</tr>
					</thead>
					<tbody className="divide-y divide-gray-200 dark:divide-gray-800">
						{isLoading ? (
							<tr>
								<td colSpan={6} className="px-6 py-4 text-center">
									Memuat...
								</td>
							</tr>
						) : fundraisers && fundraisers.length > 0 ? (
							fundraisers.map((f: Fundraiser) => (
								<tr
									key={f.id}
									className="hover:bg-gray-50 dark:hover:bg-gray-800/30"
								>
									<td className="px-6 py-4">#{f.id}</td>
									<td className="px-6 py-4 font-medium text-blue-600 flex items-center gap-2 dark:text-blue-400">
										<Users size={16} />
										{f.referral_code}
									</td>
									<td className="px-6 py-4">{f.campaign_id}</td>
									<td className="px-6 py-4 text-right font-medium text-green-600 dark:text-green-400">
										Rp {f.total_donations.toLocaleString("id-ID")}
									</td>
									<td className="px-6 py-4 text-right">{f.donation_count}</td>
									<td className="px-6 py-4">
										{new Date(f.created_at).toLocaleDateString()}
									</td>
								</tr>
							))
						) : (
							<tr>
								<td colSpan={6} className="px-6 py-4 text-center">
									Tidak ada penggalang dana ditemukan.
								</td>
							</tr>
						)}
					</tbody>
				</table>
			</div>
		</div>
	);
}
