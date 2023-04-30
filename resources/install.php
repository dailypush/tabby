<?php

function create_db($db_type, $db_name) {
    global $db;
    if ($db_type === 'pgsql') {
        $sql = file_get_contents('db_postgresql.sql');
    } else {
        $db->query('ALTER DATABASE `' . $db_name . '` DEFAULT CHARSET=utf8mb4;');
        $sql = file_get_contents('db_mysql.sql');
    }
    $result = $db->exec($sql);
    return $result !== false;
}

function create_first_user($email, $name, $password, $iban) {
    global $db;
    $insert = $db->prepare('INSERT INTO users VALUES (?,?,?,?,?)');
    $insert->execute([$email, $name, password_hash($password, PASSWORD_DEFAULT), $iban, null]);
    return $insert->rowCount() > 0;
}

function create_config($dsn, $db_username, $db_password, $app_email, $admin_email, $base_url, $currency, $days, $cron_type) {
    $webcronValue = $cron_type === 'webcron' ? 'true' : 'false';
    $config = <<<CONFIG
<?php

\$dsn = "{$dsn}";
\$db_username = "{$db_username}";
\$db_password = "{$db_password}";

\$application_email = "{$app_email}";
\$admin_email = "{$admin_email}";
\$base_url = "{$base_url}";
\$currency = "{$currency}";
\$days = {$days};
\$webcron = {$webcronValue};

CONFIG;

    $check = file_put_contents('config.php', $config);
    return $check ? 'created' : $config;
}

function check_install($base_url) {
    $check_register = strpos(file_get_contents($base_url . 'register'), 'This installation of Tabby is a private instance.') !== false;
    $check_changelog = strpos(file_get_contents($base_url . 'changelog.txt'), 'version 1.0 - commit 67b554a08bbed216423b8d968c67ddfe8169df2a') !== false;
    
    if ($check_register && !$check_changelog) {
        return 'OK';
    }
    
    if (!$check_register && $check_changelog) {
        return 'doublefail';
    }
    
    if (!$check_register) {
        return 'regfail';
    }
    
    return 'changelogfail';
}

$currencies = ['€', '£', '$', '¥', '₽', '₱', '₨', 'R'];
