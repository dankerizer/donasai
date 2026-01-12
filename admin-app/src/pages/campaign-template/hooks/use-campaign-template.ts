import { useMemo } from "react";
import { useMutation, useQueryClient } from "@tanstack/react-query";
import { toast } from "sonner";
import { useSettingsFetch } from "/src/pages/settings/hooks/use-settings-data";

// Campaign template uses same fields as Settings > Appearance
export interface CampaignTemplate {
	// Basic (FREE)
	brand_color: string;
	button_color: string;
	container_width: string;
	border_radius: string;
	campaign_layout: "sidebar-right" | "sidebar-left" | "full-width";
	sidebar_count: number;
	donor_per_page: number;
	// Advanced (PRO)
	font_family: string;
	font_size: string;
	dark_mode: boolean;
	donation_layout: "default" | "split";
	hero_style: "standard" | "wide" | "overlay";
	show_countdown: boolean;
	show_prayer_tab: boolean;
	show_updates_tab: boolean;
	show_donor_list: boolean;
}

const DEFAULT_TEMPLATE: CampaignTemplate = {
	brand_color: "#059669",
	button_color: "#ec4899",
	container_width: "1100px",
	border_radius: "12px",
	campaign_layout: "sidebar-right",
	sidebar_count: 5,
	donor_per_page: 10,
	font_family: "Inter",
	font_size: "16px",
	dark_mode: false,
	donation_layout: "default",
	hero_style: "standard",
	show_countdown: true,
	show_prayer_tab: true,
	show_updates_tab: true,
	show_donor_list: true,
};

export function useCampaignTemplate() {
	const queryClient = useQueryClient();
	const settingsQuery = useSettingsFetch();

	// Extract campaign template data from settings
	// Extract campaign template data from settings with stable reference
	const template: CampaignTemplate | undefined = useMemo(() => {
		return settingsQuery.data
			? {
					brand_color: settingsQuery.data.formData.brand_color,
					button_color: settingsQuery.data.formData.button_color,
					container_width: settingsQuery.data.formData.container_width,
					border_radius: settingsQuery.data.formData.border_radius,
					campaign_layout: settingsQuery.data.formData
						.campaign_layout as CampaignTemplate["campaign_layout"],
					sidebar_count: settingsQuery.data.formData.sidebar_count,
					donor_per_page: settingsQuery.data.formData.donor_per_page,
					font_family: settingsQuery.data.formData.font_family,
					font_size: settingsQuery.data.formData.font_size,
					dark_mode: settingsQuery.data.formData.dark_mode,
					donation_layout: settingsQuery.data.formData
						.donation_layout as CampaignTemplate["donation_layout"],
					hero_style: settingsQuery.data.formData
						.hero_style as CampaignTemplate["hero_style"],
					show_countdown: settingsQuery.data.formData.show_countdown,
					show_prayer_tab: settingsQuery.data.formData.show_prayer_tab,
					show_updates_tab: settingsQuery.data.formData.show_updates_tab,
					show_donor_list: settingsQuery.data.formData.show_donor_list,
				}
			: undefined;
	}, [settingsQuery.data]);

	// Save mutation - saves to same settings API
	const saveMutation = useMutation({
		mutationFn: async (newTemplate: CampaignTemplate) => {
			const response = await fetch("/wp-json/wpd/v1/settings", {
				method: "POST",
				headers: {
					"Content-Type": "application/json",
					"X-WP-Nonce": (window as any).wpdSettings?.nonce,
				},
				body: JSON.stringify({
					appearance: {
						brand_color: newTemplate.brand_color,
						button_color: newTemplate.button_color,
						container_width: newTemplate.container_width,
						border_radius: newTemplate.border_radius,
						campaign_layout: newTemplate.campaign_layout,
						sidebar_count: newTemplate.sidebar_count,
						donor_per_page: newTemplate.donor_per_page,
						font_family: newTemplate.font_family,
						font_size: newTemplate.font_size,
						dark_mode: newTemplate.dark_mode,
						donation_layout: newTemplate.donation_layout,
						hero_style: newTemplate.hero_style,
						show_countdown: newTemplate.show_countdown,
						show_prayer_tab: newTemplate.show_prayer_tab,
						show_updates_tab: newTemplate.show_updates_tab,
						show_donor_list: newTemplate.show_donor_list,
					},
				}),
			});

			if (!response.ok) {
				throw new Error("Failed to save campaign template");
			}

			return response.json();
		},
		onSuccess: () => {
			toast.success("Template halaman campaign berhasil disimpan");
			queryClient.invalidateQueries({ queryKey: ["settings-data"] });
		},
		onError: () => {
			toast.error("Gagal menyimpan template campaign");
		},
	});

	return {
		template: template || DEFAULT_TEMPLATE,
		isLoading: settingsQuery.isLoading,
		saveTemplate: (
			template: CampaignTemplate,
			options?: { onSettled?: () => void },
		) => {
			saveMutation.mutate(template, options);
		},
		isSaving: saveMutation.isPending,
		isProInstalled: settingsQuery.data?.isProInstalled || false,
		licenseStatus: settingsQuery.data?.licenseStatus || "inactive",
	};
}
