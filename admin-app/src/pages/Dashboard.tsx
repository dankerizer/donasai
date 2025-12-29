import { useQuery } from '@tanstack/react-query';
import { Heart, LayoutDashboard, Users, TrendingUp, Calendar, Lock } from 'lucide-react';
import { Link } from 'react-router-dom';

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
        <div className="space-y-8">
            <div className="flex justify-between items-center">
                <h2 className="text-2xl font-bold text-gray-800">Dashboard Overview</h2>
                <Link to="/donations" className="text-sm font-medium text-blue-600 hover:text-blue-800">
                    View All Donations &rarr;
                </Link>
            </div>

            {/* Standard Stats (Free) */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
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

                <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                    <div className="flex items-center gap-4">
                        <div className="p-3 bg-green-100 text-green-600 rounded-lg">
                            <Users size={24} />
                        </div>
                        <div>
                            <p className="text-sm font-medium text-gray-600">Total Donors</p>
                            <h3 className="text-2xl font-bold text-gray-900">{stats?.total_donors || 0}</h3>
                        </div>
                    </div>
                </div>

                <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
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

            {/* Pro Features (Locked) */}
            <div>
                <h3 className="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    Advanced Analytics <span className="text-xs bg-gray-200 text-gray-600 px-2 py-0.5 rounded uppercase">Pro</span>
                </h3>
                <div className="grid grid-cols-1 md:grid-cols-3 gap-6 opacity-75 grayscale hover:grayscale-0 transition-all group">

                    {/* Locked Card 1 */}
                    <div className="bg-gray-50 p-6 rounded-xl border border-gray-200 border-dashed relative overflow-hidden">
                        <div className="absolute inset-0 flex items-center justify-center bg-white/50 backdrop-blur-[1px] z-10 opacity-0 group-hover:opacity-100 transition-opacity">
                            <div className="flex items-center gap-2 text-sm font-bold text-gray-800 bg-white px-3 py-1 rounded-full shadow border border-gray-200">
                                <Lock size={14} /> Available in Pro
                            </div>
                        </div>
                        <div className="flex items-center gap-4 mb-2">
                            <div className="p-2 bg-pink-100 text-pink-600 rounded-lg">
                                <TrendingUp size={20} />
                            </div>
                            <span className="font-medium text-gray-600">Growth Rate</span>
                        </div>
                        <div className="h-16 w-full bg-gray-200 rounded animate-pulse"></div>
                    </div>

                    {/* Locked Card 2 */}
                    <div className="bg-gray-50 p-6 rounded-xl border border-gray-200 border-dashed relative overflow-hidden">
                        <div className="absolute inset-0 flex items-center justify-center bg-white/50 backdrop-blur-[1px] z-10 opacity-0 group-hover:opacity-100 transition-opacity">
                            <div className="flex items-center gap-2 text-sm font-bold text-gray-800 bg-white px-3 py-1 rounded-full shadow border border-gray-200">
                                <Lock size={14} /> Available in Pro
                            </div>
                        </div>
                        <div className="flex items-center gap-4 mb-2">
                            <div className="p-2 bg-indigo-100 text-indigo-600 rounded-lg">
                                <Calendar size={20} />
                            </div>
                            <span className="font-medium text-gray-600">Recurring Revenue</span>
                        </div>
                        <div className="h-16 w-full bg-gray-200 rounded animate-pulse"></div>
                    </div>

                    {/* Locked Card 3 */}
                    <div className="bg-gray-50 p-6 rounded-xl border border-gray-200 border-dashed relative overflow-hidden">
                        <div className="absolute inset-0 flex items-center justify-center bg-white/50 backdrop-blur-[1px] z-10 opacity-0 group-hover:opacity-100 transition-opacity">
                            <div className="flex items-center gap-2 text-sm font-bold text-gray-800 bg-white px-3 py-1 rounded-full shadow border border-gray-200">
                                <Lock size={14} /> Available in Pro
                            </div>
                        </div>
                        <div className="flex items-center gap-4 mb-2">
                            <div className="p-2 bg-orange-100 text-orange-600 rounded-lg">
                                <Users size={20} />
                            </div>
                            <span className="font-medium text-gray-600">Donor Retention</span>
                        </div>
                        <div className="h-16 w-full bg-gray-200 rounded animate-pulse"></div>
                    </div>
                </div>
            </div>
        </div>
    )
}
