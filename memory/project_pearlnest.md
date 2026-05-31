---
name: project-pearlnest
description: PearlNest Uganda property brokerage website — full PHP MVC app built in lyton project
metadata:
  type: project
---

PearlNest is a Uganda-based property broker website built in `c:\xampp\htdocs\lyton`.

**Why:** User wanted a site that connects property seekers to a broker (not directly to owners), managing hostels and rentals across Kampala.

**Stack:** PHP 8 MVC (existing scaffold), MySQL (PDO), Bootstrap-free custom CSS, Vanilla JS.

**Database name:** `pearlnest` — run `http://localhost/lyton/setup.php` once to initialise.

**Admin login:** username `admin` / password `PearlNest2024`

**Key URLs:**
- Public: `http://localhost/lyton/public`
- Properties: `/property`
- Admin dashboard: `/admin/dashboard`
- Admin login: `/admin/login`
- Setup (delete after use): `/lyton/setup.php`

**Routing pattern:** `/{Controller}/{method}/{param}` → e.g. `/property/detail/5` → `PropertyController@detail(5)`

**How to apply:** When modifying or extending this project, follow the existing MVC pattern. Models extend `App\Core\Model`, Controllers extend `App\Core\Controller`. Views live in `app/Views/` and use `require partials/header.php` / `footer.php`.
