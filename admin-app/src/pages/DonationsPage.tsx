
import { useState } from 'react'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { clsx } from 'clsx'
import { CheckCircle, Eye, Pencil, Save, X } from 'lucide-react'

// Mock Data Type
interface Donation {
    id: number
    name: string
    email: string
    phone: string
    amount: number
    status: 'pending' | 'complete' | 'failed'
    payment_method: string
    gateway_txn_id: string
    note: string
    date: string
}

export default function DonationsPage() {
    const queryClient = useQueryClient()
    const [selectedDonation, setSelectedDonation] = useState<Donation | null>(null)
    const [isEditing, setIsEditing] = useState(false);
    const [editFormData, setEditFormData] = useState<Partial<Donation>>({});

    const handleEditClick = () => {
        if (selectedDonation) {
            setEditFormData({
                name: selectedDonation.name,
                email: selectedDonation.email,
                phone: selectedDonation.phone,
                amount: selectedDonation.amount,
                status: selectedDonation.status,
                note: selectedDonation.note
            });
            setIsEditing(true);
        }
    };

    const handleSave = () => {
        if (selectedDonation) {
            mutation.mutate({ id: selectedDonation.id, data: editFormData });
        }
    };

    const handleCancel = () => {
        setIsEditing(false);
        setEditFormData({});
    };

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

    // Mutation for update
    const mutation = useMutation({
        mutationFn: async (vars: { id: number, data: Partial<Donation> }) => {
            const response = await fetch(`/wp-json/wpd/v1/donations/${vars.id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': (window as any).wpdSettings?.nonce
                },
                body: JSON.stringify(vars.data)
            });
            if (!response.ok) throw new Error('Failed to update donation');
            return response.json();
        },
        onSuccess: (data) => {
            queryClient.invalidateQueries({ queryKey: ['donations'] });

            // If we are in the modal, update selectedDonation with returned data
            if (data.success && data.data) {
                // Ensure note and phone handle nulls gracefully
                const updated = {
                    ...data.data,
                    note: data.data.note || '',
                    phone: data.data.phone || '',
                };
                setSelectedDonation(updated);
            }
            setIsEditing(false);
        }
    });

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
                                    <td className="px-6 py-4">
                                        <div>{donation.name}</div>
                                        <div className="text-xs text-gray-500">{donation.email}</div>
                                    </td>
                                    <td className="px-6 py-4">Rp {donation.amount.toLocaleString('id-ID')}</td>
                                    <td className="px-6 py-4 capitalize">
                                        <span className={clsx(
                                            'px-2 py-1 rounded-full text-xs font-semibold',
                                            donation.status === 'complete' ? 'bg-green-100 text-green-700' :
                                                donation.status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700'
                                        )}>
                                            {donation.status}
                                        </span>
                                    </td>
                                    <td className="px-6 py-4">{donation.date}</td>
                                    <td className="px-6 py-4 text-right flex justify-end gap-2">
                                        <button
                                            onClick={() => setSelectedDonation(donation)}
                                            className="text-gray-600 hover:text-blue-600 p-1 rounded hover:bg-blue-50 transition-colors"
                                            title="View Details"
                                        >
                                            <Eye size={18} />
                                        </button>

                                        {donation.status === 'pending' && (
                                            <button
                                                onClick={() => mutation.mutate({ id: donation.id, data: { status: 'complete' } })}
                                                disabled={mutation.isPending}
                                                className="text-green-600 hover:text-green-800 p-1 rounded hover:bg-green-50 transition-colors"
                                                title="Mark as Complete"
                                            >
                                                <CheckCircle size={18} />
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

            {/* Donation Detail Modal */}
            {selectedDonation && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm animate-in fade-in duration-200">
                    <div className="bg-white rounded-xl shadow-xl w-full max-w-lg overflow-hidden animate-in zoom-in-95 duration-200 flex flex-col max-h-[90vh]">
                        <div className="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 shrink-0">
                            <h3 className="text-lg font-bold text-gray-800">
                                {isEditing ? `Edit Donation #${selectedDonation.id}` : `Donation Details #${selectedDonation.id}`}
                            </h3>
                            <div className="flex items-center gap-2">
                                {!isEditing && (
                                    <button
                                        onClick={handleEditClick}
                                        className="text-gray-500 hover:text-blue-600 p-1.5 rounded-full hover:bg-blue-50 transition-colors"
                                        title="Edit"
                                        type="button"
                                    >
                                        <Pencil size={18} />
                                    </button>
                                )}
                                <button
                                    onClick={() => { setSelectedDonation(null); setIsEditing(false); }}
                                    className="text-gray-400 hover:text-gray-600 p-1 rounded-full hover:bg-gray-200 transition-colors"
                                    type="button"
                                >
                                    <X size={20} />
                                </button>
                            </div>
                        </div>

                        <div className="p-6 space-y-4 overflow-y-auto">
                            {isEditing ? (
                                <div className="grid grid-cols-1 gap-4">
                                    <div>
                                        <label className="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Donor Name</label>
                                        <input
                                            type="text"
                                            value={editFormData.name || ''}
                                            onChange={(e) => setEditFormData(prev => ({ ...prev, name: e.target.value }))}
                                            className="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        />
                                    </div>
                                    <div className="grid grid-cols-2 gap-4">
                                        <div>
                                            <label className="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Amount</label>
                                            <input
                                                type="number"
                                                value={editFormData.amount || 0}
                                                onChange={(e) => setEditFormData(prev => ({ ...prev, amount: parseFloat(e.target.value) }))}
                                                className="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            />
                                        </div>
                                        <div>
                                            <label className="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Status</label>
                                            <select
                                                value={editFormData.status || 'pending'}
                                                onChange={(e) => setEditFormData(prev => ({ ...prev, status: e.target.value as any }))}
                                                className="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white"
                                            >
                                                <option value="pending">Pending</option>
                                                <option value="complete">Complete</option>
                                                <option value="failed">Failed</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        <label className="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Email</label>
                                        <input
                                            type="email"
                                            value={editFormData.email || ''}
                                            onChange={(e) => setEditFormData(prev => ({ ...prev, email: e.target.value }))}
                                            className="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Phone</label>
                                        <input
                                            type="text"
                                            value={editFormData.phone || ''}
                                            onChange={(e) => setEditFormData(prev => ({ ...prev, phone: e.target.value }))}
                                            className="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Note</label>
                                        <textarea
                                            value={editFormData.note || ''}
                                            onChange={(e) => setEditFormData(prev => ({ ...prev, note: e.target.value }))}
                                            className="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            rows={3}
                                        />
                                    </div>
                                </div>
                            ) : (
                                <div className="space-y-4">
                                    <div className="grid grid-cols-2 gap-4">
                                        <div>
                                            <p className="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Donor Name</p>
                                            <p className="font-medium text-gray-900">{selectedDonation.name}</p>
                                        </div>
                                        <div>
                                            <p className="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Amount</p>
                                            <p className="font-medium text-green-600 text-lg">Rp {selectedDonation.amount.toLocaleString('id-ID')}</p>
                                        </div>
                                        <div>
                                            <p className="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Email</p>
                                            <p className="text-gray-700 break-all">{selectedDonation.email}</p>
                                        </div>
                                        <div>
                                            <p className="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Phone</p>
                                            <p className="text-gray-700">{selectedDonation.phone || '-'}</p>
                                        </div>
                                        <div>
                                            <p className="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Payment Method</p>
                                            <p className="capitalize text-gray-700">{selectedDonation.payment_method}</p>
                                        </div>
                                        <div>
                                            <p className="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Status</p>
                                            <span className={clsx(
                                                'px-2 py-0.5 rounded text-xs font-medium',
                                                selectedDonation.status === 'complete' ? 'bg-green-100 text-green-700' :
                                                    selectedDonation.status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700'
                                            )}>
                                                {selectedDonation.status}
                                            </span>
                                        </div>
                                    </div>

                                    {selectedDonation.gateway_txn_id && (
                                        <div className="bg-gray-50 p-3 rounded border border-gray-200">
                                            <p className="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Transaction ID</p>
                                            <code className="text-sm">{selectedDonation.gateway_txn_id}</code>
                                        </div>
                                    )}

                                    {selectedDonation.note && (
                                        <div>
                                            <p className="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Note</p>
                                            <p className="text-sm text-gray-600 italic bg-gray-50 p-3 rounded border border-gray-100">
                                                "{selectedDonation.note}"
                                            </p>
                                        </div>
                                    )}

                                    <div className="pt-2 text-xs text-gray-400 text-center">
                                        Created at: {selectedDonation.date}
                                    </div>
                                </div>
                            )}
                        </div>

                        <div className="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-2 shrink-0">
                            {isEditing ? (
                                <>
                                    <button
                                        onClick={handleCancel}
                                        className="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm"
                                        disabled={mutation.isPending}
                                        type="button"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        onClick={handleSave}
                                        disabled={mutation.isPending}
                                        className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm flex items-center gap-2"
                                        type="button"
                                    >
                                        {mutation.isPending ? 'Saving...' : (
                                            <>
                                                <Save size={16} /> Save Changes
                                            </>
                                        )}
                                    </button>
                                </>
                            ) : (
                                <>
                                    {selectedDonation.status === 'pending' && (
                                        <button
                                            onClick={() => mutation.mutate({ id: selectedDonation.id, data: { status: 'complete' } })}
                                            className="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium text-sm flex items-center gap-2"
                                            disabled={mutation.isPending}
                                            type="button"
                                        >
                                            <CheckCircle size={16} /> Mark Complete
                                        </button>
                                    )}
                                    <button
                                        onClick={() => setSelectedDonation(null)}
                                        className="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm"
                                        type="button"
                                    >
                                        Close
                                    </button>
                                </>
                            )}
                        </div>
                    </div>
                </div>
            )}
        </div>
    )
}
