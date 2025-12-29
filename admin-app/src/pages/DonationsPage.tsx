
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { clsx } from 'clsx'
import { CheckCircle } from 'lucide-react'

// Mock Data Type
interface Donation {
    id: number
    name: string
    amount: number
    status: 'pending' | 'complete'
    date: string
}

export default function DonationsPage() {
    const queryClient = useQueryClient()

    const { data: donations, isLoading } = useQuery({
        queryKey: ['donations'],
        queryFn: async () => {
            const response = await fetch('/wp-json/wpd/v1/donations', {
                headers: { 'X-WP-Nonce': (window as any).wpdSettings?.nonce }
            });
            if (!response.ok) return [];
            return response.json();
        }
    })

    // Mutation for status update
    const mutation = useMutation({
        mutationFn: async (id: number) => {
            const response = await fetch(`/wp-json/wpd/v1/donations/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': (window as any).wpdSettings?.nonce
                },
                body: JSON.stringify({ status: 'complete' })
            })
            if (!response.ok) throw new Error('Failed to update status')
            return response.json()
        },
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ['donations'] })
        }
    })

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <h2 className="text-2xl font-bold text-gray-800">Donations</h2>
                <a
                    href={`/wp-json/wpd/v1/export/donations?_wpnonce=${(window as any).wpdSettings?.nonce}`}
                    target="_blank"
                    className="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 font-medium text-sm flex items-center gap-2"
                >
                    Export CSV
                </a>
            </div>

            <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <table className="w-full text-left text-sm text-gray-600">
                    <thead className="bg-gray-50 border-b border-gray-200 font-medium text-gray-900">
                        <tr>
                            <th className="px-6 py-4">ID</th>
                            <th className="px-6 py-4">Donor</th>
                            <th className="px-6 py-4">Amount</th>
                            <th className="px-6 py-4">Status</th>
                            <th className="px-6 py-4">Date</th>
                            <th className="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-200">
                        {isLoading ? (
                            <tr><td colSpan={6} className="px-6 py-4 text-center">Loading...</td></tr>
                        ) : donations && donations.length > 0 ? (
                            donations.map((donation: Donation) => (
                                <tr key={donation.id} className="hover:bg-gray-50">
                                    <td className="px-6 py-4 font-medium text-gray-900">#{donation.id}</td>
                                    <td className="px-6 py-4">{donation.name}</td>
                                    <td className="px-6 py-4">Rp {donation.amount.toLocaleString('id-ID')}</td>
                                    <td className="px-6 py-4 capitalize">
                                        <span className={clsx(
                                            'px-2 py-1 rounded-full text-xs font-semibold',
                                            donation.status === 'complete' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'
                                        )}>
                                            {donation.status}
                                        </span>
                                    </td>
                                    <td className="px-6 py-4">{donation.date}</td>
                                    <td className="px-6 py-4 text-right flex justify-end gap-2">
                                        {donation.status === 'pending' && (
                                            <button
                                                onClick={() => mutation.mutate(donation.id)}
                                                disabled={mutation.isPending}
                                                className="text-green-600 hover:text-green-800 font-medium flex items-center gap-1"
                                                title="Mark as Complete"
                                            >
                                                <CheckCircle size={16} /> Mark Complete
                                            </button>
                                        )}
                                    </td>
                                </tr>
                            ))
                        ) : (
                            <tr><td colSpan={6} className="px-6 py-4 text-center">No donations found.</td></tr>
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    )
}
