import clsx from "clsx";
import { Check } from "lucide-react";

interface TemplateSelectorProps {
	value: string;
	onChange: (value: any) => void;
	compact?: boolean;
}

const TEMPLATES = [
	{ id: "modern", name: "Modern", color: "bg-emerald-500" },
	{ id: "classic", name: "Classic", color: "bg-gray-800" },
	{ id: "minimal", name: "Minimal", color: "bg-gray-100 border border-gray-300" },
	{ id: "corporate", name: "Corporate", color: "bg-blue-600" },
	{ id: "bold", name: "Bold", color: "bg-black" },
	{ id: "elegant", name: "Elegant", color: "bg-amber-600" },
	{ id: "simple", name: "Simple", color: "bg-white border-2 border-dashed border-gray-400" },
	{ id: "creative", name: "Creative", color: "bg-violet-500" },
	{ id: "official", name: "Official", color: "bg-slate-700" },
];

export function TemplateSelector({ value, onChange, compact = false }: TemplateSelectorProps) {
	// Compact mode for sidebar
	if (compact) {
		return (
			<div className="grid grid-cols-3 gap-1.5">
				{TEMPLATES.map((template) => (
					<button
						key={template.id}
						type="button"
						onClick={() => onChange(template.id)}
						className={clsx(
							"relative rounded-lg p-1.5 text-left transition-all border",
							value === template.id
								? "border-emerald-500 bg-emerald-50 dark:bg-emerald-950"
								: "border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-gray-300",
						)}
					>
						{value === template.id && (
							<Check size={10} className="absolute top-1 right-1 text-emerald-600" />
						)}
						<div
							className={clsx(
								"w-full h-6 rounded mb-1",
								template.color.includes("bg-") ? template.color : "bg-gray-200",
							)}
						/>
						<div className="text-[10px] font-medium text-gray-900 dark:text-white text-center truncate">
							{template.name}
						</div>
					</button>
				))}
			</div>
		);
	}

	// Original 3-column layout
	return (
		<div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
			{TEMPLATES.map((template) => (
				<div
					key={template.id}
					onClick={() => onChange(template.id)}
					className={clsx(
						"relative cursor-pointer rounded-xl border-2 p-4 transition-all hover:border-emerald-300 dark:hover:border-emerald-700",
						value === template.id
							? "border-emerald-500 bg-emerald-50 dark:bg-emerald-950/30"
							: "border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800",
					)}
				>
					<div className="flex items-start justify-between mb-3">
						<div
							className={clsx(
								"h-10 w-10 rounded-lg shadow-sm",
								template.color.includes("bg-") ? template.color : "bg-gray-200",
							)}
						/>
						{value === template.id && (
							<div className="h-6 w-6 rounded-full bg-emerald-500 flex items-center justify-center text-white">
								<Check size={14} strokeWidth={3} />
							</div>
						)}
					</div>
					<h4 className="font-semibold text-gray-900 dark:text-white">
						{template.name}
					</h4>
				</div>
			))}
		</div>
	);
}

