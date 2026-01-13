/** biome-ignore-all lint/security/noDangerouslySetInnerHtml: Required for email preview */
import { Loader2, RefreshCw } from "lucide-react";
import { useEffect, useState } from "react";
import type { EmailTemplate, EmailType } from "./hooks/use-email-template";

interface EmailPreviewProps {
	template: EmailTemplate | undefined;
	previewHtml?: { success: boolean; html: string };

	onGeneratePreview: (params: {
		template: EmailTemplate;
		type: EmailType;
	}) => void;
	onSendTestEmail: (params: {
		template: EmailTemplate;
		type: EmailType;
		email: string;
	}) => void;
	isSendingTestEmail: boolean;
}

export function EmailPreview({
	template,
	previewHtml,
	onGeneratePreview,
	onSendTestEmail,
	isSendingTestEmail,
}: EmailPreviewProps) {
	const [activeTab, setActiveTab] = useState<EmailType>("pending");

	// Generate preview when template or tab changes
	// biome-ignore lint/correctness/useExhaustiveDependencies: onGeneratePreview reference changes each render
	useEffect(() => {
		if (!template) return;

		const timer = setTimeout(() => {
			onGeneratePreview({ template, type: activeTab });
		}, 500);

		return () => clearTimeout(timer);
	}, [template, activeTab]);

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
			{/* Floating Controls - Top Right */}
			<div className="absolute top-4 right-4 z-10 flex items-center gap-2">
				{/* Tab Toggle */}
				<div className="inline-flex bg-white/80 dark:bg-gray-800/80 rounded-lg p-1">
					<button
						type="button"
						onClick={() => setActiveTab("pending")}
						className={`px-3 py-1.5 text-xs font-medium rounded-md transition-colors ${
							activeTab === "pending"
								? "bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300"
								: "text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
						}`}
					>
						⏳ Pending
					</button>
					<button
						type="button"
						onClick={() => setActiveTab("success")}
						className={`px-3 py-1.5 text-xs font-medium rounded-md transition-colors ${
							activeTab === "success"
								? "bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300"
								: "text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700"
						}`}
					>
						✓ Success
					</button>
				</div>

				{/* Refresh Button */}
				<button
					type="button"
					onClick={() => onGeneratePreview({ template, type: activeTab })}
					className="p-2 bg-white/80 dark:bg-gray-800/80 rounded-lg text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950 transition-colors"
					title="Refresh preview"
				>
					<RefreshCw size={16} />
				</button>

				{/* Send Test Email Button */}
				<button
					type="button"
					onClick={() => {
						const email = prompt(
							"Masukkan alamat email tujuan:",
							"admin@example.com",
						);
						if (email && template) {
							onSendTestEmail({ template, type: activeTab, email });
						}
					}}
					disabled={isSendingTestEmail}
					className="p-2 bg-white/80 dark:bg-gray-800/80 rounded-lg text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-950 transition-colors disabled:opacity-50"
					title="Send Test Email"
				>
					{isSendingTestEmail ? (
						<Loader2 size={16} className="animate-spin" />
					) : (
						<span className="text-xs font-bold">Try</span>
					)}
				</button>
			</div>

			{/* Preview Content */}
			<div className="flex-1 overflow-auto">
				{previewHtml?.html ? (
					<iframe
						title="Email Preview"
						srcDoc={previewHtml.html}
						className="w-full h-full min-h-[600px] bg-white"
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
		</div>
	);
}
