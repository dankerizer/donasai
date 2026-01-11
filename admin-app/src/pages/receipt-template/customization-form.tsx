import {
  Building2,
  Code,
  FileText,
  Palette,
  RefreshCcw,
  Save,
  Settings,
} from "lucide-react";
import { useState } from "react";
import { toast } from "sonner";
// Import shared components
import { LogoUploader } from "/src/components/shared/LogoUploader";
import { OrganizationForm } from "/src/components/shared/OrganizationForm";
import { Checkbox } from "/src/components/ui/Checkbox";
import { Input } from "/src/components/ui/Input";
import { Label } from "/src/components/ui/Label";
import { Radio } from "/src/components/ui/Radio";
import {
  TabsContent,
  TabsList,
  TabsRoot,
  TabsTrigger,
} from "/src/components/ui/Tabs";
import { Textarea } from "/src/components/ui/Textarea";
import { useSettingsFetch } from "/src/pages/settings/hooks/use-settings-data";
import { FooterEditor } from "./footer-editor";
import type { ReceiptTemplate } from "./hooks/use-receipt-template";
import { SignatureUploader } from "./signature-uploader";
import { TemplateSelector } from "./template-selector";

interface CustomizationFormProps {
  template: ReceiptTemplate | undefined;
  onChange: (template: ReceiptTemplate) => void;
  onSave: () => void;
  isSaving: boolean;
}

