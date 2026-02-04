import { ChevronDown, ExternalLink, Loader2, RefreshCw } from "lucide-react";
import { useEffect, useState } from "react";
import { useCampaigns } from "../campaign-template/hooks/use-campaigns";
import type { DonationFormTemplate } from "./hooks/use-donation-form-template";

interface DonationPreviewProps {
	template: DonationFormTemplate | undefined;
	lastSavedAt?: number;
}

export function DonationPreview({
	template,
	lastSavedAt,
}: DonationPreviewProps) {
	const [previewUrl, setPreviewUrl] = useState<string | null>(null);
	const [iframeLoading, setIframeLoading] = useState(true);
	const [refreshKey, setRefreshKey] = useState(0);
	const [selectedCampaignId, setSelectedCampaignId] = useState<string>("");

	const { data: campaigns = [], isLoading: isFetchingCampaigns } =
		useCampaigns();

	// Initialize selection
	useEffect(() => {
		if (campaigns.length > 0 && !selectedCampaignId) {
			const savedId = localStorage.getItem("donasai_preview_campaign_id");
			const foundSaved = campaigns.find((c) => String(c.id) === savedId);

			if (foundSaved && template) {
				setSelectedCampaignId(String(foundSaved.id));
				setPreviewUrl(
					`${foundSaved.link.replace(/\/$/, "")}/${template.payment_slug}/`,
				);
			} else if (campaigns[0] && template) {
				setSelectedCampaignId(String(campaigns[0].id));
				setPreviewUrl(
					`${campaigns[0].link.replace(/\/$/, "")}/${template.payment_slug}/`,
				);
			}
		} else if (!isFetchingCampaigns && campaigns.length === 0 && !previewUrl) {
			// Fallback if no campaigns found
			setPreviewUrl((window as any).donasaiSettings?.siteUrl || "/");
		}
	}, [
		campaigns,
		isFetchingCampaigns,
		selectedCampaignId,
		previewUrl,
		template,
	]);

	// Handle selection change
	const handleCampaignChange = (campaignId: string) => {
		setSelectedCampaignId(campaignId);
		localStorage.setItem("donasai_preview_campaign_id", campaignId);

		const campaign = campaigns.find((c) => String(c.id) === campaignId);
		if (campaign && template) {
			const url = `${campaign.link.replace(/\/$/, "")}/${template.payment_slug}/`;
			setPreviewUrl(url);
			setIframeLoading(true);
			setRefreshKey((k) => k + 1); // Force reload iframe
		}
	};

	// Auto-refresh when lastSavedAt changes
	useEffect(() => {
		if (lastSavedAt && previewUrl) {
			setIframeLoading(true);
			setRefreshKey((k) => k + 1);
		}
	}, [lastSavedAt, previewUrl]);

	const handleRefresh = () => {
		setIframeLoading(true);
		setRefreshKey((k) => k + 1);
	};

	return (
		<div className="relative h-full flex flex-col">
			{/* Toolbar - Top Right Overlay */}
			<div className="absolute top-4 right-4 z-10 flex items-center gap-2">
				{/* Campaign Selector */}
				<div className="relative group">
					<div className="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none">
						<ChevronDown size={14} className="text-gray-400" />
					</div>
					<select
						value={selectedCampaignId}
						onChange={(e) => handleCampaignChange(e.target.value)}
						className="appearance-none h-9 pl-3 pr-8 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-emerald-500 shadow-sm cursor-pointer min-w-[200px]"
					>
						{isFetchingCampaigns && <option>Memuat campaign...</option>}
						{!isFetchingCampaigns && campaigns.length === 0 && (
							<option>Tidak ada campaign</option>
						)}
						{campaigns.map((c) => (
							<option key={c.id} value={c.id}>
								{c.title || "(Tanpa Judul)"}
							</option>
						))}
					</select>
					<div className="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none">
						<ChevronDown size={14} className="text-gray-400" />
					</div>
				</div>

				{/* Open External Link */}
				{previewUrl && (
					<a
						href={previewUrl}
						target="_blank"
						rel="noreferrer"
						className="p-2 bg-white/90 dark:bg-gray-800/90 rounded-lg text-gray-500 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950 transition-colors shadow-sm"
						title="Buka di tab baru"
					>
						<ExternalLink size={16} />
					</a>
				)}

				{/* Refresh Button */}
				<button
					type="button"
					onClick={handleRefresh}
					className="p-2 bg-white/90 dark:bg-gray-800/90 rounded-lg text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950 transition-colors shadow-sm"
					title="Refresh preview"
				>
					<RefreshCw size={16} />
				</button>
			</div>

			{/* Preview Content */}
			<div className="flex-1 overflow-auto bg-gray-100 dark:bg-gray-900">
				{previewUrl ? (
					<>
						{iframeLoading && (
							<div className="absolute inset-0 flex items-center justify-center bg-white/80 dark:bg-gray-900/80 z-10 transition-opacity duration-300">
								<div className="text-center">
									<Loader2 className="w-10 h-10 text-emerald-600 animate-spin mx-auto mb-3" />
									<p className="text-sm text-gray-500 dark:text-gray-400">
										Memuat formulir donasi...
									</p>
								</div>
							</div>
						)}
						<iframe
							key={refreshKey}
							title="Donation Form Preview"
							src={previewUrl}
							className="w-full h-full min-h-[700px] bg-white shadow-sm"
							style={{ border: "none", display: "block" }}
							onLoad={() => setIframeLoading(false)}
						/>
					</>
				) : (
					<div className="h-full flex flex-col items-center justify-center text-gray-400 p-8 text-center">
						<div className="bg-gray-200 dark:bg-gray-800 p-4 rounded-full mb-4">
							<ExternalLink size={32} />
						</div>
						<p>Pilih campaign untuk melihat preview formulir donasi</p>
					</div>
				)}
			</div>
		</div>
	);
}
