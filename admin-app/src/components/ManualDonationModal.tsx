import { Input } from "@/components/ui/Input";
import { Label } from "@/components/ui/Label";
import { Select } from "@/components/ui/Select";
import { Textarea } from "@/components/ui/Textarea";
import { useMutation, useQuery } from "@tanstack/react-query";
import { Loader2, Plus, Search, X } from "lucide-react";
import { useState } from "react";
import { toast } from "sonner";
import { InputMoney } from "./ui/InputMoney";

interface ManualDonationModalProps {
  isOpen: boolean;
  onClose: () => void;
  onSuccess?: () => void;
}

interface Donor {
  email: string;
  name: string;
  phone: string;
}

interface Campaign {
  id: number;
  title: string;
}

export default function ManualDonationModal({
  isOpen,
  onClose,
  onSuccess,
}: ManualDonationModalProps) {
  const [formData, setFormData] = useState({
    campaign_id: "",
    amount: "",
    donor_name: "",
    donor_email: "",
    donor_phone: "",
    note: "",
    is_anonymous: false,
    payment_date: new Date().toISOString().split("T")[0],
  });

  const [searchQuery, setSearchQuery] = useState("");
  const [showDonorSearch, setShowDonorSearch] = useState(false);

  // Fetch campaigns
  const { data: campaignsData } = useQuery({
    queryKey: ["pro-campaigns-list"],
    queryFn: async () => {
      const res = await fetch("/wp-json/wpd-pro/v1/campaigns/list", {
        headers: {
          "X-WP-Nonce": (window as any).wpdSettings?.nonce,
        },
      });
      return res.json();
    },
    enabled: isOpen,
  });

  // Search donors
  const { data: donorsData, isLoading: isSearching } = useQuery({
    queryKey: ["pro-donors-search", searchQuery],
    queryFn: async () => {
      const res = await fetch(
        `/wp-json/wpd-pro/v1/donors/search?q=${encodeURIComponent(searchQuery)}`,
        {
          headers: {
            "X-WP-Nonce": (window as any).wpdSettings?.nonce,
          },
        },
      );
      return res.json();
    },
    enabled: searchQuery.length >= 2,
  });

  // Create donation mutation
  const createMutation = useMutation({
    mutationFn: async (data: typeof formData) => {
      const res = await fetch("/wp-json/wpd-pro/v1/donations/manual", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-WP-Nonce": (window as any).wpdSettings?.nonce,
        },
        body: JSON.stringify(data),
      });
      if (!res.ok) {
        const err = await res.json();
        throw new Error(err.message || "Failed to create donation");
      }
      return res.json();
    },
    onSuccess: () => {
      toast.success("Donasi berhasil dibuat!");
      onSuccess?.();
      onClose();
      resetForm();
    },
    onError: (err: Error) => {
      toast.error(err.message);
    },
  });

  const resetForm = () => {
    setFormData({
      campaign_id: "",
      amount: "",
      donor_name: "",
      donor_email: "",
      donor_phone: "",
      note: "",
      is_anonymous: false,
      payment_date: new Date().toISOString().split("T")[0],
    });
    setSearchQuery("");
  };

  const selectDonor = (donor: Donor) => {
    setFormData((prev) => ({
      ...prev,
      donor_name: donor.name,
      donor_email: donor.email,
      donor_phone: donor.phone,
    }));
    setShowDonorSearch(false);
    setSearchQuery("");
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    createMutation.mutate(formData);
  };

  if (!isOpen) return null;

  const campaigns: Campaign[] = campaignsData?.campaigns || [];
  const donors: Donor[] = donorsData?.donors || [];

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
      <div className="bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
        {/* Header */}
        <div className="flex items-center justify-between p-4 border-b">
          <h2 className="text-lg font-bold text-gray-900 my-0!">
            Tambah Donasi Manual
          </h2>
          <button
            type="button"
            onClick={onClose}
            className="p-1 hover:bg-gray-100 rounded-lg transition"
          >
            <X size={20} className="text-gray-500" />
          </button>
        </div>

        <form onSubmit={handleSubmit} className="p-4 space-y-4">
          {/* Campaign */}
          <div>
            <Label htmlFor="campaign_id">Campaign *</Label>
            <Select
              id="campaign_id"
              value={formData.campaign_id}
              onChange={(e) =>
                setFormData({ ...formData, campaign_id: e.target.value })
              }
              required
            >
              <option value="">Pilih Campaign</option>
              {campaigns.map((c) => (
                <option key={c.id} value={c.id}>
                  {c.title}
                </option>
              ))}
            </Select>
          </div>

          {/* Amount */}
          <div>
            <Label htmlFor="amount">Jumlah Donasi (Rp) *</Label>
            <InputMoney
              id="amount"
              value={formData.amount}
              onChange={(value) =>
                setFormData({ ...formData, amount: value.toString() })
              }
              placeholder="100000"
              required
            />
          </div>

          {/* Donor Search */}
          <div className="border-t border-gray-200 pt-4">
            <div className="flex items-center justify-between mb-2">
              <Label>Data Donatur</Label>
              <button
                type="button"
                onClick={() => setShowDonorSearch(!showDonorSearch)}
                className="text-xs text-blue-600 hover:text-blue-700 flex items-center gap-1"
              >
                <Search size={12} />
                Cari Donatur Existing
              </button>
            </div>

            {showDonorSearch && (
              <div className="mb-3 relative">
                <Input
                  type="text"
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  placeholder="Cari by email, nama, atau phone..."
                  className="pr-8"
                />
                {isSearching && (
                  <Loader2
                    size={16}
                    className="absolute right-2 top-1/2 -translate-y-1/2 animate-spin text-gray-400"
                  />
                )}

                {donors.length > 0 && (
                  <div className="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                    {donors.map((donor, idx) => (
                      <button
                        key={`${donor.email}-${idx}`}
                        type="button"
                        onClick={() => selectDonor(donor)}
                        className="w-full text-left px-3 py-2 hover:bg-gray-50 border-b last:border-b-0"
                      >
                        <div className="font-medium text-sm">{donor.name}</div>
                        <div className="text-xs text-gray-500">
                          {donor.email} • {donor.phone}
                        </div>
                      </button>
                    ))}
                  </div>
                )}
              </div>
            )}

            <div className="grid grid-cols-2 gap-3">
              <div>
                <Label htmlFor="donor_name">Nama *</Label>
                <Input
                  id="donor_name"
                  type="text"
                  value={formData.donor_name}
                  onChange={(e) =>
                    setFormData({ ...formData, donor_name: e.target.value })
                  }
                  placeholder="Nama Donatur"
                  required
                />
              </div>
              <div>
                <Label htmlFor="donor_email">Email *</Label>
                <Input
                  id="donor_email"
                  type="email"
                  value={formData.donor_email}
                  onChange={(e) =>
                    setFormData({ ...formData, donor_email: e.target.value })
                  }
                  placeholder="email@example.com"
                  required
                />
              </div>
            </div>

            <div className="mt-3">
              <Label htmlFor="donor_phone">No. Telepon</Label>
              <Input
                id="donor_phone"
                type="tel"
                value={formData.donor_phone}
                onChange={(e) =>
                  setFormData({ ...formData, donor_phone: e.target.value })
                }
                placeholder="08123456789"
              />
            </div>
          </div>

          {/* Note */}
          <div>
            <Label htmlFor="note">Catatan</Label>
            <Textarea
              id="note"
              value={formData.note}
              onChange={(e) =>
                setFormData({ ...formData, note: e.target.value })
              }
              placeholder="Catatan donasi (opsional)"
              rows={2}
            />
          </div>

          {/* Payment Date */}
          <div>
            <Label htmlFor="payment_date">Tanggal Pembayaran</Label>
            <Input
              id="payment_date"
              type="date"
              value={formData.payment_date}
              onChange={(e) =>
                setFormData({ ...formData, payment_date: e.target.value })
              }
            />
          </div>

          {/* Anonymous */}
          <div className="flex items-center gap-2">
            <input
              type="checkbox"
              id="is_anonymous"
              checked={formData.is_anonymous}
              onChange={(e) =>
                setFormData({ ...formData, is_anonymous: e.target.checked })
              }
              className="rounded text-blue-600"
            />
            <Label htmlFor="is_anonymous" className="mb-0 cursor-pointer">
              Donasi Anonim
            </Label>
          </div>

          {/* Actions */}
          <div className="flex gap-3 pt-4 border-t">
            <button
              type="button"
              onClick={onClose}
              className="flex-1 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition font-medium"
            >
              Batal
            </button>
            <button
              type="submit"
              disabled={createMutation.isPending}
              className="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium flex items-center justify-center gap-2 disabled:opacity-50"
            >
              {createMutation.isPending ? (
                <Loader2 size={16} className="animate-spin" />
              ) : (
                <Plus size={16} />
              )}
              Buat Donasi
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}
