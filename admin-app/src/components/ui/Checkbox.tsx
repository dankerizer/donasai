import { clsx } from "clsx";
import { Check } from "lucide-react";
import { forwardRef } from "react";
import { twMerge } from "tailwind-merge";

export interface CheckboxProps
	extends React.InputHTMLAttributes<HTMLInputElement> {
	error?: boolean;
}

const Checkbox = forwardRef<HTMLInputElement, CheckboxProps>(
	({ className, error, ...props }, ref) => {
		return (
			<div className="relative inline-flex items-center justify-center w-4 h-4 shrink-0">
				<input
					type="checkbox"
					className={twMerge(
						clsx(
							"peer appearance-none h-4 w-4 shrink-0 rounded-sm border border-emerald-600 bg-white checked:bg-emerald-600 checked:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 transition-colors cursor-pointer",
							"focus:ring-offset-white",
							error && "border-red-500",
							className,
						),
					)}
					ref={ref}
					{...props}
				/>
				<Check
					className="absolute w-3 h-3 text-white opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"
					strokeWidth={3}
				/>
			</div>
		);
	},
);
Checkbox.displayName = "Checkbox";

export { Checkbox };
