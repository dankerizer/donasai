/** biome-ignore-all lint/a11y/noStaticElementInteractions: <explanation> */
/** biome-ignore-all lint/a11y/useKeyWithClickEvents: <explanation> */
import { useState } from 'react'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { Check, X, Building, CreditCard, Bell, Star, Crown, Heart, Palette, Lock, Trash, Plus, Link as LinkIcon } from 'lucide-react'
import clsx from 'clsx'
import { toast } from 'sonner'

export default function Settings() {
    const queryClient = useQueryClient()
    const [activeTab, setActiveTab] = useState('general')
    const [showProModal, setShowProModal] = useState(false)
    const [isProInstalled, setIsProInstalled] = useState(false)

    // License State
    const [licenseStatus, setLicenseStatus] = useState('inactive')
    const [licenseKey, setLicenseKey] = useState('')

    // Form States
    const [formData, setFormData] = useState({
        // General
        campaign_slug: 'campaign',
        payment_slug: 'pay',
        remove_branding: false,
        confirmation_page: '',
        delete_on_uninstall_settings: false,
        delete_on_uninstall_tables: false,
        // Donation
        min_amount: 10000,
        presets: '50000,100000,200000,500000',
        anonymous_label: 'Hamba Allah',
        create_user: false,
        recurring_intervals: ['month', 'year'],
        // Appearance
        brand_color: '#059669',
        button_color: '#ec4899',
        container_width: '1100px',
        border_radius: '12px',
        campaign_layout: 'sidebar-right',
        font_family: 'Inter',
        font_size: '16px',
        dark_mode: false,
        donation_layout: 'default',
        // Bank
        bank_name: '',
        account_number: '',

        account_name: '',
        pro_accounts: [] as { id: string; bank_name: string; account_number: string; account_name: string; is_default: boolean }[],
        // Midtrans
        midtrans_enabled: false,
        midtrans_production: false,
        midtrans_server_key: '',
        // Pro Midtrans
        pro_midtrans_server_key: '',
        pro_midtrans_client_key: '',
        pro_midtrans_production: false,
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

    const [pages, setPages] = useState<Array<{ id: number, title: string }>>([])

    // Fetch Settings
    useQuery({
        queryKey: ['settings-sync'],
        queryFn: async () => {
            const response = await fetch('/wp-json/wpd/v1/settings', {
                headers: { 'X-WP-Nonce': (window as any).wpdSettings?.nonce }
            });
            const data = await response.json();

            if (data.pages) {
                setPages(data.pages);
            }

            setIsProInstalled(data.is_pro_installed || false);
            setLicenseStatus(data.license?.status || 'inactive');
            setLicenseKey(data.license?.key || '');

            setFormData(prev => ({
                ...prev,
                // General
                campaign_slug: data.general?.campaign_slug || 'campaign',
                payment_slug: data.general?.payment_slug || 'pay',
                remove_branding: data.general?.remove_branding === true || data.general?.remove_branding === '1',
                confirmation_page: data.general?.confirmation_page || '',
                delete_on_uninstall_settings: data.general?.delete_on_uninstall_settings === true || data.general?.delete_on_uninstall_settings === '1',
                delete_on_uninstall_tables: data.general?.delete_on_uninstall_tables === true || data.general?.delete_on_uninstall_tables === '1',
                // Donation
                min_amount: data.donation?.min_amount || 10000,
                presets: data.donation?.presets || '50000,100000,200000,500000',
                anonymous_label: data.donation?.anonymous_label || 'Hamba Allah',
                create_user: data.donation?.create_user === true || data.donation?.create_user === '1',
                recurring_intervals: data.donation?.recurring_intervals || ['month', 'year'],
                // Appearance
                brand_color: data.appearance?.brand_color || '#059669',
                button_color: data.appearance?.button_color || '#ec4899',
                container_width: data.appearance?.container_width || '1100px',
                border_radius: data.appearance?.border_radius || '12px',
                campaign_layout: data.appearance?.campaign_layout || 'sidebar-right',
                font_family: data.appearance?.font_family || 'Inter',
                font_size: data.appearance?.font_size || '16px',
                dark_mode: data.appearance?.dark_mode === true || data.appearance?.dark_mode === '1',
                donation_layout: data.appearance?.donation_layout || 'default',
                // Bank
                bank_name: data.bank?.bank_name || '',
                account_number: data.bank?.account_number || '',

                account_name: data.bank?.account_name || '',
                pro_accounts: data.bank?.pro_accounts || [],
                // Midtrans
                midtrans_enabled: data.midtrans?.enabled === true || data.midtrans?.enabled === '1',
                midtrans_production: data.midtrans?.is_production === true || data.midtrans?.is_production === '1',
                midtrans_server_key: data.midtrans?.server_key || '',
                // Pro Midtrans
                pro_midtrans_server_key: data.midtrans?.pro_server_key || '',
                pro_midtrans_client_key: data.midtrans?.pro_client_key || '',
                pro_midtrans_production: data.midtrans?.pro_is_production === true,
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
                    remove_branding: data.remove_branding,
                    confirmation_page: data.confirmation_page
                },
                donation: {
                    min_amount: data.min_amount,
                    presets: data.presets,
                    anonymous_label: data.anonymous_label,
                    create_user: data.create_user,
                    recurring_intervals: data.recurring_intervals
                },
                appearance: {
                    brand_color: data.brand_color,
                    button_color: data.button_color,
                    container_width: data.container_width,
                    border_radius: data.border_radius,
                    campaign_layout: data.campaign_layout,
                    font_family: data.font_family,
                    font_size: data.font_size,
                    dark_mode: data.dark_mode,
                    donation_layout: data.donation_layout,
                },
                bank: {
                    bank_name: data.bank_name,
                    account_number: data.account_number,
                    account_name: data.account_name,
                    pro_accounts: data.pro_accounts
                },
                midtrans: {
                    enabled: data.midtrans_enabled,
                    is_production: data.midtrans_production,
                    server_key: data.midtrans_server_key,
                    // Pro keys
                    pro_server_key: data.pro_midtrans_server_key,
                    pro_client_key: data.pro_midtrans_client_key,
                    pro_is_production: data.pro_midtrans_production
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
                },
                license: {
                    key: licenseKey
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
            toast.success('Pengaturan berhasil disimpan!', {
                description: 'Semua perubahan telah tersimpan ke database.'
            })
        },
        onError: (error: Error) => {
            toast.error('Gagal menyimpan pengaturan', {
                description: error.message || 'Terjadi kesalahan saat menyimpan pengaturan.'
            })
        }
    })

    const addAccount = () => {
        setFormData(prev => ({
            ...prev,
            pro_accounts: [...prev.pro_accounts, { id: Date.now().toString(), bank_name: '', account_number: '', account_name: '', is_default: false }]
        }))
    }

    const removeAccount = (index: number) => {
        const newAccounts = [...formData.pro_accounts];
        newAccounts.splice(index, 1);
        setFormData({ ...formData, pro_accounts: newAccounts });
    }

    const updateAccount = (index: number, field: string, value: any) => {
        const newAccounts = [...formData.pro_accounts];
        // @ts-ignore
        newAccounts[index][field] = value;
        setFormData({ ...formData, pro_accounts: newAccounts });
    }

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault()
        mutation.mutate(formData)
    }

    const tabs = [
        { id: 'general', label: 'General & Org', icon: Building },
        { id: 'donation', label: 'Donation Settings', icon: Heart },
        { id: 'payment', label: 'Payment', icon: CreditCard },
        { id: 'notifications', label: 'Notifications', icon: Bell },
        { id: 'appearance', label: 'Appearance', icon: Palette },
        { id: 'advanced', label: 'Advanced', icon: Star },
        { id: 'license', label: 'License', icon: Lock },
    ].filter(tab => tab.id !== 'license' || isProInstalled)

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <h2 className="text-2xl font-bold text-gray-800">Pengaturan</h2>
            </div>

            {/* Pro Banner */}
            {licenseStatus !== 'active' ? (
                <div className="relative overflow-hidden bg-gradient-to-br from-red-600 via-red-700 to-red-800 rounded-2xl p-8 text-white shadow-2xl border border-red-500">
                    {/* Decorative Elements */}
                    <div className="absolute top-0 right-0 w-64 h-64 bg-red-500 rounded-full opacity-10 blur-3xl -translate-y-1/2 translate-x-1/3"></div>
                    <div className="absolute bottom-0 left-0 w-48 h-48 bg-white rounded-full opacity-5 blur-2xl translate-y-1/2 -translate-x-1/3"></div>

                    <div className="relative flex justify-between items-center gap-6">
                        <div className="flex-1">
                            <div className="inline-flex items-center gap-2 bg-red-500/30 backdrop-blur-sm px-3 py-1.5 rounded-full mb-3 border border-red-400/30">
                                <Crown className="text-yellow-300 w-4 h-4" />
                                <span className="text-xs font-semibold tracking-wide uppercase">Premium Features</span>
                            </div>
                            <h3 className="text-2xl font-bold mb-2 tracking-tight text-white!">
                                Upgrade ke Donasai Pro
                            </h3>
                            <p className="text-red-50 text-sm leading-relaxed max-w-md">
                                Buka Donasi Berulang, Notifikasi WhatsApp, dan Konfirmasi AI dengan teknologi terdepan.
                            </p>
                        </div>
                        <button
                        type='button'
                            onClick={() => setShowProModal(true)}
                            className="group relative bg-white text-red-600 px-6 py-3.5 rounded-xl font-bold hover:bg-red-50 transition-all shadow-lg hover:shadow-xl hover:scale-105 active:scale-95"
                        >
                            <span className="relative z-10">Bandingkan Fitur</span>
                            <div className="absolute inset-0 bg-gradient-to-r from-red-50 to-white opacity-0 group-hover:opacity-100 transition-opacity rounded-xl"></div>
                        </button>
                    </div>
                </div>
            ) : (
                <div className="bg-green-100 border border-green-200 rounded-xl p-4 text-green-800 flex items-center gap-3">
                    <div className="p-2 bg-green-200 rounded-full"><Check size={20} /></div>
                    <div>
                        <h3 className="font-bold">Donasai Pro Aktif</h3>
                        <p className="text-sm opacity-90">Terima kasih telah mendukung pengembangan plugin ini!</p>
                    </div>
                </div>
            )}

            <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden min-h-[500px] flex">

                {/* Sidebar Tabs */}
                <div className="w-64 bg-gray-50 border-r border-gray-200 p-4 space-y-2">
                    {tabs.map(tab => {
                        const Icon = tab.icon
                        return (
                            <button
                                type='button'
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
                                <span> {tab.label === 'General & Org' ? 'Umum & Organisasi' :
                                    tab.label === 'Donation Settings' ? 'Pengaturan Donasi' :
                                        tab.label === 'Payment' ? 'Pembayaran' :
                                            tab.label === 'Notifications' ? 'Notifikasi' :
                                                tab.label === 'Appearance' ? 'Tampilan' :
                                                    tab.label === 'License' ? 'Lisensi' : tab.label}</span>
                            </button>
                        )
                    })}
                </div>

                {/* Content Area */}
                <div className="flex-1 p-8">
                    <form onSubmit={handleSubmit} className="max-w-2xl space-y-6">

                        {/* LICENSE TAB */}
                        {activeTab === 'license' && (
                            <div className="space-y-6">
                                <div>
                                    <h3 className="text-lg font-medium text-gray-900 mb-1">Status Lisensi</h3>
                                    <p className="text-sm text-gray-500 mb-4">Masukkan kunci lisensi Anda untuk membuka fitur Pro.</p>

                                    <div className="p-4 border border-gray-200 rounded-lg bg-gray-50">
                                        <label htmlFor="licenseKey"  className="block text-sm font-medium text-gray-700 mb-1">Kunci Lisensi</label>
                                        <div className="flex gap-2">
                                            <input
                                                type="text"
                                                id="licenseKey"
                                                className="flex-1 p-2 border border-gray-300 rounded-lg font-mono text-sm"
                                                value={licenseKey}
                                                onChange={e => setLicenseKey(e.target.value)}
                                                placeholder="Masukan License Key"
                                            />
                                            <div className={clsx("px-3 py-2 rounded-lg text-sm font-medium flex items-center gap-2",
                                                licenseStatus === 'active' ? "bg-green-100 text-green-700" : "bg-gray-200 text-gray-600"
                                            )}>
                                                {licenseStatus === 'active' ? <Check size={16} /> : <Lock size={16} />}
                                                {licenseStatus === 'active' ? 'Aktif' : 'Tidak Aktif'}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* GENERAL TAB */}
                        {activeTab === 'general' && (
                            <div className="space-y-8">
                                <div>
                                    <h3 className="text-lg font-medium text-gray-900 mb-1">Detail Organisasi</h3>
                                    <p className="text-sm text-gray-500 mb-4">Informasi ini akan muncul pada kuitansi donasi.</p>

                                    <div className="grid gap-4">
                                        <div>
                                            <label htmlFor="orgName" className="block text-sm font-medium text-gray-700 mb-1">Nama Organisasi</label>
                                            <input
                                                type="text"
                                                id="orgName"
                                                className="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 hover:border-blue-400 transition-colors"
                                                value={formData.org_name}
                                                onChange={e => setFormData({ ...formData, org_name: e.target.value })}
                                                placeholder="Contoh: Yayasan Amal Bhakti"
                                            />
                                        </div>
                                        <div>
                                            <label htmlFor="orgAddress" className="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                                            <textarea
                                                id="orgAddress"
                                                className="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                                rows={3}
                                                value={formData.org_address}
                                                onChange={e => setFormData({ ...formData, org_address: e.target.value })}
                                                placeholder="Alamat lengkap..."
                                            />
                                        </div>
                                        <div className="grid grid-cols-2 gap-4">
                                            <div>
                                                <label htmlFor="orgEmail" className="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                                <input
                                                    type="email"
                                                    className="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                                    value={formData.org_email}
                                                    onChange={e => setFormData({ ...formData, org_email: e.target.value })}
                                                />
                                            </div>
                                            <div>
                                                <label htmlFor="orgPhone" className="block text-sm font-medium text-gray-700 mb-1">Telepon / WhatsApp</label>
                                                <input
                                                    type="text"
                                                    className="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                                    value={formData.org_phone}
                                                    onChange={e => setFormData({ ...formData, org_phone: e.target.value })}
                                                />
                                            </div>
                                        </div>
                                        <div>
                                            <label htmlFor="orgLogo" className="block text-sm font-medium text-gray-700 mb-1">Logo URL</label>
                                            <input
                                                type="text"
                                                id="orgLogo"
                                                className="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                                value={formData.org_logo}
                                                onChange={e => setFormData({ ...formData, org_logo: e.target.value })}
                                                placeholder="https://..."
                                            />
                                            <p className="text-xs text-gray-500 mt-1">Unggah ke Pustaka Media dan tempel URL di sini.</p>
                                        </div>
                                    </div>
                                </div>
                                <div className="border-t border-gray-200 pt-6">
                                    <div className="flex items-center gap-3 mb-6">
                                        <div className="p-2 bg-blue-50 rounded-lg text-blue-600">
                                            <LinkIcon size={20} />
                                        </div>
                                        <div>
                                            <h3 className="text-lg font-medium text-gray-900">Pengaturan Permalink</h3>
                                        </div>
                                    </div>

                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                        <div>
                                            <label htmlFor="campaignSlug" className="block text-sm font-medium text-gray-700 mb-2">Slug Kampanye</label>
                                            <div className="flex bg-white border border-gray-300 rounded-lg shadow-sm focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500 overflow-hidden items-stretch">
                                                <span className="px-3 bg-gray-50 border-r border-gray-200 text-gray-500 text-sm flex items-center whitespace-nowrap">
                                                    {window.location.host}/
                                                </span>
                                                <input
                                                    id="campaignSlug"
                                                    type="text"
                                                    className="flex-1 w-full p-2.5 border-none focus:ring-0 text-sm text-gray-900 placeholder-gray-400 bg-transparent"
                                                    value={formData.campaign_slug}
                                                    onChange={e => setFormData({ ...formData, campaign_slug: e.target.value })}
                                                />
                                                <span className="px-3 bg-gray-50 border-l border-gray-200 text-gray-500 text-sm flex items-center whitespace-nowrap">
                                                    /judul-campaign
                                                </span>
                                            </div>
                                            <p className="mt-1.5 text-xs text-gray-500">Contoh: <code>domain.com/{formData.campaign_slug}/pembangunan-masjid</code></p>
                                        </div>

                                        <div>
                                            <label htmlFor="paymentSlug" className="block text-sm font-medium text-gray-700 mb-2">Slug Pembayaran</label>
                                            <div className="flex bg-white border border-gray-300 rounded-lg shadow-sm focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500 overflow-hidden items-stretch">
                                                <span className="px-3 bg-gray-50 border-r border-gray-200 text-gray-500 text-sm flex items-center whitespace-nowrap">
                                                    {window.location.host}/{formData.campaign_slug}/
                                                </span>
                                                <input
                                                    id="paymentSlug"
                                                    type="text"
                                                    className="flex-1 w-full p-2.5 border-none focus:ring-0 text-sm text-gray-900 placeholder-gray-400 bg-transparent"
                                                    value={formData.payment_slug}
                                                    onChange={e => setFormData({ ...formData, payment_slug: e.target.value })}
                                                />
                                            </div>
                                            <p className="mt-1.5 text-xs text-gray-500">URL halaman form donasi (default: pay, donate, dll).</p>
                                        </div>
                                    </div>

                                    <div className="bg-amber-50 border border-amber-200 rounded-lg p-4 flex gap-3 text-amber-800">
                                        <Bell size={18} className="shrink-0 mt-0.5" />
                                        <div className="text-sm">
                                            <p className="font-semibold mb-1">Penting: Simpan Permalink WordPress</p>
                                            <p>Setelah mengubah slug di atas, Anda <u>wajib</u> masuk ke menu <strong>Settings &gt; Permalinks</strong> di dashboard WordPress dan klik "Save Changes" agar URL baru dapat diakses.</p>
                                        </div>
                                    </div>
                                </div>

                                <div className="border-t border-gray-200 pt-6">
                                    <h3 className="text-lg font-medium text-gray-900 mb-4">Pengaturan Konfirmasi</h3>
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label htmlFor="confirmationPage" className="block text-sm font-medium text-gray-700 mb-1">
                                                Halaman Konfirmasi
                                            </label>
                                            <select
                                                id="confirmationPage"
                                                value={formData.confirmation_page || ''}
                                                onChange={(e) => setFormData({ ...formData, confirmation_page: e.target.value })}
                                                className="w-full p-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            >
                                                <option value="">-- Pilih Halaman --</option>
                                                {pages.map((page: any) => (
                                                    <option key={page.id} value={page.id}>{page.title}</option>
                                                ))}
                                            </select>
                                            <p className="mt-1 text-xs text-gray-500">
                                                Halaman ini harus berisi shortcode <code>[wpd_confirmation_form]</code>.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div className="border-t border-gray-200 pt-6">
                                    <h3 className="text-lg font-medium text-gray-900 mb-2 flex items-center gap-2">
                                        Branding
                                    </h3>
                                    <div className="flex items-center space-x-3">
                                        <input
                                            id="removeBranding"
                                            type="checkbox"
                                            checked={formData.remove_branding}
                                            onChange={(e) => setFormData({ ...formData, remove_branding: e.target.checked })}
                                            className="h-4 w-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                                        />
                                        <label htmlFor="removeBranding" className="text-sm font-medium text-gray-700">Hapus Branding "Powered by Donasai"</label>
                                    </div>
                                    <p className="text-xs text-gray-500 mt-1 ml-7">Opsi untuk menyembunyikan "Powered by Donasai" di footer form dan kuitansi.</p>
                                </div>
                            </div>
                        )}

                        {/* DONATION TAB */}
                        {activeTab === 'donation' && (
                            <div className="space-y-6">
                                <div>
                                    <h3 className="text-lg font-medium text-gray-900 mb-4">Opsi Donasi</h3>
                                    <div className="grid gap-4">
                                        <div>
                                            <label htmlFor="minAmount" className="block text-sm font-medium text-gray-700 mb-1">Jumlah Donasi Minimum (Rp)</label>
                                            <input
                                                type="number"
                                                id="minAmount"
                                                className="w-full p-2 border border-gray-300 rounded-lg"
                                                value={formData.min_amount}
                                                onChange={e => setFormData({ ...formData, min_amount: parseInt(e.target.value) || 0 })}
                                            />
                                        </div>
                                        <div>
                                            <label htmlFor="presets" className="block text-sm font-medium text-gray-700 mb-1">Preset Default (Rp)</label>
                                            <input
                                                type="text"
                                                id="presets"
                                                className="w-full p-2 border border-gray-300 rounded-lg"
                                                value={formData.presets}
                                                onChange={e => setFormData({ ...formData, presets: e.target.value })}
                                                placeholder="50000,100000,200000"
                                            />
                                            <p className="text-xs text-gray-500 mt-1">Pisahkan dengan koma.</p>
                                        </div>
                                        <div>
                                            <label htmlFor="anonymousLabel" className="block text-sm font-medium text-gray-700 mb-1">Label Anonim</label>
                                            <input
                                                type="text"
                                                id="anonymousLabel"
                                                className="w-full p-2 border border-gray-300 rounded-lg"
                                                value={formData.anonymous_label}
                                                onChange={e => setFormData({ ...formData, anonymous_label: e.target.value })}
                                                placeholder="Hamba Allah"
                                            />
                                            <p className="text-xs text-gray-500 mt-1">Ditampilkan saat pengguna menyembunyikan nama mereka.</p>
                                        </div>
                                    </div>
                                </div>
                                {licenseStatus === 'active' ? (
                                    <div className="border-t border-gray-200 pt-6">
                                        <h3 className="text-lg font-medium text-gray-900 mb-2 flex items-center gap-2">
                                            Donasi Berulang <span className="bg-purple-100 text-purple-700 text-xs px-2 py-0.5 rounded font-bold">PRO</span>
                                        </h3>
                                        <p className="text-sm text-gray-500 mb-3">Pilih interval penagihan yang tersedia untuk donatur.</p>
                                        <div className="grid grid-cols-2 gap-3 mb-6">
                                            {[
                                                { id: 'day', label: 'Harian' },
                                                { id: 'week', label: 'Mingguan' },
                                                { id: 'month', label: 'Bulanan' },
                                                { id: 'year', label: 'Tahunan' }
                                            ].map((interval) => (
                                                <div key={interval.id} className="flex items-center space-x-2">
                                                    <input
                                                        type="checkbox"
                                                        id={`interval-${interval.id}`}
                                                        checked={(formData.recurring_intervals as string[])?.includes(interval.id)}
                                                        onChange={(e) => {
                                                            const checked = e.target.checked;
                                                            let current = [...(formData.recurring_intervals as string[] || [])];
                                                            if (checked) {
                                                                if (!current.includes(interval.id)) current.push(interval.id);
                                                            } else {
                                                                current = current.filter(i => i !== interval.id);
                                                            }
                                                            setFormData({ ...formData, recurring_intervals: current });
                                                        }}
                                                        className="h-4 w-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                                                    />
                                                    <label htmlFor={`interval-${interval.id}`} className="text-sm text-gray-700">{interval.label}</label>
                                                </div>
                                            ))}
                                        </div>

                                        <h3 className="text-lg font-medium text-gray-900 mb-2 flex items-center gap-2">
                                            Registrasi Pengguna <span className="bg-purple-100 text-purple-700 text-xs px-2 py-0.5 rounded font-bold">PRO</span>
                                        </h3>
                                        <div className="flex items-center space-x-3">
                                            <input
                                                id="createUser"
                                                type="checkbox"
                                                checked={formData.create_user}
                                                onChange={(e) => setFormData({ ...formData, create_user: e.target.checked })}
                                                className="h-4 w-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                                            />
                                            <label htmlFor="createUser" className="text-sm font-medium text-gray-700">Otomatis buat Pengguna WordPress dari Email Donatur</label>
                                        </div>
                                    </div>
                                ) : (
                                    <div className="border-t border-gray-200 pt-6">
                                        <h3 className="text-lg font-medium text-gray-900 mb-2 flex items-center gap-2">
                                            Registrasi Pengguna <span className="bg-purple-100 text-purple-700 text-xs px-2 py-0.5 rounded font-bold">PRO</span>
                                        </h3>
                                        <div className="flex items-center space-x-3 opacity-60">
                                            <input
                                                    id="createUserPro"
                                                type="checkbox"
                                                checked={formData.create_user}
                                                onChange={() => setShowProModal(true)}
                                                className="h-4 w-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                                            />
                                            <label htmlFor="createUserPro" className="text-sm font-medium text-gray-700">Otomatis buat Pengguna WordPress dari Email Donatur</label>
                                        </div>
                                    </div>
                                )}
                            </div>
                        )}

                        {/* PAYMENT TAB */}
                        {activeTab === 'payment' && (
                            <div className="space-y-8">
                                <div>
                                    <h3 className="text-lg font-medium text-gray-900 mb-4">Transfer Bank (Manual)</h3>

                                    {licenseStatus === 'active' ? (
                                        <div className="space-y-4">
                                            <div className="bg-purple-50 p-4 rounded-lg border border-purple-200">
                                                <div className="flex justify-between items-center mb-3">
                                                    <h4 className="text-sm font-bold text-purple-800 flex items-center gap-2">
                                                        <Crown size={14} /> Multi-Akun Bank (Pro)
                                                    </h4>
                                                    <button type="button" onClick={addAccount} className="text-xs flex items-center gap-1 bg-purple-600 text-white px-2 py-1 rounded hover:bg-purple-700">
                                                        <Plus size={12} /> Tambah Rekening
                                                    </button>
                                                </div>

                                                {formData.pro_accounts.length === 0 && (
                                                    <p className="text-sm text-gray-500 italic">Belum ada rekening yang ditambahkan.</p>
                                                )}

                                                <div className="space-y-3">
                                                    {formData.pro_accounts.map((acc, idx) => (
                                                        <div key={acc.id || idx} className="bg-white p-3 rounded border border-gray-200 shadow-sm relative group">
                                                            <div className="grid md:grid-cols-3 gap-2 mb-2">
                                                                <input
                                                                    type="text"
                                                                    placeholder="Bank (e.g BCA)"
                                                                    className="p-1.5 text-sm border rounded"
                                                                    value={acc.bank_name}
                                                                    onChange={(e) => updateAccount(idx, 'bank_name', e.target.value)}
                                                                />
                                                                <input
                                                                    type="text"
                                                                    placeholder="No. Rekening"
                                                                    className="p-1.5 text-sm border rounded"
                                                                    value={acc.account_number}
                                                                    onChange={(e) => updateAccount(idx, 'account_number', e.target.value)}
                                                                />
                                                                <input
                                                                    type="text"
                                                                    placeholder="Atas Nama"
                                                                    className="p-1.5 text-sm border rounded"
                                                                    value={acc.account_name}
                                                                    onChange={(e) => updateAccount(idx, 'account_name', e.target.value)}
                                                                />
                                                            </div>
                                                            <div className="flex justify-between items-center">
                                                                <label className="flex items-center gap-2 text-xs text-gray-600 cursor-pointer">
                                                                    <input
                                                                        type="checkbox"
                                                                        checked={acc.is_default}
                                                                        onChange={(e) => updateAccount(idx, 'is_default', e.target.checked)}
                                                                        className="rounded text-purple-600 focus:ring-purple-500"
                                                                    />
                                                                    Default
                                                                </label>
                                                                <button type="button" onClick={() => removeAccount(idx)} className="text-red-500 hover:text-red-700 p-1">
                                                                    <Trash size={14} />
                                                                </button>
                                                            </div>
                                                        </div>
                                                    ))}
                                                </div>
                                            </div>

                                            <div className="opacity-50 pointer-events-none">
                                                <p className="text-xs text-gray-500 mb-2">Pengaturan Fallback (Gratis):</p>
                                                <div className="grid grid-cols-2 gap-4">
                                                    <div>
                                                        <label htmlFor="bankName" className="block text-xs font-medium text-gray-700 mb-1">Nama Bank</label>
                                                        <input type="text" id="bankName" className="w-full p-2 border border-gray-300 rounded-lg bg-gray-100" value={formData.bank_name} readOnly />
                                                    </div>
                                                    <div>
                                                        <label htmlFor="accountNumber" className="block text-xs font-medium text-gray-700 mb-1">Nomor Rekening</label>
                                                        <input type="text" id="accountNumber" className="w-full p-2 border border-gray-300 rounded-lg bg-gray-100" value={formData.account_number} readOnly />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    ) : (
                                        <div className="grid gap-4">
                                            <div className="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label htmlFor="bankName" className="block text-sm font-medium text-gray-700 mb-1">Nama Bank</label>
                                                    <input
                                                        type="text"
                                                        id="bankName"
                                                        className="w-full p-2 border border-gray-300 rounded-lg"
                                                        value={formData.bank_name}
                                                        onChange={e => setFormData({ ...formData, bank_name: e.target.value })}
                                                        placeholder="Contoh: BCA"
                                                    />
                                                </div>
                                                <div>
                                                    <label htmlFor="accountNumber" className="block text-sm font-medium text-gray-700 mb-1">Nomor Rekening</label>
                                                    <input
                                                        type="text"
                                                        id="accountNumber"
                                                        className="w-full p-2 border border-gray-300 rounded-lg"
                                                        value={formData.account_number}
                                                        onChange={e => setFormData({ ...formData, account_number: e.target.value })}
                                                    />
                                                </div>
                                            </div>
                                            <div>
                                                <label htmlFor="accountName" className="block text-sm font-medium text-gray-700 mb-1">Nama Pemilik Rekening</label>
                                                <input
                                                    type="text"
                                                    id="accountName"
                                                    className="w-full p-2 border border-gray-300 rounded-lg"
                                                    value={formData.account_name}
                                                    onChange={e => setFormData({ ...formData, account_name: e.target.value })}
                                                />
                                            </div>

                                            {/* Pro Teaser */}
                                            <div id="proTeaser" className="bg-gray-50 border border-gray-200 rounded-lg p-3 flex justify-between items-center cursor-pointer hover:bg-gray-100" onClick={() => setShowProModal(true)}>
                                                <div className="flex items-center gap-2">
                                                    <Crown size={14} className="text-purple-600" />
                                                    <span className="text-sm font-medium text-gray-600">Butuh lebih dari satu rekening bank?</span>
                                                </div>
                                                <span className="text-xs text-purple-600 font-bold">Upgrade Pro</span>
                                            </div>
                                        </div>
                                    )}
                                </div>

                                <div className="border-t border-gray-200 pt-6">
                                    <h3 className="text-lg font-medium text-gray-900 mb-4">Gerbang Pembayaran Midtrans</h3>
                                    <div className="space-y-4">
                                        <div className="flex items-center space-x-3">
                                            <input
                                                type="checkbox"
                                                id="midtrans_enabled"
                                                checked={formData.midtrans_enabled}
                                                onChange={(e) => setFormData(prev => ({ ...prev, midtrans_enabled: e.target.checked }))}
                                                className="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                            />
                                            <label htmlFor="midtrans_enabled" className="text-sm font-medium text-gray-700">Aktifkan Midtrans</label>
                                        </div>

                                        {/* Pro Settings (Only if Active) */}
                                        {licenseStatus === 'active' && (
                                            <>
                                                <div className="p-4 bg-purple-50 rounded-lg border border-purple-200">
                                                    <h4 className="text-sm font-bold text-purple-800 mb-3 flex items-center gap-2">
                                                        <Crown size={14} /> Pengaturan Lanjutan (Pro)
                                                    </h4>

                                                    <div className="space-y-4">
                                                        <div className="flex items-center space-x-3">
                                                            <input
                                                                type="checkbox"
                                                                id="pro_midtrans_production"
                                                                checked={formData.pro_midtrans_production}
                                                                onChange={(e) => setFormData(prev => ({ ...prev, pro_midtrans_production: e.target.checked }))}
                                                                className="h-4 w-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500"
                                                            />
                                                            <label htmlFor="pro_midtrans_production" className="text-sm font-medium text-purple-900">Mode Produksi (Pro)</label>
                                                        </div>

                                                        <div className="grid md:grid-cols-2 gap-4">
                                                            <div>
                                                                <label htmlFor="pro_midtrans_server_key" className="block text-xs font-medium text-purple-900 mb-1">Server Key (Pro)</label>
                                                                <input
                                                                    id="pro_midtrans_server_key"
                                                                    type="password"
                                                                    className="w-full p-2 border border-purple-200 rounded-lg font-mono text-xs bg-white"
                                                                    value={formData.pro_midtrans_server_key}
                                                                    onChange={e => setFormData({ ...formData, pro_midtrans_server_key: e.target.value })}
                                                                    placeholder="Override Free Key..."
                                                                />
                                                            </div>
                                                            <div>
                                                                <label htmlFor="pro_midtrans_client_key" className="block text-xs font-medium text-purple-900 mb-1">Client Key (Pro)</label>
                                                                <input
                                                                    id="pro_midtrans_client_key"
                                                                    type="text"
                                                                    className="w-full p-2 border border-purple-200 rounded-lg font-mono text-xs bg-white"
                                                                    value={formData.pro_midtrans_client_key}
                                                                    onChange={e => setFormData({ ...formData, pro_midtrans_client_key: e.target.value })}
                                                                    placeholder="Override Free Key..."
                                                                />
                                                            </div>
                                                        </div>
                                                        <p className="text-xs text-purple-700">Kunci ini akan menimpa pengaturan versi gratis jika diisi.</p>
                                                    </div>
                                                </div>

                                                <div className={clsx("opacity-50 pointer-events-none")}>
                                                    <label htmlFor="midtrans_server_key" className="block text-sm font-medium text-gray-700 mb-1">Server Key (Gratis)</label>
                                                    <input
                                                        type="password"
                                                        id="midtrans_server_key"
                                                        className="w-full p-2 border border-gray-300 rounded-lg font-mono text-sm bg-gray-100"
                                                        value={formData.midtrans_server_key}
                                                        readOnly
                                                        placeholder="Key Standar..."
                                                    />
                                                    <p className="text-xs text-gray-500 mt-1">Gunakan Pro Key di atas untuk fungsionalitas penuh.</p>
                                                </div>
                                            </>
                                        )}
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* NOTIFICATIONS TAB */}
                        {activeTab === 'notifications' && (
                            <div className="space-y-6">
                                <div>
                                    <h3 className="text-lg font-medium text-gray-900 mb-1">Langganan Pembaruan</h3>
                                    <p className="text-sm text-gray-500 mb-4">Terima pembaruan donatur dan pengumuman plugin.</p>

                                    <div className="grid gap-4">
                                        <div>
                                            <label htmlFor="opt_in_email" className="block text-sm font-medium text-gray-700 mb-1">Email untuk Pembaruan</label>
                                            <input
                                                id="opt_in_email"
                                                type="email"
                                                className="w-full p-2 border border-gray-300 rounded-lg"
                                                value={formData.opt_in_email}
                                                onChange={e => setFormData({ ...formData, opt_in_email: e.target.value })}
                                            />
                                            <p className="text-xs text-gray-500 mt-1">Kami akan memverifikasi email ini sebelum mengirim laporan sensitif.</p>
                                        </div>
                                        <div>
                                            <label  htmlFor="opt_in_whatsapp" className="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp (Opsional)</label>
                                            <input
                                                id="opt_in_whatsapp"
                                                type="text"
                                                className="w-full p-2 border border-gray-300 rounded-lg"
                                                value={formData.opt_in_whatsapp}
                                                onChange={e => setFormData({ ...formData, opt_in_whatsapp: e.target.value })}
                                                placeholder="Contoh: 62812..."
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div className="border-t border-gray-200 pt-6">
                                    <h3 className="text-lg font-medium text-gray-900 mb-2 flex items-center gap-2">
                                        <Bell size={18} className="text-gray-400" />
                                        Notifikasi Lanjutan <span className="bg-purple-100 text-purple-700 text-xs px-2 py-0.5 rounded font-bold">PRO</span>
                                    </h3>
                                    <p className="text-sm text-gray-500 mb-4">Tersedia di versi Pro:</p>
                                    <ul className="list-disc pl-5 text-sm text-gray-600 space-y-1">
                                        {/* <li>Notifikasi WhatsApp real-time untuk setiap donasi.</li> */}
                                        <li>Ringkasan harian via Email.</li>
                                        <li>Peringatan pembayaran gagal.</li>
                                    </ul>
                                </div>
                            </div>
                        )}

                        {/* APPEARANCE TAB */}
                        {activeTab === 'appearance' && (
                            <div className="space-y-8">
                                <div>
                                    <h3 className="text-lg font-medium text-gray-900 mb-4">Tampilan & Layout</h3>
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label htmlFor="brand_color" className="block text-sm font-medium text-gray-700 mb-2">Warna Merek Utama</label>
                                            <div className="flex items-center gap-3">
                                                <input
                                                    type="color"
                                                    id="brand_color"
                                                    value={formData.brand_color || '#059669'}
                                                    onChange={e => setFormData({ ...formData, brand_color: e.target.value })}
                                                    className="h-10 w-20 p-1 rounded border border-gray-300 cursor-pointer"
                                                />
                                                <span className="text-sm text-gray-500 font-mono uppercase">{formData.brand_color}</span>
                                            </div>
                                            <p className="text-xs text-gray-500 mt-2">Digunakan untuk lencana, jumlah, dan bilah kemajuan.</p>
                                        </div>
                                        <div>
                                            <label htmlFor="button_color" className="block text-sm font-medium text-gray-700 mb-2">Warna Tombol</label>
                                            <div className="flex items-center gap-3">
                                                <input
                                                    type="color"
                                                    id="button_color"
                                                    value={formData.button_color || '#ec4899'}
                                                    onChange={e => setFormData({ ...formData, button_color: e.target.value })}
                                                    className="h-10 w-20 p-1 rounded border border-gray-300 cursor-pointer"
                                                />
                                                <span className="text-sm text-gray-500 font-mono uppercase">{formData.button_color}</span>
                                            </div>
                                            <p className="text-xs text-gray-500 mt-2">Tombol CTA utama (Donasi, Kirim).</p>
                                        </div>
                                        <div>
                                            <label htmlFor="container_width" className="block text-sm font-medium text-gray-700 mb-1">Lebar Kontainer</label>
                                            <input
                                                type="text"
                                                id="container_width"
                                                value={formData.container_width || '1100px'}
                                                onChange={(e) => setFormData({ ...formData, container_width: e.target.value })}
                                                className="w-full p-2 border border-gray-300 rounded-lg"
                                                placeholder="1100px"
                                            />
                                            <p className="text-xs text-gray-500 mt-2">Lebar maksimal halaman campaign.</p>
                                        </div>
                                        <div>
                                            <label htmlFor="border_radius" className="block text-sm font-medium text-gray-700 mb-1">Border Radius</label>
                                            <input
                                                type="text"
                                                id="border_radius"
                                                value={formData.border_radius || '12px'}
                                                onChange={(e) => setFormData({ ...formData, border_radius: e.target.value })}
                                                className="w-full p-2 border border-gray-300 rounded-lg"
                                                placeholder="12px"
                                            />
                                            <p className="text-xs text-gray-500 mt-2">Kelengkungan sudut (card, tombol, input).</p>
                                        </div>
                                        <div className="md:col-span-2">
                                            <label htmlFor="campaign_layout" className="block text-sm font-medium text-gray-700 mb-3">Layout Halaman Campaign</label>
                                            <div className="grid grid-cols-3 gap-4">
                                                {/* Sidebar Right */}
                                                <div
                                                    id="campaign_layout"
                                                    className={clsx(
                                                        "border-2 rounded-xl p-3 cursor-pointer transition-all hover:border-blue-300",
                                                        formData.campaign_layout === 'sidebar-right' ? "border-blue-600 bg-blue-50 ring-1 ring-blue-600" : "border-gray-200 bg-white"
                                                    )}
                                                    onClick={() => setFormData({ ...formData, campaign_layout: 'sidebar-right' })}
                                                >
                                                    <div className="aspect-video bg-gray-100 rounded mb-2 flex gap-1 p-1">
                                                        <div className="bg-gray-300 h-full w-2/3 rounded-sm"></div>
                                                        <div className="bg-blue-200 h-full w-1/3 rounded-sm"></div>
                                                    </div>
                                                    <div className="text-xs font-medium text-center text-gray-700">Sidebar Kanan</div>
                                                </div>

                                                {/* Sidebar Left */}
                                                <div
                                                    id="campaign_layout"
                                                    className={clsx(
                                                        "border-2 rounded-xl p-3 cursor-pointer transition-all hover:border-blue-300",
                                                        formData.campaign_layout === 'sidebar-left' ? "border-blue-600 bg-blue-50 ring-1 ring-blue-600" : "border-gray-200 bg-white"
                                                    )}
                                                    onClick={() => setFormData({ ...formData, campaign_layout: 'sidebar-left' })}
                                                >
                                                    <div className="aspect-video bg-gray-100 rounded mb-2 flex gap-1 p-1">
                                                        <div className="bg-blue-200 h-full w-1/3 rounded-sm"></div>
                                                        <div className="bg-gray-300 h-full w-2/3 rounded-sm"></div>
                                                    </div>
                                                    <div className="text-xs font-medium text-center text-gray-700">Sidebar Kiri</div>
                                                </div>

                                                {/* Full Width */}
                                                <div
                                                    id="campaign_layout"
                                                    className={clsx(
                                                        "border-2 rounded-xl p-3 cursor-pointer transition-all hover:border-blue-300",
                                                        formData.campaign_layout === 'full-width' ? "border-blue-600 bg-blue-50 ring-1 ring-blue-600" : "border-gray-200 bg-white"
                                                    )}
                                                    onClick={() => setFormData({ ...formData, campaign_layout: 'full-width' })}
                                                >
                                                    <div className="aspect-video bg-gray-100 rounded mb-2 w-full p-1">
                                                        <div className="bg-gray-300 h-full w-full rounded-sm"></div>
                                                    </div>
                                                    <div className="text-xs font-medium text-center text-gray-700">Full Width</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/* Pro Teasers */}
                                <div className="border-t border-gray-200 pt-6">
                                    <h3 className="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
                                        Gaya Lanjutan <span className="bg-purple-100 text-purple-700 text-xs px-2 py-0.5 rounded font-bold">PRO</span>
                                    </h3>
                                    <div className="grid gap-4 md:grid-cols-2">
                                        {/* Typography */}
                                        <div className={clsx("border border-gray-200 rounded-lg p-4 bg-gray-50 relative", !['active', 'pro'].includes(licenseStatus) ? "opacity-60 cursor-pointer" : "")} onClick={() => !['active', 'pro'].includes(licenseStatus) && setShowProModal(true)}>
                                            <div className="flex justify-between items-start mb-2">
                                                <div className="font-medium text-gray-900">Tipografi</div>
                                                {!['active', 'pro'].includes(licenseStatus) ? <Lock size={14} className="text-gray-400" /> : <span className="text-xs font-bold text-green-600 bg-green-100 px-2 rounded">Active</span>}
                                            </div>

                                            {licenseStatus === 'active' ? (
                                                <div className="space-y-3 mt-3">
                                                    <div>
                                                        <label htmlFor="font_family" className="block text-xs font-medium text-gray-700 mb-1">Font Utama</label>
                                                        <select
                                                            id="font_family"
                                                            value={formData.font_family || 'Inter'}
                                                            onChange={(e) => setFormData({ ...formData, font_family: e.target.value })}
                                                            className="w-full text-sm p-2 border border-gray-300 rounded"
                                                        >
                                                            <option value="Inter">Inter (Default)</option>
                                                            <option value="Roboto">Roboto</option>
                                                            <option value="Open Sans">Open Sans</option>
                                                            <option value="Poppins">Poppins</option>
                                                            <option value="Lato">Lato</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label   htmlFor="font_size" className="block text-xs font-medium text-gray-700 mb-1">Ukuran Font Dasar</label>
                                                        <div  className="flex items-center gap-2">
                                                            <input
                                                                type="text"
                                                                id="font_size"
                                                                value={formData.font_size || '16px'}
                                                                onChange={(e) => setFormData({ ...formData, font_size: e.target.value })}
                                                                className="w-20 text-sm p-2 border border-gray-300 rounded"
                                                            />
                                                            <span className="text-xs text-gray-500">px/rem</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            ) : (
                                                <p className="text-sm text-gray-500">Font Google kustom dan kontrol ukuran.</p>
                                            )}
                                        </div>

                                        {/* Dark Mode */}
                                        <div className={clsx("border border-gray-200 rounded-lg p-4 bg-gray-50 relative", !['active', 'pro'].includes(licenseStatus) ? "opacity-60 cursor-pointer" : "")} onClick={() => !['active', 'pro'].includes(licenseStatus) && setShowProModal(true)}>
                                            <div className="flex justify-between items-start mb-2">
                                                <div className="font-medium text-gray-900">Mode Gelap</div>
                                                {!['active', 'pro'].includes(licenseStatus) ? <Lock size={14} className="text-gray-400" /> : <span className="text-xs font-bold text-green-600 bg-green-100 px-2 rounded">Active</span>}
                                            </div>

                                            {licenseStatus === 'active' ? (
                                                <div className="mt-3">
                                                    <div className="flex items-center gap-3">
                                                        <div className="relative inline-flex items-center cursor-pointer">
                                                            <input
                                                                type="checkbox"
                                                                className="sr-only peer"
                                                                checked={formData.dark_mode}
                                                                onChange={(e) => setFormData({ ...formData, dark_mode: e.target.checked })}
                                                            />
                                                            <div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                                                        </div>
                                                        <span className="text-sm text-gray-600">{formData.dark_mode ? 'Aktif' : 'Nonaktif'}</span>
                                                    </div>
                                                    <p className="text-xs text-gray-500 mt-2">Otomatis menyesuaikan warna background dan teks.</p>
                                                </div>
                                            ) : (
                                                <p className="text-sm text-gray-500">Aktifkan dukungan mode gelap di seluruh situs.</p>
                                            )}
                                        </div>

                                        {/* Donation Form Layout (Pro Only) */}
                                        <div className={clsx("border border-gray-200 rounded-lg p-4 bg-gray-50 relative md:col-span-2", !['active', 'pro'].includes(licenseStatus) ? "opacity-60 cursor-pointer" : "")} onClick={() => !['active', 'pro'].includes(licenseStatus) && setShowProModal(true)}>
                                            <div className="flex justify-between items-start mb-4">
                                                <div className="font-medium text-gray-900">Layout Formulir Donasi</div>
                                                {!['active', 'pro'].includes(licenseStatus) ? <Lock size={14} className="text-gray-400" /> : <span className="text-xs font-bold text-green-600 bg-green-100 px-2 rounded">Active</span>}
                                            </div>

                                            {licenseStatus === 'active' ? (
                                                <div className="grid grid-cols-2 gap-4">
                                                    {/* Default */}
                                                    <div
                                                        className={clsx(
                                                            "border-2 rounded-xl p-3 cursor-pointer transition-all hover:border-blue-300",
                                                            formData.donation_layout === 'default' ? "border-blue-600 bg-blue-50 ring-1 ring-blue-600" : "border-gray-200 bg-white"
                                                        )}
                                                        onClick={() => setFormData({ ...formData, donation_layout: 'default' })}
                                                    >
                                                        <div className="aspect-video bg-gray-100 rounded mb-2 flex flex-col items-center justify-center p-2">
                                                            <div className="bg-white w-2/3 h-full rounded shadow-sm border border-gray-200"></div>
                                                        </div>
                                                        <div className="text-xs font-medium text-center text-gray-700">Tunggal (Default)</div>
                                                    </div>

                                                    {/* Split */}
                                                    <div
                                                        className={clsx(
                                                            "border-2 rounded-xl p-3 cursor-pointer transition-all hover:border-blue-300",
                                                            formData.donation_layout === 'split' ? "border-blue-600 bg-blue-50 ring-1 ring-blue-600" : "border-gray-200 bg-white"
                                                        )}
                                                        onClick={() => setFormData({ ...formData, donation_layout: 'split' })}
                                                    >
                                                        <div className="aspect-video bg-gray-100 rounded mb-2 flex gap-1 p-1">
                                                            <div className="bg-blue-100 h-full w-1/2 rounded-sm border border-blue-200"></div>
                                                            <div className="bg-white h-full w-1/2 rounded-sm border border-gray-200"></div>
                                                        </div>
                                                        <div className="text-xs font-medium text-center text-gray-700">Split (Kiri Info, Kanan Form)</div>
                                                    </div>
                                                </div>
                                            ) : (
                                                <p className="text-sm text-gray-500">Pilihan tata letak untuk formulir donasi.</p>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* ADVANCED TAB */}
                        {activeTab === 'advanced' && (
                            <div className="space-y-6">
                                <div>
                                    <h3 className="text-lg font-medium text-gray-900 mb-4">Ekspor & Impor Pengaturan</h3>

                                    {/* Export Settings */}
                                    <div className="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-4">
                                        <h4 className="font-medium text-gray-800 mb-2">Ekspor Pengaturan</h4>
                                        <p className="text-sm text-gray-600 mb-3">Unduh semua pengaturan plugin Anda sebagai file JSON. Gunakan untuk backup atau memindahkan ke situs lain.</p>
                                        <button
                                            type="button"
                                            onClick={() => {
                                                const settings = {
                                                    general: {
                                                        campaign_slug: formData.campaign_slug,
                                                        payment_slug: formData.payment_slug,
                                                        remove_branding: formData.remove_branding,
                                                        confirmation_page: formData.confirmation_page,
                                                    },
                                                    donation: {
                                                        min_amount: formData.min_amount,
                                                        presets: formData.presets,
                                                        anonymous_label: formData.anonymous_label,
                                                        create_user: formData.create_user,
                                                    },
                                                    appearance: {
                                                        brand_color: formData.brand_color,
                                                        button_color: formData.button_color,
                                                        container_width: formData.container_width,
                                                        border_radius: formData.border_radius,
                                                        campaign_layout: formData.campaign_layout,
                                                        font_family: formData.font_family,
                                                        font_size: formData.font_size,
                                                        dark_mode: formData.dark_mode,
                                                        donation_layout: formData.donation_layout,
                                                    },
                                                    bank: {
                                                        bank_name: formData.bank_name,
                                                        account_number: formData.account_number,
                                                        account_name: formData.account_name,
                                                    },
                                                    organization: {
                                                        org_name: formData.org_name,
                                                        org_address: formData.org_address,
                                                        org_phone: formData.org_phone,
                                                        org_email: formData.org_email,
                                                    },
                                                    notifications: {
                                                        opt_in_email: formData.opt_in_email,
                                                        opt_in_whatsapp: formData.opt_in_whatsapp,
                                                    },
                                                    exported_at: new Date().toISOString(),
                                                    version: '1.0'
                                                };
                                                const blob = new Blob([JSON.stringify(settings, null, 2)], { type: 'application/json' });
                                                const url = URL.createObjectURL(blob);
                                                const a = document.createElement('a');
                                                a.href = url;
                                                a.download = `donasai-settings-${new Date().toISOString().split('T')[0]}.json`;
                                                a.click();
                                                URL.revokeObjectURL(url);
                                                toast.success('Ekspor berhasil!', {
                                                    description: 'File pengaturan telah diunduh.'
                                                });
                                            }}
                                            className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium text-sm"
                                        >
                                            📥 Ekspor Pengaturan
                                        </button>
                                    </div>

                                    {/* Import Settings */}
                                    <div className="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                        <h4 className="font-medium text-gray-800 mb-2">Impor Pengaturan</h4>
                                        <p className="text-sm text-gray-600 mb-3">Muat pengaturan dari file JSON yang telah diekspor sebelumnya.</p>
                                        <input
                                            type="file"
                                            accept=".json"
                                            onChange={(e) => {
                                                const file = e.target.files?.[0];
                                                if (!file) return;
                                                const reader = new FileReader();
                                                reader.onload = (event) => {
                                                    try {
                                                        const imported = JSON.parse(event.target?.result as string);
                                                        setFormData(prev => ({
                                                            ...prev,
                                                            campaign_slug: imported.general?.campaign_slug || prev.campaign_slug,
                                                            payment_slug: imported.general?.payment_slug || prev.payment_slug,
                                                            remove_branding: imported.general?.remove_branding ?? prev.remove_branding,
                                                            confirmation_page: imported.general?.confirmation_page || prev.confirmation_page,
                                                            min_amount: imported.donation?.min_amount || prev.min_amount,
                                                            presets: imported.donation?.presets || prev.presets,
                                                            anonymous_label: imported.donation?.anonymous_label || prev.anonymous_label,
                                                            create_user: imported.donation?.create_user ?? prev.create_user,
                                                            brand_color: imported.appearance?.brand_color || prev.brand_color,
                                                            button_color: imported.appearance?.button_color || prev.button_color,
                                                            container_width: imported.appearance?.container_width || prev.container_width,
                                                            border_radius: imported.appearance?.border_radius || prev.border_radius,
                                                            campaign_layout: imported.appearance?.campaign_layout || prev.campaign_layout,
                                                            font_family: imported.appearance?.font_family || prev.font_family,
                                                            font_size: imported.appearance?.font_size || prev.font_size,
                                                            dark_mode: imported.appearance?.dark_mode ?? prev.dark_mode,
                                                            donation_layout: imported.appearance?.donation_layout || prev.donation_layout,
                                                            bank_name: imported.bank?.bank_name || prev.bank_name,
                                                            account_number: imported.bank?.account_number || prev.account_number,
                                                            account_name: imported.bank?.account_name || prev.account_name,
                                                            org_name: imported.organization?.org_name || prev.org_name,
                                                            org_address: imported.organization?.org_address || prev.org_address,
                                                            org_phone: imported.organization?.org_phone || prev.org_phone,
                                                            org_email: imported.organization?.org_email || prev.org_email,
                                                            opt_in_email: imported.notifications?.opt_in_email || prev.opt_in_email,
                                                            opt_in_whatsapp: imported.notifications?.opt_in_whatsapp || prev.opt_in_whatsapp,
                                                        }));
                                                        toast.success('Pengaturan berhasil diimpor!', {
                                                            description: 'Klik "Simpan Perubahan" untuk menyimpan ke database.'
                                                        });
                                                    } catch {
                                                        toast.error('Gagal membaca file', {
                                                            description: 'Pastikan file JSON valid dan sesuai format.'
                                                        });
                                                    }
                                                };
                                                reader.readAsText(file);
                                            }}
                                            className="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                        />
                                    </div>
                                </div>

                                {/* Danger Zone */}
                                <div className="border-t border-gray-200 pt-6">
                                    <h3 className="text-lg font-bold text-red-700 mb-2">
                                        Zona Bahaya: Pengaturan Uninstall
                                    </h3>
                                    <div className="space-y-3 bg-red-50 p-4 rounded-lg border border-red-100">
                                        <div className="flex items-start space-x-3">
                                            <input
                                                type="checkbox"
                                                id="delete_on_uninstall_settings"
                                                checked={formData.delete_on_uninstall_settings}
                                                onChange={(e) => setFormData({ ...formData, delete_on_uninstall_settings: e.target.checked })}
                                                className="h-4 w-4 text-red-600 border-red-300 rounded focus:ring-red-500 mt-0.5"
                                            />
                                            <div>
                                                <label htmlFor="delete_on_uninstall_settings" className="text-sm font-medium text-gray-800">Hapus Semua Pengaturan</label>
                                                <p className="text-xs text-gray-600">Jika dicentang, semua opsi pengaturan plugin akan dihapus dari database ketika plugin di-uninstall.</p>
                                            </div>
                                        </div>
                                        <div className="flex items-start space-x-3">
                                            <input
                                                type="checkbox"
                                                id="delete_on_uninstall_tables"
                                                checked={formData.delete_on_uninstall_tables}
                                                onChange={(e) => setFormData({ ...formData, delete_on_uninstall_tables: e.target.checked })}
                                                className="h-4 w-4 text-red-600 border-red-300 rounded focus:ring-red-500 mt-0.5"
                                            />
                                            <div>
                                                <label htmlFor="delete_on_uninstall_tables" className="text-sm font-medium text-gray-800">Hapus Tabel Database</label>
                                                <p className="text-xs text-gray-600">Jika dicentang, tabel donasi dan kampanye akan <b>DIHAPUS PERMANEN</b> ketika plugin di-uninstall.</p>
                                            </div>
                                        </div>
                                    </div>
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
                                {mutation.isPending ? 'Menyimpan...' : 'Simpan Perubahan'}
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
                            <h2 className="text-2xl font-bold text-gray-900">Pilih paket Anda</h2>
                            <button type='button' onClick={() => setShowProModal(false)} className="p-2 hover:bg-gray-100 rounded-full">
                                <X size={24} className="text-gray-500" />
                            </button>
                        </div>
                        <div className="p-8">
                            <div className="grid md:grid-cols-2 gap-8">
                                {/* FREE */}
                                <div className="border border-gray-200 rounded-xl p-6">
                                    <h3 className="text-xl font-bold text-gray-900 mb-2">Gratis</h3>
                                    <p className="text-gray-500 mb-6">Untuk komunitas kecil yang baru memulai.</p>
                                    <ul className="space-y-3 mb-8">
                                        {['Kampanye Tak Terbatas', 'Formulir Donasi Dasar', 'Transfer Bank Manual', 'Pelaporan Dasar', '1 Gerbang Global'].map(f => (
                                            <li key={f} className="flex items-center gap-2 text-sm text-gray-700">
                                                <Check size={16} className="text-green-500" /> {f}
                                            </li>
                                        ))}
                                    </ul>
                                    <button type='button' className="w-full py-2 bg-gray-100 text-gray-700 font-semibold rounded-lg cursor-default">Paket Saat Ini</button>
                                </div>

                                {/* PRO */}
                                <div className="border-2 border-purple-600 rounded-xl p-6 relative bg-purple-50">
                                    <div className="absolute top-0 right-0 bg-purple-600 text-white text-xs font-bold px-3 py-1 rounded-bl-lg rounded-tr-lg">DIREKOMENDASIKAN</div>
                                    <h3 className="text-xl font-bold text-gray-900 mb-2">Pro</h3>
                                    <p className="text-gray-500 mb-6">Untuk organisasi & LSM yang sedang berkembang.</p>
                                    <ul className="space-y-3 mb-8">
                                        <li className="flex items-center gap-2 text-sm text-gray-900 font-medium">
                                            <Check size={16} className="text-purple-600" /> Semua Fitur Gratis
                                        </li>
                                        {[
                                            'Donasi Berulang (Langganan)',
                                            'Notifikasi WhatsApp',
                                            'Gerbang Lokal (Midtrans, Xendit, QRIS)',
                                            'Kuitansi PDF',
                                            'Registrasi Pengguna',
                                            'Hapus Branding',
                                            'Konfirmasi Pembayaran AI',
                                            'Analitik Lanjutan'
                                        ].map(f => (
                                            <li key={f} className="flex items-center gap-2 text-sm text-gray-700">
                                                <Star size={16} className="text-purple-600" fill="currentColor" /> {f}
                                            </li>
                                        ))}
                                    </ul>
                                    <button type='button' className="w-full py-2 bg-purple-600 text-white font-bold rounded-lg hover:bg-purple-700 shadow-md">
                                        Upgrade Sekarang (Segera Hadir)
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
