<?php
// Script to create default admin users for testing
$host = 'localhost';
$db = 'analytics_dashboard';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== CREATING DEFAULT ADMIN USERS ===\n\n";

    // Default users to create
    $defaultUsers = [
        [
            'username' => 'superadmin',
            'email' => 'superadmin@example.com',
            'password' => 'admin123',
            'role' => 'superadmin',
            'description' => 'Super Administrator - Full access to all features'
        ],
        [
            'username' => 'exam_expert',
            'email' => 'expert@example.com',
            'password' => 'expert123',
            'role' => 'exam_expert',
            'description' => 'Exam Expert - Can create subjects, questions, and exams'
        ],
        [
            'username' => 'scheduler',
            'email' => 'scheduler@example.com',
            'password' => 'admin123',
            'role' => 'admin',
            'description' => 'Exam Scheduler - Can schedule exam dates and times'
        ],
        [
            'username' => 'student',
            'email' => 'student@example.com',
            'password' => 'student123',
            'role' => 'user',
            'description' => 'Regular Student - Can take exams'
        ]
    ];

    $created = [];
    $skipped = [];

    foreach ($defaultUsers as $userData) {
        // Check if user already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$userData['username']]);

        if ($stmt->fetch()) {
            $skipped[] = $userData['username'];
            echo "⏭️  Skipped: {$userData['username']} (already exists)\n";
            continue;
        }

        // Check if email already exists in auth_identities
        $stmt = $pdo->prepare("SELECT user_id FROM auth_identities WHERE secret = ?");
        $stmt->execute([$userData['email']]);

        if ($stmt->fetch()) {
            $skipped[] = $userData['username'];
            echo "⏭️  Skipped: {$userData['username']} (email already in use)\n";
            continue;
        }

        // Hash password
        $hashedPassword = password_hash($userData['password'], PASSWORD_BCRYPT);

        // Start transaction
        $pdo->beginTransaction();

        try {
            // Insert user
            $stmt = $pdo->prepare("
                INSERT INTO users (username, active, created_at, updated_at)
                VALUES (?, 1, NOW(), NOW())
            ");
            $stmt->execute([$userData['username']]);
            $userId = $pdo->lastInsertId();

            // Insert email identity
            $stmt = $pdo->prepare("
                INSERT INTO auth_identities (user_id, type, name, secret, secret2, expires, extra, force_reset, last_used_at, created_at, updated_at)
                VALUES (?, 'email_password', ?, ?, ?, NULL, NULL, 0, NULL, NOW(), NOW())
            ");
            $stmt->execute([$userId, $userData['email'], $userData['email'], $hashedPassword]);

            // Assign user to group
            $stmt = $pdo->prepare("
                INSERT INTO auth_groups_users (user_id, `group`, created_at)
                VALUES (?, ?, NOW())
            ");
            $stmt->execute([$userId, $userData['role']]);

            $pdo->commit();

            $created[] = [
                'username' => $userData['username'],
                'password' => $userData['password'],
                'role' => $userData['role'],
                'description' => $userData['description']
            ];

            echo "✅ Created: {$userData['username']} (ID: {$userId})\n";

        } catch (Exception $e) {
            $pdo->rollBack();
            echo "❌ Failed: {$userData['username']} - " . $e->getMessage() . "\n";
        }
    }

    echo "\n=== SUMMARY ===\n\n";

    if (!empty($created)) {
        echo "✅ Successfully created " . count($created) . " user(s):\n\n";

        foreach ($created as $user) {
            echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
            echo "👤 Username: {$user['username']}\n";
            echo "🔑 Password: {$user['password']}\n";
            echo "🎭 Role:     {$user['role']}\n";
            echo "📝 About:    {$user['description']}\n";
        }
        echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    }

    if (!empty($skipped)) {
        echo "\n⏭️  Skipped " . count($skipped) . " existing user(s): " . implode(', ', $skipped) . "\n\n";
    }

    echo "🌐 Login URL: http://localhost:8080/login\n\n";

    echo "📋 Access URLs by Role:\n\n";
    echo "Super Admin:\n";
    echo "  • All Features: http://localhost:8080/admin/*\n";
    echo "  • User Management: http://localhost:8080/admin/users\n\n";

    echo "Exam Expert:\n";
    echo "  • Subjects: http://localhost:8080/admin/subjects\n";
    echo "  • Questions: http://localhost:8080/admin/questions\n";
    echo "  • Exams: http://localhost:8080/admin/exams\n\n";

    echo "Scheduler (Admin):\n";
    echo "  • Schedule Exams: http://localhost:8080/admin/exams\n\n";

    echo "Student (User):\n";
    echo "  • Dashboard: http://localhost:8080/dashboard\n";
    echo "  • Take Exams: http://localhost:8080/exam/*\n\n";

    echo "=== SETUP COMPLETE ===\n";

} catch(PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
}
