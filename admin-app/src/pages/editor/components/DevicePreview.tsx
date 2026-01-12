import clsx from "clsx";
import { Monitor, Smartphone, Tablet } from "lucide-react";
import type { ReactNode } from "react";
import {
	DEVICE_SIZES,
	useEditorState,
	type DeviceSize,
} from "../hooks/useEditorState";

const devices: { id: DeviceSize; icon: typeof Monitor; label: string }[] = [
	{ id: "mobile", icon: Smartphone, label: "Mobile (375px)" },
	{ id: "tablet", icon: Tablet, label: "Tablet (768px)" },
	{ id: "desktop", icon: Monitor, label: "Desktop" },
];

export function DeviceToggle() {
	const { deviceSize, setDeviceSize } = useEditorState();

	return (
		<div className="inline-flex bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
			{devices.map((device) => {
				const Icon = device.icon;
				const isActive = deviceSize === device.id;

				return (
					<button
						key={device.id}
						type="button"
						onClick={() => setDeviceSize(device.id)}
						className={clsx(
							"p-2 rounded-md transition-all",
							isActive
								? "bg-white dark:bg-gray-600 text-emerald-600 dark:text-emerald-400 shadow-sm"
								: "text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200",
						)}
						title={device.label}
					>
						<Icon size={18} />
					</button>
				);
			})}
		</div>
	);
}

interface DevicePreviewProps {
	children: ReactNode;
}

export function DevicePreview({ children }: DevicePreviewProps) {
	const { deviceSize } = useEditorState();
	const width = DEVICE_SIZES[deviceSize];

	return (
		<div className="flex-1 bg-gray-100 dark:bg-gray-900 overflow-auto p-6">
			<div
				className="mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-2xl overflow-hidden transition-all duration-300 h-full"
				style={{
					width: width === "100%" ? "100%" : `${width}px`,
					maxWidth: "100%",
				}}
			>
				{/* Browser Chrome */}
				<div className="bg-gray-50 dark:bg-gray-900 px-4 py-2 border-b border-gray-200 dark:border-gray-700 flex items-center gap-2">
					<div className="flex gap-1.5">
						<div className="w-3 h-3 rounded-full bg-red-400" />
						<div className="w-3 h-3 rounded-full bg-yellow-400" />
						<div className="w-3 h-3 rounded-full bg-green-400" />
					</div>
					<div className="flex-1 bg-white dark:bg-gray-700 rounded px-3 py-1 text-xs text-gray-400 ml-2">
						preview.donasai.local
					</div>
				</div>

				{/* Content */}
				<div className="h-[calc(100%-40px)] overflow-auto">{children}</div>
			</div>
		</div>
	);
}
