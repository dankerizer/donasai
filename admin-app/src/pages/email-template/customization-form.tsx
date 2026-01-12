import clsx from "clsx";
import {
	ChevronDown,
	Code,
	FileText,
	Palette,
	Settings,
	Tag,
} from "lucide-react";
import { useState } from "react";
import { LogoUploader } from "/src/components/shared/LogoUploader";
import type { LogoData } from "/src/components/shared/LogoUploader";
import { Checkbox } from "/src/components/ui/Checkbox";
import { Input } from "/src/components/ui/Input";
import { Label } from "/src/components/ui/Label";
import { Textarea } from "/src/components/ui/Textarea";
import type { EmailTemplate, EmailType } from "./hooks/use-email-template";
import { TemplateSelector } from "./template-selector";

interface CustomizationFormProps {
	template: EmailTemplate | undefined;
	onChange: (template: EmailTemplate) => void;
	onSave: () => void;
	isSaving: boolean;
	isProActive?: boolean;
}

const MERGE_TAGS = [
	{ tag: "{donor_name}", desc: "Nama donatur" },
	{ tag: "{donation_id}", desc: "ID donasi" },
	{ tag: "{amount}", desc: "Nominal donasi" },
	{ tag: "{campaign_name}", desc: "Nama campaign" },
	{ tag: "{payment_method}", desc: "Metode pembayaran" },
	{ tag: "{site_name}", desc: "Nama website" },
	{ tag: "{year}", desc: "Tahun saat ini" },
	{ tag: "{date}", desc: "Tanggal donasi" },
];

