import clsx from "clsx";
import {
	ChevronDown,
	Lock,
	Palette,
	Save,
	Settings2,
	Type,
} from "lucide-react";
import { useState } from "react";
import { LogoUploader } from "/src/components/shared/LogoUploader";
import type { LogoData } from "/src/components/shared/LogoUploader";
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

type SectionId = "design" | "content-pending" | "content-success" | "advanced";

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

export function CustomizationForm({
	template,
	onChange,
	onSave,
	isSaving,
	isProActive = true,
}: CustomizationFormProps) {
	const [expandedSections, setExpandedSections] = useState<
		Record<SectionId, boolean>
	>({
		design: true,
		"content-pending": true,
		"content-success": false,
		advanced: false,
	});

	if (!template) return null;

	const toggleSection = (section: SectionId) => {
		setExpandedSections((prev) => ({
			...prev,
			[section]: !prev[section],
		}));
	};

	const updateDesign = (
		key: keyof EmailTemplate["design"],
		value: string,
	) => {
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

	const SectionHeader = ({
		id,
		icon: Icon,
		title,
		badge,
	}: {
		id: SectionId;
		icon: React.ComponentType<{ size?: number; className?: string }>;
		title: string;
		badge?: string;
	}) => (
		<button
			type="button"
			onClick={() => toggleSection(id)}
			className="w-full flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
		>
			<div className="flex items-center gap-3">
				<div className="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900 flex items-center justify-center">
					<Icon size={16} className="text-emerald-600 dark:text-emerald-400" />
				</div>
				<span className="font-medium text-gray-900 dark:text-white">
					{title}
				</span>
				{badge && (
					<span className="text-xs bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300 px-2 py-0.5 rounded">
						{badge}
					</span>
				)}
			</div>
			<ChevronDown
				size={18}
				className={clsx(
					"text-gray-400 transition-transform",
					expandedSections[id] && "rotate-180",
				)}
			/>
		</button>
	);

	const ContentEditor = ({ type }: { type: EmailType }) => {
		const content = template.content[type];

		return (
			<div className="space-y-4 p-4 pt-0">
				<div>
					<Label className="text-xs mb-1">Subject Email</Label>
					<Input
						value={content.subject}
						onChange={(e) => updateContent(type, "subject", e.target.value)}
						placeholder={`Contoh: Menunggu Pembayaran #{donation_id}`}
					/>
				</div>
				<div>
					<Label className="text-xs mb-1">Greeting</Label>
					<Input
						value={content.greeting}
						onChange={(e) => updateContent(type, "greeting", e.target.value)}
						placeholder="Halo {donor_name},"
					/>
				</div>
				<div>
					<Label className="text-xs mb-1">Body Message</Label>
					<Textarea
						value={content.body}
						onChange={(e) => updateContent(type, "body", e.target.value)}
						placeholder={
							type === "pending"
								? "Terima kasih telah melakukan pemesanan donasi..."
								: "Terima kasih! Donasi Anda telah kami terima."
						}
						rows={3}
					/>
				</div>
				<div>
					<Label className="text-xs mb-1">Footer</Label>
					<Input
						value={content.footer}
						onChange={(e) => updateContent(type, "footer", e.target.value)}
						placeholder="© {year} {site_name}"
					/>
				</div>

				{/* Merge Tags Help */}
				<div className="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
					<p className="text-xs font-medium text-gray-600 dark:text-gray-300 mb-2">
						Available Merge Tags:
					</p>
					<div className="flex flex-wrap gap-1.5">
						{MERGE_TAGS.map((item) => (
							<code
								key={item.tag}
								className="text-[10px] bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 px-1.5 py-0.5 rounded cursor-help"
								title={item.desc}
							>
								{item.tag}
							</code>
						))}
					</div>
				</div>
			</div>
		);
	};

	return (
		<div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
			{/* Header */}
			<div className="border-b border-gray-200 dark:border-gray-700 p-4">
				<div className="flex items-center justify-between">
					<h3 className="text-lg font-semibold text-gray-900 dark:text-white my-0!">
						✏️ Customize Template
					</h3>
					<button
						type="button"
						onClick={onSave}
						disabled={isSaving}
						className="flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
					>
						<Save size={16} />
						{isSaving ? "Menyimpan..." : "Simpan"}
					</button>
				</div>
			</div>

			{/* Sections */}
			<div className="divide-y divide-gray-200 dark:divide-gray-700">
				{/* Design Section */}
				<div>
					<SectionHeader id="design" icon={Palette} title="Design & Colors" />
					{expandedSections.design && (
						<div className="p-4 pt-0 space-y-4">
							<TemplateSelector
								value={template.design.template}
								onChange={(val) => updateDesign("template", val)}
								isProActive={isProActive}
							/>

							<div className="grid grid-cols-2 gap-4">
								<div>
									<Label className="text-xs mb-1">Header Color</Label>
									<div className="flex items-center gap-2">
										<Input
											type="color"
											value={template.design.header_color}
											onChange={(e) =>
												updateDesign("header_color", e.target.value)
											}
											className="w-12 h-10 p-1 cursor-pointer"
										/>
										<span className="text-xs font-mono text-gray-500 uppercase">
											{template.design.header_color}
										</span>
									</div>
								</div>
								<div>
									<Label className="text-xs mb-1">Button Color</Label>
									<div className="flex items-center gap-2">
										<Input
											type="color"
											value={template.design.button_color}
											onChange={(e) =>
												updateDesign("button_color", e.target.value)
											}
											className="w-12 h-10 p-1 cursor-pointer"
										/>
										<span className="text-xs font-mono text-gray-500 uppercase">
											{template.design.button_color}
										</span>
									</div>
								</div>
							</div>

							{/* Logo Upload */}
							<div>
								<Label className="text-xs mb-2">Logo Email</Label>
								<LogoUploader
									value={template.logo}
									onChange={(logo: LogoData) =>
										onChange({ ...template, logo })
									}
								/>
							</div>
						</div>
					)}
				</div>

				{/* Content Pending Section */}
				<div>
					<SectionHeader
						id="content-pending"
						icon={Type}
						title="Content: Pending Email"
						badge="⏳"
					/>
					{expandedSections["content-pending"] && (
						<ContentEditor type="pending" />
					)}
				</div>

				{/* Content Success Section */}
				<div>
					<SectionHeader
						id="content-success"
						icon={Type}
						title="Content: Success Email"
						badge="✓"
					/>
					{expandedSections["content-success"] && (
						<ContentEditor type="success" />
					)}
				</div>

				{/* Advanced Section */}
				<div>
					<SectionHeader id="advanced" icon={Settings2} title="Advanced" />
					{expandedSections.advanced && (
						<div className="p-4 pt-0 space-y-4">
							<div className="space-y-3">
								{[
									{
										key: "show_campaign_info" as const,
										label: "Show Campaign Info",
									},
									{
										key: "show_donation_details" as const,
										label: "Show Donation Details",
									},
									{
										key: "show_payment_instructions" as const,
										label: "Show Payment Instructions (Pending only)",
									},
									{
										key: "show_receipt_button" as const,
										label: "Show Receipt Button (Success only)",
									},
								].map((item) => (
									<label
										key={item.key}
										className="flex items-center gap-3 cursor-pointer"
									>
										<input
											type="checkbox"
											checked={template.advanced[item.key]}
											onChange={(e) =>
												updateAdvanced(item.key, e.target.checked)
											}
											className="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
										/>
										<span className="text-sm text-gray-700 dark:text-gray-300">
											{item.label}
										</span>
									</label>
								))}
							</div>

							{/* Custom CSS */}
							<div>
								<Label className="text-xs mb-1 flex items-center gap-2">
									Custom CSS
									{!isProActive && <Lock size={12} className="text-gray-400" />}
								</Label>
								<Textarea
									value={template.design.custom_css}
									onChange={(e) => updateDesign("custom_css", e.target.value)}
									placeholder=".container { /* custom styles */ }"
									rows={4}
									disabled={!isProActive}
									className="font-mono text-xs"
								/>
							</div>
						</div>
					)}
				</div>
			</div>
		</div>
	);
}
