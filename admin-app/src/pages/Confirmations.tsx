import { useState } from 'react'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { Search, ExternalLink, Bot, Lock } from 'lucide-react'

export default function Confirmations() {
    const queryClient = useQueryClient()
    const [searchTerm, setSearchTerm] = useState('')
    const [selectedProof, setSelectedProof] = useState<any>(null)
    const [filterStatus, setFilterStatus] = useState<'unconfirmed' | 'approved' | 'rejected'>('unconfirmed')

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

    const filteredDonations = (donations || [])
        .filter((d: any) => {
            // Check for manual proof
            const hasProof = d.metadata?.proof_url;
            if (!hasProof && filterStatus === 'unconfirmed') return false;

            if (filterStatus === 'unconfirmed') {
                return d.status !== 'complete' && d.status !== 'failed' && d.status !== 'refunded';
            }
            if (filterStatus === 'approved') {
                return d.status === 'complete';
            }
            if (filterStatus === 'rejected') {
                return d.status === 'failed' || d.status === 'refunded';
            }
            return false;
        })
        .filter((d: any) =>
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
            setSelectedProof(null) // Close modal on success
        }
    })

    return (
        <div className="space-y-6">
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
                <div>
                    <h2 className="text-2xl font-bold text-gray-800">Konfirmasi Donasi</h2>
                    <p className="text-gray-500">Tinjau bukti transfer manual.</p>
                </div>

                <div className="flex items-center gap-3 w-full sm:w-auto">
                    {/* Status Filter */}
                    <div className="bg-white border border-gray-300 rounded-lg p-1 flex">
                        <button
                            onClick={() => setFilterStatus('unconfirmed')}
                            className={`px-3 py-1.5 text-sm font-medium rounded-md transition-all ${filterStatus === 'unconfirmed' ? 'bg-gray-100 text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900'}`}
                        >
                            Belum Dikonfirmasi
                        </button>
                        <button
                            onClick={() => setFilterStatus('approved')}
                            className={`px-3 py-1.5 text-sm font-medium rounded-md transition-all ${filterStatus === 'approved' ? 'bg-green-50 text-green-700 shadow-sm' : 'text-gray-500 hover:text-gray-900'}`}
                        >
                            Diterima
                        </button>
                        <button
                            onClick={() => setFilterStatus('rejected')}
                            className={`px-3 py-1.5 text-sm font-medium rounded-md transition-all ${filterStatus === 'rejected' ? 'bg-red-50 text-red-700 shadow-sm' : 'text-gray-500 hover:text-gray-900'}`}
                        >
                            Ditolak
                        </button>
                    </div>

                    <div className="relative flex-1 sm:flex-none">
                        <Search className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" size={18} />
                        <input
                            type="text"
                            placeholder="Cari donatur..."
                            className="pl-12 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 w-full"
                            value={searchTerm}
                            onChange={e => setSearchTerm(e.target.value)}
                        />
                    </div>
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
                            Verifikasi Pembayaran AI
                            <span className="bg-gray-800 mt-2 text-white text-[10px] px-1.5 py-0.5 rounded uppercase flex items-center gap-1 w-[50px]">
                                <Lock size={8} /> Pro
                            </span>
                        </h3>
                        <p className="text-sm text-gray-600">Verifikasi otomatis jumlah dan nama bukti transfer menggunakan teknologi AI OCR.</p>
                    </div>
                </div>
                <button disabled className="px-4 py-2 bg-white text-gray-400 font-medium rounded-lg border border-gray-200 cursor-not-allowed text-sm">
                    Upgrade untuk Mengaktifkan
                </button>
            </div>

            <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <table className="w-full text-left">
                    <thead className="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th className="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Donasi #</th>
                            <th className="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Donatur</th>
                            <th className="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Jumlah</th>
                            <th className="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Bukti</th>
                            <th className="px-6 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-200">
                        {isLoading ? (
                            <tr><td colSpan={5} className="px-6 py-10 text-center text-gray-500">Memuat...</td></tr>
                        ) : filteredDonations.length === 0 ? (
                            <tr><td colSpan={5} className="px-6 py-10 text-center text-gray-500">Tidak ada konfirmasi {filterStatus === 'unconfirmed' ? 'belum dikonfirmasi' : filterStatus === 'approved' ? 'diterima' : 'ditolak'} ditemukan.</td></tr>
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
                                            <span className={`inline-flex items-center text-xs font-medium px-2 py-0.5 rounded ${filterStatus === 'approved' ? 'text-green-700 bg-green-100' :
                                                filterStatus === 'rejected' ? 'text-red-700 bg-red-100' :
                                                    'text-yellow-700 bg-yellow-100'
                                                }`}>
                                                {filterStatus === 'approved' ? 'Terverifikasi' : filterStatus === 'rejected' ? 'Ditolak' : 'Menunggu'}
                                            </span>
                                        ) : (
                                            <span className="text-gray-400 text-sm">Tidak ada fail</span>
                                        )}
                                    </td>
                                    <td className="px-6 py-4 text-right">
                                        <button
                                            onClick={() => setSelectedProof(donation)}
                                            className="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-100 transition-colors"
                                        >
                                            Tinjau Bukti <ExternalLink size={14} />
                                        </button>
                                    </td>
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>

            {/* Proof Modal */}
            {selectedProof && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" onClick={() => !updateStatus.isPending && setSelectedProof(null)}>
                    <div className="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col" onClick={e => e.stopPropagation()}>

                        {/* Header */}
                        <div className="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                            <div>
                                <h3 className="font-bold text-gray-900">Tinjau Bukti Pembayaran</h3>
                                <p className="text-xs text-gray-500">Donasi #{selectedProof.id} &bull; Rp {selectedProof.amount.toLocaleString('id-ID')}</p>
                            </div>
                            <button
                                onClick={() => setSelectedProof(null)}
                                disabled={updateStatus.isPending}
                                className="p-2 hover:bg-gray-200 rounded-full text-gray-500 disabled:opacity-50"
                            >
                                ✕
                            </button>
                        </div>

                        {/* Image Body */}
                        <div className="flex-1 overflow-y-auto p-6 bg-gray-100 flex items-center justify-center min-h-[300px]">
                            {selectedProof.metadata?.proof_url ? (
                                <img
                                    src={selectedProof.metadata.proof_url}
                                    alt="Payment Proof"
                                    className="max-w-full max-h-full rounded shadow-lg border border-gray-200"
                                />
                            ) : (
                                <div className="text-gray-400 flex flex-col items-center">
                                    <ExternalLink size={48} className="mb-2 opacity-50" />
                                    <span>Tidak ada gambar bukti diunggah</span>
                                </div>
                            )}
                        </div>

                        {/* Metadata & Actions */}
                        <div className="p-6 border-t border-gray-100 bg-white">
                            <div className="grid grid-cols-2 gap-4 mb-6 text-sm">
                                <div>
                                    <span className="block text-gray-500 text-xs uppercase font-semibold mb-1">Nama Pengirim</span>
                                    <div className="font-medium text-gray-900">{selectedProof.metadata?.sender_name || '-'}</div>
                                </div>
                                <div>
                                    <span className="block text-gray-500 text-xs uppercase font-semibold mb-1">Bank Pengirim</span>
                                    <div className="font-medium text-gray-900">{selectedProof.metadata?.sender_bank || '-'}</div>
                                </div>
                            </div>

                            <div className="grid grid-cols-2 gap-3">
                                <button
                                    disabled={updateStatus.isPending}
                                    onClick={() => {
                                        if (confirm('Apakah Anda yakin ingin menolak bukti ini?')) {
                                            updateStatus.mutate({ id: selectedProof.id, status: 'failed' }); // Reject -> Failed
                                        }
                                    }}
                                    className="px-4 py-3 bg-red-50 text-red-700 font-bold rounded-xl hover:bg-red-100 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                                >
                                    {updateStatus.isPending ? 'Memproses...' : 'Tolak'}
                                </button>
                                <button
                                    disabled={updateStatus.isPending}
                                    onClick={() => {
                                        updateStatus.mutate({ id: selectedProof.id, status: 'complete' });
                                    }}
                                    className="px-4 py-3 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 shadow-lg shadow-green-200 transition-all hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none disabled:translate-y-0 flex items-center justify-center gap-2"
                                >
                                    {updateStatus.isPending ? 'Memproses...' : 'Terima Pembayaran'}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </div>
    )
}
