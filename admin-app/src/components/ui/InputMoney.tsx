import { clsx } from "clsx";
import { forwardRef } from "react";
import { twMerge } from "tailwind-merge";

export interface InputMoneyProps
	extends Omit<
		React.InputHTMLAttributes<HTMLInputElement>,
		"onChange" | "value"
	> {
	value: number | string;
	onChange: (value: number) => void;
	currency?: string;
	error?: boolean;
}

const InputMoney = forwardRef<HTMLInputElement, InputMoneyProps>(
	({ className, error, value, onChange, currency = "Rp", ...props }, ref) => {
		// Format value to display
		const formatValue = (val: number | string) => {
			if (val === "" || val === undefined || val === null) return "";
			const num =
				typeof val === "string" ? parseInt(val.replace(/\D/g, "")) : val;
			if (Number.isNaN(num)) return "";
			return new Intl.NumberFormat("id-ID").format(num);
		};

		const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
			// Remove dots/commas to get raw number
			const rawValue = e.target.value.replace(/\D/g, "");
			const numValue = parseInt(rawValue, 10);
			onChange(Number.isNaN(numValue) ? 0 : numValue);
		};

		return (
			<div className="relative w-full">
				<span className="absolute left-3 top-[47%] -translate-y-1/2 text-gray-500/50 text-sm font-medium z-10 pointer-events-none">
					{currency}
				</span>
				<input
					type="text"
					className={twMerge(
						clsx(
							"flex h-10 w-full rounded-lg border pl-8! border-gray-300 bg-white pr-3! py-2! text-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 disabled:cursor-not-allowed disabled:opacity-50 transition-colors dark:bg-zinc-800! dark:border-zinc-700! dark:text-white!",
							// Force override WP styles
							"border-gray-300!",
							"focus:ring-2! focus:border-emerald-500!",
							// Padding left for currency
							"pl-10",
							error && "border-red-500! focus:ring-red-500!",
							className,
						),
					)}
					value={formatValue(value)}
					onChange={handleChange}
					ref={ref}
					{...props}
				/>
			</div>
		);
	},
);
InputMoney.displayName = "InputMoney";

export { InputMoney };
