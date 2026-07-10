# QuestHive — The Gamers' Tavern
### Reshaped Build Plan (Grading-Aligned Edition)

---

## 0. Why the reshape

Your grading weights map cleanly onto specific technical decisions, not vibes:

| Weight | Criterion | What it actually means | Where it lives in your labs |
|---|---|---|---|
| 40% | Database | Real Eloquent relationships (1-to-many, many-to-many, polymorphic), migrations with proper FKs/indexes, seeders/factories, query scopes, eager loading | Lab 10 (CRUD/Eloquent) |
| — | Middleware | Custom `handle()` classes with before *and* after logic, role gating, registered as aliases | Lab 9 §3.1 |
| — | API call | A genuine outbound HTTP call to a third-party JSON API via Guzzle, hidden API key, try/catch error handling | Lab 12 §6.4 (Guzzle + OpenWeather) |
| — | Async JS | `fetch`, `async/await`, JSON endpoints, DOM updates without reload — layered *on top of* normal Blade routes, not replacing them | Lab 12 §6.5–6.8 |

Everything below is QuestHive's original feature set, just wired so each of those four things is unmistakable to a grader — not buried, not accidental.

---

## 1. What changed from your original spec

- **"AI silent categorization" → rule-based classifier**, upgraded with an optional real API call. True ML categorization is out of scope for a course project and contradicts "no AI, everything human." Instead:
  - **Core (counts toward Database):** a `AutoCategoryService` that scores each post against per-game keyword sets pulled from the `categories` table (title/body/tag token matching, weighted). Deterministic, explainable, no external cost.
  - **Stretch (counts toward API call):** admin can flip a `.env` flag to route categorization through a real external API instead — this becomes your second candidate for the API requirement if you want redundancy.
- **Game creation gets a real external API call.** When an admin adds a game, instead of typing genre/platform/cover art by hand, `GameManagementController` calls **RAWG.io's Video Games API** (free tier, one API key) via Guzzle — same pattern as the weather app: server-side call, key in `.env`, JSON parsed, mapped into your `games` table, try/catch on failure with a manual-entry fallback. This is your **primary, unambiguous API-call deliverable**.
- **Everything "live" becomes an AJAX endpoint**, matching the weather app's dual pattern (normal Blade route stays working, AJAX route enhances it):
  - Upvote button → `fetch` POST, updates count in place, no reload
  - Mark-as-solved → `fetch` PATCH, badge appears instantly
  - Follow/unfollow game or user → `fetch` toggle
  - Notification bell → polls a JSON endpoint every 30s
  - Tag input → `fetch` autocomplete against existing tags for that game
  - Comment submit → optional AJAX post, falls back to normal form
- **Roles become real middleware**, not just `if ($user->role === 'admin')` scattered in controllers.

Nothing else about your original vision changes — same tables, same badges, same tavern branding, same phases. This is wiring, not redesign.

---

## 2. UI direction (dark, elegant, restrained)

Keep this simple enough to actually finish:

- **Palette:** near-black background (`#0f1115` / `#14161b`), slightly lighter surface cards (`#1a1d24`), one warm accent (amber/ember `#e0a458` or similar — fits "tavern") used *sparingly* for buttons/links/badges only. Text: `#e6e6e6` primary, `#9a9a9a` muted.
- **Typography:** one clean sans (Inter or system-ui) for UI, optionally one serif/display face only for the site logo/wordmark ("QuestHive"). No more than 2 font weights per page.
- **Components:** Tailwind, flat cards with 1px hairline borders (`border-white/5`) instead of heavy shadows — that's what makes dark mode feel elegant instead of muddy. No gradients, no glow effects, no extravagant animation — just a 150ms opacity/transform transition on hover and on AJAX-driven DOM swaps (fade the vote count, don't just snap it).
- **Layout:** Blade components (`x-card`, `x-badge`, `x-post-row`) so the "simple and smooth" feel is enforced structurally, not by discipline alone.

---

## 3. Database Design (the 40%)

