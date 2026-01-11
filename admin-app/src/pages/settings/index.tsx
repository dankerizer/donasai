import { SettingsProvider } from "./SettingsContext";
import SettingsLayout from "./SettingsLayout";

export default function SettingsPage() {
  return (
    <SettingsProvider>
      <SettingsLayout />
    </SettingsProvider>
  );
}
