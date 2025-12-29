import { useState } from 'react'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { Check, X, Building, CreditCard, Bell, Star, Crown, Heart } from 'lucide-react'
import clsx from 'clsx'

export default function Settings() {
    const queryClient = useQueryClient()
    const [activeTab, setActiveTab] = useState('general')
    const [showProModal, setShowProModal] = useState(false)

    // Form States
    const [formData, setFormData] = useState({
        // General
        campaign_slug: 'campaign',
        payment_slug: 'pay',
        remove_branding: false,
        // Donation
        min_amount: 10000,
        presets: '50000,100000,200000,500000',
        anonymous_label: 'Hamba Allah',
        create_user: false,
        // Bank
        bank_name: '',
        account_number: '',
        account_name: '',
        // Midtrans
        midtrans_enabled: false,
        midtrans_production: false,
        midtrans_server_key: '',
        // Organization
        org_name: '',
        org_address: '',
        org_phone: '',
        org_email: '',
        org_logo: '',
        // Notifications
        opt_in_email: '',
        opt_in_whatsapp: ''
    })

    const [success, setSuccess] = useState('')

    // Fetch Settings
    useQuery({
        queryKey: ['settings-sync'],
        queryFn: async () => {
            const response = await fetch('/wp-json/wpd/v1/settings', {
                headers: { 'X-WP-Nonce': (window as any).wpdSettings?.nonce }
            });
            const data = await response.json();

            setFormData(prev => ({
                ...prev,
                // General
                campaign_slug: data.general?.campaign_slug || 'campaign',
                payment_slug: data.general?.payment_slug || 'pay',
                remove_branding: data.general?.remove_branding === true || data.general?.remove_branding === '1',
                // Donation
                min_amount: data.donation?.min_amount || 10000,
                presets: data.donation?.presets || '50000,100000,200000,500000',
                anonymous_label: data.donation?.anonymous_label || 'Hamba Allah',
                create_user: data.donation?.create_user === true || data.donation?.create_user === '1',
                // Bank
                bank_name: data.bank?.bank_name || '',
                account_number: data.bank?.account_number || '',
                account_name: data.bank?.account_name || '',
                // Midtrans
                midtrans_enabled: data.midtrans?.enabled === true || data.midtrans?.enabled === '1',
                midtrans_production: data.midtrans?.is_production === true || data.midtrans?.is_production === '1',
                midtrans_server_key: data.midtrans?.server_key || '',
                // Organization
                org_name: data.organization?.org_name || '',
                org_address: data.organization?.org_address || '',
                org_phone: data.organization?.org_phone || '',
                org_email: data.organization?.org_email || '',
                org_logo: data.organization?.org_logo || '',
                // Notifications
                opt_in_email: data.notifications?.opt_in_email || '',
                opt_in_whatsapp: data.notifications?.opt_in_whatsapp || ''
            }));

            return data;
        },
        staleTime: 0
    });

    // Update Settings
    const mutation = useMutation({
        mutationFn: async (data: any) => {
            const payload = {
                general: {
                    campaign_slug: data.campaign_slug,
                    payment_slug: data.payment_slug,
                    remove_branding: data.remove_branding
                },
                donation: {
                    min_amount: data.min_amount,
                    presets: data.presets,
                    anonymous_label: data.anonymous_label,
                    create_user: data.create_user
                },
                bank: {
                    bank_name: data.bank_name,
                    account_number: data.account_number,
                    account_name: data.account_name
                },
                midtrans: {
                    enabled: data.midtrans_enabled,
                    is_production: data.midtrans_production,
                    server_key: data.midtrans_server_key
                },
                organization: {
                    org_name: data.org_name,
                    org_address: data.org_address,
                    org_phone: data.org_phone,
                    org_email: data.org_email,
                    org_logo: data.org_logo
                },
                notifications: {
                    opt_in_email: data.opt_in_email,
                    opt_in_whatsapp: data.opt_in_whatsapp
                }
            };

            const response = await fetch('/wp-json/wpd/v1/settings', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': (window as any).wpdSettings?.nonce
                },
                body: JSON.stringify(payload)
            })
            if (!response.ok) throw new Error('Failed to save settings')
            return response.json()
        },
        onSuccess: () => {
            queryClient.invalidateQueries({ queryKey: ['settings'] })
            setSuccess('Settings saved successfully!')
            setTimeout(() => setSuccess(''), 3000)
        }
    })

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault()
        mutation.mutate(formData)
    }

    const tabs = [
        { id: 'general', label: 'General & Org', icon: Building },
        { id: 'donation', label: 'Donation Settings', icon: Heart },
        { id: 'payment', label: 'Payment', icon: CreditCard },
        { id: 'notifications', label: 'Notifications', icon: Bell },
    ]

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <h2 className="text-2xl font-bold text-gray-800">Settings</h2>
                {success && (
                    <div className="px-4 py-2 bg-green-100 text-green-700 rounded-lg flex items-center gap-2">
                        <Check size={16} /> {success}
                    </div>
                )}
            </div>

            {/* Pro Banner */}
            <div className="bg-linear-to-r from-purple-600 to-indigo-600 rounded-xl p-6 text-white flex justify-between items-center shadow-lg">
                <div>
                    <h3 className="text-xl font-bold flex items-center gap-2">
                        <Crown className="text-yellow-300" /> Upgrade to WP Donasi Pro
                    </h3>
                    <p className="opacity-90 mt-1">Unlock Recurring Donations, WhatsApp Notifications, and AI Confirmation.</p>
                </div>
                <button
                    onClick={() => setShowProModal(true)}
                    className="bg-white text-purple-700 px-5 py-2.5 rounded-lg font-bold hover:bg-gray-50 transition-colors shadow-sm"
                >
                    Compare Features
                </button>
            </div>

            <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden min-h-[500px] flex">

                {/* Sidebar Tabs */}
                <div className="w-64 bg-gray-50 border-r border-gray-200 p-4 space-y-2">
                    {tabs.map(tab => {
                        const Icon = tab.icon
                        return (
                            <button
                                key={tab.id}
                                onClick={() => setActiveTab(tab.id)}
                                className={clsx(
                                    "w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors text-left",
                                    activeTab === tab.id
                                        ? "bg-white text-blue-600 shadow-sm border border-gray-200"
                                        : "text-gray-600 hover:bg-gray-100 hover:text-gray-900"
                                )}
                            >
                                <Icon size={18} />
                                {tab.label}
                            </button>
                        )
                    })}
                </div>

                {/* Content Area */}
                <div className="flex-1 p-8">
                    <form onSubmit={handleSubmit} className="max-w-2xl space-y-6">

                        {/* GENERAL TAB */}
                        {activeTab === 'general' && (
                            <div className="space-y-8">
                                <div>
                                    <h3 className="text-lg font-medium text-gray-900 mb-1">Organization Details</h3>
                                    <p className="text-sm text-gray-500 mb-4">This information will appear on donation receipts.</p>

                                    <div className="grid gap-4">
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 mb-1">Organization Name</label>
                                            <input
                                                type="text"
                                                className="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 hover:border-blue-400 transition-colors"
                                                value={formData.org_name}
                                                onChange={e => setFormData({ ...formData, org_name: e.target.value })}
                                                placeholder="e.g. Yayasan Amal Bhakti"
                                            />
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                            <textarea
                                                className="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                                rows={3}
                                                value={formData.org_address}
                                                onChange={e => setFormData({ ...formData, org_address: e.target.value })}
                                                placeholder="Full address..."
                                            />
                                        </div>
                                        <div className="grid grid-cols-2 gap-4">
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                                <input
                                                    type="email"
                                                    className="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                                    value={formData.org_email}
                                                    onChange={e => setFormData({ ...formData, org_email: e.target.value })}
                                                />
                                            </div>
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700 mb-1">Phone / WhatsApp</label>
                                                <input
                                                    type="text"
                                                    className="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                                    value={formData.org_phone}
                                                    onChange={e => setFormData({ ...formData, org_phone: e.target.value })}
                                                />
                                            </div>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 mb-1">Logo URL</label>
                                            <input
                                                type="text"
                                                className="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                                value={formData.org_logo}
                                                onChange={e => setFormData({ ...formData, org_logo: e.target.value })}
                                                placeholder="https://..."
                                            />
                                            <p className="text-xs text-gray-500 mt-1">Upload to Media Library and paste URL here.</p>
                                        </div>
                                    </div>
                                </div>
                                <div className="border-t border-gray-200 pt-6">
                                    <h3 className="text-lg font-medium text-gray-900 mb-4">Permalink Settings</h3>
                                    <div className="grid grid-cols-2 gap-6">
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 mb-1">Campaign Slug</label>
                                            <div className="flex bg-gray-50 border border-gray-300 rounded-lg items-center text-gray-500 text-sm overflow-hidden">
                                                <span className="px-3 bg-gray-100 border-r border-gray-300 h-full flex items-center">/</span>
                                                <input
                                                    type="text"
                                                    className="flex-1 p-2 bg-transparent focus:outline-none"
                                                    value={formData.campaign_slug}
                                                    onChange={e => setFormData({ ...formData, campaign_slug: e.target.value })}
                                                />
                                                <span className="px-3">/judul-campaign</span>
                                            </div>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 mb-1">Payment Slug</label>
                                            <div className="flex bg-gray-50 border border-gray-300 rounded-lg items-center text-gray-500 text-sm overflow-hidden">
                                                <span className="px-3 bg-gray-100 border-r border-gray-300 h-full flex items-center">/campaign/</span>
                                                <input
                                                    type="text"
                                                    className="flex-1 p-2 bg-transparent focus:outline-none"
                                                    value={formData.payment_slug}
                                                    onChange={e => setFormData({ ...formData, payment_slug: e.target.value })}
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    <p className="text-xs text-amber-600 mt-2">Make sure to resave "Settings &gt; Permalinks" in WordPress if you change these.</p>
                                </div>
                                <div className="border-t border-gray-200 pt-6">
                                    <h3 className="text-lg font-medium text-gray-900 mb-2 flex items-center gap-2">
                                        Branding <span className="bg-purple-100 text-purple-700 text-xs px-2 py-0.5 rounded font-bold">PRO</span>
                                    </h3>
                                    <div className="flex items-center space-x-3 opacity-60">
                                        <input
                                            type="checkbox"
                                            checked={formData.remove_branding} // Visual only for now if not implemented logic in UI to block
                                            onChange={() => setShowProModal(true)}
                                            className="h-4 w-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                                        />
                                        <label className="text-sm font-medium text-gray-700">Remove "Powered by WP Donasi"</label>
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* DONATION TAB */}
                        {activeTab === 'donation' && (
                            <div className="space-y-6">
                                <div>
                                    <h3 className="text-lg font-medium text-gray-900 mb-4">Donation Options</h3>
                                    <div className="grid gap-4">
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 mb-1">Minimum Donation Amount (Rp)</label>
                                            <input
                                                type="number"
                                                className="w-full p-2 border border-gray-300 rounded-lg"
                                                value={formData.min_amount}
                                                onChange={e => setFormData({ ...formData, min_amount: parseInt(e.target.value) || 0 })}
                                            />
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 mb-1">Default Presets (Rp)</label>
                                            <input
                                                type="text"
                                                className="w-full p-2 border border-gray-300 rounded-lg"
                                                value={formData.presets}
                                                onChange={e => setFormData({ ...formData, presets: e.target.value })}
                                                placeholder="50000,100000,200000"
                                            />
                                            <p className="text-xs text-gray-500 mt-1">Separate with commas.</p>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 mb-1">Anonymous Label</label>
                                            <input
                                                type="text"
                                                className="w-full p-2 border border-gray-300 rounded-lg"
                                                value={formData.anonymous_label}
                                                onChange={e => setFormData({ ...formData, anonymous_label: e.target.value })}
                                                placeholder="Hamba Allah"
                                            />
                                            <p className="text-xs text-gray-500 mt-1">Displayed usage when user hides their name.</p>
                                        </div>
                                    </div>
                                </div>
                                <div className="border-t border-gray-200 pt-6">
                                    <h3 className="text-lg font-medium text-gray-900 mb-2 flex items-center gap-2">
                                        User Registration <span className="bg-purple-100 text-purple-700 text-xs px-2 py-0.5 rounded font-bold">PRO</span>
                                    </h3>
                                    <div className="flex items-center space-x-3 opacity-60">
                                        <input
                                            type="checkbox"
                                            checked={formData.create_user}
                                            onChange={() => setShowProModal(true)}
                                            className="h-4 w-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                                        />
                                        <label className="text-sm font-medium text-gray-700">Auto-create WordPress User from Donor Email</label>
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* PAYMENT TAB */}
                        {activeTab === 'payment' && (
                            <div className="space-y-8">
                                <div>
                                    <h3 className="text-lg font-medium text-gray-900 mb-4">Bank Transfer (Manual)</h3>
                                    <div className="grid gap-4">
                                        <div className="grid grid-cols-2 gap-4">
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
                                                <input
                                                    type="text"
                                                    className="w-full p-2 border border-gray-300 rounded-lg"
                                                    value={formData.bank_name}
                                                    onChange={e => setFormData({ ...formData, bank_name: e.target.value })}
                                                    placeholder="e.g. BCA"
                                                />
                                            </div>
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700 mb-1">Account Number</label>
                                                <input
                                                    type="text"
                                                    className="w-full p-2 border border-gray-300 rounded-lg"
                                                    value={formData.account_number}
                                                    onChange={e => setFormData({ ...formData, account_number: e.target.value })}
                                                />
                                            </div>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 mb-1">Account Holder Name</label>
                                            <input
                                                type="text"
                                                className="w-full p-2 border border-gray-300 rounded-lg"
                                                value={formData.account_name}
                                                onChange={e => setFormData({ ...formData, account_name: e.target.value })}
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div className="border-t border-gray-200 pt-6">
                                    <h3 className="text-lg font-medium text-gray-900 mb-4">Midtrans Gateway</h3>
                                    <div className="space-y-4">
                                        <div className="flex items-center space-x-3">
                                            <input
                                                type="checkbox"
                                                id="midtrans_enabled"
                                                checked={formData.midtrans_enabled}
                                                onChange={(e) => setFormData(prev => ({ ...prev, midtrans_enabled: e.target.checked }))}
                                                className="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                            />
                                            <label htmlFor="midtrans_enabled" className="text-sm font-medium text-gray-700">Enable Midtrans</label>
                                        </div>

                                        <div className="flex items-center space-x-3">
                                            <input
                                                type="checkbox"
                                                id="midtrans_production"
                                                checked={formData.midtrans_production}
                                                onChange={(e) => setFormData(prev => ({ ...prev, midtrans_production: e.target.checked }))}
                                                className="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                            />
                                            <label htmlFor="midtrans_production" className="text-sm font-medium text-gray-700">Production Mode</label>
                                        </div>

                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 mb-1">Server Key</label>
                                            <input
                                                type="password"
                                                className="w-full p-2 border border-gray-300 rounded-lg font-mono text-sm"
                                                value={formData.midtrans_server_key}
                                                onChange={e => setFormData({ ...formData, midtrans_server_key: e.target.value })}
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* NOTIFICATIONS TAB */}
                        {activeTab === 'notifications' && (
                            <div className="space-y-6">
                                <div>
                                    <h3 className="text-lg font-medium text-gray-900 mb-1">Update Subscriptions</h3>
                                    <p className="text-sm text-gray-500 mb-4">Receive donor updates and plugin announcements.</p>

                                    <div className="grid gap-4">
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 mb-1">Email for Updates</label>
                                            <input
                                                type="email"
                                                className="w-full p-2 border border-gray-300 rounded-lg"
                                                value={formData.opt_in_email}
                                                onChange={e => setFormData({ ...formData, opt_in_email: e.target.value })}
                                            />
                                            <p className="text-xs text-gray-500 mt-1">We'll verify this email before sending sensitive reports.</p>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 mb-1">WhatsApp Number (Optional)</label>
                                            <input
                                                type="text"
                                                className="w-full p-2 border border-gray-300 rounded-lg"
                                                value={formData.opt_in_whatsapp}
                                                onChange={e => setFormData({ ...formData, opt_in_whatsapp: e.target.value })}
                                                placeholder="e.g. 62812..."
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div className="border-t border-gray-200 pt-6">
                                    <h3 className="text-lg font-medium text-gray-900 mb-2 flex items-center gap-2">
                                        <Bell size={18} className="text-gray-400" />
                                        Advanced Notifications <span className="bg-purple-100 text-purple-700 text-xs px-2 py-0.5 rounded font-bold">PRO</span>
                                    </h3>
                                    <p className="text-sm text-gray-500 mb-4">Available in Pro version:</p>
                                    <ul className="list-disc pl-5 text-sm text-gray-600 space-y-1">
                                        <li>Real-time WhatsApp notifications for every donation.</li>
                                        <li>Daily summary via Email.</li>
                                        <li>Failed payment alerts.</li>
                                    </ul>
                                </div>
                            </div>
                        )}

                        {/* Save Button (Always Visible) */}
                        <div className="pt-6 border-t border-gray-200">
                            <button
                                type="submit"
                                disabled={mutation.isPending}
                                className="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium disabled:opacity-50 shadow-sm transition-all"
                            >
                                {mutation.isPending ? 'Saving...' : 'Save Changes'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {/* Pro Comparison Modal */}
            {showProModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
                    <div className="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                        <div className="p-6 border-b border-gray-100 flex justify-between items-center sticky top-0 bg-white z-10">
                            <h2 className="text-2xl font-bold text-gray-900">Choose your plan</h2>
                            <button onClick={() => setShowProModal(false)} className="p-2 hover:bg-gray-100 rounded-full">
                                <X size={24} className="text-gray-500" />
                            </button>
                        </div>
                        <div className="p-8">
                            <div className="grid md:grid-cols-2 gap-8">
                                {/* FREE */}
                                <div className="border border-gray-200 rounded-xl p-6">
                                    <h3 className="text-xl font-bold text-gray-900 mb-2">Free</h3>
                                    <p className="text-gray-500 mb-6">For small communities starting out.</p>
                                    <ul className="space-y-3 mb-8">
                                        {['Unlimited Campaigns', 'Basic Donation Form', 'Manual Bank Transfer', 'Basic Reporting', '1 Global Gateway'].map(f => (
                                            <li key={f} className="flex items-center gap-2 text-sm text-gray-700">
                                                <Check size={16} className="text-green-500" /> {f}
                                            </li>
                                        ))}
                                    </ul>
                                    <button className="w-full py-2 bg-gray-100 text-gray-700 font-semibold rounded-lg cursor-default">Current Plan</button>
                                </div>

                                {/* PRO */}
                                <div className="border-2 border-purple-600 rounded-xl p-6 relative bg-purple-50">
                                    <div className="absolute top-0 right-0 bg-purple-600 text-white text-xs font-bold px-3 py-1 rounded-bl-lg rounded-tr-lg">RECOMMENDED</div>
                                    <h3 className="text-xl font-bold text-gray-900 mb-2">Pro</h3>
                                    <p className="text-gray-500 mb-6">For growing organizations & NGOs.</p>
                                    <ul className="space-y-3 mb-8">
                                        <li className="flex items-center gap-2 text-sm text-gray-900 font-medium">
                                            <Check size={16} className="text-purple-600" /> All Free Features
                                        </li>
                                        {[
                                            'Recurring Donations (Subscriptions)',
                                            'WhatsApp Notifications',
                                            'Local Gateways (Midtrans, Xendit, QRIS)',
                                            'PDF Receipts',
                                            'User Registration',
                                            'Remove Branding',
                                            'AI Payment Confirmation',
                                            'Advanced Analytics'
                                        ].map(f => (
                                            <li key={f} className="flex items-center gap-2 text-sm text-gray-700">
                                                <Star size={16} className="text-purple-600" fill="currentColor" /> {f}
                                            </li>
                                        ))}
                                    </ul>
                                    <button className="w-full py-2 bg-purple-600 text-white font-bold rounded-lg hover:bg-purple-700 shadow-md">
                                        Upgrade Now (Coming Soon)
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </div>
    )
}