This is where you should spend the most deliberate effort — correct types, correct constraints, correct relationship shape.

```
users
  id, username (unique), email (unique), password, avatar,
  bio, steam_url, psn_url, xbox_url,
  role ENUM('guest','member','moderator','admin') default 'member',
  xp INT default 0, last_active_at TIMESTAMP NULL,
  email_verified_at, remember_token, timestamps

games
  id, name, slug (unique, indexed), cover_image, banner_image,
  trailer_url, genre, platform, developer, release_date,
  external_api_id NULLABLE (RAWG id, for re-sync),
  created_by FK->users, timestamps

game_links
  id, game_id FK, store_name, url, icon, timestamps

game_follows            (pivot, composite unique[user_id, game_id])
  user_id FK, game_id FK, timestamps

categories
  id, game_id FK, name, slug, keywords TEXT (comma list, feeds AutoCategoryService),
  timestamps
  UNIQUE(game_id, slug)

tags
  id, game_id FK, name, slug, timestamps
  UNIQUE(game_id, slug)

posts
  id, user_id FK, game_id FK, category_id FK NULLABLE,
  type ENUM('help','discussion'),
  title, body LONGTEXT, is_solved BOOL default false,
  is_pinned BOOL default false, is_spoiler BOOL default false,
  views UINT default 0, timestamps, SOFT DELETES
  INDEX(game_id, category_id), INDEX(type, is_solved)

post_tags               (pivot, composite unique[post_id, tag_id])
  post_id FK, tag_id FK

comments
  id, post_id FK, user_id FK, parent_id FK->comments NULLABLE (self-ref, 2-level enforced in app logic),
  body TEXT, is_accepted BOOL default false, timestamps, SOFT DELETES
  INDEX(post_id, parent_id)

votes                   (polymorphic, composite unique[user_id, votable_type, votable_id])
  id, user_id FK, votable_type, votable_id, timestamps

mentions
  id, comment_id FK, mentioned_user_id FK, timestamps

reports
  id, reporter_id FK->users, reportable_type, reportable_id,
  reason, status ENUM('open','resolved','dismissed') default 'open', timestamps

badges
  id, name, icon, description, condition_key (unique), timestamps

user_badges              (pivot, composite unique[user_id, badge_id])
  user_id FK, badge_id FK, earned_at

notifications
  id, user_id FK, type, data JSON, read_at NULLABLE, timestamps
  INDEX(user_id, read_at)

user_follows              (self-referential pivot, composite unique[follower_id, following_id])
  follower_id FK->users, following_id FK->users, timestamps
```

