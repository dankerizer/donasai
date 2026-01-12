import clsx from "clsx";
import {
	Banknote,
	ChevronDown,
	Layout,
	Palette,
	Settings,
	User,
} from "lucide-react";
import { useState } from "react";
import { Input } from "/src/components/ui/Input";
import { Label } from "/src/components/ui/Label";
import type { DonationFormTemplate } from "./hooks/use-donation-form-template";

interface CustomizationFormProps {
	template?: DonationFormTemplate;
	onChange: (template: DonationFormTemplate) => void;
	onSave: () => void;
	isSaving: boolean;
	isProActive?: boolean;
}

interface AccordionSectionProps {
	title: string;
	icon: React.ElementType;
	isOpen: boolean;
	onToggle: () => void;
	children: React.ReactNode;
}

function AccordionSection({
	title,
	icon: Icon,
	isOpen,
	onToggle,
	children,
}: AccordionSectionProps) {
	return (
		<div className="border-b border-gray-200 dark:border-gray-700">
			<button
				type="button"
				onClick={onToggle}
				className="w-full flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
			>
				<div className="flex items-center gap-3">
					<div className="p-2 bg-emerald-50 dark:bg-emerald-900/30 rounded-lg text-emerald-600 dark:text-emerald-400">
						<Icon size={18} />
					</div>
					<h3 className="font-medium text-gray-900 dark:text-gray-100">
						{title}
					</h3>
				</div>
				<ChevronDown
					size={16}
					className={clsx(
						"text-gray-400 transition-transform duration-200",
						isOpen ? "transform rotate-180" : "",
					)}
				/>
			</button>
			<div
				className={clsx(
					"overflow-hidden transition-all duration-200 ease-in-out",
					isOpen ? "max-h-[1000px] opacity-100" : "max-h-0 opacity-0",
				)}
			>
				<div className="p-4 pt-0 space-y-4">{children}</div>
			</div>
		</div>
	);
}

