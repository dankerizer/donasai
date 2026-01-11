import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { toast } from "sonner";
import type { LogoData } from "/src/components/shared/LogoUploader";

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
		postal_code?: string;
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
	// New Fields
	design: {
		template: "modern" | "classic" | "minimal" | "corporate" | "bold";
		custom_css: string;
	};
	signature: {
		enabled: boolean;
		image: {
			attachment_id: number;
			url: string;
			width: number;
			height: number;
		};
		label: string;
	};
	serial: {
		enabled: boolean;
		format: string;
	};
}

// Helper to provide defaults for new fields if backend returns undefined (migration)
const transformResponse = (data: ReceiptTemplate): ReceiptTemplate => {
	return {
		...data,
		design: data.design || { template: "modern", custom_css: "" },
		signature: data.signature || {
			enabled: false,
			image: { attachment_id: 0, url: "", width: 0, height: 0 },
			label: "Authorized Signature",
		},
		serial: data.serial || { enabled: true, format: "INV/{Y}/{m}/{0000}" },
	};
};

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

			const json = await response.json();
			// Ensure defaults for new fields
			json.data = transformResponse(json.data);
			return json;
		},
	});

	const updateTemplateLocal = (newTemplate: ReceiptTemplate) => {
		queryClient.setQueryData(["receipt-template"], {
			success: true,
			data: newTemplate,
		});
	};

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
		onSuccess: (data) => {
			queryClient.setQueryData(["receipt-template"], data); // Update cache with server response
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
		updateTemplate: updateTemplateLocal, // Expose local update for sync buttons
		isSaving: saveMutation.isPending,
		isUpdating: saveMutation.isPending, // Alias for sync buttons
		generatePreview: previewMutation.mutate,
		previewData: previewMutation.data,
		isGeneratingPreview: previewMutation.isPending,
	};
}
