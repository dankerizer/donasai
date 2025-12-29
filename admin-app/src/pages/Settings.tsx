import { useState } from 'react'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'

export default function Settings() {
    const queryClient = useQueryClient()
    const [formData, setFormData] = useState({
        bank_name: '',
        account_number: '',
        account_name: '',
        midtrans_enabled: false,
        midtrans_production: false,
        midtrans_server_key: '',
        license_key: '',
        license_status: 'inactive'
    })
    const [success, setSuccess] = useState('')

    // Fetch Settings
    const { } = useQuery({
        queryKey: ['settings'],
        queryFn: async () => {
            // ... existing
        }
    })

    // Sync state
    useQuery({
        queryKey: ['settings-sync'],
        queryFn: async () => {
            const response = await fetch('/wp-json/wpd/v1/settings', {
                headers: { 'X-WP-Nonce': (window as any).wpdSettings?.nonce }
            });
            const data = await response.json();
            if (data.bank) {
                setFormData(prev => ({ ...prev, ...data.bank }));
            }
            if (data.midtrans) {
                setFormData(prev => ({
                    ...prev,
                    midtrans_enabled: data.midtrans.enabled === true || data.midtrans.enabled === '1',
                    midtrans_production: data.midtrans.is_production === true || data.midtrans.is_production === '1',
                    midtrans_server_key: data.midtrans.server_key
                }));
            }
            if (data.license) {
                setFormData(prev => ({
                    ...prev,
                    license_key: data.license.key,
                    license_status: data.license.status
                }));
            }
            return data;
        }
    });


    // Update Settings
    const mutation = useMutation({
        mutationFn: async (data: any) => {
            const payload = {
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
                license: {
                    key: data.license_key
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

    return (
        <div className="space-y-6">
            <h2 className="text-2xl font-bold text-gray-800">Settings</h2>

            <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-200 max-w-2xl">
                <h3 className="text-lg font-medium text-gray-900 mb-4">Bank Account Details (Web Free)</h3>

                {success && (
                    <div className="mb-4 p-3 bg-green-50 text-green-700 rounded-lg text-sm">
                        {success}
                    </div>
                )}

                <form onSubmit={handleSubmit} className="space-y-4">
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">
                            Bank Name
                        </label>
                        <input
                            type="text"
                            value={formData.bank_name}
                            onChange={(e) => setFormData(prev => ({ ...prev, bank_name: e.target.value }))}
                            className="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="e.g. BCA, Mandiri"
                        />
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">
                            Account Number
                        </label>
                        <input
                            type="text"
                            value={formData.account_number}
                            onChange={(e) => setFormData(prev => ({ ...prev, account_number: e.target.value }))}
                            className="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="e.g. 1234567890"
                        />
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">
                            Account Holder Name
                        </label>
                        <input
                            type="text"
                            value={formData.account_name}
                            onChange={(e) => setFormData(prev => ({ ...prev, account_name: e.target.value }))}
                            className="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="e.g. Yayasan Peduli"
                        />
                    </div>

                    <div className="pt-4">
                        <button
                            type="submit"
                            disabled={mutation.isPending}
                            className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium disabled:opacity-50"
                        >
                            {mutation.isPending ? 'Saving...' : 'Save Settings'}
                        </button>
                    </div>
                </form>

            </div>

            <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-200 max-w-2xl">
                <h3 className="text-lg font-medium text-gray-900 mb-4">Payment Gateway (Midtrans)</h3>

                <form onSubmit={(e) => { e.preventDefault(); mutation.mutate(formData); }} className="space-y-4">
                    <div className="flex items-center space-x-3">
                        <input
                            type="checkbox"
                            id="midtrans_enabled"
                            checked={formData.midtrans_enabled}
                            onChange={(e) => setFormData(prev => ({ ...prev, midtrans_enabled: e.target.checked }))}
                            className="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        />
                        <label htmlFor="midtrans_enabled" className="text-sm font-medium text-gray-700">
                            Enable Midtrans Gateway
                        </label>
                    </div>

                    <div className="flex items-center space-x-3">
                        <input
                            type="checkbox"
                            id="midtrans_production"
                            checked={formData.midtrans_production}
                            onChange={(e) => setFormData(prev => ({ ...prev, midtrans_production: e.target.checked }))}
                            className="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                        />
                        <label htmlFor="midtrans_production" className="text-sm font-medium text-gray-700">
                            Production Mode (Uncheck for Sandbox)
                        </label>
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">
                            Server Key
                        </label>
                        <input
                            type="password"
                            value={formData.midtrans_server_key}
                            onChange={(e) => setFormData(prev => ({ ...prev, midtrans_server_key: e.target.value }))}
                            className="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="SB-Mid-server-xxxx..."
                        />
                        <p className="text-xs text-gray-500 mt-1">Found in your Midtrans Dashboard &gt; Settings &gt; Access Keys.</p>
                    </div>

                    <div className="pt-4">
                        <button
                            type="submit"
                            disabled={mutation.isPending}
                            className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium disabled:opacity-50"
                        >
                            {mutation.isPending ? 'Saving...' : 'Save Settings'}
                        </button>
                    </div>
                </form>
            </div>



            <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-200 max-w-2xl">
                <h3 className="text-lg font-medium text-gray-900 mb-4">WP Donasi Pro License</h3>
                <div className="bg-gray-50 border border-gray-200 rounded p-4 mb-4">
                    <p className="text-sm text-gray-600 mb-2">Activate Pro to unlock features like Automatic Payments, WhatsApp Notif, and more.</p>
                    <div className="flex items-center gap-2">
                        <span className={`px-2 py-1 text-xs font-bold rounded ${formData.license_status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-600'}`}>
                            {formData.license_status === 'active' ? 'ACTIVE' : 'INACTIVE'}
                        </span>
                    </div>
                </div>

                <form onSubmit={(e) => { e.preventDefault(); mutation.mutate(formData); }} className="space-y-4">
                    <div>
                        <label className="block text-sm font-medium text-gray-700 mb-1">
                            License Key
                        </label>
                        <input
                            type="text"
                            value={formData.license_key}
                            onChange={(e) => setFormData(prev => ({ ...prev, license_key: e.target.value }))}
                            className="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter your license key..."
                        />
                    </div>
                    <div className="pt-2">
                        <button
                            type="submit"
                            disabled={mutation.isPending}
                            className="px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 font-medium disabled:opacity-50"
                        >
                            {mutation.isPending ? 'Validating...' : 'Activate License'}
                        </button>
                    </div>
                </form>
            </div>

            <div className="bg-white p-6 rounded-xl shadow-sm border border-gray-200 max-w-2xl">
                <h3 className="text-lg font-medium text-gray-900 mb-4">Shortcodes Cheatsheet</h3>
                <div className="space-y-3">
                    <div className="p-3 bg-gray-50 rounded border border-gray-200">
                        <code className="text-blue-600 font-bold block mb-1">[wpd_my_donations]</code>
                        <p className="text-sm text-gray-600">Displays the donation history for the logged-in user.</p>
                    </div>
                    <div className="p-3 bg-gray-50 rounded border border-gray-200">
                        <code className="text-blue-600 font-bold block mb-1">[wpd_fundraiser_stats]</code>
                        <p className="text-sm text-gray-600">Displays the fundraiser dashboard (stats & referral links) for the logged-in user.</p>
                    </div>
                    <div className="p-3 bg-gray-50 rounded border border-gray-200">
                        <code className="text-blue-600 font-bold block mb-1">[wpd_campaign id="123"]</code>
                        <p className="text-sm text-gray-600">Embeds a donation form for a specific campaign ID.</p>
                    </div>
                </div>
            </div>
        </div>
    )
}
