import { useQuery } from "@tanstack/react-query";
import { clsx } from "clsx";
import {
	Calendar,
	Heart,
	LayoutDashboard,
	TrendingUp,
	Users,
} from "lucide-react";
import { Link } from "react-router-dom";
import {
	Area,
	AreaChart,
	Bar,
	BarChart,
	CartesianGrid,
	Cell,
	Legend,
	Pie,
	PieChart,
	ResponsiveContainer,
	Tooltip,
	XAxis,
	YAxis,
} from "recharts";

const COLORS = ["#0088FE", "#00C49F", "#FFBB28", "#FF8042", "#8884d8"];

export default function Dashboard() {
	const { data: stats } = useQuery({
		queryKey: ["stats"],
		queryFn: async () => {
			const response = await fetch("/wp-json/donasai/v1/stats", {
				headers: { "X-WP-Nonce": (window as any).donasaiSettings?.nonce },
			});
			if (!response.ok)
				return { total_donations: 0, total_donors: 0, active_campaigns: 0 };
			return response.json();
		},
	});

	const { data: chartData } = useQuery({
		queryKey: ["chart-stats"],
		queryFn: async () => {
			const response = await fetch("/wp-json/donasai/v1/stats/chart", {
				headers: { "X-WP-Nonce": (window as any).donasaiSettings?.nonce },
			});
			if (!response.ok) return {};
			return response.json();
		},
	});

	// Fetch Settings to check license
	const { data: settings } = useQuery({
		queryKey: ["settings"],
		queryFn: async () => {
			const response = await fetch("/wp-json/donasai/v1/settings", {
				headers: { "X-WP-Nonce": (window as any).donasaiSettings?.nonce },
			});
			if (!response.ok) return {};
			return response.json();
		},
	});

	const isProActive = settings?.license?.status === "active";

	// Mock Data for Preview
	const mockStats = {
		growth_rate: 12.5,
		recurring_revenue: 15450000,
		retention_rate: 68,
	};

	const mockChartData = {
		payment_methods: [
			{ payment_method: "BCA", count: 45 },
			{ payment_method: "QRIS", count: 30 },
			{ payment_method: "MANDIRI", count: 15 },
			{ payment_method: "E-WALLET", count: 10 },
		],
		top_campaigns: [
			{ name: "Wakaf Masjid", value: 50000000 },
			{ name: "Sedekah Jumat", value: 25000000 },
			{ name: "Yatim Piatu", value: 15000000 },
		],
	};

	const displayStats = isProActive ? stats : mockStats;
	const displayChartData = isProActive ? chartData : mockChartData;

	return (
		<div className="space-y-8">
			<div className="flex justify-between items-center">
				<h2 className="text-2xl! font-bold text-gray-800 dark:text-gray-100">
					Ringkasan Dasbor
				</h2>
				<Link
					to="/donations"
					className="text-sm font-medium text-red-600 hover:text-red-800"
				>
					Lihat Semua Donasi &rarr;
				</Link>
			</div>

			{/* Standard Stats (Free) */}
			<div className="grid grid-cols-1 md:grid-cols-3 gap-6">
				<div className="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow dark:bg-gray-900 dark:border-gray-800">
					<div className="flex items-center gap-4">
						<div className="p-3 bg-emerald-100 text-emerald-600 rounded-lg">
							<Heart size={24} />
						</div>
						<div>
							<p className="text-sm font-medium text-gray-600 my-0! dark:text-gray-400">
								Total Donasi
							</p>
							<h3 className="text-2xl! font-bold text-gray-900 my-0! dark:text-gray-100">
								Rp {stats?.total_donations?.toLocaleString("id-ID") || 0}
							</h3>
						</div>
					</div>
				</div>

				<div className="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow dark:bg-gray-900 dark:border-gray-800">
					<div className="flex items-center gap-4">
						<div className="p-3 bg-emerald-50 text-emerald-500 rounded-lg dark:bg-emerald-900/30 dark:text-emerald-400">
							<Users size={24} />
						</div>
						<div>
							<p className="text-sm font-medium text-gray-600 my-0! dark:text-gray-400">
								Total Donatur
							</p>
							<h3 className="text-2xl! font-bold text-gray-900 my-0! dark:text-gray-100">
								{stats?.total_donors || 0}
							</h3>
						</div>
					</div>
				</div>

				<div className="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow dark:bg-gray-900 dark:border-gray-800">
					<div className="flex items-center gap-4">
						<div className="p-3 bg-emerald-100 text-emerald-600 rounded-lg">
							<LayoutDashboard size={24} />
						</div>
						<div>
							<p className="text-sm font-medium text-gray-600 my-0! dark:text-gray-400">
								Kampanye Aktif
							</p>
							<h3 className="text-2xl! font-bold text-gray-900 my-0! dark:text-gray-100">
								{stats?.active_campaigns || 0}
							</h3>
						</div>
					</div>
				</div>
			</div>

			{/* Donation Trends Chart */}
			<div className="bg-white p-6 rounded-xl shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
				<div className="flex justify-between items-center mb-6">
					<div>
						<h3 className="text-lg font-bold text-gray-800 dark:text-gray-100">
							Tren Donasi
						</h3>
						<p className="text-sm text-gray-500 dark:text-gray-400">
							30 Hari Terakhir
						</p>
					</div>
					{/* Placeholder for range selector if needed later */}
				</div>

				<div className="h-[300px] w-full">
					<ResponsiveContainer width="100%" height="100%">
						<AreaChart
							data={chartData?.daily_stats || []}
							margin={{ top: 10, right: 10, left: 0, bottom: 0 }}
						>
							<defs>
								<linearGradient id="colorAmount" x1="0" y1="0" x2="0" y2="1">
									<stop offset="5%" stopColor="#dc2626" stopOpacity={0.1} />
									<stop offset="95%" stopColor="#dc2626" stopOpacity={0} />
								</linearGradient>
							</defs>
							<CartesianGrid
								strokeDasharray="3 3"
								vertical={false}
								stroke="#E5E7EB"
							/>
							<XAxis
								dataKey="date"
								axisLine={false}
								tickLine={false}
								tick={{ fill: "#6B7280", fontSize: 12 }}
								dy={10}
							/>
							<YAxis
								axisLine={false}
								tickLine={false}
								tick={{ fill: "#6B7280", fontSize: 12 }}
								tickFormatter={(value) =>
									new Intl.NumberFormat("id-ID", {
										notation: "compact",
										compactDisplay: "short",
									}).format(value)
								}
							/>
							<Tooltip
								contentStyle={{
									borderRadius: "8px",
									border: "none",
									boxShadow: "0 4px 6px -1px rgb(0 0 0 / 0.1)",
								}}
								formatter={(value: any) => [
									`Rp ${Number(value).toLocaleString("id-ID")}`,
									"Jumlah",
								]}
							/>
							<Area
								type="monotone"
								dataKey="amount"
								stroke="#dc2626"
								strokeWidth={2}
								fillOpacity={1}
								fill="url(#colorAmount)"
							/>
						</AreaChart>
					</ResponsiveContainer>
				</div>
			</div>

			{/* Pro Analytics Wrapper */}
			<div className="relative">
				{!isProActive && (
					<div className="absolute inset-0 z-10 bg-white/60 backdrop-blur-sm flex flex-col items-center justify-center text-center p-6 rounded-xl border border-dashed border-gray-300 dark:bg-gray-900/60 dark:border-gray-700">
						<div className="max-w-lg space-y-4">
							<div className="inline-flex items-center justify-center p-3 bg-emerald-100 text-emerald-600 rounded-full mb-2 dark:bg-emerald-900/30 dark:text-emerald-400">
								<TrendingUp size={32} />
							</div>
							<h3 className="text-2xl! font-bold text-gray-900 dark:text-gray-100">
								Buka Wawasan Lebih Dalam dengan Pro
							</h3>
							<p className="text-gray-600 text-base! md:text-md!">
								Dapatkan akses ke analitik lanjutan seperti pertumbuhan donasi,
								retensi donatur, dan performa kampanye secara real-time. Ambil
								keputusan berbasis data untuk meningkatkan penggalangan dana
								Anda.
							</p>
							<div className="pt-2">
								<Link
									to="/settings"
									className="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white! bg-emerald-600 hover:bg-emerald-700 md:py-3 md:text-lg md:px-8 shadow-lg hover:shadow-xl transition-all"
								>
									Upgrade ke Donasai Pro
								</Link>
							</div>
							<p className="text-xs! text-gray-500 mt-4">
								Garansi 30 hari uang kembali.
							</p>
						</div>
					</div>
				)}

				<div
					className={clsx(
						"space-y-8 transition-all",
						!isProActive && "blur-sm opacity-60 select-none",
					)}
				>
					{/* Pro Analytics (Active) */}
					<div>
						<h3 className="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2 dark:text-gray-100">
							Analitik Lanjutan{" "}
							<span className="text-xs bg-red-100 text-red-700 font-bold px-2 py-0.5 rounded uppercase dark:bg-red-900/30 dark:text-red-400">
								Pro Active
							</span>
						</h3>
						<div className="grid grid-cols-1 md:grid-cols-3 gap-6">
							{/* Growth Rate */}
							<div className="bg-white p-6 rounded-xl shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
								<div className="flex items-center gap-4 mb-2">
									<div className="p-2 bg-pink-100 text-pink-600 rounded-lg dark:bg-pink-900/30 dark:text-pink-400">
										<TrendingUp size={20} />
									</div>
									<span className="font-medium text-gray-600 dark:text-gray-400">
										Pertumbuhan (MoM)
									</span>
								</div>
								<h3
									className={clsx(
										"text-2xl font-bold",
										(displayStats?.growth_rate || 0) >= 0
											? "text-green-600"
											: "text-red-600",
									)}
								>
									{(displayStats?.growth_rate || 0) > 0 ? "+" : ""}
									{displayStats?.growth_rate || 0}%
								</h3>
								<p className="text-xs text-gray-400 mt-1">
									Dibanding bulan lalu
								</p>
							</div>

							{/* Recurring Revenue */}
							<div className="bg-white p-6 rounded-xl shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
								<div className="flex items-center gap-4 mb-2">
									<div className="p-2 bg-indigo-100 text-indigo-600 rounded-lg dark:bg-indigo-900/30 dark:text-indigo-400">
										<Calendar size={20} />
									</div>
									<span className="font-medium text-gray-600 dark:text-gray-400">
										Pendapatan Berulang
									</span>
								</div>
								<h3 className="text-2xl font-bold text-gray-900 dark:text-gray-100">
									Rp{" "}
									{displayStats?.recurring_revenue?.toLocaleString("id-ID") ||
										0}
								</h3>
								<p className="text-xs text-gray-400 mt-1">
									Total donasi rutin aktif
								</p>
							</div>

							{/* Retention Rate */}
							<div className="bg-white p-6 rounded-xl shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
								<div className="flex items-center gap-4 mb-2">
									<div className="p-2 bg-orange-100 text-orange-600 rounded-lg dark:bg-orange-900/30 dark:text-orange-400">
										<Users size={20} />
									</div>
									<span className="font-medium text-gray-600 dark:text-gray-400">
										Retensi Donatur
									</span>
								</div>
								<h3 className="text-2xl font-bold text-gray-900 dark:text-gray-100">
									{displayStats?.retention_rate || 0}%
								</h3>
								<p className="text-xs text-gray-400 mt-1">
									Donatur yang berdonasi &gt; 1 kali
								</p>
							</div>
						</div>
					</div>

					{/* Additional Charts */}
					<div className="grid grid-cols-1 md:grid-cols-2 gap-6">
						{/* Payment Methods Pie Chart */}
						<div className="bg-white p-6 rounded-xl shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
							<h3 className="text-lg font-bold text-gray-800 mb-6 dark:text-gray-100">
								Metode Pembayaran
							</h3>
							<div className="h-[300px] w-full">
								<ResponsiveContainer width="100%" height="100%">
									<PieChart>
										<Pie
											data={displayChartData?.payment_methods || []}
											cx="50%"
											cy="50%"
											innerRadius={60}
											outerRadius={80}
											paddingAngle={5}
											dataKey="count"
											nameKey="payment_method"
										>
											{(displayChartData?.payment_methods || []).map(
												(_entry: any, index: number) => (
													<Cell
														key={`cell-${index.toString()}-${_entry.payment_method}`}
														fill={COLORS[index % COLORS.length]}
													/>
												),
											)}
										</Pie>
										<Tooltip
											formatter={(value: any, name: any) => [value, name]}
										/>
										<Legend />
									</PieChart>
								</ResponsiveContainer>
							</div>
						</div>

						{/* Top Campaigns Bar Chart */}
						<div className="bg-white p-6 rounded-xl shadow-sm border border-gray-200 dark:bg-gray-900 dark:border-gray-800">
							<h3 className="text-lg font-bold text-gray-800 mb-6 dark:text-gray-100">
								Kampanye Terpopuler
							</h3>
							<div className="h-[300px] w-full">
								<ResponsiveContainer width="100%" height="100%">
									<BarChart
										layout="vertical"
										data={displayChartData?.top_campaigns || []}
										margin={{ top: 5, right: 30, left: 20, bottom: 5 }}
									>
										<CartesianGrid strokeDasharray="3 3" horizontal={false} />
										<XAxis type="number" hide />
										<YAxis
											dataKey="name"
											type="category"
											width={150}
											tick={{ fontSize: 12 }}
											tickFormatter={(value) =>
												value.length > 20
													? value.substring(0, 20) + "..."
													: value
											}
										/>
										<Tooltip
											cursor={{ fill: "transparent" }}
											formatter={(value: any) => [
												`Rp ${Number(value).toLocaleString("id-ID")}`,
												"Total",
											]}
										/>
										<Bar
											dataKey="value"
											fill="#dc2626"
											radius={[0, 4, 4, 0]}
											barSize={20}
										/>
									</BarChart>
								</ResponsiveContainer>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	);
}
