Berikut **User Stories wp-donasi** yang sudah dipecah per role dan prioritas (MVP Free vs Pro), format siap untuk development sprint.[1][2]

***

## User Roles

1. **Admin Lembaga** (masjid, NGO, sekolah) - primary user
2. **Donatur** (pengunjung yang mau donasi)
3. **Developer/Agency** (bikin website untuk lembaga)
3. **Fundraiser** (User yang menyebarkan link donasi)
4. **Developer/Agency** (bikin website untuk lembaga)
5. **Super Admin** (kelola Pro license)

---

## User Stories - MVP Free (Prioritas Tinggi)

### Admin Lembaga (Primary)

```
As an Admin Lembaga, 
I want to create a new campaign with title, target amount, and deadline
So that I can start fundraising immediately without coding
Acceptance: CPT wpd_campaign, auto-generate landing page + form, publish button [web:78][web:80]

As an Admin Lembaga, 
I want to see dashboard overview with total donations, donor count, active campaigns
So that I can monitor fundraising performance at a glance
Acceptance: React dashboard cards + simple chart (daily/weekly) [web:78][web:85]

As an Admin Lembaga, 
I want to view all donations with basic filters (date, campaign, status)
So that I can track pending payments and follow up
Acceptance: Table with export CSV, manual status update [web:78][web:81]

As an Admin Lembaga, 
I want to manually mark offline bank transfers as "complete"
So that I can record donations without payment gateway
Acceptance: Bulk/manual status change + notification [web:81]
```

### Donatur

```
As a Donatur (Zakat),
I want to calculate my Zakat Maal/Penghasilan automatically
So that I know exactly how much I should pay
Acceptance: Zakat calculator form embedded in campaign [web:25]

As a Donatur (Qurban),
I want to choose "Type A" or "Type B" animal packages
So that I can easily buy Qurban without inputting manual amount
Acceptance: Radio button selection for packages [web:45]

As a Donatur, 
I want to see list of active campaigns with progress bar
So that I can choose which campaign to support
Acceptance: Shortcode [wpd_campaigns] with grid/list view [web:78][web:80]

As a Donatur, 
I want a simple 1-step donation form with preset amounts + custom input
So that I can donate quickly from mobile
Acceptance: Form fields (name,email,phone,amount,note,anonymous), validation [web:78][web:84]

As a Donatur (logged in), 
I want to view "My Donations" page with my donation history
So that I can track my contributions
Acceptance: Shortcode [wpd_my_donations], table with status & amount [web:78][web:83]

As a Donatur, 
I want email confirmation after successful donation
So that I have proof of my contribution
Acceptance: Email template with campaign details & amount [web:81]
```

### Fundraiser (New)

```
As a Fundraiser,
I want to register for a campaign and get a unique referral link
So that I can help spread the campaign and track my impact
Acceptance: Registration button "Jadi Fundraiser", generate URL ?ref=username

As a Fundraiser,
I want to see a leaderboard of top fundraisers
So that I feel motivated to search for more donations
Acceptance: Widget "Top Pejuang Kebaikan" on campaign page
```

### Developer/Agency

```
As a Developer, 
I want to override campaign/form templates via child theme
So that I can customize styling for client
Acceptance: templates/ folder copyable to theme/wp-donasi/ [web:62][web:83]

As a Developer, 
I want shortcodes with basic parameters (category, limit, layout)
So that I can embed campaigns flexibly
Acceptance: [wpd_campaigns category="zakat" layout="grid"] [web:78][web:84]
```

***

## User Stories - Pro Features (Prioritas Menengah-Tinggi)

### Admin Lembaga (Advanced)

```
As an Admin Lembaga, 
I want multiple payment gateways (Midtrans, Xendit) with auto-webhook
So that Indonesian donors can pay conveniently with local methods
Acceptance: Gateway switcher, auto-status update via webhook [web:80][web:91]

As an Admin Lembaga, 
I want advanced donor reports (LTV, campaign favorites, segments)
So that I can plan targeted fundraising campaigns
Acceptance: Donor CRM mini view, export per segment [web:45][web:89]

As an Admin Lembaga, 
I want to set up Facebook & TikTok Pixels
So that I can track ads conversion effectively
Acceptance: Settings input for Pixel IDs, auto-fire events

As an Admin Lembaga,
I want to enable "Flying Button" WhatsApp
So that donors can easily ask questions
Acceptance: Floating button implementation with custom message

As an Admin Lembaga, 
I want recurring donations (monthly/yearly)
So that I can build sustainable funding
Acceptance: Recurring toggle in form, manage subscriptions [web:45][web:83]
```

### Donatur (Enhanced)

```
As a Donatur, 
I want to download PDF receipt from "My Donations"
So that I have official proof for tax deduction
Acceptance: PDF generator with campaign details [web:77][web:83]

As a Donatur (recurring), 
I want to pause/resume/cancel my subscription from dashboard
So that I have full control over my recurring donations
Acceptance: Subscription management UI [web:83]
```

### Developer/Agency (Pro)

```
As a Developer, 
I want custom fields builder for donation forms
So that I can collect specific info per campaign (location, purpose, etc)
Acceptance: Drag-drop field builder in React admin [web:81][web:83]

As a Developer, 
I want webhook endpoints for external CRM integration
So that client data syncs automatically
Acceptance: wpd_webhook_donation_updated action [web:83][web:89]
```

### Super Admin (Pro License)

```
As a Super Admin, 
I want license key activation with auto-updates
So that Pro customers always get latest features securely
Acceptance: License manager page, update channel [web:40][web:41]
```

***

## User Stories - Technical Foundation (Prioritas Sangat Tinggi)

```
As a Developer, 
I want React+TS admin app with HMR during development
So that I can develop dashboard efficiently without page reloads
Acceptance: Vite/wp-scripts setup, dev server proxy [web:63][web:71]

As a Developer, 
I want extensible payment gateway architecture
So that new gateways can be added via hooks
Acceptance: wpd_register_gateway hook + GatewayInterface [web:81][web:83]

As a WordPress.org reviewer, 
I want the free version to be fully functional without artificial limits
So that it passes guideline review
Acceptance: No donation caps, no nag screens, clear Pro upsell [web:50][web:52]
```

***

## Prioritas Sprint (Suggested Order)

```
Sprint 1: Core Campaign + Form (Free MVP)
Sprint 2: Basic Dashboard React + Offline Payment
Sprint 3: 1 Global Gateway + Donor Dashboard
Sprint 4: Export + Template Override
Sprint 5: Pro - Local Gateways + Recurring
Sprint 6: Pro - Advanced Reports + PDF
```

Format ini bisa langsung kamu copy-paste ke:
- Trello/Jira untuk task breakdown
- AI coding assistant (per sprint/module)
- Documentation internal tim

Mau dilanjutkan ke **Technical Tasks Breakdown** per user story atau langsung **Plugin Folder Structure** + **Database Schema**?

[1](https://wordpress.org/plugins/give/)
[2](https://givewp.com)
[3](https://donasiaja.id/)