import { clsx } from "clsx";
import { forwardRef } from "react";
import { twMerge } from "tailwind-merge";

export interface SelectProps extends React.SelectHTMLAttributes<HTMLSelectElement> {
  error?: boolean;
}

const Select = forwardRef<HTMLSelectElement, SelectProps>(
  ({ className, children, error, ...props }, ref) => {
    return (
      <div className="relative">
        <select
          className={twMerge(
            clsx(
              "flex h-10 w-full items-center justify-between rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 disabled:cursor-not-allowed disabled:opacity-50 transition-colors appearance-none",
              // WP overrides
              "border-gray-300!",
              "focus:ring-2! focus:border-emerald-500!",
              error && "border-emerald-500! focus:ring-emerald-500!",
              className,
            ),
          )}
          ref={ref}
          {...props}
        >
          {children}
        </select>
        {/* <div className="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><title>Arrow Down</title><path d="m6 9 6 6 6-6"/></svg>
        </div> */}
      </div>
    );
  },
);
Select.displayName = "Select";

export { Select };
