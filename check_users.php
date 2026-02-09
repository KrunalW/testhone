<?php
// Check users with their roles
$host = 'localhost';
$db = 'analytics_dashboard';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== USER ACCOUNTS ===\n\n";

    $stmt = $pdo->query("
        SELECT u.id, u.username, u.active, agu.group as role, ai.secret as email
        FROM users u
        LEFT JOIN auth_groups_users agu ON u.id = agu.user_id
        LEFT JOIN auth_identities ai ON u.id = ai.user_id AND ai.type = 'email_password'
        ORDER BY u.id
    ");

    $users = $stmt->fetchAll(PDO::FETCH_OBJ);

    if (empty($users)) {
        echo "❌ No users found!\n";
    } else {
        foreach ($users as $user) {
            $status = $user->active ? '✅ Active' : '❌ Inactive';
            echo "ID: {$user->id}\n";
            echo "  Username: {$user->username}\n";
            echo "  Email: {$user->email}\n";
            echo "  Role: {$user->role}\n";
            echo "  Status: {$status}\n";
            echo "---\n";
        }
    }

    echo "\nTotal users: " . count($users) . "\n";

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
