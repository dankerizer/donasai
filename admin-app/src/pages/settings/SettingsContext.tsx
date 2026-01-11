import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import {
    createContext,
    type ReactNode,
    useContext,
    useState
} from "react";
import { toast } from "sonner";

// Define Types (Extracted from usage in Settings.tsx)
export interface ProAccount {
	id: string;
	bank_name: string;
	account_number: string;
	account_name: string;
	is_default: boolean;
}

export interface SettingsFormData {
	// General
	campaign_slug: string;
	payment_slug: string;
	remove_branding: boolean;
	confirmation_page: string;
	delete_on_uninstall_settings: boolean;
	delete_on_uninstall_tables: boolean;
	// Donation
	min_amount: number;
	presets: string;
	anonymous_label: string;
	create_user: boolean;
	recurring_intervals: string[];
	// Appearance
	brand_color: string;
	button_color: string;
	container_width: string;
	border_radius: string;
	campaign_layout: string;
	hero_style: string;
	font_family: string;
	font_size: string;
	dark_mode: boolean;
	donation_layout: string;
	sidebar_count: number;
	donor_per_page: number;
	// Bank
	bank_name: string;
	account_number: string;
	account_name: string;
	pro_accounts: ProAccount[];
	// Midtrans
	midtrans_enabled: boolean;
	midtrans_production: boolean;
	midtrans_server_key: string;
	// Pro Midtrans
	pro_midtrans_server_key: string;
	pro_midtrans_client_key: string;
	pro_midtrans_production: boolean;
	// Pro Xendit
	pro_xendit_api_key: string;
	// Pro Tripay
	pro_tripay_api_key: string;
	pro_tripay_private_key: string;
	pro_tripay_merchant_code: string;
	pro_tripay_is_production: boolean;
	// Organization
	org_name: string;
	org_address: string;
	org_phone: string;
	org_email: string;
	org_logo: string;
	// Notifications
	opt_in_email: string;
	opt_in_whatsapp: string;
}

interface Page {
	id: number;
	title: string;
}

interface SettingsContextType {
	formData: SettingsFormData;
	setFormData: React.Dispatch<React.SetStateAction<SettingsFormData>>;
	isLoading: boolean;
	isSaving: boolean;
	saveSettings: (e?: React.FormEvent) => void;
	isProInstalled: boolean;
	licenseStatus: string;
	setLicenseStatus: (status: string) => void;
	licenseKey: string;
	proSettings: any;
	pages: Page[];
	showProModal: boolean;
	setShowProModal: (show: boolean) => void;
	// Helper functions
	addAccount: () => void;
	removeAccount: (index: number) => void;
	updateAccount: (index: number, field: string, value: any) => void;
}

const SettingsContext = createContext<SettingsContextType | undefined>(
	undefined,
);

const initialFormData: SettingsFormData = {
	// General
	campaign_slug: "campaign",
	payment_slug: "pay",
	remove_branding: false,
	confirmation_page: "",
	delete_on_uninstall_settings: false,
	delete_on_uninstall_tables: false,
	// Donation
	min_amount: 10000,
	presets: "50000,100000,200000,500000",
	anonymous_label: "Hamba Allah",
	create_user: false,
	recurring_intervals: ["month", "year"],
	// Appearance
	brand_color: "#059669",
	button_color: "#ec4899",
	container_width: "1100px",
	border_radius: "12px",
	campaign_layout: "sidebar-right",
	hero_style: "standard",
	font_family: "Inter",
	font_size: "16px",
	dark_mode: false,
	donation_layout: "default",
	sidebar_count: 5,
	donor_per_page: 10,
	// Bank
	bank_name: "",
	account_number: "",
	account_name: "",
	pro_accounts: [],
	// Midtrans
	midtrans_enabled: false,
	midtrans_production: false,
	midtrans_server_key: "",
	// Pro Midtrans
	pro_midtrans_server_key: "",
	pro_midtrans_client_key: "",
	pro_midtrans_production: false,
	// Pro Xendit
	pro_xendit_api_key: "",
	// Pro Tripay
	pro_tripay_api_key: "",
	pro_tripay_private_key: "",
	pro_tripay_merchant_code: "",
	pro_tripay_is_production: false,
	// Organization
	org_name: "",
	org_address: "",
	org_phone: "",
	org_email: "",
	org_logo: "",
	// Notifications
	opt_in_email: "",
	opt_in_whatsapp: "",
};

