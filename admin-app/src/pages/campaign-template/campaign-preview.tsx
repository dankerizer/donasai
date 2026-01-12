import { ChevronDown, ExternalLink, Loader2, RefreshCw } from "lucide-react";
import { useEffect, useState } from "react";
import type { CampaignTemplate } from "./hooks/use-campaign-template";
import { useCampaigns } from "./hooks/use-campaigns";

interface CampaignPreviewProps {
	template: CampaignTemplate | undefined;
	lastSavedAt?: number;
}

export function CampaignPreview({
	template,
	lastSavedAt,
}: CampaignPreviewProps) {
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

			if (foundSaved) {
				setSelectedCampaignId(String(foundSaved.id));
				setPreviewUrl(foundSaved.link);
			} else {
				setSelectedCampaignId(String(campaigns[0].id));
				setPreviewUrl(campaigns[0].link);
			}
		} else if (!isFetchingCampaigns && campaigns.length === 0 && !previewUrl) {
			// Fallback if no campaigns found
			setPreviewUrl((window as any).wpdSettings?.siteUrl || "/");
		}
	}, [campaigns, isFetchingCampaigns, selectedCampaignId, previewUrl]);

	// Handle selection change
	const handleCampaignChange = (campaignId: string) => {
		setSelectedCampaignId(campaignId);
		localStorage.setItem("donasai_preview_campaign_id", campaignId);

		const campaign = campaigns.find((c) => String(c.id) === campaignId);
		if (campaign) {
			setPreviewUrl(campaign.link);
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

	if (!template) {
		return (
			<div className="h-full flex items-center justify-center">
				<div className="text-center">
					<Loader2 className="w-12 h-12 text-emerald-600 animate-spin mx-auto mb-4" />
					<p className="text-gray-500 dark:text-gray-400">Memuat template...</p>
				</div>
			</div>
		);
	}

	return (
		<div className="relative h-full flex flex-col">
			{/* Toolbar - Top Right Overlay */}
			<div className="absolute top-4 right-4 z-10 flex items-center gap-2">
				{/* Campaign Selector */}
				<div className="relative group">
					<div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
						{isFetchingCampaigns ? (
							<Loader2 size={14} className="animate-spin text-gray-400" />
						) : (
							<ExternalLink size={14} className="text-gray-400" />
						)}
					</div>
					<select
						value={selectedCampaignId}
						onChange={(e) => handleCampaignChange(e.target.value)}
						disabled={isFetchingCampaigns || campaigns.length === 0}
						className="pl-9 pr-8 py-2 bg-white/90 dark:bg-gray-800/90 text-sm border-0 rounded-lg shadow-sm hover:bg-white dark:hover:bg-gray-800 transition-colors cursor-pointer appearance-none outline-none focus:ring-2 focus:ring-emerald-500 text-gray-700 dark:text-gray-200 min-w-[200px] max-w-[300px] truncate"
					>
						{campaigns.length === 0 && !isFetchingCampaigns && (
							<option value="">Tidak ada campaign</option>
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
							<div className="absolute inset-0 flex items-center justify-center bg-white/80 dark:bg-gray-900/80 z-10">
								<div className="text-center">
									<Loader2 className="w-10 h-10 text-emerald-600 animate-spin mx-auto mb-3" />
									<p className="text-sm text-gray-500 dark:text-gray-400">
										Memuat preview halaman campaign...
									</p>
								</div>
							</div>
						)}
						<iframe
							key={refreshKey}
							title="Campaign Preview"
							src={previewUrl}
							className="w-full h-full min-h-[700px] bg-white shadow-sm"
							style={{ border: "none", display: "block" }}
							onLoad={() => setIframeLoading(false)}
						/>
					</>
				) : (
					<div className="flex flex-col items-center justify-center h-full">
						{isFetchingCampaigns ? (
							<>
								<Loader2 className="w-12 h-12 text-emerald-600 animate-spin mb-4" />
								<p className="text-gray-500 dark:text-gray-400">
									Mencari data campaign...
								</p>
							</>
						) : (
							<div className="text-center max-w-sm px-4">
								<p className="text-gray-500 dark:text-gray-400 mb-2">
									Belum ada campaign yang dibuat.
								</p>
								<p className="text-sm text-gray-400">
									Buat campaign terlebih dahulu untuk melihat preview.
								</p>
							</div>
						)}
					</div>
				)}
			</div>
		</div>
	);
}
