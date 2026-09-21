# Divine Beauty — WordPress theme

A classic WordPress theme for **Divine Beauty & Nails By Dee**, built so every
section of every page is an Elementor widget. Once it is installed there is no
code to touch: headings, photographs, treatments, portfolio pieces, buttons and
contact details are all edited visually.

---

## Installing

1. **Install Elementor first.** *Plugins → Add New → search "Elementor" →
   Install → Activate.* The free version is all the theme needs.
2. **Upload the theme.** *Appearance → Themes → Add New → Upload Theme →*
   choose `divine-beauty.zip` *→ Install Now → Activate.*
3. On activation the theme builds the site for you: **Home**, **Services** and
   **Portfolio** are created as Elementor pages, Home is set as the front page,
   and the header and footer menus are filled in.

   If that did not run — for example the theme was activated before Elementor —
   go to *Appearance → Divine Beauty setup* and press **Set up my site**.

---

## Editing the site

### Page content — Elementor

Open any page and press **Edit with Elementor**. Every section is a widget in
the **Divine Beauty** category of the widget panel:

| Widget | What it is |
|---|---|
| Hero | The opening scene: headline, buttons, figures, image collage, emblem |
| Page hero | The smaller centred opening used on Services and Portfolio |
| Treatment marquee | The slow scrolling band of treatment names |
| Studio story | Photograph beside the studio's own words, promises and signature |
| Treatment carousel | The nine-treatment rail with arrows and dots |
| Treatment menu | The full Services list — photograph, description, benefits, booking |
| Treatment band | One alternating feature row. The home page stacks five of them |
| Promise cards | The numbered "why clients stay" grid |
| Portfolio gallery | Filterable gallery of the studio's own work, with a lightbox |
| Film band | A short studio film beside the story behind it |
| Reviews | Client quotes |
| Social strip | The follow-us band |
| Call to action | The centred closing band |
| Enquiry form | Contact details plus the appointment form |

Drag a widget in, drop it where you want it, edit it in the left panel. Sections
can be reordered, duplicated or deleted like any other Elementor content.

### Studio details — Customiser

Phone, email, address, opening hours, social links, the WhatsApp number and the
announcement bar live in **Appearance → Customise → Studio details**. They are
set once and used everywhere: the header, the footer, the contact block, the
enquiry form and the WhatsApp button.

### Menus

*Appearance → Menus.* Three locations: **Primary menu** (header), **Footer —
Explore** and **Footer — Treatments**.

### Logo

*Appearance → Customise → Site Identity → Logo.* The bundled logo is used until
one is set.

---

## Appointments

Every enquiry the website receives is **saved into WordPress** and appears under
**Appointments** in the admin menu, with a count of the new ones beside it.

The list shows the client, the treatment, their preferred date, their contact
details, the status and when it arrived. It can be filtered by status, sorted by
treatment or date, searched, and exported to a spreadsheet with **Export all
appointments (CSV)**.

Opening one shows everything the client sent, a **Reply to this client** button
that drafts an email to them, and a **Status** you set as you work through it:

> New → Contacted → Confirmed → Completed, or Cancelled

The status is for the studio's own tracking. Changing it does not notify anyone.

### How a booking arrives

Every treatment — on the services page and in the home page carousel — has a
**Book now** button. It sends the visitor to the enquiry form with that
treatment already chosen, so they only fill in their own details. Opening a
piece in the portfolio and pressing *Enquire about this* does the same and
carries the look across too.

When they submit, the enquiry is **stored first and emailed second**. Mail can
fail for reasons that have nothing to do with the visitor — a host with no
mailer, a provider throttling — and an enquiry that only ever existed as an
email is an enquiry you lose. The record in Appointments is the source of truth;
the email is a notification.

The form is rate-limited to five enquiries an hour per visitor and carries a
honeypot field, so ordinary spam does not get through. Replies go straight to
the visitor's own address.

If the notification emails are not arriving (the appointments will still be
saved), the host's `wp_mail()` is usually the cause — install an SMTP plugin and
send through the studio's real mailbox. That also keeps them out of spam.

---

## A note on the photography

The home and services pages ship with licensed editorial photography so the site
looks finished from the first minute. **The portfolio ships with the studio's own
photographs only**, and it should stay that way — it is the one page a client
reads as proof of the work.

Replace the stock images with the studio's own as better photographs are taken:
every one is an ordinary Elementor image control.

---

## Requirements

- WordPress 6.0 or newer
- PHP 7.4 or newer (tested on 8.4)
- Elementor (free)

## Files

```
divine-beauty/
├── style.css              Theme header
├── functions.php          Bootstrap — loads inc/ and starts the classes
├── header.php footer.php  Announcement bar, header, footer, WhatsApp button
├── index.php page.php single.php 404.php
├── inc/
│   ├── helpers.php              Icons, phone and WhatsApp formatting
│   ├── class-theme-setup.php    Theme support, menus, image sizes
│   ├── class-assets.php         Stylesheets and scripts
│   ├── class-customizer.php     Studio details
│   ├── class-nav-walker.php     Menu markup that matches the design
│   ├── class-appointments.php   The Appointments record, list, detail and export
│   ├── class-enquiry.php        Enquiry validation, rate limiting, storing and mail
│   ├── class-elementor.php      Widget category and registration
│   └── class-starter-content.php  Builds the site on activation
├── elementor/
│   ├── class-widget-base.php    Shared controls and output helpers
│   └── widgets/                 The fourteen widgets
└── assets/                      css, js, images
```
