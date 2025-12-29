import { useState } from 'react'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'

export default function Settings() {
    const queryClient = useQueryClient()
    const [formData, setFormData] = useState({
        bank_name: '',
        account_number: '',
        account_name: ''
    })
    const [success, setSuccess] = useState('')

    // Fetch Settings
    const { } = useQuery({
        queryKey: ['settings'],
        queryFn: async () => {
            const response = await fetch('/wp-json/wpd/v1/settings', {
                headers: { 'X-WP-Nonce': (window as any).wpdSettings?.nonce }
            })
            if (!response.ok) throw new Error('Failed to fetch settings')
            return response.json()
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
                setFormData(data.bank);
            }
            return data;
        }
    });


    // Update Settings
    const mutation = useMutation({
        mutationFn: async (data: any) => {
            const response = await fetch('/wp-json/wpd/v1/settings', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': (window as any).wpdSettings?.nonce
                },
                body: JSON.stringify({ bank: data })
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
