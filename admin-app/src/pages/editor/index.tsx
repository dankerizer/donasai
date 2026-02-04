import clsx from "clsx";
import {
	Banknote,
	ChevronDown,
	ChevronRight,
	FileText,
	Layout,
	Loader2,
	Mail,
	Palette,
	PanelLeftClose,
	PanelLeftOpen,
	Save,
} from "lucide-react";
import { useCallback, useEffect, useRef, useState } from "react";

// Email Template
import { CustomizationForm as EmailCustomizationForm } from "../email-template/customization-form";
import { EmailPreview } from "../email-template/email-preview";
import {
	useEmailTemplate,
	type EmailTemplate,
} from "../email-template/hooks/use-email-template";

// Receipt Template
import { CustomizationForm as ReceiptCustomizationForm } from "../receipt-template/customization-form";
import { ReceiptPreview } from "../receipt-template/receipt-preview";
import {
	useReceiptTemplate,
	type ReceiptTemplate,
} from "../receipt-template/hooks/use-receipt-template";

// Campaign Template
import { CustomizationForm as CampaignCustomizationForm } from "../campaign-template/customization-form";
import { CampaignPreview } from "../campaign-template/campaign-preview";
import {
	useCampaignTemplate,
	type CampaignTemplate,
} from "../campaign-template/hooks/use-campaign-template";

// Donation Form Template
import { DonationFormCustomizationForm } from "../donation-form/customization-form";
import { DonationPreview } from "../donation-form/donation-preview";
import {
	useDonationFormTemplate,
	type DonationFormTemplate,
} from "../donation-form/hooks/use-donation-form-template";

// Editor Components
import { DevicePreview, DeviceToggle } from "./components/DevicePreview";
import {
	EditorProvider,
	useEditorState,
	type EditorComponent,
} from "./hooks/useEditorState";

// Component Options
const COMPONENTS: {
	id: EditorComponent;
	label: string;
	icon: typeof Mail;
}[] = [
	{ id: "email", label: "Template Email", icon: Mail },
	{ id: "receipt", label: "Template Kuitansi", icon: FileText },
	{ id: "campaign", label: "Halaman Campaign", icon: Layout },
	{ id: "donation-form", label: "Formulir Donasi", icon: Banknote },
];

// Dropdown Component Selector
function ComponentDropdown() {
	const { selectedComponent, setSelectedComponent } = useEditorState();
	const [isOpen, setIsOpen] = useState(false);
	const dropdownRef = useRef<HTMLDivElement>(null);

	const selected = COMPONENTS.find((c) => c.id === selectedComponent);
	const Icon = selected?.icon || Mail;

	// Close dropdown when clicking outside
	useEffect(() => {
		const handleClickOutside = (event: MouseEvent) => {
			if (
				dropdownRef.current &&
				!dropdownRef.current.contains(event.target as Node)
			) {
				setIsOpen(false);
			}
		};
		document.addEventListener("mousedown", handleClickOutside);
		return () => document.removeEventListener("mousedown", handleClickOutside);
	}, []);

	return (
		<>
			<div className="bg-gray-50 dark:bg-gray-900 px-4 py-2 border-b border-gray-200 dark:border-gray-700 flex items-center gap-2 rounded-xl">
				<div className="flex gap-1.5">
					<div className="w-3 h-3 rounded-full bg-red-400" />
					<div className="w-3 h-3 rounded-full bg-yellow-400" />
					<div className="w-3 h-3 rounded-full bg-green-400" />
				</div>

				<div
					className="relative  md:ml-3 md:min-w-[300px] w-full"
					ref={dropdownRef}
				>
					<button
						type="button"
						onClick={() => setIsOpen(!isOpen)}
						className="flex w-full items-center justify-between gap-2 px-4 py-2  hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors"
					>
						<div className="flex items-center gap-2">
							<Icon size={18} className="text-emerald-600" />
							<span className="font-medium text-gray-900 dark:text-white">
								{selected?.label}
							</span>
						</div>
						<ChevronDown
							size={16}
							className={clsx(
								"text-gray-400 transition-transform",
								isOpen && "rotate-180",
							)}
						/>
					</button>

					{isOpen && (
						<div className="absolute top-full left-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 py-2 z-50">
							{COMPONENTS.map((comp) => {
								const CompIcon = comp.icon;
								const isSelected = selectedComponent === comp.id;

								return (
									<button
										key={comp.id}
										type="button"
										onClick={() => {
											setSelectedComponent(comp.id);
											setIsOpen(false);
										}}
										className={clsx(
											"w-full flex items-center gap-3 px-4 py-2.5 text-left text-sm transition-colors",
											isSelected
												? "bg-emerald-50 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300"
												: "text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700",
										)}
									>
										<CompIcon size={18} />
										<span className="font-medium">{comp.label}</span>
										{isSelected && (
											<span className="ml-auto text-emerald-600">✓</span>
										)}
									</button>
								);
							})}

							{/* Coming Soon Section */}
							<div className="border-t border-gray-200 dark:border-gray-700 mt-2 pt-2 px-4">
								<p className="text-[10px] text-gray-400 uppercase tracking-wide mb-1">
									Coming Soon
								</p>
								<div className="py-1.5 text-xs text-gray-400 flex items-center gap-2">
									<div className="w-4 h-4 rounded bg-gray-200 dark:bg-gray-700" />
									Widgets
								</div>
							</div>
						</div>
					)}
				</div>
			</div>
		</>
	);
}