**Eloquent relationships to actually implement (this is what's graded, not just the tables):**

```php
// Game.php
public function posts() { return $this->hasMany(Post::class); }
public function categories() { return $this->hasMany(Category::class); }
public function tags() { return $this->hasMany(Tag::class); }
public function followers() { return $this->belongsToMany(User::class, 'game_follows'); }

// Post.php
public function user() { return $this->belongsTo(User::class); }
public function game() { return $this->belongsTo(Game::class); }
public function category() { return $this->belongsTo(Category::class); }
public function tags() { return $this->belongsToMany(Tag::class, 'post_tags'); }
public function comments() { return $this->hasMany(Comment::class); }
public function votes() { return $this->morphMany(Vote::class, 'votable'); }
public function scopeSolved($q) { return $q->where('is_solved', true); }
public function scopeForGame($q, $gameId) { return $q->where('game_id', $gameId); }

// Comment.php — self-referential nesting
public function replies() { return $this->hasMany(Comment::class, 'parent_id'); }
public function parent() { return $this->belongsTo(Comment::class, 'parent_id'); }

// User.php
public function followedGames() { return $this->belongsToMany(Game::class, 'game_follows'); }
public function followers() { return $this->belongsToMany(User::class, 'user_follows', 'following_id', 'follower_id'); }
public function following() { return $this->belongsToMany(User::class, 'user_follows', 'follower_id', 'following_id'); }
public function badges() { return $this->belongsToMany(Badge::class, 'user_badges')->withPivot('earned_at'); }
```

Use `Vote::updateOrCreate()` inside a **DB transaction** for vote toggling (prevents double-vote race conditions), and wrap `BadgeService::checkAndAward()` in a transaction too — this is the kind of detail that separates "tables exist" from "database logic is professional."

Seed everything: a `DatabaseSeeder` with a few games, categories per game, ~30 fake users via factories, posts/comments via factories with realistic `faker` text, so your demo isn't an empty shell.

---

## 4. Middleware Stack

Registered in `bootstrap/app.php` (Laravel 11/12 style) or `Kernel.php` if you're on Laravel 10, per Lab 9 §3.1.2.

| Middleware | Type | Logic |
|---|---|---|
| `auth` | Breeze built-in | Gate all posting/commenting/voting routes |
| `role:admin` | Custom, before | Blocks non-admins from `/admin/*`, redirects with flash error |
| `role:moderator,admin` | Custom, before | Gates pin/remove/report-resolution routes |
| `track.activity` | Custom, **after** | Updates `users.last_active_at` after every authenticated request — mirrors the lab's "modify response after controller" pattern, feeds the Tavern Regular badge |
| `throttle.posts` | Custom, before | Counts a user's posts in the last 60s from the DB before allowing `PostController@store`; redirects back with a flash message if exceeded — deliberately built by hand (not Laravel's built-in throttle) so it's clearly your middleware work, and it's real spam protection |
| `verified` | Breeze built-in | Optional, if you want email verification before posting |

```php
// app/Http/Middleware/RoleMiddleware.php
public function handle(Request $request, Closure $next, ...$roles): Response
{
    if (!$request->user() || !in_array($request->user()->role, $roles)) {
        return redirect()->route('home')->with('error', 'Not authorized.');
    }
    return $next($request);
}
```

```php
// app/Http/Middleware/TrackActivity.php — AFTER middleware example
public function handle(Request $request, Closure $next): Response
{
    $response = $next($request);
    if ($request->user()) {
        $request->user()->update(['last_active_at' => now()]);
    }
    return $response;
}
```

---

## 5. External API Integration (the API-call requirement)

**Primary: RAWG Video Games Database API**, called from `Admin\GameManagementController@store`, exactly mirroring `WeatherController@getWeatherJson`:

```php
// app/Services/GameLookupService.php
public function fetchFromRawg(string $query): ?array
{
    $apiKey = env('RAWG_API_KEY');
    $client = new \GuzzleHttp\Client();
    try {
        $response = $client->get("https://api.rawg.io/api/games", [
            'query' => ['key' => $apiKey, 'search' => $query, 'page_size' => 1],
        ]);
        $data = json_decode($response->getBody(), true);
        if (empty($data['results'])) return null;
        $game = $data['results'][0];
        return [
            'name' => $game['name'],
            'cover_image' => $game['background_image'] ?? null,
            'release_date' => $game['released'] ?? null,
            'genre' => collect($game['genres'] ?? [])->pluck('name')->join(', '),
            'external_api_id' => $game['id'],
        ];
    } catch (\Exception $e) {
        return null; // controller falls back to manual form fields
    }
}
```

Key stays server-side in `.env`, never touches JavaScript — same rule the lab enforces for the OpenWeather key. Admin form pre-fills from this, then can still edit before saving (keeps "admin-controlled quality" from your original philosophy).

---

## 6. Async JavaScript Endpoints (the AJAX requirement)

Every one of these follows the exact shape from Lab 12 §6.5–6.6: a JSON-returning controller method, a named route inside the `auth` group, and a `fetch`-based handler in a dedicated JS file — normal Blade page still works if JS is disabled.

