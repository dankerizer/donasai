export {};

declare global {
  interface Window {
    donasaiSettings?: {
      root: string;
      nonce: string;
      initialPath: string;
      isPro: boolean;
      version: string;
    };
    donasaiProSettings?: {
      licenseStatus: string;
      [key: string]: any;
    };
  }
}
