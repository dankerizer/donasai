// Fee Coverage Settings Section (Pro Only)

import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { Crown, DollarSign, Loader2, Save } from "lucide-react";
import { useEffect, useState } from "react";
import { toast } from "sonner";
import { Input } from "@/components/ui/Input";
import { Label } from "@/components/ui/Label";

interface GatewayFeeConfig {
	percentage: number;
	fixed: number;
	label: string;
}

interface FeeCoverageSettings {
	enabled: boolean;
	default_checked: boolean;
	gateways: Record<string, GatewayFeeConfig>;
}

export default function FeeCoverageSection() {
	const queryClient = useQueryClient();
	const [settings, setSettings] = useState<FeeCoverageSettings>({
		enabled: false,
		default_checked: false,
		gateways: {
			midtrans: { percentage: 2.9, fixed: 2000, label: "Biaya Admin Midtrans" },
			xendit: { percentage: 2.5, fixed: 2500, label: "Biaya Admin Xendit" },
			doku: { percentage: 2.0, fixed: 3000, label: "Biaya Admin DOKU" },
			manual: { percentage: 0, fixed: 0, label: "Biaya Admin" },
		},
	});

	// Check Pro status
	const isPro = (window as unknown as Record<string, unknown>).wpdSettings as
		| Record<string, unknown>
		| undefined;
	const proSettings = (window as unknown as Record<string, unknown>)
		.wpdProSettings as Record<string, string> | undefined;
	const isProActive =
		isPro?.isPro &&
		(proSettings?.licenseStatus === "active" ||
			proSettings?.licenseStatus === "valid");

	// Fetch current settings
	const { data: fetchedSettings, isLoading } = useQuery<FeeCoverageSettings>({
		queryKey: ["fee-coverage-settings"],
		queryFn: async () => {
			const res = await fetch("/wp-json/wpd-pro/v1/settings/fee-coverage", {
				headers: {
					"X-WP-Nonce": (isPro?.nonce as string) || "",
				},
			});
			return res.json();
		},
		enabled: !!isProActive,
	});

	useEffect(() => {
		if (fetchedSettings) {
			setSettings(fetchedSettings);
		}
	}, [fetchedSettings]);

	// Save mutation
	const saveMutation = useMutation({
		mutationFn: async (data: FeeCoverageSettings) => {
			const res = await fetch("/wp-json/wpd-pro/v1/settings/fee-coverage", {
				method: "POST",
				headers: {
					"Content-Type": "application/json",
					"X-WP-Nonce": (isPro?.nonce as string) || "",
				},
				body: JSON.stringify(data),
			});
			return res.json();
		},
		onSuccess: () => {
			toast.success("Pengaturan Fee Coverage berhasil disimpan!");
			queryClient.invalidateQueries({ queryKey: ["fee-coverage-settings"] });
		},
		onError: () => {
			toast.error("Gagal menyimpan pengaturan.");
		},
	});

	const updateGateway = (
		gateway: string,
		field: keyof GatewayFeeConfig,
		value: string | number,
	) => {
		setSettings((prev) => ({
			...prev,
			gateways: {
				...prev.gateways,
				[gateway]: {
					...prev.gateways[gateway],
					[field]: field === "label" ? value : Number(value),
				},
			},
		}));
	};

	if (!isProActive) {
		return (
			<div className="bg-purple-50 border border-purple-200 rounded-xl p-6 text-center">
				<Crown className="mx-auto mb-3 text-purple-500" size={32} />
				<h4 className="text-lg font-bold text-purple-900 mb-2">
					Fee Coverage (Pro)
				</h4>
				<p className="text-sm text-purple-700 mb-4">
					Aktifkan fitur ini agar donatur dapat memilih untuk menanggung biaya
					admin pembayaran.
				</p>
			</div>
		);
	}

	if (isLoading) {
		return (
			<div className="flex items-center justify-center py-8">
				<Loader2 className="animate-spin text-gray-400" size={24} />
			</div>
		);
	}

	return (
		<div className="space-y-6">
			<div className="flex items-center justify-between">
				<div>
					<h3 className="text-lg font-semibold text-gray-900 flex! items-center gap-2 ">
						<DollarSign size={20} className="text-green-600" />
						Fee Coverage
					</h3>
					<p className="text-sm text-gray-500 mt-1">
						Biarkan donatur menanggung biaya admin payment gateway.
					</p>
				</div>
				<label className="relative inline-flex items-center cursor-pointer">
					<input
						type="checkbox"
						checked={settings.enabled}
						onChange={(e) =>
							setSettings((prev) => ({ ...prev, enabled: e.target.checked }))
						}
						className="sr-only peer"
					/>
					<div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600" />
				</label>
			</div>

			{settings.enabled && (
				<>
					<div className="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
						<input
							type="checkbox"
							id="default_checked"
							checked={settings.default_checked}
							onChange={(e) =>
								setSettings((prev) => ({
									...prev,
									default_checked: e.target.checked,
								}))
							}
							className="w-4 h-4 text-green-600 rounded"
						/>
						<label htmlFor="default_checked" className="text-sm text-gray-700">
							Checkbox dicentang secara default
						</label>
					</div>

					<div className="space-y-4">
						<h4 className="text-sm font-bold text-gray-700 uppercase tracking-wide">
							Pengaturan Fee Per Gateway
						</h4>

						{Object.entries(settings.gateways).map(([gateway, config]) => (
							<div
								key={gateway}
								className="p-4 border border-gray-200 rounded-lg bg-white"
							>
								<h5 className="font-medium text-gray-900 capitalize mb-3">
									{gateway === "manual" ? "Transfer Bank" : gateway}
								</h5>
								<div className="grid grid-cols-3 gap-3">
									<div>
										<Label className="text-xs">Persentase (%)</Label>
										<Input
											type="number"
											step="0.1"
											min="0"
											value={config.percentage}
											onChange={(e) =>
												updateGateway(gateway, "percentage", e.target.value)
											}
										/>
									</div>
									<div>
										<Label className="text-xs">Fixed (Rp)</Label>
										<Input
											type="number"
											min="0"
											value={config.fixed}
											onChange={(e) =>
												updateGateway(gateway, "fixed", e.target.value)
											}
										/>
									</div>
									<div>
										<Label className="text-xs">Label</Label>
										<Input
											type="text"
											value={config.label}
											onChange={(e) =>
												updateGateway(gateway, "label", e.target.value)
											}
										/>
									</div>
								</div>
								<p className="text-xs text-gray-500 mt-2">
									Formula: (Nominal × {config.percentage}%) + Rp{" "}
									{config.fixed.toLocaleString()}
								</p>
							</div>
						))}
					</div>

					<button
						type="button"
						onClick={() => saveMutation.mutate(settings)}
						disabled={saveMutation.isPending}
						className="w-full py-3 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 transition flex items-center justify-center gap-2 disabled:opacity-50"
					>
						{saveMutation.isPending ? (
							<Loader2 size={18} className="animate-spin" />
						) : (
							<Save size={18} />
						)}
						Simpan Pengaturan Fee Coverage
					</button>
				</>
			)}
		</div>
	);
}
