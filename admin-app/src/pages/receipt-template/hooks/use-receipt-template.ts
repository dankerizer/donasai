import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { toast } from "sonner";
import type { LogoData } from "@/components/shared/LogoUploader";

export interface ReceiptTemplate {
	logo: {
		attachment_id: number;
		url: string;
		width: number;
		height: number;
	};
	organization: {
		name: string;
		address_line_1?: string;
		address_line_2?: string;
		city?: string;
		postal_code?	: string;
		phone: string;
		email: string;
		website: string;
		tax_id: string;

		// Logo handling
		logo?: string | LogoData; // string for General, LogoData for Receipt
	};
	footer: {
		content: string;
		show_qr_code: boolean;
		show_campaign_desc: boolean;
	};
	advanced: {
		format: "html" | "pdf";
		auto_send_email: boolean;
		include_serial_number: boolean;
		header_color: string;
	};
}

export function useReceiptTemplate() {
	const queryClient = useQueryClient();

	// Fetch current template settings
	const { data, isLoading, error } = useQuery<{
		success: boolean;
		data: ReceiptTemplate;
	}>({
		queryKey: ["receipt-template"],
		queryFn: async () => {
			const response = await fetch("/wp-json/donasai-pro/v1/receipt-template", {
				headers: {
					"X-WP-Nonce": (window as any).wpApiSettings?.nonce || "",
				},
			});

			if (!response.ok) {
				throw new Error("Failed to fetch receipt template");
			}

			return response.json();
		},
	});

	// Save template settings
	const saveMutation = useMutation({
		mutationFn: async (template: ReceiptTemplate) => {
			const response = await fetch("/wp-json/donasai-pro/v1/receipt-template", {
				method: "POST",
				headers: {
					"Content-Type": "application/json",
					"X-WP-Nonce": (window as any).wpApiSettings?.nonce || "",
				},
				body: JSON.stringify(template),
			});

			if (!response.ok) {
				throw new Error("Failed to save receipt template");
			}

			return response.json();
		},
		onSuccess: () => {
			queryClient.invalidateQueries({ queryKey: ["receipt-template"] });
			toast.success("Receipt template saved successfully!");
		},
		onError: () => {
			toast.error("Failed to save receipt template");
		},
	});

	// Generate preview
	const previewMutation = useMutation({
		mutationFn: async (template: ReceiptTemplate) => {
			const response = await fetch(
				"/wp-json/donasai-pro/v1/receipt-template/preview",
				{
					method: "POST",
					headers: {
						"Content-Type": "application/json",
						"X-WP-Nonce": (window as any).wpApiSettings?.nonce || "",
					},
					body: JSON.stringify({ template }),
				},
			);

			if (!response.ok) {
				throw new Error("Failed to generate preview");
			}

			return response.json();
		},
	});

	return {
		template: data?.data,
		isLoading,
		error,
		saveTemplate: saveMutation.mutate,
		isSaving: saveMutation.isPending,
		generatePreview: previewMutation.mutate,
		previewData: previewMutation.data,
		isGeneratingPreview: previewMutation.isPending,
	};
}
