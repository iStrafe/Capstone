{{-- Uses the site's own head and navigation like the other user pages. The Breeze partials
     below are styled by resources/css/profile.css: Tailwind utilities scoped to .profile-page,
     without preflight, so the Bootstrap navbar is untouched. The scripts partial loads Vite
     (Alpine), which opens the Delete Account confirmation. --}}
@include('scripts')
@include('Navigationbar')
@vite('resources/css/profile.css')

<style>
    /* Text inputs, which Tailwind leaves unstyled without its forms plugin */
    .profile-page input[type=text], .profile-page input[type=email], .profile-page input[type=password] {
        display: block; width: 100%; padding: .375rem .75rem; border: 1px solid #ced4da; border-radius: .375rem;
    }
</style>

<div class="profile-page container py-4">
    <h2 class="mb-4">{{ __('Profile') }}</h2>

    <div class="p-4 mb-4 bg-white shadow-sm rounded">
        <div style="max-width: 36rem">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    <div class="p-4 mb-4 bg-white shadow-sm rounded">
        <div style="max-width: 36rem">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    <div class="p-4 mb-4 bg-white shadow-sm rounded">
        <div style="max-width: 36rem">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
