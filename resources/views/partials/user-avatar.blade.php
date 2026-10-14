{{-- 
    Usage: 
    @include('partials.user-avatar', [
        'user' => $user,
        'size' => 60,        // optional, default 60
        'showRing' => true,  // optional, default false
    ])
--}}

@php
    $size = $size ?? 60;
    $showRing = $showRing ?? false;
@endphp

@if($user->has_photo)
    <img src="{{ $user->photo_url }}" 
         alt="{{ $user->name }}"
         class="user-avatar {{ $showRing ? 'user-avatar-ring' : '' }}"
         style="width: {{ $size }}px; height: {{ $size }}px; object-fit: cover; border-radius: 50%;">
@else
    <div class="user-avatar-fallback {{ $showRing ? 'user-avatar-ring' : '' }}"
         style="width: {{ $size }}px; height: {{ $size }}px; font-size: {{ $size * 0.4 }}px;">
        {{ $user->initials }}
    </div>
@endif

@once
@push('styles')
<style>
.user-avatar {
    display: inline-block;
    background: #fff;
    border: 2px solid #e3e6f0;
    flex-shrink: 0;
}
.user-avatar-ring {
    border: 3px solid #fff;
    box-shadow: 0 0 0 3px var(--primary-color);
}
.user-avatar-fallback {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    color: white;
    font-weight: 700;
    border-radius: 50%;
    border: 2px solid #e3e6f0;
    flex-shrink: 0;
    letter-spacing: 1px;
}
</style>
@endpush
@endonce