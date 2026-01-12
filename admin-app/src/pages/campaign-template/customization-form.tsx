import clsx from "clsx";
import { ChevronDown, Layout, Palette, Settings, Type } from "lucide-react";
import { useState } from "react";
import { Input } from "/src/components/ui/Input";
import { Label } from "/src/components/ui/Label";
import { Select } from "/src/components/ui/Select";
import type { CampaignTemplate } from "./hooks/use-campaign-template";
import { LayoutSelector } from "/src/components/shared/LayoutSelector";

interface CustomizationFormProps {
	template: CampaignTemplate | undefined;
	onChange: (template: CampaignTemplate) => void;
	onSave: () => void;
	isSaving: boolean;
	isProActive?: boolean;
}

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
						<span className="bg-purple-100 text-purple-700 text-[10px] px-1.5 py-0.5 rounded font-bold">
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

	const toggleSection = (section: string) => {
		setOpenSection(openSection === section ? "" : section);
	};

	const updateField = <K extends keyof CampaignTemplate>(
		key: K,
		value: CampaignTemplate[K],
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
					{/* Colors - Side by side */}
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
									className="w-8 h-8 rounded border border-gray-300 dark:border-gray-600 cursor-pointer p-0"
								/>
								<Input
									type="text"
									value={template.brand_color}
									onChange={(e) => updateField("brand_color", e.target.value)}
									className="flex-1 font-mono text-xs uppercase"
									maxLength={7}
								/>
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
									className="w-8 h-8 rounded border border-gray-300 dark:border-gray-600 cursor-pointer p-0"
								/>
								<Input
									type="text"
									value={template.button_color}
									onChange={(e) => updateField("button_color", e.target.value)}
									className="flex-1 font-mono text-xs uppercase"
									maxLength={7}
								/>
							</div>
						</div>
					</div>

					{/* Container & Border */}
					<div className="grid grid-cols-2 gap-3">
						<div>
							<Label htmlFor="container_width" className="text-xs">
								Lebar Kontainer
							</Label>
							<Input
								id="container_width"
								type="text"
								value={template.container_width}
								onChange={(e) => updateField("container_width", e.target.value)}
								placeholder="1100px"
								className="mt-1 text-sm"
							/>
						</div>
						<div>
							<Label htmlFor="border_radius" className="text-xs">
								Border Radius
							</Label>
							<Input
								id="border_radius"
								type="text"
								value={template.border_radius}
								onChange={(e) => updateField("border_radius", e.target.value)}
								placeholder="12px"
								className="mt-1 text-sm"
							/>
						</div>
					</div>

					{/* Sidebar/Donor counts */}
					<div className="grid grid-cols-2 gap-3">
						<div>
							<Label htmlFor="sidebar_count" className="text-xs">
								Donatur Sidebar
							</Label>
							<Input
								id="sidebar_count"
								type="number"
								value={template.sidebar_count}
								onChange={(e) =>
									updateField(
										"sidebar_count",
										Number.parseInt(e.target.value) || 5,
									)
								}
								min={1}
								max={20}
								className="mt-1 text-sm"
							/>
						</div>
						<div>
							<Label htmlFor="donor_per_page" className="text-xs">
								Donatur/Halaman
							</Label>
							<Input
								id="donor_per_page"
								type="number"
								value={template.donor_per_page}
								onChange={(e) =>
									updateField(
										"donor_per_page",
										Number.parseInt(e.target.value) || 10,
									)
								}
								min={1}
								max={50}
								className="mt-1 text-sm"
							/>
						</div>
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
				<div className="space-y-6">
					{/* Campaign Layout */}
					<div>
						<Label className="text-xs mb-3 block text-gray-500 font-semibold">
							Layout Halaman
						</Label>
						<LayoutSelector
							value={template.campaign_layout}
							onChange={(val) =>
								updateField(
									"campaign_layout",
									val as CampaignTemplate["campaign_layout"],
								)
							}
							options={[
								{
									id: "sidebar-right",
									label: "Sidebar Kanan",
									visual: (
										<>
											<div className="bg-gray-300 dark:bg-gray-500 h-full flex-2 rounded-sm" />
											<div className="bg-emerald-200 dark:bg-emerald-700 h-full flex-1 rounded-sm" />
										</>
									),
								},
								{
									id: "sidebar-left",
									label: "Sidebar Kiri",
									visual: (
										<>
											<div className="bg-emerald-200 dark:bg-emerald-700 h-full flex-1 rounded-sm" />
											<div className="bg-gray-300 dark:bg-gray-500 h-full flex-2 rounded-sm" />
										</>
									),
								},
								{
									id: "full-width",
									label: "Full Width",
									visual: (
										<div className="bg-gray-300 dark:bg-gray-500 h-full flex-1 rounded-sm" />
									),
								},
							]}
						/>
					</div>

					{/* Donation Form Layout (PRO) */}
					{isProActive && (
						<div>
							<Label className="text-xs mb-3 block text-gray-500 font-semibold">
								Layout Form Donasi
							</Label>
							<LayoutSelector
								value={template.donation_layout}
								onChange={(val) =>
									updateField(
										"donation_layout",
										val as CampaignTemplate["donation_layout"],
									)
								}
								gridCols={2}
								options={[
									{
										id: "default",
										label: "Tunggal",
										visual: (
											<div className="bg-gray-300 dark:bg-gray-500 h-full flex-1 rounded-sm" />
										),
									},
									{
										id: "split",
										label: "Split",
										visual: (
											<>
												<div className="bg-gray-300 dark:bg-gray-500 h-full flex-1 rounded-sm" />
												<div className="bg-emerald-200 dark:bg-emerald-700 h-full flex-1 rounded-sm opacity-60" />
											</>
										),
									},
								]}
							/>
						</div>
					)}

					{/* Hero Style (PRO) */}
					{isProActive && (
						<div>
							<Label className="text-xs mb-3 block text-gray-500 font-semibold">
								Gaya Hero
							</Label>
							<LayoutSelector
								value={template.hero_style}
								onChange={(val) =>
									updateField(
										"hero_style",
										val as CampaignTemplate["hero_style"],
									)
								}
								options={[
									{ id: "standard", label: "Standard" },
									{ id: "wide", label: "Wide" },
									{ id: "overlay", label: "Overlay" },
								]}
							/>
						</div>
					)}
				</div>
			</AccordionSection>

			{/* Typography Section (PRO) */}
			<AccordionSection
				title="Tipografi"
				icon={Type}
				isOpen={openSection === "typography"}
				onToggle={() => toggleSection("typography")}
				badge={!isProActive ? "PRO" : undefined}
			>
				{isProActive ? (
					<div className="space-y-4">
						<div>
							<Label htmlFor="font_family" className="text-xs">
								Font Utama
							</Label>
							<Select
								id="font_family"
								value={template.font_family}
								onChange={(e) => updateField("font_family", e.target.value)}
								className="mt-1 text-sm"
							>
								<option value="Inter">Inter (Default)</option>
								<option value="Roboto">Roboto</option>
								<option value="Open Sans">Open Sans</option>
								<option value="Poppins">Poppins</option>
								<option value="Lato">Lato</option>
							</Select>
						</div>
						<div>
							<Label htmlFor="font_size" className="text-xs">
								Ukuran Font Dasar
							</Label>
							<Input
								id="font_size"
								type="text"
								value={template.font_size}
								onChange={(e) => updateField("font_size", e.target.value)}
								placeholder="16px"
								className="mt-1 text-sm"
							/>
						</div>
						<div className="flex items-center justify-between">
							<Label className="text-xs">Mode Gelap</Label>
							<label className="relative inline-flex items-center cursor-pointer">
								<input
									type="checkbox"
									className="sr-only peer"
									checked={template.dark_mode}
									onChange={(e) => updateField("dark_mode", e.target.checked)}
								/>
								<div className="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600" />
							</label>
						</div>
					</div>
				) : (
					<div className="text-center py-4 text-gray-500 text-sm">
						<p>Upgrade ke PRO untuk mengakses fitur tipografi.</p>
					</div>
				)}
			</AccordionSection>

			{/* Features Section (PRO) */}
			<AccordionSection
				title="Fitur Halaman"
				icon={Settings}
				isOpen={openSection === "features"}
				onToggle={() => toggleSection("features")}
				badge={!isProActive ? "PRO" : undefined}
			>
				{isProActive ? (
					<div className="space-y-3">
						{[
							{ key: "show_countdown", label: "Countdown Timer" },
							{ key: "show_prayer_tab", label: "Tab Doa" },
							{ key: "show_updates_tab", label: "Kabar Terbaru" },
							{ key: "show_donor_list", label: "List Donatur Sidebar" },
						].map((feature) => (
							<div
								key={feature.key}
								className="flex items-center justify-between"
							>
								<Label className="text-xs">{feature.label}</Label>
								<label className="relative inline-flex items-center cursor-pointer">
									<input
										type="checkbox"
										className="sr-only peer"
										checked={
											template[feature.key as keyof CampaignTemplate] as boolean
										}
										onChange={(e) =>
											updateField(
												feature.key as keyof CampaignTemplate,
												e.target.checked as any,
											)
										}
									/>
									<div className="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600" />
								</label>
							</div>
						))}
					</div>
				) : (
					<div className="text-center py-4 text-gray-500 text-sm">
						<p>Upgrade ke PRO untuk mengakses kontrol fitur halaman.</p>
					</div>
				)}
			</AccordionSection>

			{/* Info Note */}
			<div className="px-4 py-3 bg-gray-50 dark:bg-gray-900 flex items-start gap-2">
				<span className="text-gray-400 shrink-0">ℹ️</span>
				<p className="text-[11px] text-gray-500 dark:text-gray-400 leading-relaxed my-0!">
					Perubahan akan berlaku di semua halaman campaign. Preview menunjukkan
					contoh halaman campaign.
				</p>
			</div>
		</div>
	);
}
