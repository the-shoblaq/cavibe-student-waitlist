# Cavibe Student Survey + Waiting List + Admin

## Install
1. Import `schema.sql` into MySQL.
2. Configure DB_HOST, DB_NAME, DB_USER and DB_PASS, or edit `config.php`.
3. Upload to a PHP 8+ server with PDO MySQL.
4. Public survey: `/index.php`
5. Admin: `/admin/login.php`

## Admin security
The local fallback password is `password` ONLY for initial testing.
Generate a production hash:
`php -r "echo password_hash('YOUR-STRONG-PASSWORD', PASSWORD_DEFAULT);"`
Set it as the `ADMIN_PASSWORD_HASH` environment variable.

## Included
- 7-step responsive Cavibe student survey
- Summarized Sections C–G
- 1–5 rating matrices
- “Other” text input fields
- Open-ended question asking what existing social media lacks and what users want Cavibe to have
- Up-to-five feature preference enforcement
- Up-to-three churn-reason enforcement
- Earning/monetization research
- Privacy, safety, advertising and accessibility research
- Waiting-list / early-tester collection
- Flexible MySQL answer table for future questions
- Admin KPI dashboard
- Most-requested features
- Tester-interest summary
- Individual response detail
- CSV export
- CSRF protection, PDO prepared statements and honeypot

Before public deployment, use HTTPS and add your final Cavibe privacy-policy link and production admin authentication/rate limiting.