| Feature | Route | JS file |
|---|---|---|
| Upvote toggle | `POST /ajax/vote` → `VoteController@toggleAjax` | `public/js/vote.js` |
| Mark solved | `PATCH /ajax/posts/{post}/solve` | `public/js/solve.js` |
| Follow/unfollow | `POST /ajax/follow` | `public/js/follow.js` |
| Notification poll | `GET /ajax/notifications` (polled every 30s) | `public/js/notifications.js` |
| Tag autocomplete | `GET /ajax/games/{game}/tags?q=` | `public/js/tags.js` |

```js
// public/js/vote.js — same skeleton as async-weather.js
document.querySelectorAll('.vote-btn').forEach(btn => {
  btn.addEventListener('click', async (e) => {
    e.preventDefault();
    const url = btn.dataset.url;
    btn.disabled = true;
    try {
      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'Accept': 'application/json',
        },
        body: JSON.stringify({ votable_type: btn.dataset.type, votable_id: btn.dataset.id }),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.error || 'Vote failed.');
      btn.querySelector('.count').textContent = data.count;
    } catch (err) {
      console.error(err.message);
    } finally {
      btn.disabled = false;
    }
  });
});
```

---

## 7. Build Phases (unchanged timeline, reshaped content)

### Phase 1 — The Tavern Opens (6–7 weeks)
1. Laravel install, MySQL connect, `.env` setup (Lab 9 §2)
2. Breeze auth scaffold + `role` column + `RoleMiddleware`
3. Migrations for all core tables above, run + verify in phpMyAdmin
4. Models + relationships (section 3)
5. `Admin\GameManagementController` — resource controller + RAWG API integration
6. `PostController`, `CommentController` — full CRUD, resource routes, `@csrf`, `@method`, flash messages (Lab 10 pattern throughout)
7. Tag system + pivot wiring
8. Upvote-only voting, polymorphic `Vote` model
9. Solved-marking logic + green badge

### Phase 2 — Community Comes Alive (3–4 weeks)
1. `game_follows` / `user_follows` toggle logic + AJAX versions
2. `MentionService` — regex-parse `@username`, create `Notification` rows
3. `NotificationController` + AJAX polling endpoint
4. User profile pages (Eloquent eager-loading: posts, comments, badges)
5. `BadgeService` — event-driven checks (on comment accepted, on post created, on XP threshold), wrapped in DB transactions
6. Activity feed — union query across followed games + followed users, paginated

### Phase 3 — Polish & Power (2–3 weeks)
1. `AutoCategoryService` — keyword scoring against `categories.keywords`, triggered from `PostObserver::saving()`
2. TipTap or Trix rich text, spoiler-blur CSS component
3. Image upload (`Storage::disk('public')`), YouTube embed parsing (regex → iframe)
4. `throttle.posts` and `track.activity` middleware
5. `ReportController` + moderation queue view
6. Full dark-mode Tailwind pass — component extraction, accessibility contrast check

---

## 8. GitHub Maintenance Plan

- **Repo structure:** standard Laravel `.gitignore` (never commit `.env`, `vendor/`, `node_modules/`). Commit `.env.example` with dummy `RAWG_API_KEY=`.
- **Branches:** `main` (stable/demo-ready) + one branch per phase (`phase-1-core`, `phase-2-social`, `phase-3-polish`), merged via PR into `main` at each phase boundary — gives you a visible, gradeable commit history that maps to your own phase plan.
- **Commit convention:** `feat(posts): add solved-marking logic`, `fix(middleware): correct role redirect`, `db(migration): add votes polymorphic table` — makes the 40% database work traceable in `git log` alone.
- **README.md:** setup steps (composer install, `.env` config, `php artisan migrate --seed`, `npm install && npm run dev`), ERD image, and a "Grading Checklist" section explicitly linking each requirement (Database / Middleware / API / Async JS) to the file(s) that satisfy it — makes your grader's job easy, which tends to help you.
- **Issues/Projects:** one GitHub Project board with columns `Phase 1 / 2 / 3`, cards per feature from the tables above — commit messages reference issue numbers (`#12`).

---

## 9. Prompts to drive the actual build

