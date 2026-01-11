import clsx from "clsx";
import { Lock } from "lucide-react";
import type { EmailTemplate } from "./hooks/use-email-template";

interface TemplateSelectorProps {
	value: string;
	onChange: (template: EmailTemplate["design"]["template"]) => void;
	isProActive: boolean;
}

const templates = [
	{
		id: "modern",
		name: "Modern",
		description: "Clean & contemporary design",
		preview: "bg-gradient-to-br from-emerald-500 to-teal-600",
	},
	{
		id: "classic",
		name: "Classic",
		description: "Traditional email style",
		preview: "bg-gradient-to-br from-gray-600 to-gray-800",
	},
	{
		id: "minimal",
		name: "Minimal",
		description: "Simple & lightweight",
		preview: "bg-gradient-to-br from-slate-100 to-slate-300",
	},
	{
		id: "corporate",
		name: "Corporate",
		description: "Professional business look",
		preview: "bg-gradient-to-br from-blue-600 to-indigo-700",
	},
	{
		id: "bold",
		name: "Bold",
		description: "Dark & striking design",
		preview: "bg-gradient-to-br from-purple-900 to-gray-900",
	},
] as const;

export function TemplateSelector({
	value,
	onChange,
	isProActive,
}: TemplateSelectorProps) {
	return (
		<div className="space-y-3">
			<label className="block text-sm font-medium text-gray-900 dark:text-white">
				Template Style
			</label>
			<div className="grid grid-cols-5 gap-3">
				{templates.map((template) => {
					const isSelected = value === template.id;
					const isLocked = !isProActive && template.id !== "modern";

					return (
						<button
							key={template.id}
							type="button"
							disabled={isLocked}
							onClick={() =>
								!isLocked &&
								onChange(template.id as EmailTemplate["design"]["template"])
							}
							className={clsx(
								"relative rounded-xl p-3 text-left transition-all",
								"border-2",
								isSelected
									? "border-emerald-600 bg-emerald-50 dark:bg-emerald-950 ring-1 ring-emerald-600"
									: "border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600",
								isLocked && "opacity-60 cursor-not-allowed",
							)}
						>
							{isLocked && (
								<div className="absolute top-2 right-2 z-10">
									<Lock size={12} className="text-gray-400" />
								</div>
							)}
							<div
								className={clsx(
									"w-full aspect-[4/3] rounded-lg mb-2",
									template.preview,
								)}
							>
								{/* Mini email preview */}
								<div className="p-2 h-full flex flex-col">
									<div className="w-6 h-1 bg-white/30 rounded mb-1" />
									<div className="w-10 h-0.5 bg-white/20 rounded" />
									<div className="flex-1 mt-1 bg-white/10 rounded" />
								</div>
							</div>
							<div className="text-xs font-medium text-gray-900 dark:text-white truncate">
								{template.name}
							</div>
							<div className="text-[10px] text-gray-500 dark:text-gray-400 truncate">
								{template.description}
							</div>
						</button>
					);
				})}
			</div>
		</div>
	);
}
