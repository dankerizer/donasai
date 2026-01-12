import clsx from "clsx";
import { ChevronLeft, FileText, Mail, Palette } from "lucide-react";
import { useEditorState, type EditorComponent } from "../hooks/useEditorState";

const components: {
	id: EditorComponent;
	label: string;
	icon: typeof Mail;
	available: boolean;
}[] = [
	{ id: "email", label: "Template Email", icon: Mail, available: true },
	{
		id: "receipt",
		label: "Template Kuitansi",
		icon: FileText,
		available: true,
	},
];

export function ComponentSelector() {
	const {
		selectedComponent,
		setSelectedComponent,
		sidebarVisible,
		setSidebarVisible,
	} = useEditorState();

	return (
		<div
			className={clsx(
				"bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-all duration-300 flex flex-col",
				sidebarVisible ? "w-56" : "w-0 overflow-hidden",
			)}
		>
			{/* Header */}
			<div className="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
				<div className="flex items-center gap-2">
					<Palette size={18} className="text-emerald-600" />
					<span className="font-semibold text-gray-900 dark:text-white text-sm">
						Components
					</span>
				</div>
				<button
					type="button"
					onClick={() => setSidebarVisible(false)}
					className="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded"
					title="Hide sidebar"
				>
					<ChevronLeft size={16} className="text-gray-400" />
				</button>
			</div>

			{/* Component List */}
			<div className="flex-1 p-3 space-y-1">
				{components.map((comp) => {
					const Icon = comp.icon;
					const isSelected = selectedComponent === comp.id;

					return (
						<button
							key={comp.id}
							type="button"
							onClick={() => comp.available && setSelectedComponent(comp.id)}
							disabled={!comp.available}
							className={clsx(
								"w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-left text-sm transition-all",
								isSelected
									? "bg-emerald-50 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-700"
									: "text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700",
								!comp.available && "opacity-50 cursor-not-allowed",
							)}
						>
							<Icon size={16} />
							<span className="font-medium">{comp.label}</span>
						</button>
					);
				})}
			</div>

			{/* Future Components */}
			<div className="p-3 border-t border-gray-200 dark:border-gray-700">
				<p className="text-[10px] text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-2">
					Coming Soon
				</p>
				<div className="space-y-1 opacity-50">
					{["Campaign Page", "Donation Form", "Hero Section"].map((item) => (
						<div
							key={item}
							className="px-3 py-2 text-xs text-gray-400 dark:text-gray-500 flex items-center gap-2"
						>
							<div className="w-4 h-4 rounded bg-gray-200 dark:bg-gray-700" />
							{item}
						</div>
					))}
				</div>
			</div>
		</div>
	);
}

// Toggle button for when sidebar is hidden
export function SidebarToggle() {
	const { sidebarVisible, setSidebarVisible } = useEditorState();

	if (sidebarVisible) return null;

	return (
		<button
			type="button"
			onClick={() => setSidebarVisible(true)}
			className="fixed left-0 top-1/2 -translate-y-1/2 z-20 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-r-lg px-1.5 py-3 shadow-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
			title="Show sidebar"
		>
			<Palette size={16} className="text-emerald-600" />
		</button>
	);
}
