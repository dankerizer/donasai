import { clsx } from "clsx";
import { forwardRef } from "react";
import { twMerge } from "tailwind-merge";

const Label = forwardRef<
	HTMLLabelElement,
	React.LabelHTMLAttributes<HTMLLabelElement>
>(({ className, ...props }, ref) => (
	<label
		ref={ref}
		className={twMerge(
			clsx(
				"text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 text-gray-700 dark:text-gray-200! mb-2 block",
				className,
			),
		)}
		{...props}
	/>
));
Label.displayName = "Label";

export { Label };
