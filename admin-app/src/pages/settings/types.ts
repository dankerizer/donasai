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
	preset_emoji: string;
	anonymous_label: string;
	create_user: boolean;
	recurring_intervals: string[];
	pending_expiry_hours: number;
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
	show_countdown: boolean;
	show_prayer_tab: boolean;
	show_updates_tab: boolean;
	show_donor_list: boolean;
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

export interface Page {
	id: number;
	title: string;
}

export const initialFormData: SettingsFormData = {
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
	preset_emoji: "💖",
	anonymous_label: "Hamba Allah",
	create_user: false,
	recurring_intervals: ["month", "year"],
	pending_expiry_hours: 48,
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
	show_countdown: true,
	show_prayer_tab: true,
	show_updates_tab: true,
	show_donor_list: true,
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
