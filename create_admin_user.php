<?php
// Script to create admin/expert users
$host = 'localhost';
$db = 'analytics_dashboard';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "=== CREATE ADMIN/EXPERT USER ===\n\n";

    // Get user input
    echo "Enter username: ";
    $username = trim(fgets(STDIN));

    echo "Enter email: ";
    $email = trim(fgets(STDIN));

    echo "Enter password: ";
    $password = trim(fgets(STDIN));

    echo "\nSelect role:\n";
    echo "1. exam_expert (Can create subjects, questions, and exams)\n";
    echo "2. admin (Can schedule exams)\n";
    echo "3. superadmin (Full access to everything)\n";
    echo "4. user (Regular user - can only take exams)\n";
    echo "Enter choice (1-4): ";
    $roleChoice = trim(fgets(STDIN));

    $roleMap = [
        '1' => 'exam_expert',
        '2' => 'admin',
        '3' => 'superadmin',
        '4' => 'user'
    ];

    if (!isset($roleMap[$roleChoice])) {
        die("Invalid choice!\n");
    }

    $roleName = $roleMap[$roleChoice];

    // Check if user already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->fetch()) {
        die("\nError: User with this username or email already exists!\n");
    }

    // Hash password using CodeIgniter's password hasher
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Start transaction
    $pdo->beginTransaction();

    try {
        // Insert user
        $stmt = $pdo->prepare("
            INSERT INTO users (username, email, password_hash, active, created_at, updated_at)
            VALUES (?, ?, ?, 1, NOW(), NOW())
        ");
        $stmt->execute([$username, $email, $hashedPassword]);
        $userId = $pdo->lastInsertId();

        // Get group ID
        $stmt = $pdo->prepare("SELECT id FROM auth_groups_users WHERE group = ?");
        $stmt->execute([$roleName]);
        $groupRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$groupRow) {
            // Group doesn't exist in auth_groups_users, insert it
            // First check if it exists in settings
            $stmt = $pdo->prepare("SELECT id FROM auth_groups WHERE alias = ?");
            $stmt->execute([$roleName]);
            $settingsGroup = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($settingsGroup) {
                $groupId = $settingsGroup['id'];
            } else {
                // Create the group
                $stmt = $pdo->prepare("INSERT INTO auth_groups (alias, title, description) VALUES (?, ?, ?)");
                $titles = [
                    'exam_expert' => 'Exam Expert',
                    'admin' => 'Admin',
                    'superadmin' => 'Super Admin',
                    'user' => 'User'
                ];
                $descriptions = [
                    'exam_expert' => 'Can create subjects, questions, and build exams',
                    'admin' => 'Can schedule exams and manage exam timing',
                    'superadmin' => 'Complete control of the site',
                    'user' => 'General users of the site. Can take exams'
                ];
                $stmt->execute([$roleName, $titles[$roleName], $descriptions[$roleName]]);
                $groupId = $pdo->lastInsertId();
            }
        }

        // Assign user to group
        $stmt = $pdo->prepare("
            INSERT INTO auth_groups_users (user_id, group, created_at)
            VALUES (?, ?, NOW())
        ");
        $stmt->execute([$userId, $roleName]);

        // Commit transaction
        $pdo->commit();

        echo "\n✅ SUCCESS!\n";
        echo "User created successfully:\n";
        echo "  Username: $username\n";
        echo "  Email: $email\n";
        echo "  Role: $roleName\n";
        echo "  User ID: $userId\n\n";

        echo "The user can now login at: http://localhost:8080/login\n";

        if ($roleName === 'exam_expert') {
            echo "\nAccess URLs:\n";
            echo "  - Subjects: http://localhost:8080/admin/subjects\n";
            echo "  - Questions: http://localhost:8080/admin/questions\n";
            echo "  - Exams: http://localhost:8080/admin/exams\n";
        } elseif ($roleName === 'admin') {
            echo "\nAccess URLs:\n";
            echo "  - Exams (Schedule): http://localhost:8080/admin/exams\n";
        } elseif ($roleName === 'superadmin') {
            echo "\nAccess URLs:\n";
            echo "  - All admin features: http://localhost:8080/admin/*\n";
        }

    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