function EditorContent() {
	const {
		selectedComponent,
		panelVisible,
		setPanelVisible,
		isSaving,
		setIsSaving,
	} = useEditorState();

	// Email Template State
	const emailQuery = useEmailTemplate();
	const [localEmailTemplate, setLocalEmailTemplate] = useState<
		EmailTemplate | undefined
	>(undefined);

	// Receipt Template State
	const receiptQuery = useReceiptTemplate();
	const [localReceiptTemplate, setLocalReceiptTemplate] = useState<
		ReceiptTemplate | undefined
	>(undefined);

	// Campaign Template State
	const campaignQuery = useCampaignTemplate();
	const [localCampaignTemplate, setLocalCampaignTemplate] = useState<
		CampaignTemplate | undefined
	>(undefined);

	// Donation Form Template State
	const donationFormQuery = useDonationFormTemplate();
	const [localDonationTemplate, setLocalDonationTemplate] = useState<
		DonationFormTemplate | undefined
	>(undefined);

	const [lastSavedAt, setLastSavedAt] = useState<number>(0);
	const saveTimeoutRef = useRef<NodeJS.Timeout | null>(null);

	// Sync email template
	useEffect(() => {
		if (emailQuery.template) {
			setLocalEmailTemplate(emailQuery.template);
		}
	}, [emailQuery.template]);

	// Sync receipt template
	useEffect(() => {
		if (receiptQuery.template) {
			setLocalReceiptTemplate(receiptQuery.template);
		}
	}, [receiptQuery.template]);

	// Sync campaign template
	useEffect(() => {
		if (campaignQuery.template) {
			setLocalCampaignTemplate(campaignQuery.template);
		}
	}, [campaignQuery.template]);

	// Sync donation form template
	useEffect(() => {
		if (donationFormQuery.template) {
			setLocalDonationTemplate(donationFormQuery.template);
		}
	}, [donationFormQuery.template]);

	// Auto-save for Campaign
	useEffect(() => {
		if (
			selectedComponent !== "campaign" ||
			!localCampaignTemplate ||
			!campaignQuery.template
		) {
			return;
		}

		// Check if changed
		const hasChanges =
			JSON.stringify(localCampaignTemplate) !==
			JSON.stringify(campaignQuery.template);

		if (hasChanges) {
			if (saveTimeoutRef.current) clearTimeout(saveTimeoutRef.current);

			saveTimeoutRef.current = setTimeout(() => {
				handleSave();
			}, 1000);
		}

		return () => {
			if (saveTimeoutRef.current) clearTimeout(saveTimeoutRef.current);
		};
	}, [localCampaignTemplate, campaignQuery.template, selectedComponent]);

	// Auto-save for Donation Form
	useEffect(() => {
		if (
			selectedComponent !== "donation-form" ||
			!localDonationTemplate ||
			!donationFormQuery.template
		) {
			return;
		}

		const hasChanges =
			JSON.stringify(localDonationTemplate) !==
			JSON.stringify(donationFormQuery.template);

		if (hasChanges) {
			if (saveTimeoutRef.current) clearTimeout(saveTimeoutRef.current);
			saveTimeoutRef.current = setTimeout(() => {
				handleSave();
			}, 1000);
		}

		return () => {
			if (saveTimeoutRef.current) clearTimeout(saveTimeoutRef.current);
		};
	}, [localDonationTemplate, donationFormQuery.template, selectedComponent]);

	// Save handler
	const handleSave = useCallback(() => {
		setIsSaving(true);
		if (selectedComponent === "email" && localEmailTemplate) {
			emailQuery.saveTemplate(localEmailTemplate, {
				onSettled: () => setIsSaving(false),
			});
		} else if (selectedComponent === "receipt" && localReceiptTemplate) {
			receiptQuery.saveTemplate(localReceiptTemplate, {
				onSettled: () => setIsSaving(false),
			});
		} else if (selectedComponent === "campaign" && localCampaignTemplate) {
			campaignQuery.saveTemplate(localCampaignTemplate, {
				onSettled: () => {
					setIsSaving(false);
					setLastSavedAt(Date.now());
				},
			});
		} else if (selectedComponent === "donation-form" && localDonationTemplate) {
			donationFormQuery.saveTemplate(localDonationTemplate, {
				onSettled: () => {
					setIsSaving(false);
					setLastSavedAt(Date.now());
				},
			});
		}
	}, [
		selectedComponent,
		localEmailTemplate,
		localReceiptTemplate,
		localCampaignTemplate,
		localDonationTemplate,
		emailQuery,
		receiptQuery,
		campaignQuery,
		donationFormQuery,
		setIsSaving,
	]);

	const isLoading =
		selectedComponent === "email"
			? emailQuery.isLoading
			: selectedComponent === "receipt"
				? receiptQuery.isLoading
				: selectedComponent === "campaign"
					? campaignQuery.isLoading
					: donationFormQuery.isLoading;

	const handleGoBack = () => {
		window.location.hash = "#/settings";
	};

	return (
		<div className="fixed inset-0 z-99999 flex flex-col bg-gray-100 dark:bg-gray-900">
			{/* Header */}
			<header className="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 py-2.5 flex items-center justify-between shrink-0">
				{/* Left: Back + Logo */}
				<div className="flex items-center gap-3">
					<button
						type="button"
						onClick={handleGoBack}
						className="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
						title="Kembali ke Pengaturan"
					>
						<svg
							className="w-5 h-5 text-gray-500"
							fill="none"
							viewBox="0 0 24 24"
							stroke="currentColor"
						>
							<path
								strokeLinecap="round"
								strokeLinejoin="round"
								strokeWidth={2}
								d="M10 19l-7-7m0 0l7-7m-7 7h18"
							/>
						</svg>
					</button>
					<div className="w-9 h-9 bg-linear-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center">
						<Palette className="w-4 h-4 text-white" />
					</div>
					<span className="text-base font-bold text-gray-900 dark:text-white hidden sm:block">
						Donasai Editor
					</span>
				</div>

				{/* Center: Component Dropdown */}
				<ComponentDropdown />

				{/* Right: Device Toggle + Panel Toggle + Save */}
				<div className="flex items-center gap-3">
					<DeviceToggle />

					{/* Panel Toggle */}
					<button
						type="button"
						onClick={() => setPanelVisible(!panelVisible)}
						className={clsx(
							"p-2 rounded-lg transition-colors",
							panelVisible
								? "bg-emerald-100 text-emerald-600 dark:bg-emerald-900 dark:text-emerald-400"
								: "bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600",
						)}
						title={panelVisible ? "Sembunyikan Panel" : "Tampilkan Panel"}
					>
						{panelVisible ? (
							<PanelLeftClose size={18} />
						) : (
							<PanelLeftOpen size={18} />
						)}
					</button>

					{/* Save Button */}
					<button
						type="button"
						onClick={handleSave}
						disabled={isSaving}
						className="flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-sm hover:shadow-md text-sm"
					>
						{isSaving ? (
							<Loader2 size={16} className="animate-spin" />
						) : (
							<Save size={16} />
						)}
						<span className="hidden sm:inline">
							{isSaving ? "Menyimpan..." : "Simpan"}
						</span>
					</button>
				</div>
			</header>

			{/* Main Content: Left Sidebar + Preview */}
			<div className="flex-1 flex overflow-hidden">
				{/* Left Sidebar: Customization Panel */}
				<div
					className={clsx(
						"bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-all duration-300 overflow-hidden shrink-0",
						panelVisible ? "w-[400px]" : "w-0",
					)}
				>
					{/* Panel Header */}
					<div className="h-12 px-4 flex items-center justify-between border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
						<span className="font-medium text-gray-900 dark:text-white text-sm flex items-center gap-2">
							<ChevronRight size={16} className="text-emerald-500" />
							Customization
						</span>
					</div>

					{/* Panel Content */}
					<div className="h-[calc(100%-48px)] overflow-auto">
						{selectedComponent === "email" ? (
							<EmailCustomizationForm
								template={localEmailTemplate}
								onChange={setLocalEmailTemplate}
								onSave={handleSave}
								isSaving={isSaving}
								onSendTestEmail={emailQuery.sendTestEmail}
								isSendingTestEmail={emailQuery.isSendingTestEmail}
							/>
						) : selectedComponent === "receipt" ? (
							<ReceiptCustomizationForm
								template={localReceiptTemplate}
								onChange={setLocalReceiptTemplate}
								onSave={handleSave}
								isSaving={isSaving}
							/>
						) : selectedComponent === "campaign" ? (
							<CampaignCustomizationForm
								template={localCampaignTemplate}
								onChange={setLocalCampaignTemplate}
								onSave={handleSave}
								isSaving={isSaving}
								isProActive={campaignQuery.isProInstalled}
							/>
						) : (
							<DonationFormCustomizationForm
								template={localDonationTemplate}
								onChange={setLocalDonationTemplate}
								onSave={handleSave}
								isSaving={isSaving}
								isProActive={donationFormQuery.isProInstalled}
							/>
						)}
					</div>
				</div>

				{/* Preview Area (Full Width when panel hidden) */}
				<div className="flex-1 overflow-hidden">
					<DevicePreview>
						{isLoading ? (
							<div className="flex items-center justify-center h-full">
								<div className="text-center">
									<Loader2 className="w-12 h-12 text-emerald-600 animate-spin mx-auto mb-4" />
									<p className="text-gray-500">Memuat preview...</p>
								</div>
							</div>
						) : selectedComponent === "email" ? (
							<EmailPreview
								template={localEmailTemplate}
								previewHtml={emailQuery.previewData}
								onGeneratePreview={emailQuery.generatePreview}
								onSendTestEmail={emailQuery.sendTestEmail}
								isSendingTestEmail={emailQuery.isSendingTestEmail}
							/>
						) : selectedComponent === "receipt" ? (
							<ReceiptPreview
								template={localReceiptTemplate}
								previewHtml={receiptQuery.previewData}
								onGeneratePreview={(t) => receiptQuery.generatePreview(t)}
							/>
						) : selectedComponent === "campaign" ? (
							<CampaignPreview
								template={localCampaignTemplate}
								lastSavedAt={lastSavedAt}
							/>
						) : (
							<DonationPreview
								template={localDonationTemplate}
								lastSavedAt={lastSavedAt}
							/>
						)}
					</DevicePreview>
				</div>
			</div>
		</div>
	);
}

