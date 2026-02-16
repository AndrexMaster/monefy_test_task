# Invoice Management System

A full-stack application for managing financial invoices, built with **Nuxt 4**, **TailwindCSS 4**, and **Laravel 11**.

## 🚀 Quick Start

### 1. Requirements
- Docker and Docker Compose
- Node.js (for local frontend development)

### 2. Installation
```bash
docker-compose up --build

docker-compose exec laravel-api composer install
docker-compose exec laravel-api php artisan migrate --seed
```

### 3. Access

- Frontend: http://localhost:3000
- API: http://localhost:8000/api


# Project Structure
## Backend (Laravel 11)

- API First: Stateless REST API providing clean JSON responses.
- Data Integrity: Strict validation rules including fiscal logic (Gross = Net + VAT) and date consistency (Due Date >= Issue Date).
- Security: Business logic prevents editing any invoice that is not in pending status.

## Frontend (Nuxt 4)

- Modern UI: Built with TailwindCSS 4, featuring a clean interface for accountants.
- Reactive UX: Real-time calculation of Gross amounts during data entry.
- Status Handling: Color badges and adaptive forms that lock based on status.
- Validation: Integrated Vee-Validate and Zod for schema-based validation.


# Answers to Assignment Questions
## 1. How did you structure the frontend and backend?

I followed a decoupled architecture. The Backend serves as the "Source of Truth," enforcing business rules and data integrity. The Frontend focuses on User Experience, using Nuxt’s file-based routing and reactive components. I implemented a structure where the API base is handled correctly for both SSR and client-side requests within Docker.

## 2. What trade-offs did you make and why?

- Authentication: As per instructions, I omitted it to focus on core invoice lifecycle and validation.
- State Management: Used native Vue 3 composition API instead of Pinia to keep the project lightweight, as the current complexity didn't require a global store.
- Validation Balance: Client-side validation (Vee-Validate) provides instant feedback, but the Backend always re-verifies fiscal rules for security.

## 3. What would you improve in a production version?

- Automated Testing: Implement Pest for API and Vitest for components.
- Advanced Filtering: Server-side search by supplier and status filtering.
- PDF Generation: Ability to download invoices as official documents.
- Audit Log: Tracking all modifications to financial data for transparency.

## 4. What UX edge cases did you consider?

- Read-Only Lock: Forms are automatically disabled if the status is approved or rejected.
- Form Feedback: Clear error messages for invalid dates or negative amounts.
- Calculation Sync: gross_amount is calculated automatically and locked for manual input to ensure accuracy.
