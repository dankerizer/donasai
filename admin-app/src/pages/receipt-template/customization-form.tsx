import clsx from "clsx";
import {
	Building2,
	ChevronDown,
	Code,
	FileText,
	Palette,
	RefreshCcw,
	Settings,
} from "lucide-react";
import { useState } from "react";
import { toast } from "sonner";
import { LogoUploader } from "/src/components/shared/LogoUploader";
import { OrganizationForm } from "/src/components/shared/OrganizationForm";
import { Checkbox } from "/src/components/ui/Checkbox";
import { Input } from "/src/components/ui/Input";
import { Label } from "/src/components/ui/Label";
import { Radio } from "/src/components/ui/Radio";
import { Textarea } from "/src/components/ui/Textarea";
import { useSettingsFetch } from "/src/pages/settings/hooks/use-settings-data";
import { FooterEditor } from "./footer-editor";
import type { ReceiptTemplate } from "./hooks/use-receipt-template";
import { SignatureUploader } from "./signature-uploader";
import { TemplateSelector } from "./template-selector";

interface CustomizationFormProps {
	template: ReceiptTemplate | undefined;
	onChange: (template: ReceiptTemplate) => void;
	onSave: () => void;
	isSaving: boolean;
}

// Accordion Section Component
function AccordionSection({
	title,
	icon: Icon,
	isOpen,
	onToggle,
	children,
}: {
	title: string;
	icon: typeof Palette;
	isOpen: boolean;
	onToggle: () => void;
	children: React.ReactNode;
}) {
	return (
		<div className="border-b border-gray-200 dark:border-gray-700 last:border-b-0">
			<button
				type="button"
				onClick={onToggle}
				className={clsx(
					"w-full px-4 py-3 flex items-center justify-between text-left transition-colors",
					isOpen
						? "bg-emerald-50 dark:bg-emerald-900/20"
						: "hover:bg-gray-50 dark:hover:bg-gray-800",
				)}
			>
				<div className="flex items-center gap-2">
					<Icon
						size={16}
						className={clsx(
							isOpen ? "text-emerald-600" : "text-gray-400 dark:text-gray-500",
						)}
					/>
					<span
						className={clsx(
							"font-medium text-sm",
							isOpen
								? "text-emerald-700 dark:text-emerald-300"
								: "text-gray-700 dark:text-gray-300",
						)}
					>
						{title}
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
				<div className="px-4 py-4 bg-white dark:bg-gray-800 space-y-4">
					{children}
				</div>
			)}
		</div>
	);
}

