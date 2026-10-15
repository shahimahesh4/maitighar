<?php

namespace App\Livewire;

use App\Models\User;
use App\Services\CommunicationService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Edit Profile - Maitighar')]
class ProfilePage extends Component
{
    use WithFileUploads;

    public $avatarUpload;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public ?string $date_of_birth = null;

    public ?string $gender = null;

    public string $address = '';

    public string $city = '';

    public string $state = '';

    public string $postal_code = '';

    public string $emergency_contact_name = '';

    public string $emergency_contact_relationship = '';

    public string $emergency_contact_phone = '';

    public string $guardian_name = '';

    public string $guardian_phone = '';

    public string $education_level = '';

    public string $institution = '';

    public string $employee_id = '';

    public string $job_title = '';

    public string $department = '';

    public ?string $joining_date = null;

    public string $employment_type = '';

    public string $current_password = '';

    public string $new_password = '';

    public string $new_password_confirmation = '';

    public function mount(): void
    {
        $user = auth()->user();

        foreach ($this->profileFields() as $field) {
            $value = $user->{$field};
            $this->{$field} = $value instanceof \DateTimeInterface ? $value->format('Y-m-d') : ($value ?? '');
        }
    }

    public function save(): void
    {
        $user = auth()->user();
        $this->phone = $this->normalizePhone($this->phone);
        $this->emergency_contact_phone = $this->normalizeOptionalPhone($this->emergency_contact_phone);
        $this->guardian_phone = $this->normalizeOptionalPhone($this->guardian_phone);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['required', 'regex:/^\+9779[78]\d{8}$/', Rule::unique('users', 'phone')->ignore($user->id)],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', Rule::in(['female', 'male', 'other', 'prefer_not_to_say'])],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_relationship' => ['nullable', 'string', 'max:60'],
            'emergency_contact_phone' => ['nullable', 'regex:/^\+9779[78]\d{8}$/'],
            'avatarUpload' => ['nullable', 'image', 'max:2048'],
        ];

        if ($user->role === 'student') {
            $rules += [
                'guardian_name' => ['nullable', 'string', 'max:255'],
                'guardian_phone' => ['nullable', 'regex:/^\+9779[78]\d{8}$/'],
                'education_level' => ['nullable', 'string', 'max:100'],
                'institution' => ['nullable', 'string', 'max:255'],
            ];
        }

        if (in_array($user->role, ['super_admin', 'admin', 'trainer', 'staff'], true)) {
            $rules += [
                'employee_id' => ['nullable', 'string', 'max:50', Rule::unique('users', 'employee_id')->ignore($user->id)],
                'job_title' => ['nullable', 'string', 'max:100'],
                'department' => ['nullable', 'string', 'max:100'],
                'joining_date' => ['nullable', 'date', 'before_or_equal:today'],
                'employment_type' => ['nullable', Rule::in(['full_time', 'part_time', 'contract', 'intern'])],
            ];
        }

        $validated = $this->validate($rules, [
            'phone.regex' => 'Enter a valid Nepal mobile number, for example +977 9812345678.',
            'emergency_contact_phone.regex' => 'Enter a valid Nepal emergency phone number.',
            'guardian_phone.regex' => 'Enter a valid Nepal guardian phone number.',
        ]);

        unset($validated['avatarUpload']);

        // Livewire retains empty optional inputs as empty strings. Convert them
        // to null before sending nullable date and profile columns to MySQL.
        foreach ($validated as $field => $value) {
            if ($value === '' && ! in_array($field, ['name', 'email', 'phone'], true)) {
                $validated[$field] = null;
            }
        }

        $emailChanged = strcasecmp($user->email, $validated['email']) !== 0;
        $previousAvatar = $user->avatar;
        $newAvatar = null;

        if ($this->avatarUpload) {
            $newAvatar = $this->avatarUpload->store('profile-photos', 'public');
            $validated['avatar'] = $newAvatar;
        }

        if ($emailChanged && app(CommunicationService::class)->emailVerificationEnabled()) {
            $validated['email_verified_at'] = null;
        }

        try {
            $user->update($validated);
        } catch (\Throwable $exception) {
            if ($newAvatar) {
                Storage::disk('public')->delete($newAvatar);
            }

            throw $exception;
        }

        if ($newAvatar && $previousAvatar && $previousAvatar !== $newAvatar) {
            Storage::disk('public')->delete($previousAvatar);
        }

        $this->avatarUpload = null;

        if ($emailChanged && app(CommunicationService::class)->emailVerificationEnabled()) {
            $user->sendEmailVerificationNotification();
            $this->dispatch('profile-saved', message: 'Profile updated. Check your new email address to verify it.');
        } else {
            $this->dispatch('profile-saved', message: 'Your profile has been updated successfully.');
        }
    }

    public function render(): View
    {
        $user = auth()->user()->fresh(['trainer']);

        return view('livewire.profile-page', [
            'user' => $user,
            'roleLabel' => User::roleOptions()[$user->role] ?? ucfirst($user->role),
        ]);
    }

    public function updatePassword(): void
    {
        $validated = $this->validate([
            'current_password' => ['required', 'current_password:web'],
            'new_password' => [
                'required',
                'different:current_password',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
        ], [
            'current_password.current_password' => 'The current password is incorrect.',
            'new_password.different' => 'Your new password must be different from the current password.',
            'new_password.confirmed' => 'The new password confirmation does not match.',
        ]);

        auth()->user()->update(['password' => $validated['new_password']]);

        $this->reset('current_password', 'new_password', 'new_password_confirmation');
        $this->resetValidation(['current_password', 'new_password']);
        $this->dispatch('password-saved', message: 'Your password has been changed successfully.');
    }

    private function profileFields(): array
    {
        return [
            'name', 'email', 'phone', 'date_of_birth', 'gender', 'address', 'city', 'state', 'postal_code',
            'emergency_contact_name', 'emergency_contact_relationship', 'emergency_contact_phone',
            'guardian_name', 'guardian_phone', 'education_level', 'institution', 'employee_id', 'job_title',
            'department', 'joining_date', 'employment_type',
        ];
    }

    private function normalizeOptionalPhone(string $phone): string
    {
        return trim($phone) === '' ? '' : $this->normalizePhone($phone);
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone);
        if (str_starts_with($digits, '977')) {
            $digits = substr($digits, 3);
        }

        return '+977'.$digits;
    }
}
