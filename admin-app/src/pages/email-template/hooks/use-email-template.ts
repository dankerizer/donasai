import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { toast } from "sonner";

export interface EmailTemplate {
	design: {
		template: "modern" | "classic" | "minimal" | "corporate" | "bold";
		header_color: string;
		button_color: string;
		custom_css: string;
	};
	logo: {
		attachment_id: number;
		url: string;
		width: number;
		height: number;
	};
	content: {
		pending: {
			subject: string;
			greeting: string;
			body: string;
			footer: string;
		};
		success: {
			subject: string;
			greeting: string;
			body: string;
			footer: string;
		};
	};
	advanced: {
		show_campaign_info: boolean;
		show_donation_details: boolean;
		show_payment_instructions: boolean;
		show_receipt_button: boolean;
	};
}

export type EmailType = "pending" | "success";

// Helper to provide defaults for new fields
const transformResponse = (data: EmailTemplate): EmailTemplate => {
	return {
		...data,
		design: data.design || {
			template: "modern",
			header_color: "#059669",
			button_color: "#2563eb",
			custom_css: "",
		},
		logo: data.logo || { attachment_id: 0, url: "", width: 0, height: 0 },
		content: data.content || {
			pending: { subject: "", greeting: "", body: "", footer: "" },
			success: { subject: "", greeting: "", body: "", footer: "" },
		},
		advanced: data.advanced || {
			show_campaign_info: true,
			show_donation_details: true,
			show_payment_instructions: true,
			show_receipt_button: true,
		},
	};
};

export function useEmailTemplate() {
	const queryClient = useQueryClient();

	// Fetch current template settings
	const { data, isLoading, error } = useQuery<{
		success: boolean;
		data: EmailTemplate;
	}>({
		queryKey: ["email-template"],
		queryFn: async () => {
			const response = await fetch("/wp-json/donasai-pro/v1/email-template", {
				headers: {
					"X-WP-Nonce": (window as any).wpApiSettings?.nonce || "",
				},
			});

			if (!response.ok) {
				throw new Error("Failed to fetch email template");
			}

			const json = await response.json();
			json.data = transformResponse(json.data);
			return json;
		},
	});

	const updateTemplateLocal = (newTemplate: EmailTemplate) => {
		queryClient.setQueryData(["email-template"], {
			success: true,
			data: newTemplate,
		});
	};

	// Save template settings
	const saveMutation = useMutation({
		mutationFn: async (template: EmailTemplate) => {
			const response = await fetch("/wp-json/donasai-pro/v1/email-template", {
				method: "POST",
				headers: {
					"Content-Type": "application/json",
					"X-WP-Nonce": (window as any).wpApiSettings?.nonce || "",
				},
				body: JSON.stringify(template),
			});

			if (!response.ok) {
				throw new Error("Failed to save email template");
			}

			return response.json();
		},
		onSuccess: (data) => {
			queryClient.setQueryData(["email-template"], data);
			toast.success("Email template saved successfully!");
		},
		onError: () => {
			toast.error("Failed to save email template");
		},
	});

	// Generate preview
	const previewMutation = useMutation({
		mutationFn: async ({
			template,
			type,
		}: { template: EmailTemplate; type: EmailType }) => {
			const response = await fetch(
				"/wp-json/donasai-pro/v1/email-template/preview",
				{
					method: "POST",
					headers: {
						"Content-Type": "application/json",
						"X-WP-Nonce": (window as any).wpApiSettings?.nonce || "",
					},
					body: JSON.stringify({ template, type }),
				},
			);

			if (!response.ok) {
				throw new Error("Failed to generate preview");
			}

			return response.json();
		},
	});

	// Send test email
	const testEmailMutation = useMutation({
		mutationFn: async ({
			template,
			type,
			email,
		}: { template: EmailTemplate; type: EmailType; email: string }) => {
			const response = await fetch(
				"/wp-json/donasai-pro/v1/email-template/send-test",
				{
					method: "POST",
					headers: {
						"Content-Type": "application/json",
						"X-WP-Nonce": (window as any).wpApiSettings?.nonce || "",
					},
					body: JSON.stringify({ template, type, email }),
				},
			);

			if (!response.ok) {
				throw new Error("Failed to send test email");
			}

			return response.json();
		},
		onSuccess: (data) => {
			if (data.success) {
				toast.success(data.message);
			} else {
				toast.error(data.message);
			}
		},
		onError: () => {
			toast.error("Failed to send test email");
		},
	});

	return {
		template: data?.data,
		isLoading,
		error,
		saveTemplate: saveMutation.mutate,
		updateTemplate: updateTemplateLocal,
		isSaving: saveMutation.isPending,
		generatePreview: previewMutation.mutate,
		previewData: previewMutation.data,
		isGeneratingPreview: previewMutation.isPending,
		sendTestEmail: testEmailMutation.mutate,
		isSendingTestEmail: testEmailMutation.isPending,
	};
}
