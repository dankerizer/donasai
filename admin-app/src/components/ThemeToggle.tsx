import { Moon, Sun } from "lucide-react";
import { useTheme } from "./ThemeProvider";

export function ThemeToggle() {
	const { theme, setTheme } = useTheme();

	return (
		<button
			onClick={() => setTheme(theme === "light" ? "dark" : "light")}
			className="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
			aria-label="Toggle Theme"
			type="button"
		>
			<Sun className="h-5 w-5 rotate-0 scale-100 transition-all dark:-rotate-90 dark:scale-0 text-orange-500" />
			<Moon className="absolute h-5 w-5 rotate-90 scale-0 transition-all dark:rotate-0 dark:scale-100 text-slate-100 -mt-5" />
			<span className="sr-only">Toggle theme</span>
		</button>
	);
}
