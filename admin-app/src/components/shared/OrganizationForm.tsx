import { Input } from "/src/components/ui/Input";
import { Label } from "/src/components/ui/Label";
import { Textarea } from "/src/components/ui/Textarea";
import type { ReceiptTemplate } from "/src/pages/receipt-template/hooks/use-receipt-template";
import { LogoUploader } from "./LogoUploader";

// Unified interface covers both needs
// export interface OrganizationData {
// 	name: string;
// 	email: string;
// 	phone: string;
// 	website?: string; // Optional in General?
// 	// Address handling
// 	address?: string; // For simple mode
// 	address_line_1?: string; // For detailed mode
// 	address_line_2?: string;
// 	city?: string;
// 	postal_code?: string;

// 	tax_id?: string; // Detailed only
// 	// Logo handling
// 	logo?: string | LogoData; // string for General, LogoData for Receipt
// }

interface OrganizationFormProps {
	data: ReceiptTemplate["organization"];
	onChange: (data: ReceiptTemplate["organization"]) => void;
	mode?: "simple" | "detailed";
	showLogo?: boolean;
}

export function OrganizationForm({
	data,
	onChange,
	mode = "simple",
	showLogo = true,
}: OrganizationFormProps) {
	const handleChange = (
		field: keyof ReceiptTemplate["organization"],
		value: any,
	) => {
		onChange({
			...data,
			[field]: value,
		});
	};

	return (
		<div className="space-y-6">
			<div className="space-y-4">
				{/* Name */}
				<div>
					<Label htmlFor="org-name">
						Nama Organisasi <span className="text-red-500">*</span>
					</Label>
					<Input
						id="org-name"
						type="text"
						value={data.name}
						onChange={(e) => handleChange("name", e.target.value)}
						placeholder="contoh: Yayasan Peduli Sesama"
						required
					/>
				</div>

				{/* Mode: Simple (General Settings) */}
				{mode === "simple" && (
					<div>
						<Label htmlFor="org-address">Alamat</Label>
						<Textarea
							id="org-address"
							rows={3}
							value={data.address_line_1 || ""}
							onChange={(e) => handleChange("address_line_1", e.target.value)}
							placeholder="Alamat lengkap..."
						/>
					</div>
				)}

				{/* Mode: Detailed (Receipt Template) */}
				{mode === "detailed" && (
					<>
						<div>
							<Label htmlFor="org-address1">Alamat Baris 1</Label>
							<Input
								id="org-address1"
								type="text"
								value={data.address_line_1 || ""}
								onChange={(e) => handleChange("address_line_1", e.target.value)}
								placeholder="contoh: Jl. Kemanusiaan No. 123"
							/>
						</div>
						<div>
							<Label htmlFor="org-address2">Alamat Baris 2</Label>
							<Input
								id="org-address2"
								type="text"
								value={data.address_line_2 || ""}
								onChange={(e) => handleChange("address_line_2", e.target.value)}
								placeholder="contoh: Kelurahan Bantuan, Kecamatan Sosial"
							/>
						</div>
						<div className="grid grid-cols-2 gap-4">
							<div>
								<Label htmlFor="org-city">Kota</Label>
								<Input
									id="org-city"
									type="text"
									value={data.city || ""}
									onChange={(e) => handleChange("city", e.target.value)}
									placeholder="contoh: Jakarta"
								/>
							</div>
							<div>
								<Label htmlFor="org-postal">Kode Pos</Label>
								<Input
									id="org-postal"
									type="text"
									value={data.postal_code || ""}
									onChange={(e) => handleChange("postal_code", e.target.value)}
									placeholder="contoh: 12345"
								/>
							</div>
						</div>
					</>
				)}

				{/* Contact Info */}
				<div className="grid grid-cols-1 md:grid-cols-2 gap-4">
					<div>
						<Label htmlFor="org-email">Email</Label>
						<Input
							id="org-email"
							type="email"
							value={data.email}
							onChange={(e) => handleChange("email", e.target.value)}
							placeholder="contoh: info@yayasan.org"
						/>
					</div>
					<div>
						<Label htmlFor="org-phone">Telepon / WhatsApp</Label>
						<Input
							id="org-phone"
							type="tel"
							value={data.phone}
							onChange={(e) => handleChange("phone", e.target.value)}
							placeholder="contoh: 021-1234567"
						/>
					</div>
				</div>

				{/* Detailed Extras */}
				{mode === "detailed" && (
					<>
						<div>
							<Label htmlFor="org-website">Website</Label>
							<Input
								id="org-website"
								type="url"
								value={data.website || ""}
								onChange={(e) => handleChange("website", e.target.value)}
								placeholder="contoh: https://yayasan.org"
							/>
						</div>
						<div>
							<Label htmlFor="org-taxid">NPWP (Opsional)</Label>
							<Input
								id="org-taxid"
								type="text"
								value={data.tax_id || ""}
								onChange={(e) => handleChange("tax_id", e.target.value)}
								placeholder="contoh: 01.234.567.8-901.000"
							/>
						</div>
					</>
				)}

				{/* Logo - Embedded */}
				{showLogo && (
					<div className="pt-4 border-t border-gray-100 dark:border-gray-800">
						<LogoUploader
							value={data.logo}
							onChange={(logoData) =>
								handleChange(
									"logo",
									mode === "simple" ? logoData.url : logoData,
								)
							}
						/>
					</div>
				)}
			</div>
		</div>
	);
}
