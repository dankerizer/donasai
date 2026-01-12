import clsx from "clsx";
import type { ReactNode } from "react";

export interface LayoutOption {
	id: string;
	label: string;
	visual?: ReactNode;
}

interface LayoutSelectorProps {
	options: LayoutOption[];
	value: string;
	onChange: (value: string) => void;
	gridCols?: 2 | 3 | 4;
	disabled?: boolean;
}

export function LayoutSelector({
	options,
	value,
	onChange,
	gridCols = 3,
	disabled = false,
}: LayoutSelectorProps) {
	return (
		<div
			className={clsx(
				"grid gap-2",
				gridCols === 2 && "grid-cols-2",
				gridCols === 3 && "grid-cols-3",
				gridCols === 4 && "grid-cols-4",
				disabled && "opacity-60 pointer-events-none",
			)}
		>
			{options.map((option) => (
				<button
					key={option.id}
					type="button"
					onClick={() => onChange(option.id)}
					disabled={disabled}
					className={clsx(
						"p-2 rounded-lg border text-center transition-all h-full flex flex-col items-center justify-center gap-1.5",
						value === option.id
							? "border-emerald-500 bg-emerald-50 dark:bg-emerald-950/30 ring-1 ring-emerald-500/50"
							: "border-gray-200 dark:border-gray-700 hover:border-emerald-200 dark:hover:border-emerald-800 hover:bg-gray-50 dark:hover:bg-gray-800/50",
					)}
				>
					{option.visual && (
						<div className="w-full h-10 bg-gray-100 dark:bg-gray-800 rounded border border-gray-100 dark:border-gray-700 flex gap-0.5 p-0.5 overflow-hidden">
							{option.visual}
						</div>
					)}
					<span
						className={clsx(
							"text-[10px] font-medium leading-tight",
							value === option.id
								? "text-emerald-700 dark:text-emerald-400"
								: "text-gray-600 dark:text-gray-400",
						)}
					>
						{option.label}
					</span>
				</button>
			))}
		</div>
	);
}
