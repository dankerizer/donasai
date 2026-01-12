import { useMemo } from "react";
import { useMutation, useQueryClient } from "@tanstack/react-query";
import { toast } from "sonner";
import { useSettingsFetch } from "/src/pages/settings/hooks/use-settings-data";

export interface DonationFormTemplate {
	// Design (Appearance)
	brand_color: string;
	button_color: string;
	border_radius: string;

	// Layout (Appearance)
	donation_layout: "default" | "split";

	// Settings (Donation)
	min_amount: number;
	presets: string;
	anonymous_label: string;
	create_user: boolean;

	// Internal (Read-only for preview construction)
	payment_slug: string;
}

const DEFAULT_TEMPLATE: DonationFormTemplate = {
	brand_color: "#059669",
	button_color: "#ec4899",
	border_radius: "12px",
	donation_layout: "default",
	min_amount: 10000,
	presets: "50000,100000,200000,500000",
	anonymous_label: "Hamba Allah",
	create_user: true,
	payment_slug: "pay",
};

export function useDonationFormTemplate() {
	const queryClient = useQueryClient();
	const settingsQuery = useSettingsFetch();

	// Extract donation form data from settings with stable reference
	const template: DonationFormTemplate | undefined = useMemo(() => {
		return settingsQuery.data
			? {
					// Design
					brand_color: settingsQuery.data.formData.brand_color,
					button_color: settingsQuery.data.formData.button_color,
					border_radius: settingsQuery.data.formData.border_radius,
					// Layout
					donation_layout: settingsQuery.data.formData
						.donation_layout as DonationFormTemplate["donation_layout"],
					// Settings
					min_amount: settingsQuery.data.formData.min_amount,
					presets: settingsQuery.data.formData.presets,
					anonymous_label: settingsQuery.data.formData.anonymous_label,
					create_user: settingsQuery.data.formData.create_user,

					// General
					payment_slug: settingsQuery.data.formData.payment_slug,
				}
			: undefined;
	}, [settingsQuery.data]);

	// Save mutation
	const saveMutation = useMutation({
		mutationFn: async (newTemplate: DonationFormTemplate) => {
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
						border_radius: newTemplate.border_radius,
						donation_layout: newTemplate.donation_layout,
					},
					donation: {
						min_amount: newTemplate.min_amount,
						presets: newTemplate.presets,
						anonymous_label: newTemplate.anonymous_label,
						create_user: newTemplate.create_user,
					},
				}),
			});

			if (!response.ok) {
				throw new Error("Failed to save donation form template");
			}

			return response.json();
		},
		onSuccess: () => {
			toast.success("Template donasi berhasil disimpan");
			queryClient.invalidateQueries({ queryKey: ["settings-data"] });
		},
		onError: () => {
			toast.error("Gagal menyimpan template donasi");
		},
	});

	return {
		template: template || DEFAULT_TEMPLATE,
		isLoading: settingsQuery.isLoading,
		saveTemplate: (
			template: DonationFormTemplate,
			options?: { onSettled?: () => void },
		) => {
			saveMutation.mutate(template, options);
		},
		isSaving: saveMutation.isPending,
		isProInstalled: settingsQuery.data?.isProInstalled || false,
	};
}
