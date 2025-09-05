
# Influencer Marketplace (PHP + MySQL)

Lightweight Freelancer-style MVP for influencer marketing (companies post campaigns; influencers bid; basic chat). 
Designed for shared hosting (e.g., Hostinger). No Composer required.

## Quick Deploy
1. Create a MySQL database in your hosting panel.
2. Upload this folder's contents to your hosting root (e.g., `public_html/`).
3. Import `db/schema.sql` into your database.
4. Edit `config/config.php` with your DB credentials.
5. Visit your domain. Register as a company or influencer and explore.

## Credentials & Roles
- Register new users and choose a role (company or influencer).
- Company: post campaigns, view bids, chat.
- Influencer: browse campaigns, place bids, chat.

## Notes
- Chat uses simple polling (no websockets) for shared hosting compatibility.
- Payment is a placeholder page ready to wire to Stripe/PayPal.
- This is a starter template: secure it further, add validations, CSRF tokens (basic included), and harden before production.