export function CustomizationForm({
	template,
	onChange,
}: CustomizationFormProps) {
	const { data: globalSettings } = useSettingsFetch();
	const [openSection, setOpenSection] = useState("design");

	if (!template) {
		return (
			<div className="p-4">
				<div className="animate-pulse space-y-3">
					<div className="h-12 bg-gray-200 dark:bg-gray-700 rounded" />
					<div className="h-12 bg-gray-200 dark:bg-gray-700 rounded" />
					<div className="h-12 bg-gray-200 dark:bg-gray-700 rounded" />
				</div>
			</div>
		);
	}

	// Handlers
	const handleLogoChange = (logo: ReceiptTemplate["logo"]) =>
		onChange({ ...template, logo });
	const handleOrgChange = (organization: ReceiptTemplate["organization"]) =>
		onChange({ ...template, organization });
	const handleFooterChange = (footer: ReceiptTemplate["footer"]) =>
		onChange({ ...template, footer });
	const handleAdvancedChange = (
		field: keyof ReceiptTemplate["advanced"],
		value: any,
	) => {
		onChange({
			...template,
			advanced: { ...template.advanced, [field]: value },
		});
	};
	const handleDesignChange = (
		field: keyof ReceiptTemplate["design"],
		value: any,
	) => {
		onChange({ ...template, design: { ...template.design, [field]: value } });
	};
	const handleSignatureChange = (signature: ReceiptTemplate["signature"]) =>
		onChange({ ...template, signature });
	const handleSerialChange = (
		field: keyof ReceiptTemplate["serial"],
		value: any,
	) => {
		onChange({ ...template, serial: { ...template.serial, [field]: value } });
	};

	const toggleSection = (section: string) => {
		setOpenSection(openSection === section ? "" : section);
	};

	// Sync Function
	const handleSyncFromGeneral = () => {
		if (!globalSettings) {
			toast.error("Gagal memuat pengaturan utama");
			return;
		}
		const { formData } = globalSettings;
		handleOrgChange({
			...template.organization,
			name: formData.org_name,
			email: formData.org_email,
			phone: formData.org_phone,
			address_line_1: formData.org_address,
			address_line_2: "",
			city: "",
			postal_code: "",
		});
		if (formData.org_logo) {
			handleLogoChange({
				url: formData.org_logo,
				attachment_id: 0,
				width: 0,
				height: 0,
			});
		}
		toast.success("Data disinkronkan dari Pengaturan Utama");
	};

	return (
		<div className="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
			{/* Design Section */}
			<AccordionSection
				title="Desain"
				icon={Palette}
				isOpen={openSection === "design"}
				onToggle={() => toggleSection("design")}
			>
				<div className="space-y-4">
					<div>
						<Label className="text-xs mb-2 block">Template</Label>
						<TemplateSelector
							value={template.design?.template || "modern"}
							onChange={(val) => handleDesignChange("template", val)}
							compact
						/>
					</div>

					<div>
						<Label className="text-xs mb-2 block">Logo</Label>
						<LogoUploader
							value={template.logo}
							onChange={handleLogoChange}
							compact
						/>
					</div>

					<div>
						<Label htmlFor="header-color" className="text-xs">
							Warna Aksen
						</Label>
						<div className="flex items-center gap-2 mt-1">
							<input
								type="color"
								id="header-color"
								value={template.advanced.header_color}
								onChange={(e) =>
									handleAdvancedChange("header_color", e.target.value)
								}
								className="w-8 h-8 rounded border border-gray-300 dark:border-gray-600 cursor-pointer p-0"
							/>
							<Input
								type="text"
								value={template.advanced.header_color}
								onChange={(e) =>
									handleAdvancedChange("header_color", e.target.value)
								}
								className="flex-1 font-mono text-xs uppercase"
								maxLength={7}
							/>
						</div>
					</div>

					<div>
						<Label
							htmlFor="custom_css"
							className="text-xs flex items-center gap-1"
						>
							<Code size={12} /> Custom CSS
						</Label>
						<Textarea
							id="custom_css"
							value={template.design?.custom_css || ""}
							onChange={(e) => handleDesignChange("custom_css", e.target.value)}
							placeholder=".receipt-header { ... }"
							className="mt-1 font-mono text-xs h-20"
						/>
					</div>
				</div>
			</AccordionSection>

			{/* Organization Section */}
			<AccordionSection
				title="Organisasi"
				icon={Building2}
				isOpen={openSection === "organization"}
				onToggle={() => toggleSection("organization")}
			>
				<div className="space-y-4">
					<button
						type="button"
						onClick={handleSyncFromGeneral}
						className="w-full flex items-center justify-center gap-2 px-3 py-2 text-xs font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg border border-emerald-200 transition-colors"
					>
						<RefreshCcw size={14} />
						Ambil dari Pengaturan Utama
					</button>

					<OrganizationForm
						data={template.organization}
						onChange={handleOrgChange}
						mode="detailed"
						showLogo={false}
						compact
					/>

					<div className="pt-4 border-t border-gray-200 dark:border-gray-700">
						<Label className="text-xs mb-2 block">Tanda Tangan Digital</Label>
						<SignatureUploader
							value={
								template.signature || {
									enabled: false,
									image: { attachment_id: 0, url: "" },
									label: "",
								}
							}
							onChange={handleSignatureChange}
							compact
						/>
					</div>
				</div>
			</AccordionSection>

			{/* Content Section */}
			<AccordionSection
				title="Konten"
				icon={FileText}
				isOpen={openSection === "content"}
				onToggle={() => toggleSection("content")}
			>
				<FooterEditor
					footer={template.footer}
					onChange={handleFooterChange}
					compact
				/>
			</AccordionSection>

			{/* Settings Section */}
			<AccordionSection
				title="Pengaturan"
				icon={Settings}
				isOpen={openSection === "settings"}
				onToggle={() => toggleSection("settings")}
			>
				<div className="space-y-4">
					{/* Receipt Format */}
					<div>
						<Label className="text-xs mb-2 block">Format Dokumen</Label>
						<div className="space-y-2">
							<label className="flex items-center gap-2 p-2 border border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer hover:border-emerald-300 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/50">
								<Radio
									name="format"
									value="html"
									checked={template.advanced.format === "html"}
									onChange={(e) =>
										handleAdvancedChange("format", e.target.value)
									}
								/>
								<span className="text-xs font-medium text-gray-900">
									HTML Link
								</span>
							</label>
							<label className="flex items-center gap-2 p-2 border border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer hover:border-emerald-300 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/50">
								<Radio
									name="format"
									value="pdf"
									checked={template.advanced.format === "pdf"}
									onChange={(e) =>
										handleAdvancedChange("format", e.target.value)
									}
								/>
								<span className="text-xs font-medium text-gray-900">
									PDF Attachment
								</span>
							</label>
						</div>
					</div>

					{/* Serial Number & Auto Send */}
					<div className="space-y-3 pt-3 border-t border-gray-200 dark:border-gray-700">
						<label
							htmlFor="auto_send_email"
							className="flex items-start gap-2 cursor-pointer group"
						>
							<Checkbox
								id="auto_send_email"
								checked={template.advanced.auto_send_email}
								onChange={(e) =>
									handleAdvancedChange("auto_send_email", e.target.checked)
								}
								className="mt-0.5"
							/>
							<div>
								<span className="text-sm font-medium text-gray-900 dark:text-white group-hover:text-emerald-600">
									Kirim otomatis via email
								</span>
								<p className="text-[10px] text-gray-500">
									Setelah donasi berhasil
								</p>
							</div>
						</label>

						<label
							htmlFor="include_serial_number"
							className="flex items-start gap-2 cursor-pointer group"
						>
							<Checkbox
								id="include_serial_number"
								checked={template.serial?.enabled ?? true}
								onChange={(e) => handleSerialChange("enabled", e.target.checked)}
								className="mt-0.5"
							/>
							<div>
								<span className="text-sm font-medium text-gray-900 dark:text-white group-hover:text-emerald-600">
									Nomor Seri Kuitansi
								</span>
								<p className="text-[10px] text-gray-500">
									Generate nomor unik
								</p>
							</div>
						</label>

						{(template.serial?.enabled ?? true) && (
							<div className="pl-6 space-y-2">
								<Label htmlFor="serial_format" className="text-xs">
									Format Nomor Seri
								</Label>
								<Input
									id="serial_format"
									value={template.serial?.format || "INV/{Y}/{m}/{0000}"}
									onChange={(e) => handleSerialChange("format", e.target.value)}
									className="font-mono text-xs uppercase"
									placeholder="INV/{Y}/{0000}"
								/>
								<p className="text-[10px] text-gray-500">
									<code className="bg-gray-100 px-1 rounded">{"{Y}"}</code>{" "}
									Tahun,{" "}
									<code className="bg-gray-100 px-1 rounded">{"{m}"}</code>{" "}
									Bulan,{" "}
									<code className="bg-gray-100 px-1 rounded">{"{0000}"}</code>{" "}
									Nomor
								</p>
							</div>
						)}
					</div>
				</div>
			</AccordionSection>
		</div>
	);
}
