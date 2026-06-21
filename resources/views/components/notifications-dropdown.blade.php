@php
    $notifications = $notifications ?? collect();
    $unreadNotificationCount = $unreadNotificationCount ?? 0;
@endphp

@once
<style>
    .notification-trigger {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        border: 1px solid #e5e7eb;
        background: #fff;
        color: #374151;
        transition: all 0.2s ease;
    }

    .notification-trigger:hover,
    .notification-trigger.is-open {
        color: #2563eb;
        border-color: #bfdbfe;
        background: #eff6ff;
    }

    .notification-badge {
        position: absolute;
        top: 4px;
        right: 4px;
        min-width: 18px;
        height: 18px;
        padding: 0 5px;
        border-radius: 999px;
        background: #ef4444;
        color: #fff;
        font-size: 0.68rem;
        font-weight: 700;
        line-height: 18px;
        text-align: center;
    }

    .notification-panel {
        position: absolute;
        top: calc(100% + 12px);
        right: 0;
        width: min(380px, calc(100vw - 32px));
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        box-shadow: 0 16px 40px rgba(15, 23, 42, 0.12);
        opacity: 0;
        visibility: hidden;
        transform: translateY(8px);
        transition: all 0.2s ease;
        z-index: 1000;
    }

    .notification-wrapper.is-open .notification-panel {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .notification-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 18px;
        border-bottom: 1px solid #eef2f7;
    }

    .notification-panel-title {
        font-size: 1rem;
        font-weight: 700;
        color: #111827;
        margin: 0;
    }

    .notification-new-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 999px;
        background: #f3f4f6;
        color: #4b5563;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .notification-list {
        max-height: 360px;
        overflow-y: auto;
    }

    .notification-item {
        display: flex;
        gap: 12px;
        padding: 14px 18px;
        border-bottom: 1px solid #f3f4f6;
        text-decoration: none;
        color: inherit;
        transition: background 0.15s ease;
    }

    .notification-item:hover {
        background: #f9fafb;
    }

    .notification-item.is-unread {
        background: #f8fbff;
    }

    .notification-item-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: #ecfdf5;
        color: #059669;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .notification-item-title {
        font-size: 0.88rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 4px;
    }

    .notification-item-message {
        font-size: 0.82rem;
        color: #6b7280;
        line-height: 1.45;
        margin-bottom: 6px;
    }

    .notification-item-time {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.75rem;
        color: #9ca3af;
    }

    .notification-empty {
        padding: 28px 18px;
        text-align: center;
        color: #6b7280;
        font-size: 0.9rem;
    }

    .notification-mark-all {
        border: 0;
        background: transparent;
        color: #2563eb;
        font-size: 0.78rem;
        font-weight: 600;
        padding: 0;
        cursor: pointer;
    }

    .notification-mark-all:disabled {
        color: #9ca3af;
        cursor: not-allowed;
    }

    .notification-wrapper {
        position: relative;
    }
</style>
@endonce

<div class="notification-wrapper" data-notification-wrapper>
    <button type="button"
            class="notification-trigger"
            data-notification-toggle
            aria-label="Notifikasi"
            aria-expanded="false">
        <i class="feather-bell"></i>
        @if($unreadNotificationCount > 0)
            <span class="notification-badge" data-notification-count>{{ $unreadNotificationCount > 9 ? '9+' : $unreadNotificationCount }}</span>
        @endif
    </button>

    <div class="notification-panel" data-notification-panel>
        <div class="notification-panel-header">
            <h4 class="notification-panel-title">Notifications</h4>
            <div class="d-flex align-items-center gap-2">
                <span class="notification-new-badge" data-notification-new-label>{{ $unreadNotificationCount }} New</span>
                <button type="button"
                        class="notification-mark-all"
                        data-notification-mark-all
                        {{ $unreadNotificationCount === 0 ? 'disabled' : '' }}>
                    Tandai dibaca
                </button>
            </div>
        </div>

        <div class="notification-list" data-notification-list>
            @forelse($notifications as $notification)
                <a href="{{ $notification->link ?: '#' }}"
                   class="notification-item {{ $notification->isUnread() ? 'is-unread' : '' }}"
                   data-notification-item
                   data-notification-id="{{ $notification->id }}"
                   data-notification-link="{{ $notification->link }}">
                    <span class="notification-item-icon">
                        <i class="feather-{{ $notification->icon }}"></i>
                    </span>
                    <span class="flex-grow-1">
                        <div class="notification-item-title">{{ $notification->title }}</div>
                        <div class="notification-item-message">{{ $notification->message }}</div>
                        <span class="notification-item-time">
                            <i class="feather-clock"></i>
                            {{ $notification->created_at?->diffForHumans() }}
                        </span>
                    </span>
                </a>
            @empty
                <div class="notification-empty">Belum ada notifikasi.</div>
            @endforelse
        </div>
    </div>
</div>

@once
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-notification-wrapper]').forEach(function (wrapper) {
        const toggle = wrapper.querySelector('[data-notification-toggle]');
        const panel = wrapper.querySelector('[data-notification-panel]');
        const markAllBtn = wrapper.querySelector('[data-notification-mark-all]');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (!toggle || !panel) {
            return;
        }

        const closePanel = () => {
            wrapper.classList.remove('is-open');
            toggle.classList.remove('is-open');
            toggle.setAttribute('aria-expanded', 'false');
        };

        toggle.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            const isOpen = wrapper.classList.toggle('is-open');
            toggle.classList.toggle('is-open', isOpen);
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        document.addEventListener('click', function (event) {
            if (!wrapper.contains(event.target)) {
                closePanel();
            }
        });

        wrapper.querySelectorAll('[data-notification-item]').forEach(function (item) {
            item.addEventListener('click', function (event) {
                const notificationId = item.dataset.notificationId;
                const link = item.dataset.notificationLink;

                if (!notificationId || !csrfToken) {
                    return;
                }

                event.preventDefault();

                fetch(`{{ url('/notifications') }}/${notificationId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                }).finally(function () {
                    item.classList.remove('is-unread');
                    if (link) {
                        window.location.href = link;
                    } else {
                        window.location.reload();
                    }
                });
            });
        });

        if (markAllBtn) {
            markAllBtn.addEventListener('click', function () {
                if (markAllBtn.disabled || !csrfToken) {
                    return;
                }

                fetch(`{{ route('notifications.read-all') }}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                }).then(function () {
                    wrapper.querySelectorAll('[data-notification-item]').forEach(function (item) {
                        item.classList.remove('is-unread');
                    });

                    const badge = wrapper.querySelector('[data-notification-count]');
                    if (badge) {
                        badge.remove();
                    }

                    const newLabel = wrapper.querySelector('[data-notification-new-label]');
                    if (newLabel) {
                        newLabel.textContent = '0 New';
                    }

                    markAllBtn.disabled = true;
                });
            });
        }
    });
});
</script>
@endonce