export function DonationFormCustomizationForm({
	template,
	onChange,
	isProActive = true,
}: CustomizationFormProps) {
	const [openSection, setOpenSection] = useState("design");

	if (!template) {
		return (
			<div className="p-8 text-center text-gray-500">
				Memuat pengaturan donasi...
			</div>
		);
	}

	const toggleSection = (section: string) => {
		setOpenSection(openSection === section ? "" : section);
	};

	const updateField = <K extends keyof DonationFormTemplate>(
		key: K,
		value: DonationFormTemplate[K],
	) => {
		onChange({ ...template, [key]: value });
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
					<div className="grid grid-cols-2 gap-3">
						<div>
							<Label htmlFor="brand_color" className="text-xs">
								Warna Merek
							</Label>
							<div className="flex items-center gap-2 mt-1">
								<input
									type="color"
									id="brand_color"
									value={template.brand_color}
									onChange={(e) => updateField("brand_color", e.target.value)}
									className="w-8 h-8 rounded border border-gray-300 dark:border-gray-600 cursor-pointer p-0.5"
								/>
								<span className="text-xs text-gray-500 font-mono">
									{template.brand_color}
								</span>
							</div>
						</div>
						<div>
							<Label htmlFor="button_color" className="text-xs">
								Warna Tombol
							</Label>
							<div className="flex items-center gap-2 mt-1">
								<input
									type="color"
									id="button_color"
									value={template.button_color}
									onChange={(e) => updateField("button_color", e.target.value)}
									className="w-8 h-8 rounded border border-gray-300 dark:border-gray-600 cursor-pointer p-0.5"
								/>
								<span className="text-xs text-gray-500 font-mono">
									{template.button_color}
								</span>
							</div>
						</div>
					</div>

					<div>
						<Label htmlFor="border_radius" className="text-xs">
							Border Radius
						</Label>
						<Input
							id="border_radius"
							value={template.border_radius}
							onChange={(e) => updateField("border_radius", e.target.value)}
							className="mt-1"
							placeholder="12px"
						/>
					</div>
				</div>
			</AccordionSection>

			{/* Layout Section */}
			<AccordionSection
				title="Layout"
				icon={Layout}
				isOpen={openSection === "layout"}
				onToggle={() => toggleSection("layout")}
			>
				<div className="space-y-4">
					<div className="flex items-center justify-between mb-2">
						<Label className="text-xs font-semibold text-gray-900 dark:text-gray-100">
							Layout Formulir
						</Label>
						{!isProActive && (
							<span className="text-[10px] font-bold bg-purple-100 text-purple-700 px-2 py-0.5 rounded uppercase">
								PRO
							</span>
						)}
					</div>

					<div
						className={clsx(
							"grid grid-cols-2 gap-3",
							!isProActive && "opacity-60 pointer-events-none",
						)}
					>
						{[
							{ id: "default", label: "Tunggal" },
							{ id: "split", label: "Split (Kiri-Kanan)" },
						].map((layout) => (
							<button
								key={layout.id}
								type="button"
								onClick={() =>
									updateField(
										"donation_layout",
										layout.id as DonationFormTemplate["donation_layout"],
									)
								}
								className={clsx(
									"p-2 rounded-lg border text-center transition-all",
									template.donation_layout === layout.id
										? "border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 ring-1 ring-emerald-500"
										: "border-gray-200 dark:border-gray-700 hover:border-emerald-200 dark:hover:border-emerald-800",
								)}
							>
								<div className="text-xs font-medium">{layout.label}</div>
							</button>
						))}
					</div>
				</div>
			</AccordionSection>

			{/* Nominal Section */}
			<AccordionSection
				title="Pengaturan Nominal"
				icon={Banknote}
				isOpen={openSection === "nominal"}
				onToggle={() => toggleSection("nominal")}
			>
				<div className="space-y-4">
					<div>
						<Label htmlFor="min_amount" className="text-xs">
							Minimal Donasi (Rp)
						</Label>
						<Input
							type="number"
							id="min_amount"
							value={template.min_amount}
							onChange={(e) =>
								updateField("min_amount", Number(e.target.value) || 0)
							}
							className="mt-1"
						/>
					</div>
					<div>
						<Label htmlFor="presets" className="text-xs">
							Pilihan Nominal (Pisahkan dengan koma)
						</Label>
						<Input
							id="presets"
							value={template.presets}
							onChange={(e) => updateField("presets", e.target.value)}
							className="mt-1"
							placeholder="50000,100000,250000"
						/>
						<p className="text-[10px] text-gray-500 mt-1">
							Contoh: 50000,100000,250000
						</p>
					</div>
				</div>
			</AccordionSection>

			{/* Donor Fields Section */}
			<AccordionSection
				title="Field Donatur"
				icon={User}
				isOpen={openSection === "donor"}
				onToggle={() => toggleSection("donor")}
			>
				<div className="space-y-4">
					<div>
						<Label htmlFor="anonymous_label" className="text-xs">
							Label Anonim
						</Label>
						<Input
							id="anonymous_label"
							value={template.anonymous_label}
							onChange={(e) => updateField("anonymous_label", e.target.value)}
							className="mt-1"
							placeholder="Hamba Allah"
						/>
					</div>

					<div className="flex items-center justify-between">
						<div>
							<Label htmlFor="create_user" className="text-xs">
								Buat Akun Otomatis
							</Label>
							<p className="text-[10px] text-gray-500">
								Buat akun user untuk donatur baru
							</p>
						</div>
						<label className="relative inline-flex items-center cursor-pointer">
							<input
								type="checkbox"
								id="create_user"
								className="sr-only peer"
								checked={template.create_user}
								onChange={(e) => updateField("create_user", e.target.checked)}
							/>
							<div className="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600" />
						</label>
					</div>
				</div>
			</AccordionSection>
		</div>
	);
}
