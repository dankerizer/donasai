import { useState } from 'react'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { Search, ExternalLink, Bot, Lock } from 'lucide-react'

export default function Confirmations() {
    const queryClient = useQueryClient()
    const [searchTerm, setSearchTerm] = useState('')

    // Fetch Donations
    const { data: donations, isLoading } = useQuery({
        queryKey: ['donations'],
        queryFn: async () => {
            const response = await fetch('/wp-json/wpd/v1/donations', {
                headers: { 'X-WP-Nonce': (window as any).wpdSettings?.nonce }
            })
            if (!response.ok) throw new Error('Failed to fetch donations')
            return response.json()
        }
    })

    // Filter for Pending Confirmations (on-hold OR has proof_url but not complete)
    const pendingConfirmations = donations?.filter((d: any) => {
        const hasProof = d.metadata?.proof_url;
        const isPending = d.status === 'on-hold' || d.status === 'pending';
        return hasProof && isPending;
    }) || []

    const filteredDonations = pendingConfirmations.filter((d: any) =>
        d.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
        d.email.toLowerCase().includes(searchTerm.toLowerCase())
    )

    // Update Status Mutation
    const updateStatus = useMutation({
        mutationFn: async ({ id, status }: { id: number, status: string }) => {
            const response = await fetch(`/wp-json/wpd/v1/donations/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': (window as any).wpdSettings?.nonce
                },
                body: JSON.stringify({ status })
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
            <div className="flex justify-between items-end">
                <div>
                    <h2 className="text-2xl font-bold text-gray-800">Donation Confirmations</h2>
                    <p className="text-gray-500">Review manual transfer receipts.</p>
                </div>
                <div className="relative">
                    <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" size={18} />
                    <input
                        type="text"
                        placeholder="Search donor..."
                        className="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        value={searchTerm}
                        onChange={e => setSearchTerm(e.target.value)}
                    />
                </div>
            </div>

            {/* AI Banner (Locked) */}
            <div className="bg-linear-to-r from-indigo-50 to-purple-50 border border-indigo-100 rounded-xl p-4 flex items-center justify-between">
                <div className="flex items-center gap-4">
                    <div className="p-3 bg-white text-indigo-600 rounded-lg shadow-sm">
                        <Bot size={24} />
                    </div>
                    <div>
                        <h3 className="font-bold text-gray-900 flex items-center gap-2">
                            AI Payment Verification
                            <span className="bg-gray-800 text-white text-[10px] px-1.5 py-0.5 rounded uppercase flex items-center gap-1">
                                <Lock size={8} /> Pro
                            </span>
                        </h3>
                        <p className="text-sm text-gray-600">Automatically verify receipt amounts and names using AI OCR technology.</p>
                    </div>
                </div>
                <button disabled className="px-4 py-2 bg-white text-gray-400 font-medium rounded-lg border border-gray-200 cursor-not-allowed text-sm">
                    Upgrade to Enable
                </button>
            </div>

            <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <table className="w-full text-left">
                    <thead className="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th className="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Donation #</th>
                            <th className="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Donor</th>
                            <th className="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Amount</th>
                            <th className="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Proof</th>
                            <th className="px-6 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-200">
                        {isLoading ? (
                            <tr><td colSpan={5} className="px-6 py-10 text-center text-gray-500">Loading...</td></tr>
                        ) : filteredDonations.length === 0 ? (
                            <tr><td colSpan={5} className="px-6 py-10 text-center text-gray-500">No pending confirmations found.</td></tr>
                        ) : (
                            filteredDonations.map((donation: any) => (
                                <tr key={donation.id} className="hover:bg-gray-50">
                                    <td className="px-6 py-4 font-medium text-gray-900">#{donation.id}</td>
                                    <td className="px-6 py-4">
                                        <div className="font-medium text-gray-900">{donation.name}</div>
                                        <div className="text-sm text-gray-500">{donation.email}</div>
                                    </td>
                                    <td className="px-6 py-4 font-medium text-green-600">
                                        Rp {donation.amount.toLocaleString('id-ID')}
                                    </td>
                                    <td className="px-6 py-4">
                                        {donation.metadata?.proof_url ? (
                                            <a
                                                href={donation.metadata.proof_url}
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                className="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 underline"
                                            >
                                                View Receipt <ExternalLink size={12} />
                                            </a>
                                        ) : (
                                            <span className="text-gray-400 text-sm">No file</span>
                                        )}
                                    </td>
                                    <td className="px-6 py-4 text-right space-x-2">
                                        <button
                                            onClick={() => updateStatus.mutate({ id: donation.id, status: 'complete' })}
                                            className="px-3 py-1 bg-green-100 text-green-700 rounded text-sm font-medium hover:bg-green-200"
                                        >
                                            Approve
                                        </button>
                                        <button
                                            onClick={() => updateStatus.mutate({ id: donation.id, status: 'pending' })} // Or some rejected status
                                            className="px-3 py-1 bg-red-100 text-red-700 rounded text-sm font-medium hover:bg-red-200"
                                        >
                                            Reject
                                        </button>
                                    </td>
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    )
}
