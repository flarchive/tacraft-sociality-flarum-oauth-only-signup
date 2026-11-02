<?php

declare(strict_types=1);

namespace TacraftSociality\OauthOnlySignup;

use Flarum\Api\Resource\ForumResource;
use Flarum\Extend;
use Flarum\Foundation\ValidationException;
use Flarum\User\Event\Saving;
use Flarum\User\RegistrationToken;

// Runtime extension id: Flarum derives it from the composer package name by
// dropping the "flarum-" segment (`tacraft-sociality/flarum-oauth-only-signup`).
const HINT_SETTING = 'tacraft-sociality-oauth-only-signup.hint';

return [
    (new Extend\Frontend('forum'))->js(__DIR__.'/js/dist/forum.js'),

    (new Extend\Frontend('admin'))->js(__DIR__.'/js/dist/admin.js'),

    new Extend\Locales(__DIR__.'/locale'),

    // Hint shown at the bottom of the login modal. Defaults to the sentence
    // below, and hides itself when an administrator clears the setting.
    (new Extend\Settings())
        ->default(
            HINT_SETTING,
            '本站已关闭邮箱注册，请使用下方第三方账号登录；已有账号可以直接用邮箱和密码登录。'
        )
        ->serializeToForum('oauthOnlySignupHint', HINT_SETTING),

    // Hide the "sign up" link on the login modal. The registration endpoint
    // itself stays live behind the scenes, because OAuth sign-ups are
    // recognised by the registration token they carry — not by this flag.
    (new Extend\ApiResource(ForumResource::class))->field('allowSignUp', function ($field) {
        return $field->get(fn () => false);
    }),

    (new Extend\Event())->listen(Saving::class, function (Saving $event) {
        $user = $event->user;

        // Only brand new accounts are affected, and administrators may still
        // create accounts for other people from the admin panel.
        if ($user->exists || $event->actor->isAdmin()) {
            return;
        }

        $token = $event->data['attributes']['token'] ?? null;

        if (is_string($token) && $token !== '') {
            try {
                RegistrationToken::validOrFail($token);

                // A valid registration token can only come from an OAuth
                // provider callback, so this sign-up is allowed through.
                return;
            } catch (\Throwable) {
                // Invalid or expired token: fall through, it is not an OAuth
                // sign-up and must be rejected like any other email sign-up.
            }
        }

        throw new ValidationException([
            'email' => '本站已关闭邮箱注册，请使用 QQ、GitHub、微软或 TcsSkin 登录。',
        ]);
    }),
];
