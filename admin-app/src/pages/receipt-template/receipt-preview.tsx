/** biome-ignore-all lint/security/noDangerouslySetInnerHtml: harus html */
import { Loader2, RefreshCw } from "lucide-react";
import { useEffect } from "react";
import type { ReceiptTemplate } from "./hooks/use-receipt-template";

interface ReceiptPreviewProps {
	template: ReceiptTemplate | undefined;
	previewHtml?: { success: boolean; html: string };
	onGeneratePreview: (template: ReceiptTemplate) => void;
}

export function ReceiptPreview({
	template,
	previewHtml,
	onGeneratePreview,
}: ReceiptPreviewProps) {
	// Generate preview when template changes (debounced)
	useEffect(() => {
		if (!template) return;

		const timer = setTimeout(() => {
			onGeneratePreview(template);
		}, 500);

		return () => clearTimeout(timer);
	}, [template, onGeneratePreview]);

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
			{/* Floating Refresh - Top Right */}
			<div className="absolute top-4 right-4 z-10">
				<button
					type="button"
					onClick={() => onGeneratePreview(template)}
					className="p-2 bg-white/80 dark:bg-gray-800/80 rounded-lg text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950 transition-colors flex items-center gap-2"
					title="Refresh preview"
				>
					<RefreshCw size={16} />
					<span className="text-xs font-medium">Refresh</span>
				</button>
			</div>

			{/* Preview Content */}
			<div className="flex-1 overflow-auto">
				{previewHtml?.html ? (
					<iframe
						title="Receipt Preview"
						srcDoc={previewHtml.html}
						className="w-full h-full min-h-[700px] bg-white"
						style={{ border: "none", display: "block" }}
					/>
				) : (
					<div className="flex flex-col items-center justify-center h-full">
						<Loader2 className="w-12 h-12 text-emerald-600 animate-spin mb-4" />
						<p className="text-gray-500 dark:text-gray-400">
							Generating preview...
						</p>
					</div>
				)}
			</div>

			{/* Floating Info - Bottom */}
			<div className="absolute bottom-4 left-4 right-4 z-10">
				<div className="bg-white/90 dark:bg-gray-800/90 rounded-lg px-3 py-2 flex items-center gap-2">
					<span className="text-gray-400">ℹ️</span>
					<p className="text-xs text-gray-500 dark:text-gray-400">
						Preview menggunakan data contoh. Kuitansi sebenarnya menggunakan data donasi real.
					</p>
				</div>
			</div>
		</div>
	);
}
