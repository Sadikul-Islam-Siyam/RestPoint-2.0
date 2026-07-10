# QuestHive — Full Build Checklist & Prompt Set (From Scratch)

Check items off as you complete them. Each section has a matching numbered prompt in Part B you can hand to Claude Code in order — build top to bottom, don't skip ahead, since later sections depend on earlier ones (migrations before models, models before controllers, controllers before AJAX, auth before roles, etc.)

---

## PART A — FEATURE CHECKLIST

### 0. Project Foundation
- [ ] Laravel 11 project created, runs on `php artisan serve`
- [ ] MySQL database created, `.env` connected, `php artisan migrate` runs clean
- [ ] Laravel Breeze installed (Blade stack)
- [ ] Tailwind dark-mode base theme applied (bg, surface, accent colors defined as CSS vars or Tailwind config)
- [ ] Base layout `layouts/app.blade.php` — nav bar, logo, search bar, notification bell, profile dropdown
- [ ] `.env.example` committed, `.env` in `.gitignore`
- [ ] Git repo initialized, first commit pushed, README started

### 1. Users & Roles
- [ ] `users` migration extended: `username` (unique), `avatar`, `bio`, `steam_url`, `psn_url`, `xbox_url`, `role` enum, `xp`, `last_active_at`
- [ ] `User` model: fillable, relationships stubbed
- [ ] Registration/login/logout via Breeze working
- [ ] Role seeded on registration (`member` default)
- [ ] `RoleMiddleware` created and registered as alias
- [ ] Admin-only route group protected by `role:admin`
- [ ] Moderator+ route group protected by `role:moderator,admin`
- [ ] Manual test: non-admin hitting `/admin/*` gets redirected with flash error

### 2. Core Database Schema (the other tables)
- [ ] `games` migration (+ `external_api_id` column)
- [ ] `game_links` migration
- [ ] `game_follows` pivot migration (composite unique)
- [ ] `categories` migration (+ `keywords` column)
- [ ] `tags` migration
- [ ] `posts` migration (+ soft deletes, indexes on `game_id`/`category_id`/`type`)
- [ ] `post_tags` pivot migration
- [ ] `comments` migration (self-referential `parent_id`, soft deletes)
- [ ] `votes` migration (polymorphic, composite unique)
- [ ] `mentions` migration
- [ ] `reports` migration (polymorphic)
- [ ] `badges` migration
- [ ] `user_badges` pivot migration
- [ ] `notifications` migration (JSON `data` column)
- [ ] `user_follows` self-referential pivot migration
- [ ] All migrations run clean, tables verified in phpMyAdmin
- [ ] All Eloquent models created with `$fillable`
- [ ] All relationships written (see Part C reference) and spot-checked in `php artisan tinker`
- [ ] `DatabaseSeeder` + factories: games, categories, tags, users, posts, comments
- [ ] `php artisan migrate:fresh --seed` works end to end

### 3. Game Library (Admin-Managed)
- [ ] `Admin\GameManagementController` resource controller (index/create/store/edit/update/destroy)
- [ ] Game create/edit Blade forms — cover art + banner upload, trailer URL, genre, platform, developer, release date
- [ ] `GameLookupService::fetchFromRawg()` — Guzzle call to RAWG API, `.env` key, try/catch
- [ ] AJAX "search game" box on admin create form → pre-fills fields via fetch, admin can still edit before saving
- [ ] Game store/purchase links CRUD (`game_links`) — add multiple per game
- [ ] Public game library page — grid of games, filter by genre/platform
- [ ] Public game hub page — cover/banner, trailer embed, stats (post count, active members, discussions this week), pinned posts, category list, member/follower list
- [ ] Follow-game button (see §7)

### 4. Category System (Auto, Invisible)
- [ ] Admin can pre-seed categories per game (`Boss Strategy`, `Walkthrough`, `Builds & Loadouts`, `Item Locations`, `Lore & Story`, `Technical / Bugs`, `General`) with keyword lists
- [ ] `AutoCategoryService::categorize()` — keyword scoring against title+body+tags
- [ ] `PostObserver::saving()` hook — runs categorization silently on create/update
- [ ] Game hub page — browse posts filtered by category
- [ ] Mod/admin "reassign category" control on a post (manual override)

