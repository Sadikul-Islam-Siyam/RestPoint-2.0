<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input id="username" name="username" type="text" class="mt-1 block w-full" :value="old('username', $user->username)" required />
            <x-input-error class="mt-2" :messages="$errors->get('username')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="bio" :value="__('Bio')" />
            <textarea id="bio" name="bio" rows="3" class="mt-1 block w-full bg-white dark:bg-darkbg text-gray-900 dark:text-darktext border-gray-300 dark:border-white/5 rounded focus:ring-darkaccent focus:border-darkaccent shadow-sm text-sm" placeholder="Write a short gamer bio...">{{ old('bio', $user->bio) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
        </div>

        <div>
            <x-input-label for="avatar_file" :value="__('Avatar Upload')" />
            <input id="avatar_file" name="avatar_file" type="file" class="mt-1 block w-full text-sm text-gray-500 dark:text-darkmuted file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-darkaccent/10 file:text-darkaccent hover:file:bg-darkaccent/20 cursor-pointer" />
            <x-input-error class="mt-2" :messages="$errors->get('avatar_file')" />

            <x-input-label for="avatar_url" :value="__('Or Avatar Image URL')" class="mt-3" />
            <x-text-input id="avatar_url" name="avatar_url" type="text" class="mt-1 block w-full" :value="old('avatar', $user->avatar)" placeholder="https://example.com/avatar.png" />
            <x-input-error class="mt-2" :messages="$errors->get('avatar_url')" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <x-input-label for="steam_url" :value="__('Steam Profile URL')" />
                <x-text-input id="steam_url" name="steam_url" type="url" class="mt-1 block w-full" :value="old('steam_url', $user->steam_url)" placeholder="https://steamcommunity.com/id/..." />
                <x-input-error class="mt-2" :messages="$errors->get('steam_url')" />
            </div>
            <div>
                <x-input-label for="psn_url" :value="__('PlayStation Profile URL')" />
                <x-text-input id="psn_url" name="psn_url" type="url" class="mt-1 block w-full" :value="old('psn_url', $user->psn_url)" placeholder="https://my.playstation.com/..." />
                <x-input-error class="mt-2" :messages="$errors->get('psn_url')" />
            </div>
            <div>
                <x-input-label for="xbox_url" :value="__('Xbox Profile URL')" />
                <x-text-input id="xbox_url" name="xbox_url" type="url" class="mt-1 block w-full" :value="old('xbox_url', $user->xbox_url)" placeholder="https://xboxgamertag.com/search/..." />
                <x-input-error class="mt-2" :messages="$errors->get('xbox_url')" />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
