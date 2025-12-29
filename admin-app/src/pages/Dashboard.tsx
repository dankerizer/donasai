import { useQuery } from '@tanstack/react-query';
import { Heart, LayoutDashboard } from 'lucide-react';


export default function Dashboard() {
    const { data: stats } = useQuery({
        queryKey: ['stats'],
        queryFn: async () => {
            const response = await fetch('/wp-json/wpd/v1/stats', {
                headers: { 'X-WP-Nonce': (window as any).wpdSettings?.nonce }
            });
            if (!response.ok) return { total_donations: 0, total_donors: 0, active_campaigns: 0 };
            return response.json();
        }
    })

    return (
        <div className="space-y-6">
            <h2 className="text-2xl font-bold text-gray-800">Dashboard Overview</h2>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div className="flex items-center gap-4">
                        <div className="p-3 bg-blue-100 text-blue-600 rounded-lg">
                            <Heart size={24} />
                        </div>
                        <div>
                            <p className="text-sm font-medium text-gray-600">Total Donations</p>
                            <h3 className="text-2xl font-bold text-gray-900">Rp {stats?.total_donations?.toLocaleString('id-ID') || 0}</h3>
                        </div>
                    </div>
                </div>

                <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div className="flex items-center gap-4">
                        <div className="p-3 bg-green-100 text-green-600 rounded-lg">
                            <span className="text-xl font-bold">👥</span>
                        </div>
                        <div>
                            <p className="text-sm font-medium text-gray-600">Total Donors</p>
                            <h3 className="text-2xl font-bold text-gray-900">{stats?.total_donors || 0}</h3>
                        </div>
                    </div>
                </div>

                <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div className="flex items-center gap-4">
                        <div className="p-3 bg-purple-100 text-purple-600 rounded-lg">
                            <LayoutDashboard size={24} />
                        </div>
                        <div>
                            <p className="text-sm font-medium text-gray-600">Active Campaigns</p>
                            <h3 className="text-2xl font-bold text-gray-900">{stats?.active_campaigns || 0}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    )
}