### 5. Posts — Help & Discussion
- [ ] `PostController` resource controller, validated store/update
- [ ] Post type selector (help / discussion) on create form
- [ ] Help post: "Mark as Solved" action, restricted to OP, sets `is_accepted` on chosen comment + `is_solved` on post
- [ ] Green "Solved" badge shown on post card and post page
- [ ] Solved posts remain open for further replies
- [ ] Discussion post: no solved state, open-ended
- [ ] Post view count increments on view (debounced/session-guarded so refresh spam doesn't inflate it)
- [ ] Post edit/delete restricted to owner (or mod/admin)
- [ ] Pin/unpin action restricted to mod/admin
- [ ] Spoiler flag on post (`is_spoiler`) — content blurred until clicked

### 6. Comments & Interaction
- [ ] `CommentController` — store/update/destroy
- [ ] Nested replies, 2 levels deep enforced (reply-to-reply disabled in UI past depth 2)
- [ ] Rich text editor (TipTap or Trix) on post body and comment body — bold/italic/code block
- [ ] Spoiler-blur inline markup (`||text||` or toolbar button) rendered as a click-to-reveal blur
- [ ] Image upload in posts and comments (`Storage::disk('public')`, image-only validation, size limit)
- [ ] YouTube link auto-embed — regex-detect YouTube URLs in body, render as iframe
- [ ] `@mention` autocomplete while typing, parsed on save via `MentionService`, creates `Notification` row
- [ ] Quote-reply — clicking reply on a comment prefills quoted text in the editor
- [ ] Upvote-only voting on posts and comments (`Vote` polymorphic model)
- [ ] Report button on posts/comments → `reports` table entry
- [ ] Edit/delete own comment
- [ ] Comment edit/delete restricted to owner (or mod/admin)

### 7. Tags
- [ ] Free-text tag input on post create (comma or chip-style entry)
- [ ] Tags scoped per game (`tags.game_id`)
- [ ] Tag autocomplete AJAX endpoint (`GET /ajax/games/{game}/tags?q=`)
- [ ] Clickable tags → filtered post list by tag
- [ ] Popular tags sidebar widget on game hub page

### 8. Social & Community Features
- [ ] Follow/unfollow a game — `game_follows`, AJAX toggle button
- [ ] Follow/unfollow a user — `user_follows`, AJAX toggle button
- [ ] User profile page — avatar, bio, favorite games, linked accounts (Steam/PSN/Xbox), post history, comment history, badges, join date
- [ ] "Tavern Regulars" section — mutual follows shown on profile
- [ ] Activity feed on homepage — posts from followed games + followed users, paginated
- [ ] Trending posts widget on homepage (highest upvotes in last N days)

### 9. Reputation & Badges
- [ ] `badges` table seeded: Tavern Regular, Helper, Lorekeeper, [Game] Veteran, Trending Voice, Legend
- [ ] `BadgeService::checkAndAward()` — event-driven, wrapped in DB transaction
- [ ] Triggered on: post created, comment marked solved, XP threshold reached, post reaches 100 upvotes, monthly top-contributor calculation
- [ ] Badge icons displayed on profile and next to username on posts/comments
- [ ] XP increment logic tied to actions (post created, answer accepted, etc.)

### 10. Notifications
- [ ] `NotificationController` — index (Blade) + `indexJson` (AJAX)
- [ ] Notification created on: mention, reply to your post, your answer marked solved, someone follows you
- [ ] Notification bell — unread count badge, AJAX polling every 30s
- [ ] Mark-as-read on click (AJAX)
- [ ] Notification list page (non-AJAX fallback)

### 11. Moderation & Reporting
- [ ] `ReportController` — submit report (member), view queue (mod/admin)
- [ ] Moderation queue view — list open reports, resolve/dismiss actions
- [ ] Pin/unpin post (mod/admin)
- [ ] Remove post/comment (mod/admin) — soft delete, reason logged
- [ ] Category reassignment override (mod/admin)

### 12. Middleware (full set)
- [ ] `RoleMiddleware` (before) — admin/mod route gating
- [ ] `TrackActivity` (after) — updates `last_active_at` post-response
- [ ] `ThrottlePosts` (before, custom-built) — blocks rapid-fire posting, flash message on block
- [ ] `verified` (Breeze) applied if email verification required before posting
- [ ] All middleware registered in `bootstrap/app.php`, applied to correct route groups
- [ ] Manual test of each: trigger the block condition, confirm correct redirect/message

### 13. External API Integration
- [ ] RAWG API key obtained, stored in `.env`, never exposed to JS
- [ ] `GameLookupService` — Guzzle client, try/catch, JSON mapped to `games` fields
- [ ] Admin AJAX "lookup" endpoint wired to the service
- [ ] Graceful fallback to manual entry if API call fails or returns no results

### 14. Async JavaScript / AJAX Layer
- [ ] `public/js/vote.js` — fetch + async/await upvote toggle
- [ ] `public/js/solve.js` — mark-as-solved without reload
- [ ] `public/js/follow.js` — follow/unfollow toggle (game + user)
- [ ] `public/js/notifications.js` — polling + mark-as-read
- [ ] `public/js/tags.js` — tag autocomplete
- [ ] `public/js/mentions.js` — @mention autocomplete in editor
- [ ] `public/js/game-lookup.js` — admin RAWG pre-fill
- [ ] CSRF token read from `<meta name="csrf-token">` in every fetch call
- [ ] Every AJAX feature degrades gracefully (page still works if JS fails/disabled, where feasible)

### 15. Pages (full page inventory)
- [ ] Homepage — trending posts, active games, followed feed
- [ ] Game Library (browse/filter)
- [ ] Game Hub page
- [ ] Post Page (thread + comments + tags + solved badge)
- [ ] Create Post page
- [ ] Edit Post page
- [ ] User Profile page
- [ ] Notifications page
- [ ] Search results page
- [ ] Admin: Game management (list/create/edit)
- [ ] Admin: Category management
- [ ] Admin: User management (role changes, bans)
- [ ] Moderation queue page
- [ ] 404 / 403 error pages styled to match dark theme

### 16. Search
- [ ] Basic search — posts by title/body match (start with simple `LIKE` query or Laravel Scout later)
- [ ] Search filters: by game, by type (help/discussion), by solved status
- [ ] Search bar in nav wired to search results page

### 17. Polish Pass
- [ ] Dark mode palette consistent across every page (no stray light-mode Tailwind defaults)
- [ ] Reusable Blade components extracted (`x-card`, `x-badge`, `x-post-row`, `x-avatar`)
- [ ] Hover/transition states consistent (150ms, no extravagant animation)
- [ ] Mobile responsive check on all pages
- [ ] Empty states designed (no posts yet, no notifications, no followers)
- [ ] Loading states on AJAX actions (disabled button / spinner during fetch)
- [ ] Accessibility pass — contrast ratios, alt text on images, focus states

### 18. GitHub Maintenance
- [ ] `.gitignore` correct (`vendor/`, `node_modules/`, `.env`, `storage/*.key`)
- [ ] Branch per phase, merged via PR into `main`
- [ ] Commit messages follow convention (`feat:`, `fix:`, `db:`, `chore:`)
- [ ] README with setup steps + grading checklist section mapping features to files
- [ ] GitHub Project board with columns per phase, cards per feature above

---

## PART B — COMPLETE PROMPT SEQUENCE

Run these in order in Claude Code. Each corresponds to a checklist section above — after each prompt, verify the matching checkboxes before moving on.

**Prompt 0 — Foundation**
> Create a new Laravel 11 project called `questhive`. Install Laravel Breeze with the Blade stack and run the frontend build. Configure `.env` for a local MySQL database named `questhive`. Set up a Tailwind dark theme: background `#0f1115`, surface `#1a1d24`, border `border-white/5`, text `#e6e6e6` primary / `#9a9a9a` muted, accent amber `#e0a458`. Build `resources/views/layouts/app.blade.php` with a top nav containing: logo/wordmark "QuestHive", a search input, a notification bell icon (static for now), and a profile dropdown (login/register links if guest). Initialize git, create `.gitignore` for Laravel, commit.

**Prompt 1 — Users & Roles**
> Add a migration extending the `users` table with: `username` (string, unique), `avatar` (string, nullable), `bio` (text, nullable), `steam_url`, `psn_url`, `xbox_url` (string, nullable), `role` (enum: guest, member, moderator, admin — default member), `xp` (integer, default 0), `last_active_at` (timestamp, nullable). Update the `User` model's `$fillable`. Update Breeze's registration to require `username` and default `role` to `member`. Create `app/Http/Middleware/RoleMiddleware.php` with a variadic `handle(Request $request, Closure $next, ...$roles)` that redirects to `route('home')` with a flash `error` message if the user's role isn't in `$roles`. Register it as alias `role` in `bootstrap/app.php`. Create an `admin` route group prefixed `/admin` protected by `role:admin`, and confirm a non-admin gets redirected.

**Prompt 2 — Full Database Schema**
> Generate Laravel migrations for the following tables with correct foreign keys, ON DELETE CASCADE where sensible, composite unique indexes on all pivot tables, and soft deletes on `posts` and `comments`:
> [paste the schema block from QuestHive_Project_Plan.md Section 3]
> Then generate Eloquent models for each with the relationships listed in that same document (hasMany, belongsTo, belongsToMany, morphMany, self-referential for comments and user_follows). Add query scopes `scopeSolved` and `scopeForGame` to `Post`. Create a `DatabaseSeeder` that seeds: 5 `badges` rows (Tavern Regular, Helper, Lorekeeper, [Game] Veteran, Trending Voice, Legend), 5 games with realistic names/genres, 7 categories per game with keyword lists, 25 fake users via a `UserFactory`, and ~100 fake posts/comments distributed across games via `PostFactory`/`CommentFactory`. Run `migrate:fresh --seed` and confirm no errors.

**Prompt 3 — Game Library & Admin Management**
> Create `App\Services\GameLookupService` with a `fetchFromRawg(string $query): ?array` method using Guzzle to call `https://api.rawg.io/api/games` with the API key from `env('RAWG_API_KEY')`, wrapped in try/catch, returning a mapped array (name, cover_image, release_date, genre, external_api_id) or null on failure/empty results. Add `RAWG_API_KEY=` to `.env.example`. Create `App\Http\Controllers\Admin\GameManagementController` as a resource controller (index/create/store/edit/update/destroy), protected by `role:admin`. Build the create/edit Blade forms with fields for name, cover/banner image upload, trailer URL, genre, platform, developer, release date, plus a "Search RAWG" text input. Add a JSON route `GET /admin/games/lookup?q=` calling the service, and a `public/js/game-lookup.js` that fetches it and pre-fills the form fields via `async/await`, without submitting. Build the `game_links` CRUD (store name, url, icon) nested under a game.

**Prompt 4 — Public Game Pages**
> Build the public game library page (`/games`) — grid of game cards (cover image, name, genre, platform, post count), with genre/platform filter dropdowns. Build the game hub page (`/games/{game:slug}`) showing: banner + trailer YouTube embed, store/purchase links, stats (total posts, active members this week, discussions this week — computed via Eloquent aggregate queries), a follow-game button (non-functional placeholder for now, wired in a later prompt), a pinned-posts section, and category tabs/filters that filter the post list below via query string.

**Prompt 5 — Auto Categorization**
> Create `App\Services\AutoCategoryService` with a `categorize(Post $post): ?int` method: tokenize the post's title, body, and tag names, score against each of the post's game's `categories.keywords` (comma-separated) by counting matched tokens, and return the `id` of the highest-scoring category above a minimum threshold, or null. Create `App\Observers\PostObserver` with a `saving(Post $post)` method that calls this service and sets `category_id` if it's not already manually overridden. Register the observer in `AppServiceProvider::boot()`. Add a mod/admin-only "reassign category" dropdown control on the post page that updates `category_id` directly, bypassing the auto-categorizer.

**Prompt 6 — Posts CRUD (Help & Discussion)**
> Build `App\Http\Controllers\PostController` as a resource controller. The create form has a game selector, a type toggle (help/discussion radio), title, rich body (plain textarea for now), and a tag input (comma-separated for now). Validate with `required` rules and `type` in `['help','discussion']`. On store, attach tags (create-or-find per game), flash a success message, redirect to the post page. On the post show page: display type badge, solved badge (green) if `is_solved`, view count (increment once per session using a session-based "viewed posts" array to prevent refresh spam), edit/delete buttons restricted to the owner via a Blade `@can` or manual `auth()->id() === $post->user_id` check, and a pin/unpin button restricted to `role:moderator,admin`. Add "Mark as Solved" — restricted to the post owner, POST route that sets a chosen comment's `is_accepted = true` and the post's `is_solved = true`, non-AJAX version first.

**Prompt 7 — Comments & Nesting**
> Build `App\Http\Controllers\CommentController` — store/update/destroy. Support `parent_id` for nested replies, enforcing exactly 2 levels deep in the Blade template (top-level comments render their replies, but reply forms don't appear on already-nested replies). Add "quote reply" — clicking reply on a comment prefills a hidden field and shows a quoted excerpt above the textarea. Add edit/delete restricted to comment owner or mod/admin. Add a report button on posts and comments posting to a `ReportController@store` that creates a polymorphic `reports` row.

**Prompt 8 — Rich Text, Images, Embeds**
> Integrate TipTap (or Trix) into the post/comment body fields, replacing the plain textarea, submitting HTML into the `body` column. Add a spoiler-blur toolbar button that wraps selected text in a `<span class="spoiler">` rendered blurred until clicked (CSS + a small inline JS toggle). Add image upload — a file input in the editor toolbar that uploads to `Storage::disk('public')`, validated as image, max 5MB, and inserts the resulting URL as an `<img>` tag. Add YouTube auto-embed — a helper (`app/Helpers/EmbedHelper.php` or a Blade component) that regex-detects YouTube URLs in rendered body HTML and replaces them with a responsive iframe embed.

**Prompt 9 — Tags**
> Build the tag system fully: tag chip input UI on the post create form (comma-to-chip conversion via JS, no framework needed), tags scoped to `tags.game_id`, and a JSON autocomplete route `GET /ajax/games/{game}/tags?q=` returning matching existing tags for that game. Write `public/js/tags.js` using fetch + async/await to query this endpoint as the user types and show a dropdown of suggestions. Make tags clickable everywhere they appear, linking to a filtered post list (`/games/{game}/tags/{tag}`). Add a "Popular Tags" sidebar widget on the game hub page (top N tags by post count for that game).

**Prompt 10 — Voting (AJAX)**
> Create the polymorphic `Vote` model usage: `VoteController@toggleAjax` — a JSON POST route inside the `auth` middleware group at `/ajax/vote`, accepting `votable_type` and `votable_id`, using `updateOrCreate`/delete-if-exists toggle logic wrapped in a DB transaction, returning the new vote count as JSON. Write `public/js/vote.js` — fetch POST with CSRF token from a `<meta name="csrf-token">` tag, async/await, try/catch, updates the vote count in the DOM without reload, disables the button during the request. Add upvote buttons to post cards and comments wired to this script.

**Prompt 11 — Following (AJAX)**
> Build `FollowController@toggleAjax` handling both game-follows and user-follows via a `type` parameter (`game`|`user`) and a `target_id`, using the respective pivot table, wrapped in a transaction. Write `public/js/follow.js` mirroring the vote.js pattern. Wire follow buttons on the game hub page and user profile page. Add a followed-games list and followed-users list to the user's own profile.

**Prompt 12 — Mentions & Notifications**
> Create `App\Services\MentionService::parse(string $body): array` that regex-extracts `@username` tokens, resolves them to user IDs, and creates `mentions` + `notifications` rows (type `mention`), skipping self-mentions. Call it from `CommentController@store` and `PostController@store`. Also create notifications on: reply to your post (type `reply`), your comment marked solved (type `solved`), someone follows you (type `follow`). Build `NotificationController@index` (Blade page) and `NotificationController@indexJson` (JSON, unread-first, paginated). Write `public/js/notifications.js` — polls `indexJson` every 30 seconds via fetch, updates an unread-count badge on the bell icon, and a `PATCH /ajax/notifications/{id}/read` endpoint marking one as read on click.

**Prompt 13 — @Mention Autocomplete**
> Add `public/js/mentions.js` — listens for `@` typed inside the comment/post editor, debounced fetch to a `GET /ajax/users/search?q=` endpoint returning matching usernames, shows a dropdown, inserts the selected `@username` into the editor on click/enter.

**Prompt 14 — Badges & XP**
> Seed the six badges from the checklist with `condition_key` values. Build `App\Services\BadgeService::checkAndAward(User $user, string $event)` wrapped in a DB transaction — checks conditions per event (`post_created`, `comment_accepted`, `days_active`, `post_upvotes`, `monthly_top_contributor`) against the user's current stats, and inserts into `user_badges` if newly earned and not already present (idempotent). Call it from the relevant controller actions (post store, mark-solved, vote toggle reaching 100). Increment `users.xp` on the same trigger events with sensible point values. Display badge icons on the profile page and as small icons next to usernames on posts/comments.

**Prompt 15 — Middleware: Activity Tracking & Throttling**
> Create `App\Http\Middleware\TrackActivity` — an AFTER middleware (call `$next($request)` first, then update) that sets `users.last_active_at = now()` for the authenticated user after every request. Register it globally for authenticated routes. Create `App\Http\Middleware\ThrottlePosts` — a BEFORE middleware that queries `Post::where('user_id', $userId)->where('created_at', '>=', now()->subSeconds(60))->count()` and, if greater than 1, redirects back with a flash `error` message instead of calling `$next()`. Apply it to `PostController@store`. Manually test both.

**Prompt 16 — Homepage & Activity Feed**
> Build the homepage: a "Trending" section (posts ordered by vote count in the last 7 days, limit 5), an "Active Games" section (games ordered by recent post activity), and — for authenticated users — an activity feed combining posts from followed games and followed users, paginated, ordered by `created_at desc`. Use eager loading (`with('user','game','tags')`) to avoid N+1 queries; verify with `DB::enableQueryLog()` or Laravel Debugbar that the feed doesn't fire a query per post.

**Prompt 17 — User Profiles**
> Build the user profile page (`/u/{username}`) showing: avatar, bio, linked accounts (Steam/PSN/Xbox as icon links), favorite/most-active games (top games by post count for that user), post history and comment history (tabbed, paginated), earned badges, join date, and a "Tavern Regulars" section (users appearing in both `following` and `followers`, i.e. mutual follows — an Eloquent query intersecting both relationships). Add a follow-user button wired to `follow.js` from Prompt 11.

**Prompt 18 — Search**
> Build a search results page (`/search?q=`) querying posts where title or body matches the term (start with `LIKE '%term%'` across `title`/`body`, indexed appropriately, upgrade to Laravel Scout later if needed). Add filters for game, post type, and solved status as query string params combined with the search term. Wire the nav search bar to submit here.

**Prompt 19 — Moderation & Reporting**
> Build `App\Http\Controllers\ReportController` — `store` (member-facing, already partially built in Prompt 7) and `index`/`update` (mod/admin-facing moderation queue), protected by `role:moderator,admin`. The queue page lists open reports with a link to the reported content, and resolve/dismiss action buttons that update `reports.status`. Add pin/unpin buttons (mod/admin only) on posts, and a soft-delete "remove" action on posts/comments with a required reason, logged (you can reuse the `reports` table or add a lightweight `moderation_logs` table if you prefer a clean audit trail).

**Prompt 20 — Admin User Management**
> Build `App\Http\Controllers\Admin\UserManagementController` (mod/admin protected, role changes restricted to admin only) — list all users with search/filter by role, a role-change dropdown per user (admin-only), and a ban/suspend toggle (add an `is_banned` boolean + `banned_at` to `users` if not already present; banned users blocked from posting via a check in `ThrottlePosts` or a new lightweight `EnsureNotBanned` middleware).

**Prompt 21 — Error Pages & Empty States**
> Create styled `resources/views/errors/404.blade.php` and `403.blade.php` matching the dark theme. Add empty-state Blade partials for: no posts yet on a game page, no notifications, no followers/following, no search results — each with a short friendly message and a relevant call-to-action link.

**Prompt 22 — Final Polish Pass**
> Audit every page for dark-mode consistency (no stray white backgrounds), extract repeated markup into Blade components (`x-card`, `x-badge`, `x-post-row`, `x-avatar`, `x-vote-button`), add consistent 150ms transition classes to hover/AJAX-updated elements, add loading/disabled states to every AJAX-triggered button, and do a responsive pass at mobile width on the homepage, game hub, post page, and profile page.

**Prompt 23 — README & GitHub**
> Write a `README.md` covering: project description, setup steps (`composer install`, `.env` config including `RAWG_API_KEY`, `php artisan migrate --seed`, `npm install && npm run dev`, `php artisan serve`), and a "Grading Checklist" section explicitly listing: Database (link to migrations/models folders), Middleware (link to `RoleMiddleware`, `TrackActivity`, `ThrottlePosts`), API call (link to `GameLookupService`), Async JS (link to `public/js/*.js` files) — with one sentence each on what they do.

---

## PART C — Quick Relationship Reference

Keep this open while running Prompt 2 to sanity-check the generated models:

```
User        hasMany Post, hasMany Comment, belongsToMany Game (via game_follows),
            belongsToMany Badge (via user_badges, withPivot earned_at),
            belongsToMany User as followers/following (via user_follows)

Game        hasMany Post, hasMany Category, hasMany Tag, hasMany GameLink,
            belongsToMany User as followers (via game_follows)

Post        belongsTo User, belongsTo Game, belongsTo Category,
            belongsToMany Tag (via post_tags), hasMany Comment,
            morphMany Vote as votable

Comment     belongsTo Post, belongsTo User, belongsTo Comment as parent,
            hasMany Comment as replies, morphMany Vote as votable

Vote        belongsTo User, morphTo votable

Tag         belongsTo Game, belongsToMany Post (via post_tags)

Badge       belongsToMany User (via user_badges)

Report      belongsTo User as reporter, morphTo reportable

Notification belongsTo User
```
