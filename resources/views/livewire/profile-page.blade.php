<div class="min-h-screen bg-slate-50 py-8 sm:py-12">
    <div class="mx-auto w-[calc(100%-32px)] max-w-[1150px]">
        <nav class="mb-6 flex items-center gap-2 text-sm text-slate-500" aria-label="Breadcrumb">
            <a wire:navigate href="{{ route('dashboard') }}" class="font-semibold hover:text-[#EC0205]">Dashboard</a>
            <i class="bi bi-chevron-right text-xs"></i><span class="font-semibold text-slate-800">Edit profile</span>
        </nav>

        <div class="mb-6 rounded-[28px] bg-gradient-to-r from-[#091b38] to-[#0C1E8C] px-6 py-7 text-white shadow-lg sm:px-8">
            <p class="text-xs font-bold uppercase tracking-[.2em] text-red-400">Account settings</p>
            <h1 class="mt-2 text-2xl font-extrabold sm:text-3xl">Complete your profile</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-white/70">Keep your personal and contact information current so we can provide a better shopping experience and support.</p>
        </div>

        <form wire:submit="save" class="space-y-6">
            <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                <div class="flex flex-col gap-5 border-b border-slate-100 pb-6 sm:flex-row sm:items-center">
                    <div class="relative h-24 w-24 shrink-0">
                        @if($avatarUpload)
                            <img src="{{ $avatarUpload->temporaryUrl() }}" alt="New profile photo preview" class="h-24 w-24 rounded-3xl object-cover">
                        @elseif($user->avatar)
                            <img src="{{ Storage::disk('public')->url($user->avatar) }}" alt="{{ $user->name }}" class="h-24 w-24 rounded-3xl object-cover">
                        @else
                            <span class="grid h-24 w-24 place-items-center rounded-3xl bg-blue-50 text-3xl font-extrabold text-[#0C1E8C]">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        @endif
                        <label for="profile-avatar" class="absolute -bottom-2 -right-2 grid h-9 w-9 cursor-pointer place-items-center rounded-xl border-2 border-white bg-[#EC0205] hover:bg-[#C90204] text-white shadow" title="Change profile photo"><i class="bi bi-camera"></i></label>
                        <input id="profile-avatar" type="file" wire:model="avatarUpload" accept="image/png,image/jpeg,image/webp" class="sr-only">
                    </div>
                    <div><h2 class="text-xl font-extrabold text-slate-900">Personal information</h2><p class="mt-1 text-sm leading-6 text-slate-500">Upload a JPG, PNG, or WebP image up to 2 MB.</p><p wire:loading wire:target="avatarUpload" class="mt-1 text-xs font-semibold text-[#0C1E8C]">Uploading preview…</p>@error('avatarUpload')<p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror</div>
                </div>

                <div class="mt-6 grid gap-5 md:grid-cols-2">
                    @foreach([
                        ['name', 'Full name', 'text', 'Your full name', 'person'],
                        ['email', 'Email address', 'email', 'you@example.com', 'envelope'],
                        ['phone', 'Phone number', 'tel', '+977 9812345678', 'telephone'],
                        ['date_of_birth', 'Date of birth', 'date', '', 'calendar3'],
                    ] as [$field, $label, $type, $placeholder, $icon])
                        <div>
                            <label for="profile-{{ $field }}" class="mb-2 block text-sm font-bold text-slate-700">{{ $label }} @if(in_array($field, ['name', 'email', 'phone']))<b class="text-red-500">*</b>@endif</label>
                            <div class="relative"><i class="bi bi-{{ $icon }} absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i><input id="profile-{{ $field }}" type="{{ $type }}" wire:model="{{ $field }}" placeholder="{{ $placeholder }}" class="h-12 w-full rounded-xl border border-slate-300 bg-white pl-11 pr-4 text-sm text-slate-800 outline-none transition focus:border-[#0C1E8C] focus:ring-4 focus:ring-blue-100"></div>
                            @error($field)<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                        </div>
                    @endforeach
                    <div>
                        <label for="profile-gender" class="mb-2 block text-sm font-bold text-slate-700">Gender</label>
                        <select id="profile-gender" wire:model="gender" class="h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm text-slate-800 outline-none focus:border-[#0C1E8C] focus:ring-4 focus:ring-blue-100"><option value="">Select gender</option><option value="female">Female</option><option value="male">Male</option><option value="other">Other</option><option value="prefer_not_to_say">Prefer not to say</option></select>
                        @error('gender')<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">Account role</label>
                        <div class="flex h-12 items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 text-sm font-semibold text-slate-600"><i class="bi bi-shield-check text-[#0C1E8C]"></i>{{ $roleLabel }} <span class="ml-auto text-xs font-normal text-slate-400">Managed by admin</span></div>
                    </div>
                </div>
            </section>

            <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                <div><h2 class="text-xl font-extrabold text-slate-900">Contact & address</h2><p class="mt-1 text-sm text-slate-500">Used for communication and order delivery.</p></div>
                <div class="mt-6 grid gap-5 md:grid-cols-2">
                    @foreach([
                        ['address', 'Street address', 'Street, area, or ward', 'house', 'md:col-span-2'],
                        ['city', 'City', 'Bhairahawa', 'geo-alt', ''],
                        ['state', 'Province / State', 'Lumbini Province', 'map', ''],
                        ['postal_code', 'Postal code', 'Postal code', 'mailbox', ''],
                    ] as [$field, $label, $placeholder, $icon, $span])
                        <div class="{{ $span }}"><label for="profile-{{ $field }}" class="mb-2 block text-sm font-bold text-slate-700">{{ $label }}</label><div class="relative"><i class="bi bi-{{ $icon }} absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i><input id="profile-{{ $field }}" type="text" wire:model="{{ $field }}" placeholder="{{ $placeholder }}" class="h-12 w-full rounded-xl border border-slate-300 pl-11 pr-4 text-sm outline-none focus:border-[#0C1E8C] focus:ring-4 focus:ring-blue-100"></div>@error($field)<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror</div>
                    @endforeach
                </div>
            </section>

            <section class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
                <div><h2 class="text-xl font-extrabold text-slate-900">Emergency contact</h2><p class="mt-1 text-sm text-slate-500">Someone we can contact if necessary.</p></div>
                <div class="mt-6 grid gap-5 md:grid-cols-3">
                    @foreach([
                        ['emergency_contact_name', 'Contact name', 'Full name', 'person'],
                        ['emergency_contact_relationship', 'Relationship', 'Parent, spouse…', 'people'],
                        ['emergency_contact_phone', 'Phone number', '+977 9812345678', 'telephone'],
                    ] as [$field, $label, $placeholder, $icon])
                        <div><label for="profile-{{ $field }}" class="mb-2 block text-sm font-bold text-slate-700">{{ $label }}</label><div class="relative"><i class="bi bi-{{ $icon }} absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i><input id="profile-{{ $field }}" type="text" wire:model="{{ $field }}" placeholder="{{ $placeholder }}" class="h-12 w-full rounded-xl border border-slate-300 pl-11 pr-4 text-sm outline-none focus:border-[#0C1E8C] focus:ring-4 focus:ring-blue-100"></div>@error($field)<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror</div>
                    @endforeach
                </div>
            </section>

            @if($user->role === 'student')
                <section class="rounded-3xl border border-slate-200 bg-gradient-to-br from-white to-blue-50/20 p-5 shadow-sm sm:p-7">
                    <div><p class="text-xs font-bold uppercase tracking-[.18em] text-[#0C1E8C]">Student details</p><h2 class="mt-2 text-xl font-extrabold text-slate-900">Guardian & education</h2></div>
                    <div class="mt-6 grid gap-5 md:grid-cols-2">
                        @foreach([
                            ['guardian_name', 'Guardian name', 'Parent or guardian name'], ['guardian_phone', 'Guardian phone', '+977 9812345678'],
                            ['education_level', 'Education level', 'Highest education completed'], ['institution', 'School / institution', 'Institution name'],
                        ] as [$field, $label, $placeholder])
                            <div><label for="profile-{{ $field }}" class="mb-2 block text-sm font-bold text-slate-700">{{ $label }}</label><input id="profile-{{ $field }}" type="text" wire:model="{{ $field }}" placeholder="{{ $placeholder }}" class="h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm outline-none focus:border-[#0C1E8C] focus:ring-4 focus:ring-blue-100">@error($field)<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror</div>
                        @endforeach
                    </div>
                    @if($user->trainer)<p class="mt-5 rounded-2xl bg-white/80 p-4 text-sm text-slate-600"><i class="bi bi-person-badge mr-2 text-[#0C1E8C]"></i>Assigned trainer: <strong>{{ $user->trainer->name }}</strong></p>@endif
                </section>
            @endif

            @if(in_array($user->role, ['super_admin', 'admin', 'trainer', 'staff'], true))
                <section class="rounded-3xl border border-[#d9dde5] bg-white p-5 shadow-sm sm:p-7">
                    <div><p class="text-xs font-bold uppercase tracking-[.18em] text-slate-500">Staff details</p><h2 class="mt-2 text-xl font-extrabold text-slate-900">Employment information</h2></div>
                    <div class="mt-6 grid gap-5 md:grid-cols-2">
                        @foreach([
                            ['employee_id', 'Employee ID', 'MTG-001', 'text'], ['job_title', 'Job title', 'Store associate', 'text'],
                            ['department', 'Department', 'Operations', 'text'], ['joining_date', 'Joining date', '', 'date'],
                        ] as [$field, $label, $placeholder, $type])
                            <div><label for="profile-{{ $field }}" class="mb-2 block text-sm font-bold text-slate-700">{{ $label }}</label><input id="profile-{{ $field }}" type="{{ $type }}" wire:model="{{ $field }}" placeholder="{{ $placeholder }}" class="h-12 w-full rounded-xl border border-slate-300 px-4 text-sm outline-none focus:border-[#0C1E8C] focus:ring-4 focus:ring-blue-100">@error($field)<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror</div>
                        @endforeach
                        <div><label for="profile-employment-type" class="mb-2 block text-sm font-bold text-slate-700">Employment type</label><select id="profile-employment-type" wire:model="employment_type" class="h-12 w-full rounded-xl border border-slate-300 bg-white px-4 text-sm outline-none focus:border-[#0C1E8C] focus:ring-4 focus:ring-blue-100"><option value="">Select type</option><option value="full_time">Full time</option><option value="part_time">Part time</option><option value="contract">Contract</option><option value="intern">Intern</option></select>@error('employment_type')<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror</div>
                    </div>
                </section>
            @endif

            <div class="sticky bottom-4 z-20 flex items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white/95 p-4 shadow-xl backdrop-blur-sm">
                <a wire:navigate href="{{ route('dashboard') }}" class="rounded-xl px-4 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-100">Cancel</a>
                <button type="submit" wire:loading.attr="disabled" wire:target="save,avatarUpload" class="inline-flex items-center gap-2 rounded-xl bg-[#EC0205] hover:bg-[#C90204] px-6 py-3 text-sm font-bold text-white shadow-md transition disabled:cursor-wait disabled:opacity-60"><span wire:loading.remove wire:target="save">Save profile</span><span wire:loading wire:target="save"><i class="bi bi-arrow-repeat animate-spin"></i> Saving…</span></button>
            </div>
        </form>

        <form wire:submit="updatePassword" class="mt-6 rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
            <div class="flex items-start gap-4">
                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-blue-50 text-[#0C1E8C]"><i class="bi bi-shield-lock"></i></span>
                <div><h2 class="text-xl font-extrabold text-slate-900">Change password</h2><p class="mt-1 text-sm leading-6 text-slate-500">Use at least 8 characters with uppercase, lowercase, number, and symbol.</p></div>
            </div>

            <div class="mt-6 grid gap-5 lg:grid-cols-3">
                @foreach([
                    ['current_password', 'Current password', 'current-password'],
                    ['new_password', 'New password', 'new-password'],
                    ['new_password_confirmation', 'Confirm new password', 'new-password'],
                ] as [$field, $label, $autocomplete])
                    <div>
                        <label for="profile-{{ $field }}" class="mb-2 block text-sm font-bold text-slate-700">{{ $label }} <b class="text-red-500">*</b></label>
                        <div class="kba-auth-input-wrap kba-auth-password-wrap" x-data="{ showPassword: false }">
                            <i class="bi bi-lock"></i>
                            <input :type="showPassword ? 'text' : 'password'" id="profile-{{ $field }}" wire:model="{{ $field }}" autocomplete="{{ $autocomplete }}" required>
                            <button type="button" class="kba-password-toggle" @click="showPassword = ! showPassword" :aria-label="showPassword ? 'Hide password' : 'Show password'" :title="showPassword ? 'Hide password' : 'Show password'"><i class="bi" :class="showPassword ? 'bi-eye-slash' : 'bi-eye'"></i></button>
                        </div>
                        @error($field)<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>
                @endforeach
            </div>

            <div class="mt-6 flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-center sm:justify-between">
                <a wire:navigate href="{{ route('forgot-password') }}" class="text-sm font-semibold text-[#0C1E8C] hover:underline">Forgot your current password?</a>
                <button type="submit" wire:loading.attr="disabled" wire:target="updatePassword" class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#0C1E8C] hover:bg-[#091b38] px-6 py-3 text-sm font-bold text-white transition disabled:opacity-60"><span wire:loading.remove wire:target="updatePassword">Update password</span><span wire:loading wire:target="updatePassword"><i class="bi bi-arrow-repeat animate-spin"></i> Updating…</span></button>
            </div>
        </form>
    </div>
</div>

@script
<script>
    const showProfileSuccess = (message, title) => {
        window.dispatchEvent(new CustomEvent('kba-toast', { detail: {
            type: 'success',
            title,
            message,
            timer: 10000,
        }}));
    };

    $wire.on('profile-saved', (event) => showProfileSuccess(event.message, 'Profile updated'));
    $wire.on('password-saved', (event) => showProfileSuccess(event.message, 'Password changed'));
</script>
@endscript
