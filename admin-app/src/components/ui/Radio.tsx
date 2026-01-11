
import { clsx } from 'clsx';
import { forwardRef } from 'react';
import { twMerge } from 'tailwind-merge';

export interface RadioProps
  extends React.InputHTMLAttributes<HTMLInputElement> {
  error?: boolean;
}

const Radio = forwardRef<HTMLInputElement, RadioProps>(
  ({ className, error, ...props }, ref) => {
    return (
      <div className="relative inline-flex items-center justify-center w-4 h-4 shrink-0">
        <input
          type="radio"
          className={twMerge(
            clsx(
              'peer appearance-none h-4 w-4 shrink-0 rounded-full border border-emerald-600 bg-white checked:bg-emerald-600 checked:border-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 transition-colors cursor-pointer',
              'focus:ring-offset-white',
              error && 'border-red-500',
              className
            )
          )}
          ref={ref}
          {...props}
        />
        <div className="absolute w-2 h-2 rounded-full bg-white opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity" />
      </div>
    );
  }
);
Radio.displayName = 'Radio';

export { Radio };