export function EditorPage() {
	// Check Pro status
	const isPro = (window as any).donasaiSettings?.isPro;
	const proSettings = (window as any).donasaiProSettings || {};
	const isLicenseActive =
		proSettings.licenseStatus === "active" ||
		proSettings.licenseStatus === "valid";

	// Pro-only feature
	if (!isPro || !isLicenseActive) {
		return (
			<div className="fixed inset-0 z-99999 bg-gray-50 dark:bg-gray-900 flex items-center justify-center p-8">
				<div className="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-200 dark:border-gray-700 p-8 max-w-md text-center">
					<div className="w-16 h-16 bg-emerald-100 dark:bg-emerald-900 rounded-2xl flex items-center justify-center mx-auto mb-4">
						<Palette className="w-8 h-8 text-emerald-600 dark:text-emerald-400" />
					</div>
					<h2 className="text-lg! md:text-xl! my-0! font-bold text-gray-900 dark:text-white mb-2">
						Donasai Editor
					</h2>
					<p className="text-gray-600 dark:text-gray-400 mb-6">
						Fitur ini hanya tersedia untuk pengguna{" "}
						<span className="font-bold text-emerald-600">Donasai Pro</span>.
						Upgrade untuk membuka visual editor dengan live preview.
					</p>
					<a
						href="https://donasai.com/pricing"
						target="_blank"
						rel="noopener noreferrer"
						className="inline-block px-6 py-3 bg-emerald-600 hover:bg-emerald-50-700 text-white! font-medium rounded-lg transition-colors"
					>
						Upgrade ke Pro
					</a>
				</div>
			</div>
		);
	}

	return (
		<EditorProvider>
			<EditorContent />
		</EditorProvider>
	);
}

export default EditorPage;
