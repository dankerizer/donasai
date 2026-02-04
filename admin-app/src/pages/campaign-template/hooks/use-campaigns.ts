import { useQuery } from "@tanstack/react-query";

export interface Campaign {
	id: number;
	title: string;
	link: string;
}

export function useCampaigns() {
	return useQuery<Campaign[]>({
		queryKey: ["campaigns-list"],
		queryFn: async () => {
			const response = await fetch("/wp-json/donasai/v1/campaigns/list", {
				headers: { "X-WP-Nonce": (window as any).donasaiSettings?.nonce },
			});

			if (!response.ok) {
				throw new Error("Failed to fetch campaigns");
			}

			const data = await response.json();
			return data; // Endpoint returns array of objects { id, title, link }
		},
		staleTime: 5 * 60 * 1000, // 5 minutes
	});
}
