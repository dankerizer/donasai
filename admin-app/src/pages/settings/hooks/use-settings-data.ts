import { useQuery } from "@tanstack/react-query";
import { initialFormData, type Page, type SettingsFormData } from "../types";

export interface SettingsDataResult {
	formData: SettingsFormData;
	pages: Page[];
	isProInstalled: boolean;
	licenseStatus: string;
	licenseKey: string;
}

export function useSettingsFetch() {
	return useQuery({
		queryKey: ["settings-data"],
		queryFn: async (): Promise<SettingsDataResult> => {
			const response = await fetch("/wp-json/wpd/v1/settings", {
				headers: { "X-WP-Nonce": (window as any).wpdSettings?.nonce },
			});
			const data = await response.json();

			// Transform API data to SettingsFormData structure
			const transformedFormData: SettingsFormData = {
				...initialFormData,
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
				preset_emoji: data.donation?.preset_emoji || "💖",
				anonymous_label: data.donation?.anonymous_label || "Hamba Allah",
				create_user:
					data.donation?.create_user === true ||
					data.donation?.create_user === "1",
				recurring_intervals: data.donation?.recurring_intervals || [
					"month",
					"year",
				],
				pending_expiry_hours: data.donation?.pending_expiry_hours || 48,
				email_reminder_enabled:
					data.donation?.email_reminder_enabled === true ||
					data.donation?.email_reminder_enabled === "1",
				email_reminder_delay: data.donation?.email_reminder_delay || 24,
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
				show_countdown:
					data.appearance?.show_countdown !== false &&
					data.appearance?.show_countdown !== "0",
				show_prayer_tab:
					data.appearance?.show_prayer_tab !== false &&
					data.appearance?.show_prayer_tab !== "0",
				show_updates_tab:
					data.appearance?.show_updates_tab !== false &&
					data.appearance?.show_updates_tab !== "0",
				show_donor_list:
					data.appearance?.show_donor_list !== false &&
					data.appearance?.show_donor_list !== "0",
				show_leaderboard:
					data.appearance?.show_leaderboard !== false &&
					data.appearance?.show_leaderboard !== "0",
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
			};

			return {
				formData: transformedFormData,
				pages: data.pages || [],
				isProInstalled: data.is_pro_installed || false,
				licenseStatus: data.license?.status || "inactive",
				licenseKey: data.license?.key || "",
			};
		},
		staleTime: 0,
	});
}
