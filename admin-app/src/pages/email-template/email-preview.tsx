/** biome-ignore-all lint/security/noDangerouslySetInnerHtml: Required for email preview */
import { Loader2, Mail, RefreshCw } from "lucide-react";
import { useEffect, useState } from "react";
import { Input } from "/src/components/ui/Input";
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
	const [testEmail, setTestEmail] = useState("");
	const [showTestEmailInput, setShowTestEmailInput] = useState(false);

	// Generate preview when template or tab changes
	useEffect(() => {
		if (!template) return;

		const timer = setTimeout(() => {
			onGeneratePreview({ template, type: activeTab });
		}, 500);

		return () => clearTimeout(timer);
	}, [template, activeTab, onGeneratePreview]);

	const handleSendTestEmail = () => {
		if (!template || !testEmail) return;
		onSendTestEmail({ template, type: activeTab, email: testEmail });
		setShowTestEmailInput(false);
		setTestEmail("");
	};

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
			{/* Header with tabs */}
			<div className="flex items-center justify-between">
				<div>
					<h3 className="text-lg font-semibold text-gray-900 dark:text-white! flex items-center gap-2 my-0!">
						📧 Email Preview
					</h3>
					<p className="text-xs text-gray-500 dark:text-gray-400 mt-0.5!">
						Preview menggunakan data donasi contoh
					</p>
				</div>

				{/* Tab Toggle */}
				<div className="flex items-center gap-2">
					<div className="inline-flex bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
						<button
							type="button"
							onClick={() => setActiveTab("pending")}
							className={`px-3 py-1.5 text-sm font-medium rounded-md transition-colors ${
								activeTab === "pending"
									? "bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm"
									: "text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white"
							}`}
						>
							⏳ Pending
						</button>
						<button
							type="button"
							onClick={() => setActiveTab("success")}
							className={`px-3 py-1.5 text-sm font-medium rounded-md transition-colors ${
								activeTab === "success"
									? "bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm"
									: "text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white"
							}`}
						>
							✓ Success
						</button>
					</div>
					<button
						type="button"
						onClick={() => onGeneratePreview({ template, type: activeTab })}
						className="flex items-center gap-2 px-3 py-2 text-sm text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950 rounded-lg transition-colors"
						title="Refresh preview"
					>
						<RefreshCw size={16} />
					</button>
				</div>
			</div>

			{/* Preview Container */}
			<div className="bg-white dark:bg-gray-800 rounded-xl border-2 border-gray-200 dark:border-gray-700 overflow-hidden shadow-lg">
				<div className="bg-gray-50 dark:bg-gray-900 px-4 py-2 border-b border-gray-200 dark:border-gray-700">
					<div className="flex items-center gap-2">
						<div className="flex gap-1.5">
							<div className="w-3 h-3 rounded-full bg-red-400" />
							<div className="w-3 h-3 rounded-full bg-yellow-400" />
							<div className="w-3 h-3 rounded-full bg-green-400" />
						</div>
						<span className="text-xs text-gray-500 dark:text-gray-400 ml-2">
							email-{activeTab}.html
						</span>
					</div>
				</div>

				<div className="bg-gray-100 dark:bg-gray-900 relative">
					{previewHtml?.html ? (
						<iframe
							title="Email Preview"
							srcDoc={previewHtml.html}
							className="w-full h-[600px] bg-white mx-auto shadow-xl"
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

			{/* Send Test Email */}
			<div className="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
				{showTestEmailInput ? (
					<div className="flex-1 flex items-center gap-2">
						<Input
							type="email"
							placeholder="Enter email address"
							value={testEmail}
							onChange={(e) => setTestEmail(e.target.value)}
							className="flex-1"
						/>
						<button
							type="button"
							onClick={handleSendTestEmail}
							disabled={!testEmail || isSendingTestEmail}
							className="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
						>
							{isSendingTestEmail ? (
								<Loader2 size={14} className="animate-spin" />
							) : (
								<Mail size={14} />
							)}
							Send
						</button>
						<button
							type="button"
							onClick={() => setShowTestEmailInput(false)}
							className="px-3 py-2 text-gray-600 hover:text-gray-900 text-sm"
						>
							Cancel
						</button>
					</div>
				) : (
					<>
						<Mail size={16} className="text-gray-400" />
						<p className="flex-1 text-sm text-gray-600 dark:text-gray-400">
							Kirim email test untuk melihat hasil sebenarnya di inbox.
						</p>
						<button
							type="button"
							onClick={() => setShowTestEmailInput(true)}
							className="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors"
						>
							Send Test Email
						</button>
					</>
				)}
			</div>
		</div>
	);
}
