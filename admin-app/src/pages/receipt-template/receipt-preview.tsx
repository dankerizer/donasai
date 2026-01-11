/** biome-ignore-all lint/security/noDangerouslySetInnerHtml: harus html*/
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
			<div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-8 flex items-center justify-center min-h-[600px] shadow-sm">
				<div className="text-center">
					<Loader2 className="w-12 h-12 text-emerald-600 animate-spin mx-auto mb-4" />
					<p className="text-gray-500 dark:text-gray-400">Memuat template...</p>
				</div>
			</div>
		);
	}

	return (
		<div className="space-y-4">
			{/* Header */}
			<div className="flex items-center justify-between">
				<div>
					<h3 className="text-lg font-semibold text-gray-900 dark:text-white! flex items-center gap-2 my-0!">
						👁️ Live Preview
					</h3>
					<p className="text-xs text-gray-500 dark:text-gray-400 mt-0.5!">
						Preview menggunakan data donasi contoh
					</p>
				</div>
				<button
					type="button"
					onClick={() => onGeneratePreview(template)}
					className="flex items-center gap-2 px-3 py-2 text-sm text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950 rounded-lg transition-colors"
					title="Refresh preview"
				>
					<RefreshCw size={16} />
					<span className="hidden sm:inline">Refresh</span>
				</button>
			</div>

			{/* Preview Container */}
			<div className="bg-white dark:bg-gray-800 rounded-xl border-2 border-gray-200 dark:border-gray-700 overflow-hidden shadow-lg">
				<div className="bg-gray-50 dark:bg-gray-900 px-4 py-2 border-b border-gray-200 dark:border-gray-700">
					<div className="flex items-center gap-2">
						<div className="flex gap-1.5">
							<div className="w-3 h-3 rounded-full bg-red-400"></div>
							<div className="w-3 h-3 rounded-full bg-yellow-400"></div>
							<div className="w-3 h-3 rounded-full bg-green-400"></div>
						</div>
						<span className="text-xs text-gray-500 dark:text-gray-400 ml-2">
							receipt-preview.html
						</span>
					</div>
				</div>

				<div className="bg-gray-100 dark:bg-gray-900 relative">
					{previewHtml?.html ? (
						<iframe
							title="Receipt Preview"
							srcDoc={previewHtml.html}
							className="w-full h-[800px] bg-white mx-auto shadow-xl"
							style={{
								border: "none",
								display: "block",
							}}
						/>
					) : (
						<div className="flex flex-col items-center justify-center min-h-[400px] bg-white rounded-lg shadow-xl max-w-[800px] mx-auto">
							<Loader2 className="w-12 h-12 text-emerald-600 animate-spin mb-4" />
							<p className="text-gray-500 dark:text-gray-400">
								Generating preview...
							</p>
						</div>
					)}
				</div>
			</div>

			{/* Info */}
			<div className="flex items-start gap-2 p-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
				<svg
					className="w-4 h-4 text-gray-400 shrink-0 mt-0.5"
					fill="currentColor"
					viewBox="0 0 20 20"
				>
					<title>Info</title>
					<path
						fillRule="evenodd"
						d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
						clipRule="evenodd"
					/>
				</svg>
				<p className="text-xs text-gray-600 dark:text-gray-400">
					Preview ini menunjukkan tampilan kuitansi dengan data contoh. Kuitansi
					sebenarnya akan menggunakan data donasi yang real.
				</p>
			</div>
		</div>
	);
}