export function CustomizationForm({
  template,
  onChange,
  onSave,
  isSaving,
}: CustomizationFormProps) {
  // Fetch global settings for Sync
  const { data: globalSettings } = useSettingsFetch();
  const [activeTab, setActiveTab] = useState("design");

  if (!template) {
    return (
      <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-8">
        <div className="animate-pulse space-y-4">
          <div className="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4"></div>
          <div className="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
        </div>
      </div>
    );
  }

  // Handlers
  const handleLogoChange = (logo: ReceiptTemplate["logo"]) =>
    onChange({ ...template, logo });
  const handleOrgChange = (organization: ReceiptTemplate["organization"]) =>
    onChange({ ...template, organization });
  const handleFooterChange = (footer: ReceiptTemplate["footer"]) =>
    onChange({ ...template, footer });
  const handleAdvancedChange = (
    field: keyof ReceiptTemplate["advanced"],
    value: any,
  ) => {
    onChange({
      ...template,
      advanced: { ...template.advanced, [field]: value },
    });
  };
  // New Handlers
  const handleDesignChange = (
    field: keyof ReceiptTemplate["design"],
    value: any,
  ) => {
    onChange({ ...template, design: { ...template.design, [field]: value } });
  };
  const handleSignatureChange = (signature: ReceiptTemplate["signature"]) =>
    onChange({ ...template, signature });
  const handleSerialChange = (
    field: keyof ReceiptTemplate["serial"],
    value: any,
  ) => {
    onChange({ ...template, serial: { ...template.serial, [field]: value } });
  };

  // Sync Function
  const handleSyncFromGeneral = () => {
    if (!globalSettings) {
      toast.error("Gagal memuat pengaturan utama");
      return;
    }
    const { formData } = globalSettings;
    handleOrgChange({
      ...template.organization,
      name: formData.org_name,
      email: formData.org_email,
      phone: formData.org_phone,
      address_line_1: formData.org_address,
      address_line_2: "",
      city: "",
      postal_code: "",
    });
    if (formData.org_logo) {
      handleLogoChange({
        url: formData.org_logo,
        attachment_id: 0,
        width: 0,
        height: 0,
      });
    }
    toast.success("Data disinkronkan dari Pengaturan Utama");
  };

  return (
    <div className="space-y-6">
      <TabsRoot value={activeTab} onValueChange={setActiveTab}>
        <div className="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
          <div className="border-b border-gray-200 dark:border-gray-700 px-6 py-2">
            <TabsList className="bg-transparent p-0 gap-6 h-12 w-auto justify-start">
              <TabsTrigger
                value="design"
                className="data-[state=active]:bg-transparent data-[state=active]:shadow-none data-[state=active]:border-b-2 data-[state=active]:border-emerald-500 rounded-none px-0 pb-2 h-full bg-transparent hover:bg-transparent text-gray-500 data-[state=active]:text-emerald-600 font-medium"
              >
                <Palette size={16} className="mr-2" /> Desain
              </TabsTrigger>
              <TabsTrigger
                value="organization"
                className="data-[state=active]:bg-transparent data-[state=active]:shadow-none data-[state=active]:border-b-2 data-[state=active]:border-emerald-500 rounded-none px-0 pb-2 h-full bg-transparent hover:bg-transparent text-gray-500 data-[state=active]:text-emerald-600 font-medium"
              >
                <Building2 size={16} className="mr-2" /> Organisasi
              </TabsTrigger>
              <TabsTrigger
                value="content"
                className="data-[state=active]:bg-transparent data-[state=active]:shadow-none data-[state=active]:border-b-2 data-[state=active]:border-emerald-500 rounded-none px-0 pb-2 h-full bg-transparent hover:bg-transparent text-gray-500 data-[state=active]:text-emerald-600 font-medium"
              >
                <FileText size={16} className="mr-2" /> Konten
              </TabsTrigger>
              <TabsTrigger
                value="settings"
                className="data-[state=active]:bg-transparent data-[state=active]:shadow-none data-[state=active]:border-b-2 data-[state=active]:border-emerald-500 rounded-none px-0 pb-2 h-full bg-transparent hover:bg-transparent text-gray-500 data-[state=active]:text-emerald-600 font-medium"
              >
                <Settings size={16} className="mr-2" /> Pengaturan
              </TabsTrigger>
            </TabsList>
          </div>

          <div className="p-6">
            {/* --- DESIGN TAB --- */}
            <TabsContent value="design" className="mt-0 space-y-8">
              <div>
                <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                  Pilih Template
                </h3>
                <TemplateSelector
                  value={template.design?.template || "modern"}
                  onChange={(val) => handleDesignChange("template", val)}
                />
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <div>
                  <h3 className="font-medium text-gray-900 dark:text-white mb-2">
                    Logo Kuitansi
                  </h3>
                  <LogoUploader
                    value={template.logo}
                    onChange={handleLogoChange}
                  />
                </div>
                <div className="space-y-4">
                  <div>
                    <Label htmlFor="header-color">Warna Aksen / Header</Label>
                    <div className="flex items-center gap-3 mt-2">
                      <input
                        type="color"
                        id="header-color"
                        value={template.advanced.header_color}
                        onChange={(e) =>
                          handleAdvancedChange("header_color", e.target.value)
                        }
                        className="w-10 h-10 rounded-lg border-2 border-gray-300 dark:border-gray-600 cursor-pointer p-0 overflow-hidden"
                      />
                      <Input
                        type="text"
                        value={template.advanced.header_color}
                        onChange={(e) =>
                          handleAdvancedChange("header_color", e.target.value)
                        }
                        className="flex-1 font-mono uppercase"
                        maxLength={7}
                      />
                    </div>
                  </div>

                  <div>
                    <Label
                      htmlFor="custom_css"
                      className="flex items-center gap-2"
                    >
                      <Code size={14} /> Custom CSS
                    </Label>
                    <p className="text-xs text-gray-500 mb-2">
                      Override style template (Advanced)
                    </p>
                    <Textarea
                      id="custom_css"
                      value={template.design?.custom_css || ""}
                      onChange={(e) =>
                        handleDesignChange("custom_css", e.target.value)
                      }
                      placeholder=".receipt-header { background: red; }"
                      className="font-mono text-xs h-32"
                    />
                  </div>
                </div>
              </div>
            </TabsContent>

            {/* --- ORGANIZATION TAB --- */}
            <TabsContent value="organization" className="mt-0 space-y-6">
              <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                  <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                    Data Organisasi
                  </h3>
                  <p className="text-sm text-gray-500">
                    Akan muncul di kop kuitansi
                  </p>
                </div>
                <button
                  type="button"
                  onClick={handleSyncFromGeneral}
                  className="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-emerald-700 bg-emerald-50 hover:bg-emerald-100 rounded-lg border border-emerald-200 transition-colors"
                >
                  <RefreshCcw size={16} />
                  Ambil dari Pengaturan Utama
                </button>
              </div>

              <OrganizationForm
                data={template.organization}
                onChange={handleOrgChange}
                mode="detailed"
                showLogo={false}
              />

              <div className="pt-6 border-t border-gray-200 dark:border-gray-700">
                <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                  Tanda Tangan Digital
                </h3>
                <SignatureUploader
                  value={
                    template.signature || {
                      enabled: false,
                      image: { attachment_id: 0, url: "" },
                      label: "",
                    }
                  }
                  onChange={handleSignatureChange}
                />
              </div>
            </TabsContent>

            {/* --- CONTENT TAB --- */}
            <TabsContent value="content" className="mt-0 space-y-6">
              <FooterEditor
                footer={template.footer}
                onChange={handleFooterChange}
              />
            </TabsContent>

            {/* --- SETTINGS TAB --- */}
            <TabsContent value="settings" className="mt-0 space-y-6">
              <div>
                <h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                  Format & Pengiriman
                </h3>
              </div>

              {/* Receipt Format */}
              <div>
                <Label>Format Dokumen</Label>
                <div className="space-y-3 mt-3">
                  <label className="flex items-start gap-3 p-4 border border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer hover:border-emerald-300 transition-colors has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/50">
                    <Radio
                      name="format"
                      value="html"
                      checked={template.advanced.format === "html"}
                      onChange={(e) =>
                        handleAdvancedChange("format", e.target.value)
                      }
                    />
                    <div>
                      <span className="text-sm font-medium text-gray-900">
                        HTML Link (Rekomendasi)
                      </span>
                      <p className="text-xs text-gray-500 mt-1">
                        Lebih cepat, ringan, dan mudah diakses di HP.
                      </p>
                    </div>
                  </label>
                  <label className="flex items-start gap-3 p-4 border border-gray-200 dark:border-gray-700 rounded-lg cursor-pointer hover:border-emerald-300 transition-colors has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/50">
                    <Radio
                      name="format"
                      value="pdf"
                      checked={template.advanced.format === "pdf"}
                      onChange={(e) =>
                        handleAdvancedChange("format", e.target.value)
                      }
                    />
                    <div>
                      <span className="text-sm font-medium text-gray-900">
                        PDF Attachment
                      </span>
                      <p className="text-xs text-gray-500 mt-1">
                        Lampiran file PDF. Membutuhkan resource server lebih
                        besar.
                      </p>
                    </div>
                  </label>
                </div>
              </div>

              {/* Serial Number & Auto Send */}
              <div className="space-y-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div className="flex items-start gap-3 group">
                  <Checkbox
                    id="auto_send_email"
                    checked={template.advanced.auto_send_email}
                    onChange={(e) =>
                      handleAdvancedChange("auto_send_email", e.target.checked)
                    }
                    className="mt-0.5"
                  />
                  <label htmlFor="auto_send_email" className="cursor-pointer">
                    <span className="text-sm font-medium text-gray-900">
                      Kirim otomatis via email
                    </span>
                    <p className="text-xs text-gray-500">
                      Email akan dikirim segera setelah donasi berstatus
                      berhasil.
                    </p>
                  </label>
                </div>

                <div className="space-y-3">
                  <div className="flex items-start gap-3 group">
                    <Checkbox
                      id="include_serial_number"
                      checked={template.serial?.enabled ?? true}
                      onChange={(e) =>
                        handleSerialChange("enabled", e.target.checked)
                      }
                      className="mt-0.5"
                    />
                    <label
                      htmlFor="include_serial_number"
                      className="cursor-pointer"
                    >
                      <span className="text-sm font-medium text-gray-900">
                        Gunakan Nomor Seri Kuitansi
                      </span>
                      <p className="text-xs text-gray-500">
                        Generate nomor unik untuk setiap kuitansi.
                      </p>
                    </label>
                  </div>

                  {(template.serial?.enabled ?? true) && (
                    <div className="pl-7 space-y-2 animate-in slide-in-from-top-1">
                      <Label htmlFor="serial_format">Format Nomor Seri</Label>
                      <div className="flex gap-2">
                        <Input
                          id="serial_format"
                          value={
                            template.serial?.format || "INV/{Y}/{m}/{0000}"
                          }
                          onChange={(e) =>
                            handleSerialChange("format", e.target.value)
                          }
                          className="font-mono text-sm uppercase"
                          placeholder="INV/{Y}/{0000}"
                        />
                      </div>
                      <p className="text-xs text-gray-500">
                        Placeholders:{" "}
                        <code className="bg-gray-100 px-1 rounded">
                          {"{Y}"}
                        </code>{" "}
                        Tahun (4 digit),{" "}
                        <code className="bg-gray-100 px-1 rounded">
                          {"{m}"}
                        </code>{" "}
                        Bulan,{" "}
                        <code className="bg-gray-100 px-1 rounded">
                          {"{0000}"}
                        </code>{" "}
                        Nomor urut (jumlah nol menentukan digit padding).
                      </p>
                    </div>
                  )}
                </div>
              </div>
            </TabsContent>
          </div>
        </div>
      </TabsRoot>

      {/* Save Button */}
      <div className="sticky bottom-0 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-lg z-10 flex items-center justify-between">
        <p className="text-sm text-gray-600 dark:text-gray-400">
          Pastikan untuk menyimpan perubahan Anda
        </p>
        <button
          type="button"
          onClick={onSave}
          disabled={isSaving}
          className="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 shadow-sm hover:shadow-md"
        >
          {isSaving ? (
            <>
              <div className="animate-spin rounded-full h-4 w-4 border-2 border-white/30 border-t-white"></div>
              Menyimpan...
            </>
          ) : (
            <>
              <Save size={18} />
              Simpan Perubahan
            </>
          )}
        </button>
      </div>
    </div>
  );
}
