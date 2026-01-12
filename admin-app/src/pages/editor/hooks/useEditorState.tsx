import { createContext, useContext, useState, type ReactNode } from "react";

export type EditorComponent = "email" | "receipt";
export type DeviceSize = "mobile" | "tablet" | "desktop";

interface EditorState {
	// Selected component
	selectedComponent: EditorComponent;
	setSelectedComponent: (component: EditorComponent) => void;

	// Device preview
	deviceSize: DeviceSize;
	setDeviceSize: (size: DeviceSize) => void;

	// Panel visibility
	sidebarVisible: boolean;
	setSidebarVisible: (visible: boolean) => void;
	panelVisible: boolean;
	setPanelVisible: (visible: boolean) => void;

	// Saving state
	isSaving: boolean;
	setIsSaving: (saving: boolean) => void;
}

const EditorContext = createContext<EditorState | null>(null);

export function EditorProvider({ children }: { children: ReactNode }) {
	const [selectedComponent, setSelectedComponent] =
		useState<EditorComponent>("email");
	const [deviceSize, setDeviceSize] = useState<DeviceSize>("desktop");
	const [sidebarVisible, setSidebarVisible] = useState(true);
	const [panelVisible, setPanelVisible] = useState(true);
	const [isSaving, setIsSaving] = useState(false);

	return (
		<EditorContext.Provider
			value={{
				selectedComponent,
				setSelectedComponent,
				deviceSize,
				setDeviceSize,
				sidebarVisible,
				setSidebarVisible,
				panelVisible,
				setPanelVisible,
				isSaving,
				setIsSaving,
			}}
		>
			{children}
		</EditorContext.Provider>
	);
}

export function useEditorState() {
	const context = useContext(EditorContext);
	if (!context) {
		throw new Error("useEditorState must be used within EditorProvider");
	}
	return context;
}

// Device size pixel values
export const DEVICE_SIZES: Record<DeviceSize, number | "100%"> = {
	mobile: 375,
	tablet: 768,
	desktop: "100%",
};
