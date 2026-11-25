<?php

declare(strict_types=1);

return [
    'sidebar' => [
        ['type' => 'separator', 'label' => 'Application'],
        ['type' => 'item', 'title' => 'Dashboard', 'icon' => 'home', 'route' => 'dashboard'],
    ],

    'auth' => [
        'login' => [
            'route' => 'login',
            'title' => 'Sign in',
            'heading' => 'Access your account',
            'description' => 'Sign in with your email and password.',
        ],
        'register' => [
            'route' => 'register',
            'title' => 'Create account',
            'heading' => 'Create your account',
            'description' => 'Sign up to access the dashboard.',
        ],
        'forgot-password' => [
            'route' => 'password.request',
            'title' => 'Recover password',
            'heading' => 'Forgot your password?',
            'description' => 'Enter your email and we will send you a link to reset your password.',
        ],
        'reset-password' => [
            'route' => 'password.reset',
            'title' => 'Reset password',
            'heading' => 'Reset password',
            'description' => 'Create a new password to access your account.',
        ],
        'verify-email' => [
            'route' => 'verification.*',
            'title' => 'Confirm your email',
            'heading' => 'Confirm your email',
            'description' => 'We sent a confirmation link to your email.',
        ],
        'change-password' => [
            'route' => 'password.change',
            'title' => 'Change password',
            'heading' => 'Change password',
            'description' => 'Enter your current password and set a new one.',
        ],
        'logout' => [
            'route' => 'logout',
            'title' => 'Sign out',
            'heading' => 'Sign out of the system',
            'description' => 'Do you want to end your session on this device?',
        ],
    ],
];
