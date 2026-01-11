import { FileText, Info, Loader2 } from "lucide-react";
import React, { useCallback, useState } from "react";
import { CustomizationForm } from "./customization-form";
import type { ReceiptTemplate } from "./hooks/use-receipt-template";
import { useReceiptTemplate } from "./hooks/use-receipt-template";
import { ReceiptPreview } from "./receipt-preview";

export default function ReceiptTemplatePage() {
  const {
    template,
    isLoading,
    saveTemplate,
    isSaving,
    generatePreview,
    previewData,
  } = useReceiptTemplate();

  const [localTemplate, setLocalTemplate] = useState<
    ReceiptTemplate | undefined
  >(template);

  // Update local state when template loads
  React.useEffect(() => {
    if (template) {
      setLocalTemplate(template);
    }
  }, [template]);

  const handleSave = useCallback(() => {
    if (localTemplate) {
      saveTemplate(localTemplate);
    }
  }, [localTemplate, saveTemplate]);

  const handleTemplateChange = useCallback((newTemplate: ReceiptTemplate) => {
    setLocalTemplate(newTemplate);
  }, []);

  if (isLoading) {
    return (
      <div className="flex items-center justify-center min-h-screen bg-gray-50 dark:bg-gray-900">
        <div className="text-center">
          <Loader2 className="w-12 h-12 text-emerald-600 animate-spin mx-auto mb-4" />
          <p className="text-gray-500 dark:text-gray-400">
            Memuat template kuitansi...
          </p>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50 dark:bg-gray-900">
      {/* Header */}
      <div className="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-[32px] z-10 shadow-sm">
        <div className="max-w-7xl mx-auto px-6 py-6">
          <div className="flex items-center gap-3 mb-2">
            <div className="w-12 h-12 bg-emerald-100 dark:bg-emerald-900 rounded-xl flex items-center justify-center">
              <FileText className="w-6 h-6 text-emerald-600 dark:text-emerald-400" />
            </div>
            <div>
              <h1 className="text-2xl font-bold text-gray-900 dark:text-white! my-1! py-0!">
                Kustomisasi Template Kuitansi
              </h1>
              <p className="text-sm text-gray-600 dark:text-gray-400 my-0!">
                Personalisasi kuitansi donasi dengan branding organisasi Anda
              </p>
            </div>
          </div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-6 py-8">
        {/* Info Alert */}
        <div className="mb-6 bg-blue-50 dark:bg-blue-950 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
          <div className="flex gap-3">
            <Info className="w-5 h-5 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5" />
            <div className="flex-1">
              <p className="font-medium text-blue-900 dark:text-blue-100 mb-2">
                Cara Menggunakan:
              </p>
              <ul className="space-y-1.5 text-sm text-blue-800 dark:text-blue-200">
                <li className="flex items-start gap-2">
                  <span className="text-blue-500 dark:text-blue-400 mt-1">
                    •
                  </span>
                  <span>
                    Customize template di panel kanan, lihat perubahan real-time
                    di preview kiri
                  </span>
                </li>
                <li className="flex items-start gap-2">
                  <span className="text-blue-500 dark:text-blue-400 mt-1">
                    •
                  </span>
                  <span>
                    Gunakan merge tags seperti{" "}
                    <code className="bg-blue-100 dark:bg-blue-900 px-1.5 py-0.5 rounded text-xs">
                      {"{donor_name}"}
                    </code>{" "}
                    dan{" "}
                    <code className="bg-blue-100 dark:bg-blue-900 px-1.5 py-0.5 rounded text-xs">
                      {"{campaign_name}"}
                    </code>{" "}
                    di footer
                  </span>
                </li>
                <li className="flex items-start gap-2">
                  <span className="text-blue-500 dark:text-blue-400 mt-1">
                    •
                  </span>
                  <span>
                    Pilih antara link HTML atau file PDF untuk pengiriman
                    kuitansi
                  </span>
                </li>
              </ul>
            </div>
          </div>
        </div>

        {/* Main Content - Split Layout */}
        <div className="grid grid-cols-1 xl:grid-cols-2 gap-6">
          {/* Left: Preview (Sticky) */}
          <div className="xl:sticky xl:top-[180px] xl:self-start">
            <ReceiptPreview
              template={localTemplate}
              previewHtml={previewData}
              onGeneratePreview={generatePreview}
            />
          </div>

          {/* Right: Customization Form */}
          <div>
            <CustomizationForm
              template={localTemplate}
              onChange={handleTemplateChange}
              onSave={handleSave}
              isSaving={isSaving}
            />
          </div>
        </div>
      </div>
    </div>
  );
}
