# NHA Premium Store Design Guide

This document outlines the Design System used to modernize the e-commerce UI.

## Color Palette

The color scheme is designed to create a premium, trustworthy, and modern feel.

- **Primary Color:** `#2563EB` (Used for primary buttons, active states, and highlights).
- **Dark Color:** `#0F172A` (Used for admin sidebars, dark buttons, and primary headings).
- **Accent Color:** `#F59E0B` (Used for warning states or special call-to-actions).
- **Background Color:** `#F8FAFC` (Used for the main application background to provide a clean look).
- **Surface Color:** `#FFFFFF` (Used for cards, forms, and elevated elements).
- **Text Primary:** `#1E293B` (Used for all main body text).
- **Text Secondary:** `#64748B` (Used for muted text, labels, and secondary information).

## Typography

- **Primary Font Family:** `'Inter', 'Segoe UI', Roboto, Helvetica, Arial, sans-serif`.
- **Headings:** Bold (`700`), dark color, creating strong visual hierarchy.
- **Body Text:** Clean, legible, with a line height of `1.6`.

## UI Components

### Product Cards (`.product-card`)
- **Structure:** 4:3 Aspect Ratio for images with a light gray background (`#f1f5f9`) to make product photos stand out.
- **Styling:** `1rem` border radius, subtle border (`1px solid #E2E8F0`).
- **Interactions:** Smooth hover effect that elevates the card (`transform: translateY(-8px)`), increases the shadow, and changes the border color to Primary. Image scales up slightly on hover.

### Buttons (`.btn-primary-custom`, `.btn-dark-custom`)
- **Styling:** Rounded corners (`0.5rem`), bold text (`600`), and consistent padding.
- **Interactions:** Hover states include a subtle lift (`translateY(-2px)`) and a medium drop shadow.

### Forms & Inputs (`.modern-form-container`)
- **Structure:** Floating labels using Bootstrap 5 `.form-floating` for a modern, compact look.
- **Styling:** Inputs have subtle borders that transition to the primary color on focus with a custom focus ring. Container is elevated with a large shadow (`box-shadow: var(--shadow-lg)`).

### Admin Dashboard (`.admin-content`, `.admin-card`)
- **Structure:** Clean, spacious layout. 
- **Tables:** `table-custom` with customized header backgrounds (`#F1F5F9`) and vertical alignment.
- **Navigation:** Icon-paired buttons using Bootstrap Icons for intuitive actions (Edit, Delete, Add).

## Layout & Framework

- **CSS Framework:** Bootstrap 5 (via CDN).
- **Grid System:** Utilized Bootstrap's responsive grid (`row`, `col-*`) for responsive product displays and centered forms.
- **Offcanvas:** Used Bootstrap's Offcanvas component for the Shopping Cart sidebar to provide a seamless slide-out experience on all devices.
- **Icons:** Bootstrap Icons (`bi-cart`, `bi-trash`, etc.) used extensively for better visual communication.
