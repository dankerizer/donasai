import {
	type LogoData,
	LogoUploader,
} from "/src/components/shared/LogoUploader";
import { Checkbox } from "/src/components/ui/Checkbox";
import { Input } from "/src/components/ui/Input";
import { Label } from "/src/components/ui/Label";

interface SignatureValue {
	enabled: boolean;
	image: {
		attachment_id: number;
		url: string;
		width: number;
		height: number;
	};
	label: string;
}

interface SignatureUploaderProps {
	value: SignatureValue;
	onChange: (value: SignatureValue) => void;
}

export function SignatureUploader({ value, onChange }: SignatureUploaderProps) {
	const handleImageChange = (newImage: LogoData) => {
		onChange({
			...value,
			image: {
				attachment_id: newImage.attachment_id,
				url: newImage.url,
				width: newImage.width,
				height: newImage.height,
			},
		});
	};

	return (
		<div className="space-y-4">
			<div className="flex items-start gap-3 group">
				<Checkbox
					id="enable_signature"
					checked={value.enabled}
					onChange={(e) => onChange({ ...value, enabled: e.target.checked })}
					className="mt-0.5"
				/>
				<label htmlFor="enable_signature" className="cursor-pointer flex-1">
					<span className="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">
						Tampilkan Tanda Tangan Digital
					</span>
					<p className="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
						Menambahkan gambar tanda tangan dan label di bagian bawah kuitansi.
					</p>
				</label>
			</div>

			{value.enabled && (
				<div className="pl-7 space-y-4 animate-in slide-in-from-top-2 duration-200">
					<div className="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-gray-200 dark:border-gray-700">
						<Label className="mb-2 block">Gambar Tanda Tangan</Label>
						<LogoUploader value={value.image} onChange={handleImageChange} />
						<p className="text-xs text-gray-500 mt-2">
							Disarankan menggunakan gambar PNG transparan.
						</p>
					</div>

					<div>
						<Label htmlFor="sig_label">Label / Nama Penanda Tangan</Label>
						<Input
							id="sig_label"
							type="text"
							value={value.label}
							onChange={(e) => onChange({ ...value, label: e.target.value })}
							placeholder="Authorized Signature"
							className="mt-1.5"
						/>
					</div>
				</div>
			)}
		</div>
	);
}
