
import { useQuery } from '@tanstack/react-query'
import { Users } from 'lucide-react'

interface Fundraiser {
    id: number
    user_id: number
    campaign_id: number
    referral_code: string
    total_donations: number
    donation_count: number
    created_at: string
}

export default function FundraisersPage() {
    const { data: fundraisers, isLoading } = useQuery({
        queryKey: ['fundraisers'],
        queryFn: async () => {
            const response = await fetch('/wp-json/wpd/v1/fundraisers', {
                headers: { 'X-WP-Nonce': (window as any).wpdSettings?.nonce }
            })
            if (!response.ok) return []
            return response.json()
        }
    })

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <h2 className="text-2xl font-bold text-gray-800">Penggalang Dana</h2>
                {/* Future: Add Invite/Create Button */}
            </div>

            <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <table className="w-full text-left text-sm text-gray-600">
                    <thead className="bg-gray-50 border-b border-gray-200 font-medium text-gray-900">
                        <tr>
                            <th className="px-6 py-4">ID</th>
                            <th className="px-6 py-4">Kode (Pengguna)</th>
                            <th className="px-6 py-4">ID Kampanye</th>
                            <th className="px-6 py-4 text-right">Donasi Terkumpul</th>
                            <th className="px-6 py-4 text-right">Jumlah</th>
                            <th className="px-6 py-4">Bergabung Pada</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-gray-200">
                        {isLoading ? (
                            <tr><td colSpan={6} className="px-6 py-4 text-center">Memuat...</td></tr>
                        ) : fundraisers && fundraisers.length > 0 ? (
                            fundraisers.map((f: Fundraiser) => (
                                <tr key={f.id} className="hover:bg-gray-50">
                                    <td className="px-6 py-4">#{f.id}</td>
                                    <td className="px-6 py-4 font-medium text-blue-600 flex items-center gap-2">
                                        <Users size={16} />
                                        {f.referral_code}
                                    </td>
                                    <td className="px-6 py-4">{f.campaign_id}</td>
                                    <td className="px-6 py-4 text-right font-medium text-green-600">
                                        Rp {f.total_donations.toLocaleString('id-ID')}
                                    </td>
                                    <td className="px-6 py-4 text-right">{f.donation_count}</td>
                                    <td className="px-6 py-4">{new Date(f.created_at).toLocaleDateString()}</td>
                                </tr>
                            ))
                        ) : (
                            <tr><td colSpan={6} className="px-6 py-4 text-center">Tidak ada penggalang dana ditemukan.</td></tr>
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    )
}
