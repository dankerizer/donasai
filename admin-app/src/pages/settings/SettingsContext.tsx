import { useMutation, useQueryClient } from "@tanstack/react-query";
import {
	createContext,
	type ReactNode,
	useContext,
	useEffect,
	useState,
} from "react";
import { toast } from "sonner";
import { useSettingsFetch } from "./hooks/use-settings-data";
import { initialFormData, type Page, type SettingsFormData } from "./types";

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
	proSettings: Record<string, unknown>;
	pages: Page[];
	showProModal: boolean;
	setShowProModal: (show: boolean) => void;
	// Helper functions
	addAccount: () => void;
	removeAccount: (index: number) => void;
	updateAccount: (
		index: number,
		field: string,
		value: string | boolean,
	) => void;
}

const SettingsContext = createContext<SettingsContextType | undefined>(
	undefined,
);

export function SettingsProvider({ children }: { children: ReactNode }) {
	const queryClient = useQueryClient();
	const [formData, setFormData] = useState<SettingsFormData>(initialFormData);
	const [isProInstalled, setIsProInstalled] = useState(false);
	const [licenseStatus, setLicenseStatus] = useState("inactive");
	const [licenseKey, setLicenseKey] = useState("");

	// Pro Settings from Localization
	const proSettings: Record<string, unknown> =
		(window as unknown as { wpdProSettings?: Record<string, unknown> })
			.wpdProSettings || {};

	// Pages State
	const [pages, setPages] = useState<Page[]>([]);
	const [showProModal, setShowProModal] = useState(false);

	// Fetch Settings using extracted hook
	const { data: fetchResult, isLoading } = useSettingsFetch();

	// Sync fetched data to local state
	useEffect(() => {
		if (fetchResult) {
			setFormData(fetchResult.formData);
			setPages(fetchResult.pages);
			setIsProInstalled(fetchResult.isProInstalled);
			setLicenseStatus(fetchResult.licenseStatus);
			setLicenseKey(fetchResult.licenseKey);
		}
	}, [fetchResult]);

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
					pending_expiry_hours: data.pending_expiry_hours,
					email_reminder_enabled: data.email_reminder_enabled,
					email_reminder_delay: data.email_reminder_delay,
					enable_pdf_download: data.enable_pdf_download,
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
					sidebar_count: data.sidebar_count,
					donor_per_page: data.donor_per_page,
					show_countdown: data.show_countdown,
					show_prayer_tab: data.show_prayer_tab,
					show_updates_tab: data.show_updates_tab,
					show_donor_list: data.show_donor_list,
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
			};

			const response = await fetch("/wp-json/wpd/v1/settings", {
				method: "POST",
				headers: {
					"Content-Type": "application/json",
					"X-WP-Nonce":
						(window as unknown as { wpdSettings?: { nonce?: string } })
							.wpdSettings?.nonce ?? "",
				},
				body: JSON.stringify(payload),
			});
			if (!response.ok) throw new Error("Failed to save settings");
			return response.json();
		},
		onSuccess: () => {
			queryClient.invalidateQueries({ queryKey: ["settings-data"] });
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

	const updateAccount = (
		index: number,
		field: string,
		value: string | boolean,
	) => {
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
