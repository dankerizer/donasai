import clsx from "clsx";
import { Lock } from "lucide-react";
import { Input } from "/src/components/ui/Input";
import { Label } from "/src/components/ui/Label";
import { Select } from "/src/components/ui/Select";
import { useSettings } from "../SettingsContext";

export default function AppearanceSection() {
  const { formData, setFormData, licenseStatus, setShowProModal } =
    useSettings();

  const isProActive = ["active", "pro"].includes(licenseStatus);

  return (
    <div className="space-y-8">
      <div>
        <h3 className="text-lg font-medium text-gray-900 mb-4">
          Tampilan & Layout
        </h3>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <Label htmlFor="brand_color" className="mb-2">
              Warna Merek Utama
            </Label>
            <div className="flex items-center gap-3">
              <Input
                type="color"
                id="brand_color"
                value={formData.brand_color || "#059669"}
                onChange={(e) =>
                  setFormData({
                    ...formData,
                    brand_color: e.target.value,
                  })
                }
                className="h-10 w-20 p-1 cursor-pointer"
              />
              <span className="text-sm text-gray-500 font-mono uppercase">
                {formData.brand_color}
              </span>
            </div>
            <p className="text-xs text-gray-500 mt-2">
              Digunakan untuk lencana, jumlah, dan bilah kemajuan.
            </p>
          </div>
          <div>
            <Label htmlFor="button_color" className="mb-2">
              Warna Tombol
            </Label>
            <div className="flex items-center gap-3">
              <Input
                type="color"
                id="button_color"
                value={formData.button_color || "#ec4899"}
                onChange={(e) =>
                  setFormData({
                    ...formData,
                    button_color: e.target.value,
                  })
                }
                className="h-10 w-20 p-1 cursor-pointer"
              />
              <span className="text-sm text-gray-500 font-mono uppercase">
                {formData.button_color}
              </span>
            </div>
            <p className="text-xs text-gray-500 mt-2">
              Tombol CTA utama (Donasi, Kirim).
            </p>
          </div>
          <div>
            <Label htmlFor="container_width">Lebar Kontainer</Label>
            <Input
              type="text"
              id="container_width"
              value={formData.container_width || "1100px"}
              onChange={(e) =>
                setFormData({
                  ...formData,
                  container_width: e.target.value,
                })
              }
              placeholder="1100px"
            />
            <p className="text-xs text-gray-500 mt-2">
              Lebar maksimal halaman campaign.
            </p>
          </div>
          <div>
            <Label htmlFor="border_radius">Border Radius</Label>
            <Input
              type="text"
              id="border_radius"
              value={formData.border_radius || "12px"}
              onChange={(e) =>
                setFormData({
                  ...formData,
                  border_radius: e.target.value,
                })
              }
              placeholder="12px"
            />
            <p className="text-xs text-gray-500 mt-2">
              Kelengkungan sudut (card, tombol, input).
            </p>
          </div>
          <div className="md:col-span-2">
            <label
              htmlFor="campaign_layout"
              className="block text-sm font-medium text-gray-700 mb-3"
            >
              Layout Halaman Campaign
            </label>
            <div className="grid grid-cols-3 gap-4">
              {/* Sidebar Right */}
              <button
                type="button"
                id="campaign_layout"
                className={clsx(
                  "w-full text-left border-2 rounded-xl p-3 cursor-pointer transition-all hover:border-blue-300",
                  formData.campaign_layout === "sidebar-right"
                    ? "border-blue-600 bg-blue-50 ring-1 ring-blue-600"
                    : "border-gray-200 bg-white",
                )}
                onClick={() =>
                  setFormData({
                    ...formData,
                    campaign_layout: "sidebar-right",
                  })
                }
              >
                <div className="aspect-video bg-gray-100 rounded mb-2 flex gap-1 p-1">
                  <div className="bg-gray-300 h-full w-2/3 rounded-sm"></div>
                  <div className="bg-blue-200 h-full w-1/3 rounded-sm"></div>
                </div>
                <div className="text-xs font-medium text-center text-gray-700">
                  Sidebar Kanan
                </div>
              </button>

              {/* Sidebar Left */}
              <button
                type="button"
                className={clsx(
                  "w-full text-left border-2 rounded-xl p-3 cursor-pointer transition-all hover:border-blue-300",
                  formData.campaign_layout === "sidebar-left"
                    ? "border-blue-600 bg-blue-50 ring-1 ring-blue-600"
                    : "border-gray-200 bg-white",
                )}
                onClick={() =>
                  setFormData({
                    ...formData,
                    campaign_layout: "sidebar-left",
                  })
                }
              >
                <div className="aspect-video bg-gray-100 rounded mb-2 flex gap-1 p-1">
                  <div className="bg-blue-200 h-full w-1/3 rounded-sm"></div>
                  <div className="bg-gray-300 h-full w-2/3 rounded-sm"></div>
                </div>
                <div className="text-xs font-medium text-center text-gray-700">
                  Sidebar Kiri
                </div>
              </button>

              {/* Full Width */}
              <button
                type="button"
                className={clsx(
                  "w-full text-left border-2 rounded-xl p-3 cursor-pointer transition-all hover:border-blue-300",
                  formData.campaign_layout === "full-width"
                    ? "border-blue-600 bg-blue-50 ring-1 ring-blue-600"
                    : "border-gray-200 bg-white",
                )}
                onClick={() =>
                  setFormData({
                    ...formData,
                    campaign_layout: "full-width",
                  })
                }
              >
                <div className="aspect-video bg-gray-100 rounded mb-2 w-full p-1">
                  <div className="bg-gray-300 h-full w-full rounded-sm"></div>
                </div>
                <div className="text-xs font-medium text-center text-gray-700">
                  Full Width
                </div>
              </button>
            </div>
          </div>
        </div>

        {/* Donor Limits */}
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 pt-6 border-t border-gray-200">
          <div>
            <Label htmlFor="sidebar_count">Jumlah Donatur di Sidebar</Label>
            <Input
              type="number"
              id="sidebar_count"
              value={formData.sidebar_count}
              onChange={(e) =>
                setFormData({
                  ...formData,
                  sidebar_count: parseInt(e.target.value) || 5,
                })
              }
              min={1}
              max={20}
              className="mt-1"
            />
            <p className="text-xs text-gray-500 mt-2">
              Jumlah donatur terakhir yang ditampilkan di sidebar.
            </p>
          </div>
          <div>
            <Label htmlFor="donor_per_page">Jumlah Donatur per Halaman</Label>
            <Input
              type="number"
              id="donor_per_page"
              value={formData.donor_per_page}
              onChange={(e) =>
                setFormData({
                  ...formData,
                  donor_per_page: parseInt(e.target.value) || 10,
                })
              }
              min={1}
              max={50}
              className="mt-1"
            />
            <p className="text-xs text-gray-500 mt-2">
              Jumlah donatur yang dimuat per klik "Muat Lebih Banyak".
            </p>
          </div>
        </div>
      </div>

      {/* Pro Teasers */}
      <div className="border-t border-gray-200 pt-6">
        <h3 className="text-lg font-medium text-gray-900 mb-4 flex items-center gap-2">
          Gaya Lanjutan{" "}
          <span className="bg-purple-100 text-purple-700 text-xs px-2 py-0.5 rounded font-bold">
            PRO
          </span>
        </h3>
        <div className="grid gap-4 md:grid-cols-2">
          {/* Typography */}
          <div
            role="button"
            tabIndex={0}
            className={clsx(
              "border border-gray-200 rounded-lg p-4 bg-gray-50 relative",
              !isProActive ? "opacity-60 cursor-pointer" : "",
            )}
            onClick={() => !isProActive && setShowProModal(true)}
          >
            <div className="flex justify-between items-start mb-2">
              <div className="font-medium text-gray-900">Tipografi</div>
              {!isProActive ? (
                <Lock size={14} className="text-gray-400" />
              ) : (
                <span className="text-xs font-bold text-green-600 bg-green-100 px-2 rounded">
                  Active
                </span>
              )}
            </div>

            {isProActive ? (
              <div className="space-y-3 mt-3">
                <div>
                  <Label htmlFor="font_family" className="text-xs mb-1">
                    Font Utama
                  </Label>
                  <Select
                    id="font_family"
                    value={formData.font_family || "Inter"}
                    onChange={(e) =>
                      setFormData({
                        ...formData,
                        font_family: e.target.value,
                      })
                    }
                    className="text-sm"
                  >
                    <option value="Inter">Inter (Default)</option>
                    <option value="Roboto">Roboto</option>
                    <option value="Open Sans">Open Sans</option>
                    <option value="Poppins">Poppins</option>
                    <option value="Lato">Lato</option>
                  </Select>
                </div>
                <div>
                  <Label htmlFor="font_size" className="text-xs mb-1">
                    Ukuran Font Dasar
                  </Label>
                  <div className="flex items-center gap-2">
                    <Input
                      type="text"
                      id="font_size"
                      value={formData.font_size || "16px"}
                      onChange={(e) =>
                        setFormData({
                          ...formData,
                          font_size: e.target.value,
                        })
                      }
                      className="w-20 text-sm p-2"
                    />
                    <span className="text-xs text-gray-500">px/rem</span>
                  </div>
                </div>
              </div>
            ) : (
              <p className="text-sm text-gray-500">
                Font Google kustom dan kontrol ukuran.
              </p>
            )}
          </div>

          {/* Dark Mode */}
          <div
            className={clsx(
              "border border-gray-200 rounded-lg p-4 bg-gray-50 relative",
              !isProActive ? "opacity-60 cursor-pointer" : "",
            )}
            onClick={() => !isProActive && setShowProModal(true)}
          >
            <div className="flex justify-between items-start mb-2">
              <div className="font-medium text-gray-900">Mode Gelap</div>
              {!isProActive ? (
                <Lock size={14} className="text-gray-400" />
              ) : (
                <span className="text-xs font-bold text-green-600 bg-green-100 px-2 rounded">
                  Active
                </span>
              )}
            </div>

            {isProActive ? (
              <div className="mt-3">
                <div className="flex items-center gap-3">
                  <label className="relative inline-flex items-center cursor-pointer">
                    <input
                      type="checkbox"
                      className="sr-only peer"
                      checked={formData.dark_mode}
                      onChange={(e) =>
                        setFormData({
                          ...formData,
                          dark_mode: e.target.checked,
                        })
                      }
                    />
                    <div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                  </label>
                  <span className="text-sm text-gray-600">
                    {formData.dark_mode ? "Aktif" : "Nonaktif"}
                  </span>
                </div>
                <p className="text-xs text-gray-500 mt-2">
                  Otomatis menyesuaikan warna background dan teks.
                </p>
              </div>
            ) : (
              <p className="text-sm text-gray-500">
                Aktifkan dukungan mode gelap di seluruh situs.
              </p>
            )}
          </div>

          {/* Donation Form Layout (Pro Only) */}
          <div
            className={clsx(
              "border border-gray-200 rounded-lg p-4 bg-gray-50 relative md:col-span-2",
              !isProActive ? "opacity-60 cursor-pointer" : "",
            )}
            onClick={() => !isProActive && setShowProModal(true)}
          >
            <div className="flex justify-between items-start mb-4">
              <div className="font-medium text-gray-900">
                Layout Formulir Donasi
              </div>
              {!isProActive ? (
                <Lock size={14} className="text-gray-400" />
              ) : (
                <span className="text-xs font-bold text-green-600 bg-green-100 px-2 rounded">
                  Active
                </span>
              )}
            </div>

            {isProActive ? (
              <div className="grid grid-cols-2 gap-4">
                {/* Default */}
                <button
                  type="button"
                  className={clsx(
                    "w-full text-left border-2 rounded-xl p-3 cursor-pointer transition-all hover:border-blue-300",
                    formData.donation_layout === "default"
                      ? "border-blue-600 bg-blue-50 ring-1 ring-blue-600"
                      : "border-gray-200 bg-white",
                  )}
                  onClick={() =>
                    setFormData({
                      ...formData,
                      donation_layout: "default",
                    })
                  }
                >
                  <div className="aspect-video bg-gray-100 rounded mb-2 flex flex-col items-center justify-center p-2">
                    <div className="bg-white w-2/3 h-full rounded shadow-sm border border-gray-200"></div>
                  </div>
                  <div className="text-xs font-medium text-center text-gray-700">
                    Tunggal (Default)
                  </div>
                </button>

                {/* Split */}
                <button
                  type="button"
                  className={clsx(
                    "w-full text-left border-2 rounded-xl p-3 cursor-pointer transition-all hover:border-blue-300",
                    formData.donation_layout === "split"
                      ? "border-blue-600 bg-blue-50 ring-1 ring-blue-600"
                      : "border-gray-200 bg-white",
                  )}
                  onClick={() =>
                    setFormData({
                      ...formData,
                      donation_layout: "split",
                    })
                  }
                >
                  <div className="aspect-video bg-gray-100 rounded mb-2 flex gap-1 p-1">
                    <div className="bg-blue-100 h-full w-1/2 rounded-sm border border-blue-200"></div>
                    <div className="bg-white h-full w-1/2 rounded-sm border border-gray-200"></div>
                  </div>
                  <div className="text-xs font-medium text-center text-gray-700">
                    Split (Kiri Info, Kanan Form)
                  </div>
                </button>
              </div>
            ) : (
              <p className="text-sm text-gray-500">
                Pilihan tata letak untuk formulir donasi.
              </p>
            )}
          </div>

          {/* Hero Style (Pro Only) */}
          <div
            className={clsx(
              "border border-gray-200 rounded-lg p-4 bg-gray-50 relative md:col-span-2",
              !isProActive ? "opacity-60 cursor-pointer" : "",
            )}
            onClick={() => !isProActive && setShowProModal(true)}
          >
            <div className="flex justify-between items-start mb-4">
              <div className="font-medium text-gray-900">Gaya Hero Section</div>
              {!isProActive ? (
                <Lock size={14} className="text-gray-400" />
              ) : (
                <span className="text-xs font-bold text-green-600 bg-green-100 px-2 rounded">
                  Active
                </span>
              )}
            </div>

            {isProActive ? (
              <div className="grid grid-cols-3 gap-4">
                {/* Standard */}
                <button
                  type="button"
                  className={clsx(
                    "w-full text-left border-2 rounded-xl p-3 cursor-pointer transition-all hover:border-blue-300",
                    formData.hero_style === "standard"
                      ? "border-blue-600 bg-blue-50 ring-1 ring-blue-600"
                      : "border-gray-200 bg-white",
                  )}
                  onClick={() =>
                    setFormData({
                      ...formData,
                      hero_style: "standard",
                    })
                  }
                >
                  <div className="aspect-video bg-gray-100 rounded mb-2 flex flex-col items-center justify-center p-2 gap-1">
                    <div className="bg-gray-300 w-full h-1/2 rounded-sm"></div>
                    <div className="bg-gray-400 w-3/4 h-2 rounded-sm"></div>
                  </div>
                  <div className="text-xs font-medium text-center text-gray-700">
                    Standard
                  </div>
                </button>

                {/* Wide */}
                <button
                  type="button"
                  className={clsx(
                    "w-full text-left border-2 rounded-xl p-3 cursor-pointer transition-all hover:border-blue-300",
                    formData.hero_style === "wide"
                      ? "border-blue-600 bg-blue-50 ring-1 ring-blue-600"
                      : "border-gray-200 bg-white",
                  )}
                  onClick={() =>
                    setFormData({
                      ...formData,
                      hero_style: "wide",
                    })
                  }
                >
                  <div className="aspect-video bg-gray-100 rounded mb-2 flex flex-col gap-1 p-1">
                    <div className="bg-gray-300 w-full h-2/3 rounded-sm"></div>
                    <div className="bg-gray-400 w-1/2 h-2 rounded-sm ml-1"></div>
                  </div>
                  <div className="text-xs font-medium text-center text-gray-700">
                    Wide (Lebar Penuh)
                  </div>
                </button>

                {/* Overlay */}
                <button
                  type="button"
                  className={clsx(
                    "w-full text-left border-2 rounded-xl p-3 cursor-pointer transition-all hover:border-blue-300",
                    formData.hero_style === "overlay"
                      ? "border-blue-600 bg-blue-50 ring-1 ring-blue-600"
                      : "border-gray-200 bg-white",
                  )}
                  onClick={() =>
                    setFormData({
                      ...formData,
                      hero_style: "overlay",
                    })
                  }
                >
                  <div className="aspect-video bg-gray-300 rounded mb-2 flex items-center justify-center relative overflow-hidden">
                    <div className="absolute inset-0 bg-black/30"></div>
                    <div className="relative bg-white w-2/3 h-2 rounded-sm"></div>
                  </div>
                  <div className="text-xs font-medium text-center text-gray-700">
                    Overlay (Teks diatas Gambar)
                  </div>
                </button>
              </div>
            ) : (
              <p className="text-sm text-gray-500">
                Pilihan gaya tampilan gambar utama (cover) campaign.
              </p>
            )}
          </div>

          {/* Feature Controls (Pro Only) */}
          <div
            className={clsx(
              "border border-gray-200 rounded-lg p-4 bg-gray-50 relative md:col-span-2",
              !isProActive ? "opacity-60 cursor-pointer" : "",
            )}
            onClick={() => !isProActive && setShowProModal(true)}
          >
            <div className="flex justify-between items-start mb-4">
              <div className="font-medium text-gray-900">
                Kontrol Fitur Halaman
              </div>
              {!isProActive ? (
                <Lock size={14} className="text-gray-400" />
              ) : (
                <span className="text-xs font-bold text-green-600 bg-green-100 px-2 rounded">
                  Active
                </span>
              )}
            </div>

            {isProActive ? (
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div className="flex items-center gap-3">
                  <label className="relative inline-flex items-center cursor-pointer">
                    <input
                      type="checkbox"
                      className="sr-only peer"
                      checked={formData.show_countdown}
                      onChange={(e) =>
                        setFormData({
                          ...formData,
                          show_countdown: e.target.checked,
                        })
                      }
                    />
                    <div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                  </label>
                  <span className="text-sm text-gray-700">
                    Tampilkan Countdown Timer
                  </span>
                </div>

                <div className="flex items-center gap-3">
                  <label className="relative inline-flex items-center cursor-pointer">
                    <input
                      type="checkbox"
                      className="sr-only peer"
                      checked={formData.show_prayer_tab}
                      onChange={(e) =>
                        setFormData({
                          ...formData,
                          show_prayer_tab: e.target.checked,
                        })
                      }
                    />
                    <div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                  </label>
                  <span className="text-sm text-gray-700">
                    Tampilkan Tab Doa
                  </span>
                </div>

                <div className="flex items-center gap-3">
                  <label className="relative inline-flex items-center cursor-pointer">
                    <input
                      type="checkbox"
                      className="sr-only peer"
                      checked={formData.show_updates_tab}
                      onChange={(e) =>
                        setFormData({
                          ...formData,
                          show_updates_tab: e.target.checked,
                        })
                      }
                    />
                    <div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                  </label>
                  <span className="text-sm text-gray-700">
                    Tampilkan Kabar Terbaru
                  </span>
                </div>

                <div className="flex items-center gap-3">
                  <label className="relative inline-flex items-center cursor-pointer">
                    <input
                      type="checkbox"
                      className="sr-only peer"
                      checked={formData.show_donor_list}
                      onChange={(e) =>
                        setFormData({
                          ...formData,
                          show_donor_list: e.target.checked,
                        })
                      }
                    />
                    <div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-600"></div>
                  </label>
                  <span className="text-sm text-gray-700">
                    Tampilkan List Donatur Sidebar
                  </span>
                </div>
              </div>
            ) : (
              <p className="text-sm text-gray-500">
                Aktifkan/nonaktifkan elemen seperti Countdown, Doa, dan List
                Donatur.
              </p>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
