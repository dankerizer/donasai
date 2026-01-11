import { Upload, X } from "lucide-react";
import { useState } from "react";
import { toast } from "sonner";
import { Label } from "@/components/ui/Label";

export interface LogoData {
	url: string;
	attachment_id: number;
	width: number;
	height: number;
}

interface LogoUploaderProps {
	value?: string | LogoData;
	onChange: (logo: LogoData) => void;
	label?: string;
	description?: string;
	className?: string; // Additional classes for customization
}

export function LogoUploader({
	value,
	onChange,
	label = "📷 Logo Organisasi",
	description = "Rekomendasi: 400×200px (maks 500KB), format PNG atau JPG",
	className,
}: LogoUploaderProps) {
	const [isUploading] = useState(false);

	// Normalize value to object or null URL
	const currentUrl = typeof value === "string" ? value : value?.url;

	const openMediaUploader = () => {
		// Use WordPress Media Library
		const wpMedia = (window as any).wp?.media;

		if (!wpMedia) {
			toast.error("WordPress media library tidak tersedia");
			return;
		}

		const frame = wpMedia({
			title: "Pilih Logo Organisasi",
			button: {
				text: "Gunakan gambar ini",
			},
			multiple: false,
			library: {
				type: "image",
			},
		});

		frame.on("select", () => {
			const attachment = frame.state().get("selection").first().toJSON();

			onChange({
				url: attachment.url,
				attachment_id: attachment.id,
				width: attachment.width,
				height: attachment.height,
			});

			toast.success("Logo berhasil diupload!");
		});

		frame.open();
	};

	const removeLogo = () => {
		onChange({
			url: "",
			attachment_id: 0,
			width: 0,
			height: 0,
		});
		toast.success("Logo dihapus");
	};

	return (
		<div className={`space-y-4 ${className || ""}`}>
			<div>
				<Label className="text-gray-900 dark:text-gray-100">{label}</Label>
				{description && (
					<p className="text-xs text-gray-500 dark:text-gray-400 mt-1">
						{description}
					</p>
				)}
			</div>

			{currentUrl ? (
				<div className="relative inline-block group">
					<div className="relative overflow-hidden rounded-lg border-2 border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 p-4">
						<img
							src={currentUrl}
							alt="Logo Preview"
							className="max-h-32 max-w-full object-contain"
						/>
					</div>
					<button
						type="button"
						onClick={removeLogo}
						className="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1.5 hover:bg-red-600 transition-colors shadow-md"
						title="Hapus logo"
					>
						<X size={16} />
					</button>
					<button
						type="button"
						onClick={openMediaUploader}
						className="mt-3 text-sm text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 dark:hover:text-emerald-300 font-medium transition-colors"
					>
						Ganti logo
					</button>
				</div>
			) : (
				<button
					type="button"
					onClick={openMediaUploader}
					disabled={isUploading}
					className="flex flex-col items-center justify-center gap-3 px-6 py-8 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg hover:border-emerald-500 dark:hover:border-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950 transition-all w-full max-w-md group"
				>
					<div className="w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center group-hover:bg-emerald-100 dark:group-hover:bg-emerald-900 transition-colors">
						<Upload
							size={24}
							className="text-gray-400 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors"
						/>
					</div>
					<div className="text-center">
						<span className="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
							{isUploading ? "Mengupload..." : "Klik untuk upload logo"}
						</span>
						<p className="text-xs text-gray-500 dark:text-gray-400 mt-1">
							Seret dan lepas file atau klik di sini
						</p>
					</div>
				</button>
			)}
		</div>
	);
}
