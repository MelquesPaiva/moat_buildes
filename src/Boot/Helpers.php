<?php

/**
 * @param string $path
 * @return string
 */
function url(string $path = null): string
{
    if ($path) {
        return CONF_URL_TEST . "/" . ($path[0] == "/" ? mb_substr($path, 1) : $path);
    }
    return CONF_URL_TEST . "/";
}

/**
 * @param string $url
 */
function redirect(string $url): void
{
    header("HTTP/1.1 302 Redirect");
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        header("Location: {$url}");
        exit;
    }

    if (filter_input(INPUT_GET, "route", FILTER_DEFAULT) != $url) {
        $location = url($url);
        header("Location: {$location}");
        exit;
    }
}


/**
 * ################
 * ##### DATE #####
 * ################
 */

/**
 * Data em formato BR
 *
 * @param string $date
 * @param string $format
 * @param string $formatTo
 * @return string
 */
function date_br(string $date, string $format = "Y-m-d H:i:s", string $formatTo = "d/m/Y H:i:s"): string
{
    return DateTime::createFromFormat(
        $format,
        $date,
        time_zone()
    )->format($formatTo);
}

/**
 * @return DateTimeZone
 */
function time_zone(): DateTimeZone
{
    return new DateTimeZone(CONF_DATE_TIMEZONE);
}

/**
 * @param string|null $path
 * @return string
 */
function asset(string $path = null): string
{
    if ($path) {
        return CONF_URL_TEST . "/" . ($path[0] == "/" ? mb_substr($path, 1) : $path);
    }

    return CONF_URL_TEST;
}


/**
 * ####################
 * ##### VALIDATE #####
 * ####################
 */

/**
 * @param string $email
 * @return bool
 */
function is_email(string $email): bool
{
    $emailVerify = filter_var($email, FILTER_VALIDATE_EMAIL);
    return (!$emailVerify ? false : true);
}

/**
 * @param string $password
 * @return bool
 */
function is_passwd(string $password): bool
{
    if (password_get_info($password)['algo'] || (mb_strlen($password) >= CONF_PASSWD_MIN_LEN && mb_strlen($password) <= CONF_PASSWD_MAX_LEN)) {
        return true;
    }

    return false;
}


/**
 * ####################
 * ##### PASSWORD #####
 * ####################
 */

/**
 * @param string $password
 * @return string
 */
function passwd(string $password): string
{
    if (!empty(password_get_info($password)['algo'])) {
        return $password;
    }

    return password_hash($password, CONF_PASSWD_ALGO, CONF_PASSWD_OPTION);
}

/**
 * @param string $password
 * @param string $hash
 * @return bool
 */
function passwd_verify(string $password, string $hash): bool
{
    return password_verify($password, $hash);
}

/**
 * @param string $hash
 * @return bool
 */
function passwd_rehash(string $hash): bool
{
    return password_needs_rehash($hash, CONF_PASSWD_ALGO, CONF_PASSWD_OPTION);
}


/**
 * @return string|null
 */
function flash(): ?string
{
    $session = new \Source\Core\Session();
    if ($flash = $session->flash()) {
        return $flash;
    }

    return null;
}