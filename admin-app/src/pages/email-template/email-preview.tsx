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

			{/* Floating Test Email - Bottom */}
			<div className="absolute bottom-4 left-4 right-4 z-10">
				<div className="bg-white/90 dark:bg-gray-800/90 rounded-lg p-3">
					{showTestEmailInput ? (
						<div className="flex items-center gap-2">
							<Input
								type="email"
								placeholder="Enter email address"
								value={testEmail}
								onChange={(e) => setTestEmail(e.target.value)}
								className="flex-1 text-sm"
							/>
							<button
								type="button"
								onClick={handleSendTestEmail}
								disabled={!testEmail || isSendingTestEmail}
								className="px-3 py-2 bg-emerald-600 text-white rounded-lg text-xs font-medium hover:bg-emerald-700 disabled:opacity-50 flex items-center gap-1.5"
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
								className="px-2 py-2 text-gray-500 hover:text-gray-700 text-xs"
							>
								✕
							</button>
						</div>
					) : (
						<div className="flex items-center justify-between">
							<p className="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2">
								<Mail size={14} />
								Kirim email test untuk melihat hasil di inbox
							</p>
							<button
								type="button"
								onClick={() => setShowTestEmailInput(true)}
								className="px-3 py-1.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-xs font-medium hover:bg-gray-200 dark:hover:bg-gray-600"
							>
								Send Test Email
							</button>
						</div>
					)}
				</div>
			</div>
		</div>
	);
}
