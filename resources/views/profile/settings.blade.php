@extends('layouts.app')

@section('title', 'Profile Settings - Boarding School System')
@section('page_title', 'Profile & Account Settings')

@section('content')
    <div class="glass-card" style="max-width: 600px; margin: 0 auto;">
        <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">Update Profile Information</h3>

        <form action="{{ route('profile.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" name="first_name" id="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}" required>
                </div>
                <div class="form-group">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" name="last_name" id="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                </div>
                <div class="form-group">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                </div>
            </div>

            <h4 style="font-size: 1rem; font-weight: 600; margin: 2rem 0 1rem 0; border-top: 1px solid var(--border-color); padding-top: 1.5rem; color: var(--text-secondary);">Security / Change Password</h4>
            <p style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 1rem;">Leave these blank if you do not want to change your password.</p>

            <div class="form-group">
                <label for="current_password" class="form-label">Current Password</label>
                <input type="password" name="current_password" id="current_password" class="form-control" placeholder="••••••••">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 2rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="new_password" class="form-label">New Password</label>
                    <input type="password" name="new_password" id="new_password" class="form-control" placeholder="••••••••">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="form-control" placeholder="••••••••">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;">Save Changes</button>
            </div>
        </form>
    </div>

    <div class="glass-card" style="max-width: 600px; margin: 1.5rem auto 0 auto;">
        <h3 style="font-size: 1.15rem; font-weight: 600; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">Change Password</h3>

        <form action="{{ route('profile.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="current_password_change" class="form-label">Current Password *</label>
                <input type="password" name="current_password" id="current_password_change" class="form-control" placeholder="Enter your current password" autocomplete="current-password">
                @error('current_password')
                    <span style="color: var(--danger-color); font-size: 0.8rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="new_password_change" class="form-label">New Password *</label>
                <input type="password" name="new_password" id="new_password_change" class="form-control" placeholder="At least 8 characters" autocomplete="new-password">
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="new_password_confirmation_change" class="form-label">Confirm New Password *</label>
                <input type="password" name="new_password_confirmation" id="new_password_confirmation_change" class="form-control" placeholder="Repeat new password" autocomplete="new-password">
            </div>

            <div style="display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary">Update Password</button>
            </div>
        </form>
    </div>
@endsection
