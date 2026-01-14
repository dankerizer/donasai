import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { clsx } from "clsx";
import {
	CheckCircle,
	ChevronDown,
	Eye,
	Pencil,
	Plus,
	Save,
	Trash2,
	X,
} from "lucide-react";
import { useState } from "react";
import ManualDonationModal from "/src/components/ManualDonationModal";
import { useSettingsFetch } from "./settings/hooks/use-settings-data";

// Mock Data Type

// Mock Data Type
interface Donation {
	id: number;
	name: string;
	email: string;
	phone: string;
	amount: number;
	status: "pending" | "complete" | "failed" | "expired";
	payment_method: string;
	gateway_txn_id: string;
	note: string;
	date: string;
	metadata?: {
		proof_url?: string;
		sender_bank?: string;
		sender_name?: string;
		confirmed_at?: string;
	};
}

export default function DonationsPage() {
	const queryClient = useQueryClient();

	// Fetch Settings to check active gateways
	const { data: settingsData } = useSettingsFetch();
	const settings = settingsData?.formData;

	const activeGateways = [
		{ id: "manual", label: "Bank" }, // Always active
		...(settings?.midtrans_enabled
			? [{ id: "midtrans", label: "Midtrans" }]
			: []),
		...(settings?.pro_xendit_api_key
			? [{ id: "xendit", label: "Xendit" }]
			: []),
		...(settings?.pro_tripay_api_key
			? [{ id: "tripay", label: "Tripay" }]
			: []),
	];
	const [selectedDonation, setSelectedDonation] = useState<Donation | null>(
		null,
	);
	const [isEditing, setIsEditing] = useState(false);
	const [editFormData, setEditFormData] = useState<Partial<Donation>>({});

	const handleEditClick = () => {
		if (selectedDonation) {
			setEditFormData({
				name: selectedDonation.name,
				email: selectedDonation.email,
				phone: selectedDonation.phone,
				amount: selectedDonation.amount,
				status: selectedDonation.status,
				note: selectedDonation.note,
			});
			setIsEditing(true);
		}
	};

	const handleSave = () => {
		if (selectedDonation) {
			mutation.mutate({ id: selectedDonation.id, data: editFormData });
		}
	};

	const handleCancel = () => {
		setIsEditing(false);
		setEditFormData({});
	};

	const [startDate, setStartDate] = useState("");
	const [endDate, setEndDate] = useState("");
	const [selectedStatuses, setSelectedStatuses] = useState<string[]>([]);
	const [selectedCampaigns, setSelectedCampaigns] = useState<string[]>([]);
	const [selectedGateways, setSelectedGateways] = useState<string[]>([]);
	const [recurringFilter, setRecurringFilter] = useState<string>(""); // '', 'recurring', 'one-time'
	const [isCampaignDropdownOpen, setIsCampaignDropdownOpen] = useState(false);
	const [isManualDonationOpen, setIsManualDonationOpen] = useState(false);

	// Pagination State
	const [page, setPage] = useState(1);
	const perPage = 20;

	// Check if Pro is active
	const isPro = (window as any).wpdSettings?.isPro;
	const proSettings = (window as any).wpdProSettings || {};
	const isProActive =
		isPro &&
		(proSettings.licenseStatus === "active" ||
			proSettings.licenseStatus === "valid");

	const toggleStatus = (status: string) => {
		setPage(1); // Reset page on filter change
		setSelectedStatuses((prev) => {
			const list = Array.isArray(prev) ? prev : [];
			return list.includes(status)
				? list.filter((s) => s !== status)
				: [...list, status];
		});
	};

	const toggleCampaign = (id: string) => {
		setPage(1); // Reset page on filter change
		setSelectedCampaigns((prev) => {
			const list = Array.isArray(prev) ? prev : [];
			return list.includes(id) ? list.filter((c) => c !== id) : [...list, id];
		});
	};

	const toggleGateway = (gateway: string) => {
		setPage(1);
		setSelectedGateways((prev) => {
			const list = Array.isArray(prev) ? prev : [];
			return list.includes(gateway)
				? list.filter((g) => g !== gateway)
				: [...list, gateway];
		});
	};

	const { data: queryData, isLoading } = useQuery({
		queryKey: [
			"donations",
			startDate,
			endDate,
			selectedStatuses,
			selectedCampaigns,
			selectedGateways,
			recurringFilter,
			page,
		],
		queryFn: async () => {
			const params = new URLSearchParams();
			params.append("page", String(page));
			params.append("per_page", String(perPage));
			if (startDate) params.append("start_date", startDate);
			if (endDate) params.append("end_date", endDate);
			if (selectedStatuses.length > 0)
				params.append("status", selectedStatuses.join(","));
			if (selectedCampaigns.length > 0)
				params.append("campaign_id", selectedCampaigns.join(","));
			if (selectedGateways.length > 0)
				params.append("payment_method", selectedGateways.join(","));
			if (recurringFilter) params.append("is_recurring", recurringFilter);

			const response = await fetch(
				`/wp-json/wpd/v1/donations?${params.toString()}`,
				{
					headers: { "X-WP-Nonce": (window as any).wpdSettings?.nonce },
				},
			);
			if (!response.ok) return { data: [], meta: { total: 0, total_pages: 0 } };
			return response.json();
		},
	});

	const donations = queryData?.data || [];
	const meta = queryData?.meta || { total: 0, total_pages: 0, current_page: 1 };

	// Mutation for update
	const mutation = useMutation({
		mutationFn: async (vars: { id: number; data: Partial<Donation> }) => {
			const response = await fetch(`/wp-json/wpd/v1/donations/${vars.id}`, {
				method: "POST",
				headers: {
					"Content-Type": "application/json",
					"X-WP-Nonce": (window as any).wpdSettings?.nonce,
				},
				body: JSON.stringify(vars.data),
			});
			if (!response.ok) throw new Error("Failed to update donation");
			return response.json();
		},
		onSuccess: (data) => {
			queryClient.invalidateQueries({ queryKey: ["donations"] });

			// If we are in the modal, update selectedDonation with returned data
			if (data.success && data.data) {
				// Ensure note and phone handle nulls gracefully
				const updated = {
					...data.data,
					note: data.data.note || "",
					phone: data.data.phone || "",
				};
				setSelectedDonation(updated);
			}
			setIsEditing(false);
		},
	});

	// Fetch Campaigns List
	const { data: campaigns } = useQuery({
		queryKey: ["campaigns-list"],
		queryFn: async () => {
			const response = await fetch("/wp-json/wpd/v1/campaigns/list", {
				headers: { "X-WP-Nonce": (window as any).wpdSettings?.nonce },
			});
			if (!response.ok) return [];
			return response.json();
		},
	});

	// Expire Mutation
	const expireMutation = useMutation({
		mutationFn: async () => {
			const formData = new FormData();
			formData.append("action", "wpd_expire_donations_manual");
			// formData.append('nonce', ...); // Optional if enforced

			const ajaxUrl = (window as any).ajaxurl || "/wp-admin/admin-ajax.php";

			const response = await fetch(ajaxUrl, {
				method: "POST",
				body: formData,
			});
			if (!response.ok) throw new Error("Network error");
			return response.json();
		},
		onSuccess: (data: any) => {
			if (data.success) {
				alert(data.data.message);
				queryClient.invalidateQueries({ queryKey: ["donations"] });
			} else {
				alert("Error: " + data.data);
			}
		},
		onError: () => {
			alert("Failed to connect to server.");
		},
	});

	const getExportUrl = () => {
		let url = `/wp-json/wpd/v1/export/donations?_wpnonce=${(window as any).wpdSettings?.nonce}`;
		if (startDate) url += `&start_date=${startDate}`;
		if (endDate) url += `&end_date=${endDate}`;
		if (selectedStatuses.length > 0)
			url += `&status=${selectedStatuses.join(",")}`;
		if (selectedCampaigns.length > 0)
			url += `&campaign_id=${selectedCampaigns.join(",")}`;
		if (selectedGateways.length > 0)
			url += `&payment_method=${selectedGateways.join(",")}`;
		if (recurringFilter) url += `&is_recurring=${recurringFilter}`;
		return url;
	};

	return (
		<div className="space-y-6">
			<div className="flex flex-col gap-4">
				<div className="flex justify-between items-center">
					<h2 className="text-2xl font-bold text-gray-800 dark:text-gray-100">
						Donasi
					</h2>
					{isProActive && (
						<div className="flex gap-2">
							<button
								type="button"
								onClick={() => {
									if (confirm("Bersihkan donasi pending yang sudah expired?")) {
										expireMutation.mutate();
									}
								}}
								disabled={expireMutation.isPending}
								className="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700 font-medium text-sm flex items-center gap-2 shadow-sm"
							>
								{expireMutation.isPending ? (
									"Processing..."
								) : (
									<>
										<Trash2 size={16} />
										Cleanup Expired
									</>
								)}
							</button>
							<button
								type="button"
								onClick={() => setIsManualDonationOpen(true)}
								className="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 font-medium text-sm flex items-center gap-2 shadow-md"
							>
								<Plus size={16} />
								Tambah Manual
							</button>
						</div>
					)}
				</div>

				{/* Filter Bar */}
				<div className="bg-white dark:bg-gray-800 p-5 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col gap-5">
					<div className="flex flex-wrap gap-6 items-end">
						{/* Date Range */}
						<div className="flex flex-col gap-1.5">
							<span className="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
								Periode
							</span>
							<div className="flex items-center gap-2 bg-gray-50 dark:bg-gray-700/50 p-1 rounded-lg border border-gray-200 dark:border-gray-600">
								<input
									type="date"
									aria-label="Tanggal Mulai"
									className="px-2 py-1.5 bg-transparent border-none! text-sm focus:ring-0! text-gray-700 dark:text-gray-200 w-[130px] outline-none!"
									value={startDate}
									onChange={(e) => {
										setPage(1);
										setStartDate(e.target.value);
									}}
								/>
								<span className="text-gray-400 font-medium">-</span>
								<input
									type="date"
									aria-label="Tanggal Selesai"
									className="px-2 py-1.5 bg-transparent border-none! text-sm focus:ring-0! text-gray-700 dark:text-gray-200 w-[130px] outline-none!"
									value={endDate}
									onChange={(e) => {
										setPage(1);
										setEndDate(e.target.value);
									}}
								/>
							</div>
						</div>

						{/* Status Filter */}
						<div className="flex flex-col gap-1.5">
							<span className="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
								Status
							</span>
							<div className="flex gap-2">
								{["pending", "complete", "failed", "expired"].map((status) => {
									const isSelected = selectedStatuses.includes(status);
									return (
										<button
											type="button"
											key={status}
											onClick={() => toggleStatus(status)}
											className={clsx(
												"px-3 py-2 rounded-lg text-sm font-medium border transition-all flex items-center gap-1.5",
												isSelected
													? "bg-blue-600 border-blue-600 text-white shadow-md shadow-blue-200 dark:shadow-none"
													: "bg-white border-gray-300 text-gray-600 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-700",
											)}
										>
											{status === "complete"
												? "Selesai"
												: status === "pending"
													? "Menunggu"
													: status === "failed"
														? "Gagal"
														: status === "expired"
															? "Kadaluarsa"
															: status}
											{isSelected && (
												<div className="w-1.5 h-1.5 rounded-full bg-white animate-pulse" />
											)}
										</button>
									);
								})}
							</div>
						</div>

						{/* Gateway Filter - Pro Only */}
						{isProActive && (
							<div className="flex flex-col gap-1.5">
								<span className="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
									Gateway
								</span>
								<div className="flex gap-2">
									{activeGateways.map((gateway) => {
										const isSelected = selectedGateways.includes(gateway.id);
										return (
											<button
												type="button"
												key={gateway.id}
												onClick={() => toggleGateway(gateway.id)}
												className={clsx(
													"px-3 py-2 rounded-lg text-sm font-medium border transition-all flex items-center gap-1.5",
													isSelected
														? "bg-purple-600 border-purple-600 text-white shadow-md shadow-purple-200 dark:shadow-none"
														: "bg-white border-gray-300 text-gray-600 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-700",
												)}
											>
												{gateway.label}
												{isSelected && (
													<div className="w-1.5 h-1.5 rounded-full bg-white animate-pulse" />
												)}
											</button>
										);
									})}
								</div>
							</div>
						)}

						{/* Recurring Filter - Pro Only */}
						{isProActive && (
							<div className="flex flex-col gap-1.5">
								<span className="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
									Tipe
								</span>
								<div className="flex gap-2">
									{[
										{ id: "", label: "Semua" },
										{ id: "one-time", label: "Sekali" },
										{ id: "recurring", label: "Berlangganan" },
									].map((option) => {
										const isSelected = recurringFilter === option.id;
										return (
											<button
												type="button"
												key={option.id}
												onClick={() => {
													setPage(1);
													setRecurringFilter(option.id);
												}}
												className={clsx(
													"px-3 py-2 rounded-lg text-sm font-medium border transition-all flex items-center gap-1.5",
													isSelected
														? "bg-emerald-600 border-emerald-600 text-white shadow-md shadow-emerald-200 dark:shadow-none"
														: "bg-white border-gray-300 text-gray-600 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-700",
												)}
											>
												{option.label}
												{isSelected && option.id !== "" && (
													<div className="w-1.5 h-1.5 rounded-full bg-white animate-pulse" />
												)}
											</button>
										);
									})}
								</div>
							</div>
						)}
					</div>

					<div className="flex flex-wrap sm:flex-nowrap justify-between items-end gap-4 border-t border-gray-100 pt-4">
						{/* Campaign Filter (Custom Dropdown) */}
						<div className="flex flex-col gap-1.5 w-full sm:w-auto relative">
							<span className="text-xs font-bold text-gray-500 uppercase tracking-wide">
								Kampanye
							</span>
							<div className="relative">
								<button
									type="button"
									onClick={() =>
										setIsCampaignDropdownOpen(!isCampaignDropdownOpen)
									}
									className="w-full sm:w-[300px] flex justify-between items-center px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm text-gray-700 hover:border-gray-400 focus:ring-2 focus:ring-blue-100 transition-colors text-left dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200 dark:focus:ring-blue-900"
								>
									<span className="truncate">
										{selectedCampaigns.length === 0
											? "Semua Kampanye"
											: `${selectedCampaigns.length} Kampanye Terpilih`}
									</span>
									<ChevronDown
										size={16}
										className={clsx(
											"text-gray-400 transition-transform",
											isCampaignDropdownOpen && "rotate-180",
										)}
									/>
								</button>

								{isCampaignDropdownOpen && (
									<>
										<button
											type="button"
											className="fixed inset-0 z-10 w-full h-full cursor-default bg-transparent border-none"
											aria-label="Close dropdown"
											onClick={() => setIsCampaignDropdownOpen(false)}
										/>
										<div className="absolute top-full mt-2 left-0 w-full sm:w-[300px] bg-white border border-gray-200 rounded-xl shadow-xl z-20 max-h-[300px] overflow-y-auto p-2 dark:bg-gray-800 dark:border-gray-700">
											{campaigns?.map((c: any) => (
												<label
													htmlFor={`campaign-${c.id}`}
													key={c.id}
													className="flex items-start gap-3 p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg cursor-pointer transition-colors"
												>
													<div className="relative flex items-center mt-0.5">
														<input
															id={`campaign-${c.id}`}
															type="checkbox"
															className="peer h-4 w-4 border-gray-300 rounded text-blue-600 focus:ring-blue-500"
															checked={selectedCampaigns.includes(String(c.id))}
															onChange={() => toggleCampaign(String(c.id))}
														/>
													</div>
													<span className="text-sm text-gray-700 dark:text-gray-200 leading-snug select-none">
														{c.title}
													</span>
												</label>
											))}
											{(!campaigns || campaigns.length === 0) && (
												<div className="p-3 text-sm text-gray-500 text-center">
													Tidak ada kampanye aktif
												</div>
											)}
										</div>
									</>
								)}
							</div>
						</div>

						<div className="grow"></div>

						<a
							href={getExportUrl()}
							target="_blank"
							className="w-full sm:w-auto px-6 py-2.5 bg-gray-900 text-white! rounded-xl hover:bg-black font-bold text-sm flex justify-center items-center gap-2 shadow-lg hover:shadow-xl transition-all active:scale-95"
							rel="noreferrer"
						>
							<svg
								width="18"
								height="18"
								fill="none"
								viewBox="0 0 24 24"
								stroke="currentColor"
							>
								<title>Download CSV</title>
								<path
									strokeLinecap="round"
									strokeLinejoin="round"
									strokeWidth="2"
									d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
								/>
							</svg>
							Download CSV
						</a>
					</div>
				</div>
			</div>

			<div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden dark:bg-gray-800 dark:border-gray-700">
				<table className="w-full text-left text-sm text-gray-600 dark:text-gray-300">
					<thead className="bg-gray-50 border-b border-gray-200 font-medium text-gray-900 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-200">
						<tr>
							<th className="px-6 py-4">ID</th>
							<th className="px-6 py-4">Donatur</th>
							<th className="px-6 py-4">Jumlah</th>
							<th className="px-6 py-4">Status</th>
							<th className="px-6 py-4">Tanggal</th>
							<th className="px-6 py-4 text-right">Aksi</th>
						</tr>
					</thead>
					<tbody className="divide-y divide-gray-200">
						{isLoading ? (
							<tr>
								<td colSpan={6} className="px-6 py-4 text-center">
									Memuat...
								</td>
							</tr>
						) : donations && donations.length > 0 ? (
							donations.map((donation: Donation) => (
								<tr
									key={donation.id}
									className="hover:bg-gray-50 dark:hover:bg-gray-700/50"
								>
									<td className="px-6 py-4 font-medium text-gray-900 dark:text-gray-100">
										#{donation.id}
									</td>
									<td className="px-6 py-4">
										<div>{donation.name}</div>
										<div className="text-xs text-gray-500">
											{donation.email}
										</div>
									</td>
									<td className="px-6 py-4">
										Rp {donation.amount.toLocaleString("id-ID")}
									</td>
									<td className="px-6 py-4 capitalize">
										<span
											className={clsx(
												"px-2 py-1 rounded-full text-xs font-semibold",
												donation.status === "complete"
													? "bg-green-100 text-green-700"
													: donation.status === "pending"
														? "bg-yellow-100 text-yellow-700"
														: donation.status === "failed"
															? "bg-red-100 text-red-700"
															: "bg-gray-100 text-gray-700", // expired
											)}
										>
											{donation.status === "complete"
												? "Selesai"
												: donation.status === "pending"
													? "Menunggu"
													: donation.status === "failed"
														? "Gagal"
														: donation.status === "expired"
															? "Kadaluarsa"
															: donation.status}
										</span>
									</td>
									<td className="px-6 py-4">{donation.date}</td>
									<td className="px-6 py-4 text-right flex justify-end gap-2">
										<button
											type="button"
											onClick={() => setSelectedDonation(donation)}
											className="text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 p-1 rounded hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
											title="Lihat Detail"
										>
											<Eye size={18} />
										</button>

										{donation.status === "pending" && (
											<button
												type="button"
												onClick={() =>
													mutation.mutate({
														id: donation.id,
														data: { status: "complete" },
													})
												}
												disabled={mutation.isPending}
												className="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300 p-1 rounded hover:bg-green-50 dark:hover:bg-green-900/20 transition-colors"
												title="Tandai Selesai"
											>
												<CheckCircle size={18} />
											</button>
										)}
									</td>
								</tr>
							))
						) : (
							<tr>
								<td colSpan={6} className="px-6 py-4 text-center">
									Tidak ada donasi ditemukan.
								</td>
							</tr>
						)}
					</tbody>
				</table>

				{/* Pagination Controls */}
				<div className="px-6 py-4 border-t border-gray-200 flex items-center justify-between bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
					<span className="text-sm text-gray-500">
						Menampilkan {donations.length} dari {meta.total} data
					</span>
					<div className="flex gap-2">
						<button
							type="button"
							onClick={() => setPage((p) => Math.max(1, p - 1))}
							disabled={page === 1}
							className="px-3 py-1 bg-white border border-gray-300 rounded text-sm disabled:opacity-50 hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-600"
						>
							Previous
						</button>
						<span className="px-3 py-1 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
							Page {page} of {meta.total_pages || 1}
						</span>
						<button
							type="button"
							onClick={() =>
								setPage((p) =>
									meta.total_pages && p < meta.total_pages ? p + 1 : p,
								)
							}
							disabled={!meta.total_pages || page >= meta.total_pages}
							className="px-3 py-1 bg-white border border-gray-300 rounded text-sm disabled:opacity-50 hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-600"
						>
							Next
						</button>
					</div>
				</div>
			</div>

			{/* Donation Detail Modal */}
			{selectedDonation && (
				<div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm animate-in fade-in duration-200">
					<div className="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden animate-in zoom-in-95 duration-200 flex flex-col max-h-[90vh] dark:bg-gray-800 dark:border dark:border-gray-700">
						<div className="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 shrink-0 dark:bg-gray-900 dark:border-gray-700">
							<h3 className="text-lg font-bold text-gray-800 dark:text-gray-100">
								{isEditing
									? `Edit Donasi #${selectedDonation.id}`
									: `Detail Donasi #${selectedDonation.id}`}
							</h3>
							<div className="flex items-center gap-2">
								{!isEditing && (
									<button
										onClick={handleEditClick}
										className="text-gray-500 hover:text-blue-600 p-1.5 rounded-full hover:bg-blue-50 transition-colors"
										title="Edit"
										type="button"
									>
										<Pencil size={18} />
									</button>
								)}
								<button
									onClick={() => {
										setSelectedDonation(null);
										setIsEditing(false);
									}}
									className="text-gray-400 hover:text-gray-600 p-1 rounded-full hover:bg-gray-200 transition-colors"
									type="button"
								>
									<X size={20} />
								</button>
							</div>
						</div>

						<div className="p-6 space-y-4 overflow-y-auto">
							{isEditing ? (
								<div className="grid grid-cols-1 gap-4">
									<div>
										<label
											htmlFor="edit-name"
											className="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 dark:text-gray-400"
										>
											Nama Donatur
										</label>
										<input
											id="edit-name"
											type="text"
											value={editFormData.name || ""}
											onChange={(e) =>
												setEditFormData((prev) => ({
													...prev,
													name: e.target.value,
												}))
											}
											className="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
										/>
									</div>
									<div className="grid grid-cols-2 gap-4">
										<div>
											<label
												htmlFor="edit-amount"
												className="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 dark:text-gray-400"
											>
												Jumlah
											</label>
											<input
												id="edit-amount"
												type="number"
												value={editFormData.amount || 0}
												onChange={(e) =>
													setEditFormData((prev) => ({
														...prev,
														amount: Number.parseFloat(e.target.value),
													}))
												}
												className="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
											/>
										</div>
										<div>
											<label
												htmlFor="edit-status"
												className="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 dark:text-gray-400"
											>
												Status
											</label>
											<select
												id="edit-status"
												value={editFormData.status || "pending"}
												onChange={(e) =>
													setEditFormData((prev) => ({
														...prev,
														status: e.target.value as any,
													}))
												}
												className="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
											>
												<option value="pending">Menunggu</option>
												<option value="complete">Selesai</option>
												<option value="failed">Gagal</option>
												<option value="expired">Kadaluarsa</option>
											</select>
										</div>
									</div>
									<div>
										<label
											htmlFor="edit-email"
											className="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 dark:text-gray-400"
										>
											Email
										</label>
										<input
											id="edit-email"
											type="email"
											value={editFormData.email || ""}
											onChange={(e) =>
												setEditFormData((prev) => ({
													...prev,
													email: e.target.value,
												}))
											}
											className="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
										/>
									</div>
									<div>
										<label
											htmlFor="edit-phone"
											className="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 dark:text-gray-400"
										>
											Telepon
										</label>
										<input
											id="edit-phone"
											type="text"
											value={editFormData.phone || ""}
											onChange={(e) =>
												setEditFormData((prev) => ({
													...prev,
													phone: e.target.value,
												}))
											}
											className="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
										/>
									</div>
									<div>
										<label
											htmlFor="edit-note"
											className="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 dark:text-gray-400"
										>
											Catatan
										</label>
										<textarea
											id="edit-note"
											value={editFormData.note || ""}
											onChange={(e) =>
												setEditFormData((prev) => ({
													...prev,
													note: e.target.value,
												}))
											}
											className="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100"
											rows={3}
										/>
									</div>
								</div>
							) : (
								<div className="space-y-4">
									<div className="grid grid-cols-2 gap-4">
										<div>
											<p className="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 dark:text-gray-400">
												Nama Donatur
											</p>
											<p className="font-medium text-gray-900 dark:text-gray-100">
												{selectedDonation.name}
											</p>
										</div>
										<div>
											<p className="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 dark:text-gray-400">
												Jumlah
											</p>
											<p className="font-medium text-green-600 text-lg dark:text-green-400">
												Rp {selectedDonation.amount.toLocaleString("id-ID")}
											</p>
										</div>
										<div>
											<p className="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 dark:text-gray-400">
												Email
											</p>
											<p className="text-gray-700 break-all dark:text-gray-300">
												{selectedDonation.email}
											</p>
										</div>
										<div>
											<p className="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 dark:text-gray-400">
												Catatan
											</p>
											<p className="text-gray-700 italic dark:text-gray-300">
												{selectedDonation.note || "-"}
											</p>
										</div>
										<div>
											<p className="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 dark:text-gray-400">
												Metode Pembayaran
											</p>
											<p className="capitalize text-gray-700 dark:text-gray-300">
												{selectedDonation.payment_method}
											</p>
										</div>
										<div>
											<p className="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 dark:text-gray-400">
												Status
											</p>
											<span
												className={clsx(
													"px-2 py-0.5 rounded text-xs font-medium",
													selectedDonation.status === "complete"
														? "bg-green-100 text-green-700"
														: selectedDonation.status === "pending"
															? "bg-yellow-100 text-yellow-700"
															: selectedDonation.status === "failed"
																? "bg-red-100 text-red-700"
																: "bg-gray-100 text-gray-700",
												)}
											>
												{selectedDonation.status === "complete"
													? "Selesai"
													: selectedDonation.status === "pending"
														? "Menunggu"
														: selectedDonation.status === "failed"
															? "Gagal"
															: selectedDonation.status === "expired"
																? "Kadaluarsa"
																: selectedDonation.status}
											</span>
										</div>
									</div>

									{/* Confirmation Details (Metadata) */}
									{selectedDonation.metadata &&
										(selectedDonation.metadata.sender_bank ||
											selectedDonation.metadata.proof_url ||
											selectedDonation.metadata.sender_name) && (
											<div className="bg-blue-50 p-4 rounded-lg border border-blue-100 dark:bg-blue-950 dark:border-blue-900">
												<h4 className="text-sm font-bold text-blue-800 mb-3 flex items-center gap-2 dark:text-blue-200">
													<CheckCircle size={16} /> Konfirmasi Pembayaran
												</h4>
												<div className="grid grid-cols-2 gap-y-3 gap-x-4 text-sm">
													{selectedDonation.metadata?.sender_name && (
														<div>
															<p className="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 dark:text-gray-400">
																Nama Pengirim
															</p>
															<p className="text-gray-900 dark:text-gray-100">
																{selectedDonation.metadata.sender_name}
															</p>
														</div>
													)}
													{selectedDonation.metadata?.sender_bank && (
														<div>
															<p className="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 dark:text-gray-400">
																Bank Pengirim
															</p>
															<p className="text-gray-900 dark:text-gray-100">
																{selectedDonation.metadata.sender_bank}
															</p>
														</div>
													)}
													{selectedDonation.metadata?.proof_url && (
														<div className="col-span-2 pt-2 border-t border-blue-100 mt-1 dark:border-blue-900">
															<a
																href={selectedDonation.metadata.proof_url}
																target="_blank"
																rel="noopener noreferrer"
																className="text-blue-600 underline hover:text-blue-800 font-medium flex items-center gap-1 dark:text-blue-400 dark:hover:text-blue-300"
															>
																<Eye size={14} /> Lihat Bukti Transfer
															</a>
														</div>
													)}
												</div>
											</div>
										)}

									{selectedDonation.gateway_txn_id && (
										<div className="bg-gray-50 p-3 rounded border border-gray-200 dark:bg-gray-900 dark:border-gray-700">
											<p className="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 dark:text-gray-400">
												ID Transaksi
											</p>
											<code className="text-sm text-gray-800 dark:text-gray-200">
												{selectedDonation.gateway_txn_id}
											</code>
										</div>
									)}

									{selectedDonation.metadata?.proof_url && (
										<div className="col-span-2">
											<p className="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 dark:text-gray-400">
												Bukti Transfer
											</p>
											<a
												href={selectedDonation.metadata.proof_url}
												target="_blank"
												rel="noopener noreferrer"
												className="block w-full h-48 bg-gray-100 rounded-lg overflow-hidden border border-gray-200 flex items-center justify-center dark:bg-gray-700 dark:border-gray-600"
											>
												<img
													src={selectedDonation.metadata.proof_url}
													alt="Bukti Transfer"
													className="object-contain max-h-full max-w-full"
												/>
											</a>
										</div>
									)}

									{selectedDonation.note && (
										<div>
											<p className="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 dark:text-gray-400">
												Catatan
											</p>
											<p className="text-sm text-gray-600 italic bg-gray-50 p-3 rounded border border-gray-100 dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
												"{selectedDonation.note}"
											</p>
										</div>
									)}

									<div className="pt-2 text-xs text-gray-400 text-center">
										Dibuat pada: {selectedDonation.date}
									</div>
								</div>
							)}
						</div>

						<div className="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-2 shrink-0 dark:bg-gray-900 dark:border-gray-700">
							{isEditing ? (
								<>
									<button
										onClick={handleCancel}
										className="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
										disabled={mutation.isPending}
										type="button"
									>
										Batal
									</button>
									<button
										onClick={handleSave}
										disabled={mutation.isPending}
										className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm flex items-center gap-2"
										type="button"
									>
										{mutation.isPending ? (
											"Menyimpan..."
										) : (
											<>
												<Save size={16} /> Simpan Perubahan
											</>
										)}
									</button>
								</>
							) : (
								<>
									{selectedDonation.status === "pending" && (
										<button
											onClick={() =>
												mutation.mutate({
													id: selectedDonation.id,
													data: { status: "complete" },
												})
											}
											className="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium text-sm flex items-center gap-2"
											disabled={mutation.isPending}
											type="button"
										>
											<CheckCircle size={16} /> Tandai Selesai
										</button>
									)}
									<button
										onClick={() => setSelectedDonation(null)}
										className="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
										type="button"
									>
										Tutup
									</button>
								</>
							)}
						</div>
					</div>
				</div>
			)}

			{/* Manual Donation Modal (Pro) */}
			<ManualDonationModal
				isOpen={isManualDonationOpen}
				onClose={() => setIsManualDonationOpen(false)}
				onSuccess={() =>
					queryClient.invalidateQueries({ queryKey: ["donations"] })
				}
			/>
		</div>
	);
}
