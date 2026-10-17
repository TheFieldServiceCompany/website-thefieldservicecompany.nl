<?php

/**
 * test hermes vacatures create
 */
add_action('rest_api_init', function () {
    register_rest_route('tfsc-test/v1', '/auth', [
        'methods' => 'GET',
        'permission_callback' => '__return_true',

        'callback' => function () {

            $username = $_SERVER['PHP_AUTH_USER'] ?? '';
            $password = $_SERVER['PHP_AUTH_PW'] ?? '';

            $result = wp_authenticate_application_password(
                null,
                $username,
                $password
            );

            return [
                'rest_request' => defined('REST_REQUEST') && REST_REQUEST,
                'username' => $username,
                'password_present' => $password !== '',
                'app_passwords_available' => wp_is_application_passwords_available(),
                'app_passwords_available_for_user' =>
                    ($u = get_user_by('login', $username))
                        ? wp_is_application_passwords_available_for_user($u)
                        : null,

                'result_type' => is_wp_error($result)
                    ? 'WP_Error'
                    : ($result instanceof WP_User ? 'WP_User' : gettype($result)),

                'error_code' => is_wp_error($result)
                    ? $result->get_error_code()
                    : null,

                'error_message' => is_wp_error($result)
                    ? $result->get_error_message()
                    : null,

                'authenticated_user' => $result instanceof WP_User
                    ? $result->user_login
                    : null,
            ];
        },
    ]);
});