export function SettingsProvider({ children }: { children: ReactNode }) {
	const queryClient = useQueryClient();
	const [formData, setFormData] = useState<SettingsFormData>(initialFormData);
	const [isProInstalled, setIsProInstalled] = useState(false);
	const [licenseStatus, setLicenseStatus] = useState("inactive");
	const [licenseKey, setLicenseKey] = useState("");

	// Pro Settings from Localization
	const proSettings = (window as any).wpdProSettings || {};

	// Pages State
	const [pages, setPages] = useState<Page[]>([]);
	const [showProModal, setShowProModal] = useState(false);

	// Fetch Settings
	const { isLoading } = useQuery({
		queryKey: ["settings-sync"],
		queryFn: async () => {
			const response = await fetch("/wp-json/wpd/v1/settings", {
				headers: { "X-WP-Nonce": (window as any).wpdSettings?.nonce },
			});
			const data = await response.json();

			if (data.pages) {
				setPages(data.pages);
			}

			setIsProInstalled(data.is_pro_installed || false);
			setLicenseStatus(data.license?.status || "inactive");
			setLicenseKey(data.license?.key || "");

			setFormData((prev) => ({
				...prev,
				// General
				campaign_slug: data.general?.campaign_slug || "campaign",
				payment_slug: data.general?.payment_slug || "pay",
				remove_branding:
					data.general?.remove_branding === true ||
					data.general?.remove_branding === "1",
				confirmation_page: data.general?.confirmation_page || "",
				delete_on_uninstall_settings:
					data.general?.delete_on_uninstall_settings === true ||
					data.general?.delete_on_uninstall_settings === "1",
				delete_on_uninstall_tables:
					data.general?.delete_on_uninstall_tables === true ||
					data.general?.delete_on_uninstall_tables === "1",
				// Donation
				min_amount: data.donation?.min_amount || 10000,
				presets: data.donation?.presets || "50000,100000,200000,500000",
				anonymous_label: data.donation?.anonymous_label || "Hamba Allah",
				create_user:
					data.donation?.create_user === true ||
					data.donation?.create_user === "1",
				recurring_intervals: data.donation?.recurring_intervals || [
					"month",
					"year",
				],
				// Appearance
				brand_color: data.appearance?.brand_color || "#059669",
				button_color: data.appearance?.button_color || "#ec4899",
				container_width: data.appearance?.container_width || "1100px",
				border_radius: data.appearance?.border_radius || "12px",
				campaign_layout: data.appearance?.campaign_layout || "sidebar-right",
				hero_style: data.appearance?.hero_style || "standard",
				font_family: data.appearance?.font_family || "Inter",
				font_size: data.appearance?.font_size || "16px",
				dark_mode:
					data.appearance?.dark_mode === true ||
					data.appearance?.dark_mode === "1",
				donation_layout: data.appearance?.donation_layout || "default",
				sidebar_count: data.appearance?.sidebar_count || 5,
				donor_per_page: data.appearance?.donor_per_page || 10,
				// Bank
				bank_name: data.bank?.bank_name || "",
				account_number: data.bank?.account_number || "",
				account_name: data.bank?.account_name || "",
				pro_accounts: data.bank?.pro_accounts || [],
				// Midtrans
				midtrans_enabled:
					data.midtrans?.enabled === true || data.midtrans?.enabled === "1",
				midtrans_production:
					data.midtrans?.is_production === true ||
					data.midtrans?.is_production === "1",
				midtrans_server_key: data.midtrans?.server_key || "",
				// Pro Midtrans
				pro_midtrans_server_key: data.midtrans?.pro_server_key || "",
				pro_midtrans_client_key: data.midtrans?.pro_client_key || "",
				pro_midtrans_production: data.midtrans?.pro_is_production === true,
				// Pro Xendit
				pro_xendit_api_key: data.xendit?.api_key || "",
				// Pro Tripay
				pro_tripay_api_key: data.tripay?.api_key || "",
				pro_tripay_private_key: data.tripay?.private_key || "",
				pro_tripay_merchant_code: data.tripay?.merchant_code || "",
				pro_tripay_is_production: data.tripay?.is_production === true,
				// Organization
				org_name: data.organization?.org_name || "",
				org_address: data.organization?.org_address || "",
				org_phone: data.organization?.org_phone || "",
				org_email: data.organization?.org_email || "",
				org_logo: data.organization?.org_logo || "",
				// Notifications
				opt_in_email: data.notifications?.opt_in_email || "",
				opt_in_whatsapp: data.notifications?.opt_in_whatsapp || "",
			}));

			return data;
		},
		staleTime: 0,
	});

	// Update Settings
	const mutation = useMutation({
		mutationFn: async (data: SettingsFormData) => {
			const payload = {
				general: {
					campaign_slug: data.campaign_slug,
					payment_slug: data.payment_slug,
					remove_branding: data.remove_branding,
					confirmation_page: data.confirmation_page,
				},
				donation: {
					min_amount: data.min_amount,
					presets: data.presets,
					anonymous_label: data.anonymous_label,
					create_user: data.create_user,
					recurring_intervals: data.recurring_intervals,
				},
				appearance: {
					brand_color: data.brand_color,
					button_color: data.button_color,
					container_width: data.container_width,
					border_radius: data.border_radius,
					campaign_layout: data.campaign_layout,
					hero_style: data.hero_style,
					font_family: data.font_family,
					font_size: data.font_size,
					dark_mode: data.dark_mode,
					donation_layout: data.donation_layout,
				},
				bank: {
					bank_name: data.bank_name,
					account_number: data.account_number,
					account_name: data.account_name,
					pro_accounts: data.pro_accounts,
				},
				midtrans: {
					enabled: data.midtrans_enabled,
					is_production: data.midtrans_production,
					server_key: data.midtrans_server_key,
					// Pro keys
					pro_server_key: data.pro_midtrans_server_key,
					pro_client_key: data.pro_midtrans_client_key,
					pro_is_production: data.pro_midtrans_production,
				},
				xendit: {
					api_key: data.pro_xendit_api_key,
				},
				tripay: {
					api_key: data.pro_tripay_api_key,
					private_key: data.pro_tripay_private_key,
					merchant_code: data.pro_tripay_merchant_code,
					is_production: data.pro_tripay_is_production,
				},
				organization: {
					org_name: data.org_name,
					org_address: data.org_address,
					org_phone: data.org_phone,
					org_email: data.org_email,
					org_logo: data.org_logo,
				},
				notifications: {
					opt_in_email: data.opt_in_email,
					opt_in_whatsapp: data.opt_in_whatsapp,
				},
				license: {
					key: licenseKey,
				},
			};

			const response = await fetch("/wp-json/wpd/v1/settings", {
				method: "POST",
				headers: {
					"Content-Type": "application/json",
					"X-WP-Nonce": (window as any).wpdSettings?.nonce,
				},
				body: JSON.stringify(payload),
			});
			if (!response.ok) throw new Error("Failed to save settings");
			return response.json();
		},
		onSuccess: () => {
			queryClient.invalidateQueries({ queryKey: ["settings"] });
			toast.success("Pengaturan berhasil disimpan!", {
				description: "Semua perubahan telah tersimpan ke database.",
			});
		},
		onError: (error: Error) => {
			toast.error("Gagal menyimpan pengaturan", {
				description:
					error.message || "Terjadi kesalahan saat menyimpan pengaturan.",
			});
		},
	});

	const addAccount = () => {
		setFormData((prev) => ({
			...prev,
			pro_accounts: [
				...prev.pro_accounts,
				{
					id: Date.now().toString(),
					bank_name: "",
					account_number: "",
					account_name: "",
					is_default: false,
				},
			],
		}));
	};

	const removeAccount = (index: number) => {
		const newAccounts = [...formData.pro_accounts];
		newAccounts.splice(index, 1);
		setFormData({ ...formData, pro_accounts: newAccounts });
	};

	const updateAccount = (index: number, field: string, value: any) => {
		const newAccounts = [...formData.pro_accounts];
		// @ts-expect-error - Dynamic field
		newAccounts[index][field] = value;
		setFormData({ ...formData, pro_accounts: newAccounts });
	};

	const saveSettings = (e?: React.FormEvent) => {
		if (e) e.preventDefault();
		mutation.mutate(formData);
	};

	return (
		<SettingsContext.Provider
			value={{
				formData,
				setFormData,
				isLoading,
				isSaving: mutation.isPending,
				saveSettings,
				isProInstalled,
				licenseStatus,
				setLicenseStatus,
				licenseKey,
				proSettings,
				pages,
				showProModal,
				setShowProModal,
				addAccount,
				removeAccount,
				updateAccount,
			}}
		>
			{children}
		</SettingsContext.Provider>
	);
}

export function useSettings() {
	const context = useContext(SettingsContext);
	if (!context) {
		throw new Error("useSettings must be used within a SettingsProvider");
	}
	return context;
}
