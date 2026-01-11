import clsx from "clsx";
import {
  Bell,
  Building,
  Check,
  CreditCard,
  Crown,
  FileText,
  Heart,
  Lock,
  Mail,
  Palette,
  Star,
} from "lucide-react";
import { useEffect, useState } from "react";
import { useSettings } from "./SettingsContext";
import AdvancedSection from "./sections/AdvancedSection";
import AppearanceSection from "./sections/AppearanceSection";
import DonationSection from "./sections/DonationSection";
// Section Components
import GeneralSection from "./sections/GeneralSection";
import LicenseSection from "./sections/LicenseSection";
import NotificationSection from "./sections/NotificationSection";
import PaymentSection from "./sections/PaymentSection";

export default function SettingsLayout() {
  const {
    isProInstalled,
    licenseStatus,
    proSettings,
    isLoading,
    saveSettings,
    setShowProModal,
    isSaving,
  } = useSettings();

  // Get initial tab from URL
  const searchParams = new URLSearchParams(window.location.search);
  const initialTab = searchParams.get("tab") || "general";
  const [activeTab, setActiveTab] = useState(initialTab);

  // Sync activeTab with URL
  // biome-ignore lint/correctness/useExhaustiveDependencies: <explanation>
  useEffect(() => {
    const params = new URLSearchParams(window.location.search);
    const currentTab = params.get("tab");
    if (currentTab && currentTab !== activeTab) {
      setActiveTab(currentTab);
    }
  }, []);

  const handleTabChange = (tabId: string) => {
    if (tabId === "receipt-template") {
      window.location.hash = "#/receipt-template";
      return;
    }
    if (tabId === "email-template") {
      window.location.hash = "#/email-template";
      return;
    }

    const params = new URLSearchParams(window.location.search);
    params.set("tab", tabId);
    window.history.pushState(
      {},
      "",
      `${window.location.pathname}?${params.toString()}`,
    );
    setActiveTab(tabId);
  };

  const tabs = [
    { id: "general", label: "General & Org", icon: Building },
    { id: "donation", label: "Donation Settings", icon: Heart },
    { id: "payment", label: "Payment", icon: CreditCard },
    { id: "notifications", label: "Notifications", icon: Bell },
    { id: "appearance", label: "Appearance", icon: Palette },
    { id: "advanced", label: "Advanced", icon: Star },
    { id: "receipt-template", label: "Receipt Template", icon: FileText },
    { id: "email-template", label: "Email Template", icon: Mail },
    { id: "license", label: "License", icon: Lock },
  ].filter(
    (tab) =>
      (tab.id !== "license" && tab.id !== "receipt-template" && tab.id !== "email-template") || isProInstalled,
  );

  const showActivationLock = isProInstalled && licenseStatus !== "active";

  if (isLoading) {
    return (
      <div className="flex items-center justify-center min-h-[400px]">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-emerald-600" />
      </div>
    );
  }

  return (
    <div className="space-y-6 relative">
      {/* Activation Lock Modal - Reusing the component if available or custom logic */}
      {/* The original code had a custom inline modal. We can extract it or keep it. 
                For now, I'll use the ActivationLock component if it matches, 
                but looking at the original code, it had a CUSTOM modal implementation inside Settings.tsx.
                I will stick to the original implementation for now to ensure exact parity, 
                or if ActivationLock component is the same, use it. 
                Ref: line 373 in Settings.tsx. It looks like a custom modal.
            */}
      {showActivationLock && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-white/50 backdrop-blur-xl animate-in fade-in duration-500">
          <div className="bg-white rounded-2xl shadow-2xl border border-gray-100 max-w-md w-full overflow-hidden p-8 text-center ring-1 ring-black/5 relative">
            {/* Decorative Background Blob */}
            <div className="absolute top-0 left-1/2 -translate-x-1/2 w-full h-32 bg-linear-to-b from-blue-50 to-transparent -z-10"></div>

            <div className="w-20 h-20 bg-blue-600 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-blue-200 shadow-xl rotate-3 transition-transform hover:rotate-6">
              <Lock className="text-white" size={40} strokeWidth={2.5} />
            </div>

            <h2 className="text-2xl font-bold text-gray-900 mb-2 tracking-tight">
              Aktivasi Donasai Pro
            </h2>

            <p className="text-gray-500 mb-8 leading-relaxed text-[15px]">
              Plugin <strong>Donasai Pro</strong> telah terpasang. Harap
              hubungkan lisensi Anda untuk membuka semua fitur premium.
            </p>

            <div className="space-y-3">
              {proSettings.connectUrl ? (
                <a
                  href={proSettings.connectUrl}
                  className="block w-full py-3.5 px-6 bg-blue-600 hover:bg-blue-700 text-white! font-bold rounded-xl transition-all shadow-lg shadow-blue-600/20 hover:shadow-blue-600/30 active:scale-95 focus:text-blue-100!"
                >
                  Hubungkan & Aktivasi
                </a>
              ) : (
                <div className="p-4 bg-red-50 text-red-600 rounded-xl text-sm border border-red-100 mb-4">
                  <p className="font-semibold">Error Konfigurasi</p>
                  <p>URL Aktivasi tidak ditemukan.</p>
                </div>
              )}

              <a
                href="/wp-admin/plugins.php"
                className="block w-full py-3.5 px-6 bg-white hover:bg-gray-50 text-gray-600 font-medium rounded-xl border border-gray-200 transition-colors"
              >
                Nonaktifkan Plugin
              </a>
            </div>

            <p className="mt-8 text-xs text-gray-400">
              Masalah dengan lisensi?{" "}
              <a
                href="https://donasai.com/contact"
                className="underline hover:text-gray-600"
              >
                Hubungi Dukungan
              </a>
            </p>
          </div>
        </div>
      )}

      <div className="flex justify-between items-center">
        <h2 className="text-2xl font-bold text-gray-800">Pengaturan</h2>
      </div>

      {/* Pro Banner */}
      {licenseStatus !== "active" ? (
        <div className="relative overflow-hidden bg-linear-to-br from-emerald-600 via-emerald-700 to-emerald-800 rounded-2xl p-8 text-white shadow-2xl border border-emerald-500">
          {/* Decorative Elements */}
          <div className="absolute top-0 right-0 w-64 h-64 bg-emerald-500 rounded-full opacity-10 blur-3xl -translate-y-1/2 translate-x-1/3"></div>
          <div className="absolute bottom-0 left-0 w-48 h-48 bg-white rounded-full opacity-5 blur-2xl translate-y-1/2 -translate-x-1/3"></div>

          <div className="relative flex justify-between items-center gap-6">
            <div className="flex-1">
              <div className="inline-flex items-center gap-2 bg-emerald-500/30 backdrop-blur-sm px-3 py-1.5 rounded-full mb-3 border border-emerald-400/30">
                <Crown className="text-yellow-300 w-4 h-4" />
                <span className="text-xs font-semibold tracking-wide uppercase">
                  Premium Features
                </span>
              </div>
              <h3 className="text-2xl! font-bold mb-2! mt-0! tracking-tight text-white!">
                Upgrade ke Donasai Pro
              </h3>
              <p className="text-emerald-50 text-sm leading-relaxed max-w-md">
                Buka Donasi Berulang, Notifikasi WhatsApp, dan Konfirmasi AI
                dengan teknologi terdepan.
              </p>
            </div>
            <button
              type="button"
              onClick={() => setShowProModal(true)}
              className="group relative bg-white text-emerald-600 px-6 py-3.5 rounded-xl font-bold hover:bg-emerald-50 transition-all shadow-lg hover:shadow-xl hover:scale-105 active:scale-95"
            >
              <span className="relative z-10">Bandingkan Fitur</span>
              <div className="absolute inset-0 bg-linear-to-r from-emerald-50 to-white opacity-0 group-hover:opacity-100 transition-opacity rounded-xl"></div>
            </button>
          </div>
        </div>
      ) : (
        <div className="bg-green-100 border border-green-200 rounded-xl p-4 text-green-800 flex items-center gap-3">
          <div className="p-2 bg-green-200 rounded-full">
            <Check size={20} />
          </div>
          <div>
            <h3 className="font-bold">Donasai Pro Aktif</h3>
            <p className="text-sm opacity-90">
              Terima kasih telah mendukung pengembangan plugin ini!
            </p>
          </div>
        </div>
      )}

      <div className="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden min-h-[500px] flex">
        {/* Sidebar Tabs */}
        <div className="w-64 bg-gray-50 border-r border-gray-200 p-4 space-y-2">
          {tabs.map((tab) => {
            const Icon = tab.icon;
            return (
              <button
                type="button"
                key={tab.id}
                onClick={() => handleTabChange(tab.id)}
                className={clsx(
                  "w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-colors text-left",
                  activeTab === tab.id
                    ? "bg-white text-blue-600 shadow-sm border border-gray-200"
                    : "text-gray-600 hover:bg-gray-100 hover:text-gray-900",
                )}
              >
                <Icon size={18} />
                <span>
                  {tab.label === "General & Org"
                    ? "Umum & Organisasi"
                    : tab.label === "Donation Settings"
                      ? "Pengaturan Donasi"
                      : tab.label === "Payment"
                        ? "Pembayaran"
                        : tab.label === "Notifications"
                          ? "Notifikasi"
                          : tab.label === "Appearance"
                            ? "Tampilan"
                            : tab.label === "License"
                              ? "Lisensi"
                              : tab.label === "Receipt Template"
                                ? "Template Kuitansi"
                                : tab.label === "Email Template"
                                  ? "Template Email"
                                  : tab.label}
                </span>
              </button>
            );
          })}
        </div>

        {/* Content Area */}
        <div className="flex-1 p-8">
          <form onSubmit={saveSettings} className="max-w-2xl space-y-6">
            {activeTab === "general" && <GeneralSection />}
            {activeTab === "donation" && <DonationSection />}
            {activeTab === "payment" && <PaymentSection />}
            {activeTab === "notifications" && <NotificationSection />}
            {activeTab === "appearance" && <AppearanceSection />}
            {activeTab === "advanced" && <AdvancedSection />}
            {activeTab === "license" && <LicenseSection />}

            <div className="flex justify-end pt-6 border-t border-gray-100">
              <button
                type="submit"
                disabled={isLoading || isSaving}
                className="bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-6 py-2.5 rounded-lg shadow-sm hover:shadow-md transition-all flex items-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed"
              >
                {isSaving ? (
                  <>
                    <div className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                    <span>Menyimpan...</span>
                  </>
                ) : (
                  <>
                    <Check size={18} />
                    <span>Simpan Perubahan</span>
                  </>
                )}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
}
