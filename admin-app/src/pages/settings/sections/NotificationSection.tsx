import { Input } from "@/components/ui/Input";
import { Label } from "@/components/ui/Label";
import { Bell } from "lucide-react";
import { useSettings } from "../SettingsContext";

export default function NotificationSection() {
  const { formData, setFormData } = useSettings();

  return (
    <div className="space-y-6">
      <div>
        <h3 className="text-lg font-medium text-gray-900 mb-1">
          Langganan Pembaruan
        </h3>
        <p className="text-sm text-gray-500 mb-4">
          Terima pembaruan donatur dan pengumuman plugin.
        </p>

        <div className="grid gap-4">
          <div>
            <Label htmlFor="opt_in_email">Email untuk Pembaruan</Label>
            <Input
              id="opt_in_email"
              type="email"
              value={formData.opt_in_email}
              onChange={(e) =>
                setFormData({
                  ...formData,
                  opt_in_email: e.target.value,
                })
              }
            />
            <p className="text-xs text-gray-500 mt-1">
              Kami akan memverifikasi email ini sebelum mengirim laporan
              sensitif.
            </p>
          </div>
          <div>
            <Label htmlFor="opt_in_whatsapp">Nomor WhatsApp (Opsional)</Label>
            <Input
              id="opt_in_whatsapp"
              type="text"
              value={formData.opt_in_whatsapp}
              onChange={(e) =>
                setFormData({
                  ...formData,
                  opt_in_whatsapp: e.target.value,
                })
              }
              placeholder="Contoh: 62812..."
            />
          </div>
        </div>
      </div>

      <div className="border-t border-gray-200 pt-6">
        <h3 className="text-lg font-medium text-gray-900 mb-2 flex items-center gap-2">
          <Bell size={18} className="text-gray-400" />
          Notifikasi Lanjutan{" "}
          <span className="bg-purple-100 text-purple-700 text-xs px-2 py-0.5 rounded font-bold">
            PRO
          </span>
        </h3>
        <p className="text-sm text-gray-500 mb-4">Tersedia di versi Pro:</p>
        <ul className="list-disc pl-5 text-sm text-gray-600 space-y-1">
          <li>Ringkasan harian via Email.</li>
          <li>Peringatan pembayaran gagal.</li>
        </ul>
      </div>
    </div>
  );
}
