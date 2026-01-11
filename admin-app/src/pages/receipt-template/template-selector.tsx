import { Check } from "lucide-react";
import { cn } from "../../utils/cn";

interface TemplateSelectorProps {
  value: string;
  onChange: (value: any) => void;
}

const TEMPLATES = [
  {
    id: "modern",
    name: "Modern",
    description: "Bersih, modern dengan aksen warna.",
    color: "bg-emerald-500",
  },
  {
    id: "classic",
    name: "Classic",
    description: "Formal, font serif, border ganda.",
    color: "bg-gray-800",
  },
  {
    id: "minimal",
    name: "Minimal",
    description: "Sederhana, tanpa border, fokus konten.",
    color: "bg-gray-100 border border-gray-300",
  },
  {
    id: "corporate",
    name: "Corporate",
    description: "Profesional, full header background.",
    color: "bg-blue-600",
  },
  {
    id: "bold",
    name: "Bold",
    description: "Tegas, kontras tinggi, tipografi besar.",
    color: "bg-black",
  },
  {
    id: "elegant",
    name: "Elegant",
    description: "Mewah, font serif klasik, aksen emas.",
    color: "bg-amber-600",
  },
  {
    id: "simple",
    name: "Simple",
    description: "Ala printer struk, font monospace.",
    color: "bg-white border-2 border-dashed border-gray-400",
  },
  {
    id: "creative",
    name: "Creative",
    description: "Ramah, sudut bulat, warna cerah.",
    color: "bg-violet-500",
  },
  {
    id: "official",
    name: "Official",
    description: "Resmi, bingkai tegas, layout sertifikat.",
    color: "bg-slate-700",
  },
];

export function TemplateSelector({ value, onChange }: TemplateSelectorProps) {
  return (
    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      {TEMPLATES.map((template) => (
        <div
          key={template.id}
          onClick={() => onChange(template.id)}
          className={cn(
            "relative cursor-pointer rounded-xl border-2 p-4 transition-all hover:border-emerald-300 dark:hover:border-emerald-700",
            value === template.id
              ? "border-emerald-500 bg-emerald-50 dark:bg-emerald-950/30"
              : "border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800",
          )}
        >
          <div className="flex items-start justify-between mb-3">
            <div
              className={cn(
                "h-10 w-10 rounded-lg shadow-sm",
                template.color.includes("bg-") ? template.color : "bg-gray-200",
              )}
            />
            {value === template.id && (
              <div className="h-6 w-6 rounded-full bg-emerald-500 flex items-center justify-center text-white">
                <Check size={14} strokeWidth={3} />
              </div>
            )}
          </div>
          <div>
            <h4 className="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
              {template.name}
            </h4>
            <p className="text-xs text-gray-500 dark:text-gray-400 mt-1">
              {template.description}
            </p>
          </div>
        </div>
      ))}
    </div>
  );
}
