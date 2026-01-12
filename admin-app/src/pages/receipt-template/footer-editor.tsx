import clsx from "clsx";
import { useEffect, useRef } from "react";
import { Checkbox } from "/src/components/ui/Checkbox";
import { Label } from "/src/components/ui/Label";
import { Textarea } from "/src/components/ui/Textarea";
import type { ReceiptTemplate } from "./hooks/use-receipt-template";

interface FooterEditorProps {
	footer: ReceiptTemplate["footer"];
	onChange: (footer: ReceiptTemplate["footer"]) => void;
	compact?: boolean;
}

export function FooterEditor({ footer, onChange, compact = false }: FooterEditorProps) {
	const editorRef = useRef<HTMLTextAreaElement>(null);

	const handleContentChange = (content: string) => {
		onChange({
			...footer,
			content,
		});
	};

	const handleToggle = (field: "show_qr_code" | "show_campaign_desc") => {
		onChange({
			...footer,
			[field]: !footer[field],
		});
	};

	// Auto-resize textarea
	useEffect(() => {
		if (editorRef.current) {
			editorRef.current.style.height = "auto";
			editorRef.current.style.height = editorRef.current.scrollHeight + "px";
		}
	}, []);

	return (
		<div className="space-y-6">
			<div>
				<h3 className="text-lg font-semibold text-gray-900 dark:text-white mb-1">
					📝 Footer Kuitansi
				</h3>
				<p className="text-sm text-gray-500 dark:text-gray-400">
					Tambahkan ucapan terima kasih, informasi legal, atau kontak
				</p>
			</div>

			{/* Content Editor */}
			<div className="space-y-4">
				<div>
					<Label htmlFor="footer-content">Teks Footer</Label>
					<Textarea
						id="footer-content"
						ref={editorRef}
						value={footer.content}
						onChange={(e) => handleContentChange(e.target.value)}
						placeholder="contoh: Terima kasih atas donasi Anda untuk {campaign_name}. Tanda terima ini adalah bukti donasi yang sah."
						rows={4}
						maxLength={500}
					/>
					<div className="flex justify-between items-start mt-2">
						<div className="flex-1">
							<p className="text-xs text-gray-500 dark:text-gray-400">
								<strong>Tag yang tersedia:</strong>
							</p>
							<div className="flex flex-wrap gap-1 mt-1">
								{[
									"{organization_name}",
									"{campaign_name}",
									"{year}",
									"{website}",
									"{donor_name}",
									"{amount}",
								].map((tag) => (
									<code
										key={tag}
										className="text-xs bg-gray-100 dark:bg-gray-800 px-1.5 py-0.5 rounded text-emerald-600 dark:text-emerald-400 border border-gray-200 dark:border-gray-700"
									>
										{tag}
									</code>
								))}
							</div>
						</div>
						<span className="text-xs text-gray-500 dark:text-gray-400 ml-4 shrink-0">
							{footer.content.length}/500
						</span>
					</div>
				</div>

				{/* Options */}
				<div className="space-y-3 pt-4 border-t border-gray-200 dark:border-gray-700">
					<div className="flex items-start gap-3 group">
						<Checkbox
							id="show_qr_code"
							checked={footer.show_qr_code}
							onChange={() => handleToggle("show_qr_code")}
							className="mt-0.5"
						/>
						<label htmlFor="show_qr_code" className="cursor-pointer flex-1">
							<span className="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">
								Tampilkan QR Code
							</span>
							<p className="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
								Tambahkan QR code untuk verifikasi keaslian kuitansi
							</p>
						</label>
					</div>

					<div className="flex items-start gap-3 group">
						<Checkbox
							id="show_campaign_desc"
							checked={footer.show_campaign_desc}
							onChange={() => handleToggle("show_campaign_desc")}
							className="mt-0.5"
						/>
						<label
							htmlFor="show_campaign_desc"
							className="cursor-pointer flex-1"
						>
							<span className="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors">
								Tampilkan Deskripsi Kampanye
							</span>
							<p className="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
								Sertakan excerpt kampanye di kuitansi
							</p>
						</label>
					</div>
				</div>
			</div>
		</div>
	);
}
