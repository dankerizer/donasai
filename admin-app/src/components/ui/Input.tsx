import { clsx } from "clsx";
import { forwardRef } from "react";
import { twMerge } from "tailwind-merge";

export interface InputProps
	extends React.InputHTMLAttributes<HTMLInputElement> {
	error?: boolean;
}

const Input = forwardRef<HTMLInputElement, InputProps>(
	({ className, error, type, ...props }, ref) => {
		return (
			<input
				type={type}
				className={twMerge(
					clsx(
						"flex h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 disabled:cursor-not-allowed disabled:opacity-50 transition-colors dark:bg-zinc-800! dark:border-zinc-700! dark:text-white!",
						// Force override WP styles
						"border-gray-300!",
						"focus:ring-2! focus:border-emerald-500!",
						error && "border-red-500! focus:ring-red-500!",
						className,
					),
				)}
				ref={ref}
				{...props}
			/>
		);
	},
);
Input.displayName = "Input";

export { Input };