// Accordion Section Component
function AccordionSection({
	title,
	icon: Icon,
	isOpen,
	onToggle,
	children,
	badge,
}: {
	title: string;
	icon: typeof Palette;
	isOpen: boolean;
	onToggle: () => void;
	children: React.ReactNode;
	badge?: string;
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
					{badge && (
						<span className="text-[10px] px-1.5 py-0.5 rounded bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300">
							{badge}
						</span>
					)}
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

// Collapsible Merge Tags
function MergeTags() {
	const [isOpen, setIsOpen] = useState(false);

	return (
		<div className="bg-gray-50 dark:bg-gray-900 rounded-lg overflow-hidden">
			<button
				type="button"
				onClick={() => setIsOpen(!isOpen)}
				className="w-full px-3 py-2 flex items-center justify-between text-left hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
			>
				<span className="text-xs font-medium text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
					<Tag size={12} />
					Merge Tags
				</span>
				<ChevronDown
					size={14}
					className={clsx(
						"text-gray-400 transition-transform",
						isOpen && "rotate-180",
					)}
				/>
			</button>
			{isOpen && (
				<div className="px-3 pb-3 flex flex-wrap gap-1.5">
					{MERGE_TAGS.map((item) => (
						<code
							key={item.tag}
							className="text-[10px] bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-1.5 py-0.5 rounded cursor-help"
							title={item.desc}
						>
							{item.tag}
						</code>
					))}
				</div>
			)}
		</div>
	);
}

export function CustomizationForm({
	template,
	onChange,
	isProActive = true,
}: CustomizationFormProps) {
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
	const handleLogoChange = (logo: LogoData) => onChange({ ...template, logo });

	const updateDesign = (key: keyof EmailTemplate["design"], value: string) => {
		onChange({
			...template,
			design: { ...template.design, [key]: value },
		});
	};

	const updateContent = (
		type: EmailType,
		key: keyof EmailTemplate["content"]["pending"],
		value: string,
	) => {
		onChange({
			...template,
			content: {
				...template.content,
				[type]: { ...template.content[type], [key]: value },
			},
		});
	};

	const updateAdvanced = (
		key: keyof EmailTemplate["advanced"],
		value: boolean,
	) => {
		onChange({
			...template,
			advanced: { ...template.advanced, [key]: value },
		});
	};

	const toggleSection = (section: string) => {
		setOpenSection(openSection === section ? "" : section);
	};

	// Content Section
	const ContentSection = ({ type }: { type: EmailType }) => {
		const content = template.content[type];

		return (
			<div className="space-y-4">
				<div>
					<Label htmlFor={`${type}-subject`} className="text-xs">
						Subject Email
					</Label>
					<Input
						id={`${type}-subject`}
						value={content.subject}
						onChange={(e) => updateContent(type, "subject", e.target.value)}
						placeholder={`${type === "pending" ? "Menunggu" : "Pembayaran Diterima"} #{donation_id}`}
						className="mt-1 text-sm"
					/>
				</div>

				<div>
					<Label htmlFor={`${type}-greeting`} className="text-xs">
						Greeting
					</Label>
					<Input
						id={`${type}-greeting`}
						value={content.greeting}
						onChange={(e) => updateContent(type, "greeting", e.target.value)}
						placeholder="Halo {donor_name},"
						className="mt-1 text-sm"
					/>
				</div>

				<div>
					<Label htmlFor={`${type}-body`} className="text-xs">
						Body Message
					</Label>
					<Textarea
						id={`${type}-body`}
						value={content.body}
						onChange={(e) => updateContent(type, "body", e.target.value)}
						placeholder={
							type === "pending"
								? "Terima kasih telah melakukan pemesanan donasi..."
								: "Terima kasih! Donasi Anda telah kami terima."
						}
						rows={3}
						className="mt-1 text-sm"
					/>
				</div>

				<div>
					<Label htmlFor={`${type}-footer`} className="text-xs">
						Footer
					</Label>
					<Input
						id={`${type}-footer`}
						value={content.footer}
						onChange={(e) => updateContent(type, "footer", e.target.value)}
						placeholder="© {year} {site_name}"
						className="mt-1 text-sm"
					/>
				</div>

				<MergeTags />
			</div>
		);
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
							onChange={(val) => updateDesign("template", val)}
							isProActive={isProActive}
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

					<div className="grid grid-cols-2 gap-3">
						<div>
							<Label htmlFor="header-color" className="text-xs">
								Warna Header
							</Label>
							<div className="flex items-center gap-2 mt-1">
								<input
									type="color"
									id="header-color"
									value={template.design.header_color}
									onChange={(e) => updateDesign("header_color", e.target.value)}
									className="w-8 h-8 rounded border border-gray-300 dark:border-gray-600 cursor-pointer p-0"
								/>
								<Input
									type="text"
									value={template.design.header_color}
									onChange={(e) => updateDesign("header_color", e.target.value)}
									className="flex-1 font-mono text-xs uppercase"
									maxLength={7}
								/>
							</div>
						</div>

						<div>
							<Label htmlFor="button-color" className="text-xs">
								Warna Tombol
							</Label>
							<div className="flex items-center gap-2 mt-1">
								<input
									type="color"
									id="button-color"
									value={template.design.button_color}
									onChange={(e) => updateDesign("button_color", e.target.value)}
									className="w-8 h-8 rounded border border-gray-300 dark:border-gray-600 cursor-pointer p-0"
								/>
								<Input
									type="text"
									value={template.design.button_color}
									onChange={(e) => updateDesign("button_color", e.target.value)}
									className="flex-1 font-mono text-xs uppercase"
									maxLength={7}
								/>
							</div>
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
							onChange={(e) => updateDesign("custom_css", e.target.value)}
							placeholder=".email-header { ... }"
							className="mt-1 font-mono text-xs h-20"
							disabled={!isProActive}
						/>
					</div>
				</div>
			</AccordionSection>

			{/* Email Pending Section */}
			<AccordionSection
				title="Email Pending"
				icon={FileText}
				isOpen={openSection === "pending"}
				onToggle={() => toggleSection("pending")}
				badge="⏳"
			>
				<ContentSection type="pending" />
			</AccordionSection>

			{/* Email Success Section */}
			<AccordionSection
				title="Email Success"
				icon={FileText}
				isOpen={openSection === "success"}
				onToggle={() => toggleSection("success")}
				badge="✓"
			>
				<ContentSection type="success" />
			</AccordionSection>

			{/* Settings Section */}
			<AccordionSection
				title="Pengaturan"
				icon={Settings}
				isOpen={openSection === "settings"}
				onToggle={() => toggleSection("settings")}
			>
				<div className="space-y-3">
					{[
						{
							id: "show_campaign_info",
							label: "Info Campaign",
							desc: "Nama program donasi",
						},
						{
							id: "show_donation_details",
							label: "Detail Donasi",
							desc: "Nominal, ID, tanggal, metode",
						},
						{
							id: "show_payment_instructions",
							label: "Instruksi Pembayaran",
							desc: "Untuk email pending",
						},
						{
							id: "show_receipt_button",
							label: "Tombol Kuitansi",
							desc: "Untuk email success",
						},
					].map((item) => (
						<label
							key={item.id}
							htmlFor={item.id}
							className="flex items-start gap-2 cursor-pointer group"
						>
							<Checkbox
								id={item.id}
								checked={
									template.advanced[item.id as keyof EmailTemplate["advanced"]]
								}
								onChange={(e) =>
									updateAdvanced(
										item.id as keyof EmailTemplate["advanced"],
										e.target.checked,
									)
								}
								className="mt-0.5"
							/>
							<div>
								<span className="text-sm font-medium text-gray-900 dark:text-white group-hover:text-emerald-600">
									{item.label}
								</span>
								<p className="text-[10px] text-gray-500">{item.desc}</p>
							</div>
						</label>
					))}
				</div>
			</AccordionSection>
		</div>
	);
}