Use these one at a time, in order, in Claude Code (or this chat) — each is scoped to a single deliverable so the output stays reviewable instead of a giant unauditable dump.

**Prompt 1 — Project skeleton**
> Set up a fresh Laravel 11 project called `questhive`. Install Laravel Breeze with Blade stack. Configure `.env` for MySQL. Set the color scheme to dark mode using Tailwind (near-black background `#0f1115`, surface `#1a1d24`, amber accent `#e0a458`). Create a base `layouts/app.blade.php` with a top nav (logo, search, notifications bell, profile dropdown) styled per this palette. Don't add any feature logic yet — just the shell.

**Prompt 2 — Database layer**
> Using this schema [paste Section 3 of this doc], generate all Laravel migrations with correct foreign keys, unique composite indexes on pivot tables, soft deletes where noted, and an ENUM for `users.role`. Then generate the Eloquent models with the relationships listed, plus a `DatabaseSeeder` that creates 5 games, 5 categories per game, 25 fake users via factory, and ~100 fake posts/comments distributed across them.

**Prompt 3 — Role middleware**
> Create a `RoleMiddleware` matching the pattern in Lab 9 §3.1 — accepts variadic roles, redirects unauthorized users to `home` with a flash `error` message. Register it as alias `role` in `bootstrap/app.php`. Apply `role:admin` to a new `Admin` route group and `role:moderator,admin` to pin/remove/report routes.

**Prompt 4 — Game management + RAWG API**
> Build `Admin\GameManagementController` as a resource controller. Add `GameLookupService::fetchFromRawg()` using Guzzle exactly like the OpenWeather integration in Lab 12 §6.4 — API key from `.env`, try/catch, JSON mapped to game fields. The admin "create game" form has a search-by-name field that AJAX-calls a `GET /admin/games/lookup?q=` JSON endpoint, pre-fills the form client-side with fetch/async-await, and the admin can still edit before submitting.

**Prompt 5 — Posts & comments CRUD**
> Build `PostController` and `CommentController` as resource controllers following the Student CRUD pattern from Lab 10 (validate → create → flash message → redirect; `@csrf` and `@method('put')`/`@method('delete')` in Blade forms). Support `type` (help/discussion), nested comments to 2 levels via `parent_id`, and `is_solved` toggling restricted to the post owner.

**Prompt 6 — Auto-categorization**
> Create `AutoCategoryService::categorize(Post $post): ?int` that scores the post's title+body+tag names against each `Category`'s `keywords` column (simple token overlap scoring) and returns the best-matching `category_id`, or null if no category scores above a threshold. Hook it into a `PostObserver::saving()` so it runs silently before every post save, invisible to the user.

**Prompt 7 — Voting, following, notifications (AJAX layer)**
> Add `VoteController@toggleAjax`, `FollowController@toggleAjax`, and `NotificationController@indexJson` as JSON-only endpoints inside the `auth` middleware group. Then write `public/js/vote.js`, `follow.js`, and `notifications.js` using fetch + async/await + try/catch, matching the structure of Lab 12's `async-weather.js` — no page reload, CSRF token read from a meta tag, errors shown inline instead of thrown to console only.

**Prompt 8 — Badges & throttle middleware**
> Build `BadgeService::checkAndAward(User $user, string $event)` wrapped in a DB transaction, called after post/comment creation and solved-marking events. Then build a custom `ThrottlePosts` middleware (not Laravel's built-in throttle) that queries the user's posts from the last 60 seconds and blocks with a flash message if more than 1 — apply it to `PostController@store`.

**Prompt 9 — Polish pass**
> Add TipTap rich text to the post/comment body fields, image upload to posts (`Storage::disk('public')`, validated to images only, max 5MB), and automatic YouTube embed — detect a YouTube URL pasted into body text and replace it with an iframe embed on render. Keep all of this inside the existing dark-mode component system, no new colors introduced.

---

Keep this file in your repo root as `PLAN.md` — reference it in commit messages and your README's grading checklist section.
