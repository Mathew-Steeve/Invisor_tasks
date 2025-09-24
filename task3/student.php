<?php
session_start();

// Initialize storage in session
if (!isset($_SESSION['students'])) {
    $_SESSION['students'] = []; // id => [id, name, email, course, year]
    $_SESSION['next_id'] = 1;
}

// Simple flash message helper
function flash($msg = null) {
    if ($msg === null) {
        if (isset($_SESSION['_flash'])) {
            $m = $_SESSION['_flash'];
            unset($_SESSION['_flash']);
            return $m;
        }
        return null;
    }
    $_SESSION['_flash'] = $msg;
}

// CSRF token helper
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}
function check_csrf($token) {
    return isset($token) && hash_equals($_SESSION['csrf_token'], $token);
}

$action = $_GET['action'] ?? 'list';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post_action = $_POST['form_action'] ?? '';
    $token = $_POST['csrf_token'] ?? '';
    if (!check_csrf($token)) {
        http_response_code(400);
        flash('Invalid CSRF token. Try again.');
        header('Location: ?');
        exit;
    }

    if ($post_action === 'create') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $course = trim($_POST['course'] ?? '');
        $year = trim($_POST['year'] ?? '');

        $errors = [];
        if ($name === '') $errors[] = 'Name is required.';
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
        if ($course === '') $errors[] = 'Course is required.';
        if ($year === '' || !ctype_digit($year) || (int)$year < 1) $errors[] = 'Valid year is required.';

        if (count($errors) === 0) {
            $id = $_SESSION['next_id']++;
            $_SESSION['students'][$id] = [
                'id' => $id,
                'name' => htmlspecialchars($name, ENT_QUOTES),
                'email' => htmlspecialchars($email, ENT_QUOTES),
                'course' => htmlspecialchars($course, ENT_QUOTES),
                'year' => (int)$year,
            ];
            flash('Student created successfully.');
            header('Location: ?');
            exit;
        } else {
            // On error, keep the submitted values in session temporary store
            $_SESSION['_old'] = ['name'=>$name,'email'=>$email,'course'=>$course,'year'=>$year];
            flash(implode(' ', $errors));
            header('Location: ?action=create');
            exit;
        }
    }

    if ($post_action === 'update') {
        $id = $_POST['id'] ?? '';
        if (!isset($_SESSION['students'][$id])) {
            flash('Student not found.');
            header('Location: ?');
            exit;
        }
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $course = trim($_POST['course'] ?? '');
        $year = trim($_POST['year'] ?? '');

        $errors = [];
        if ($name === '') $errors[] = 'Name is required.';
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
        if ($course === '') $errors[] = 'Course is required.';
        if ($year === '' || !ctype_digit($year) || (int)$year < 1) $errors[] = 'Valid year is required.';

        if (count($errors) === 0) {
            $_SESSION['students'][$id] = [
                'id' => $id,
                'name' => htmlspecialchars($name, ENT_QUOTES),
                'email' => htmlspecialchars($email, ENT_QUOTES),
                'course' => htmlspecialchars($course, ENT_QUOTES),
                'year' => (int)$year,
            ];
            flash('Student updated successfully.');
            header('Location: ?');
            exit;
        } else {
            $_SESSION['_old'] = ['name'=>$name,'email'=>$email,'course'=>$course,'year'=>$year];
            flash(implode(' ', $errors));
            header('Location: ?action=edit&id=' . urlencode($id));
            exit;
        }
    }

    if ($post_action === 'delete') {
        $id = $_POST['id'] ?? '';
        if (isset($_SESSION['students'][$id])) {
            unset($_SESSION['students'][$id]);
            flash('Student deleted.');
        } else {
            flash('Student not found.');
        }
        header('Location: ?');
        exit;
    }
}

// Helpers for views
function old($key, $default='') {
    if (isset($_SESSION['_old'][$key])) return htmlspecialchars($_SESSION['_old'][$key], ENT_QUOTES);
    return $default;
}
function clear_old() {
    if (isset($_SESSION['_old'])) unset($_SESSION['_old']);
}

// Start output
?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Mini Student Registry</title>
    <style>
        body{font-family:system-ui,Arial;margin:24px;background:#f9fafb;color:#111}
        .container{max-width:900px;margin:0 auto;background:#fff;padding:20px;border-radius:8px;box-shadow:0 6px 18px rgba(0,0,0,0.06)}
        h1{margin-top:0}
        table{width:100%;border-collapse:collapse;margin-top:12px}
        th,td{padding:8px;border-bottom:1px solid #eee;text-align:left}
        a.button, button{display:inline-block;padding:8px 12px;border-radius:6px;text-decoration:none;border:none;background:#2563eb;color:#fff}
        form .row{margin-bottom:10px}
        input[type=text], input[type=email], input[type=number], select{width:100%;padding:8px;border-radius:6px;border:1px solid #ddd}
        .muted{color:#666;font-size:0.9em}
        .flash{background:#ecfdf5;color:#065f46;padding:8px;border-radius:6px;margin-bottom:12px}
        .danger{background:#fff1f2;color:#9f1239}
        .small{font-size:0.9em}
        .actions{display:flex;gap:8px}
    </style>
</head>
<body>
<div class="container">
    <h1>Mini Student Registry</h1>
    <p class="muted">No database — data is stored in PHP sessions. Refreshing or closing the browser may end the session depending on your PHP configuration.</p>

    <?php if ($m = flash()): ?>
        <div class="flash"><?php echo htmlspecialchars($m, ENT_QUOTES); ?></div>
    <?php endif; ?>

    <?php if ($action === 'list'): ?>
        <p><a href="?action=create" class="button">+ Add student</a></p>
        <?php if (count($_SESSION['students']) === 0): ?>
            <p class="muted">No students yet. Add one to get started.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr><th>#</th><th>Name</th><th>Email</th><th>Course</th><th>Year</th><th>Actions</th></tr>
                </thead>
                <tbody>
                <?php foreach ($_SESSION['students'] as $s): ?>
                    <tr>
                        <td><?php echo $s['id']; ?></td>
                        <td><?php echo $s['name']; ?></td>
                        <td><?php echo $s['email']; ?></td>
                        <td><?php echo $s['course']; ?></td>
                        <td><?php echo $s['year']; ?></td>
                        <td class="actions">
                            <a href="?action=edit&id=<?php echo $s['id']; ?>" class="button">Edit</a>
                            <form method="post" style="display:inline;margin:0">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <input type="hidden" name="form_action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $s['id']; ?>">
                                <button type="submit" onclick="return confirm('Delete this student?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    <?php elseif ($action === 'create'): ?>
        <p><a href="?" class="button">⟵ Back to list</a></p>
        <h2>Create student</h2>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="form_action" value="create">
            <div class="row">
                <label class="small">Name</label>
                <input type="text" name="name" value="<?php echo old('name',''); ?>">
            </div>
            <div class="row">
                <label class="small">Email</label>
                <input type="email" name="email" value="<?php echo old('email',''); ?>">
            </div>
            <div class="row">
                <label class="small">Course</label>
                <input type="text" name="course" value="<?php echo old('course',''); ?>">
            </div>
            <div class="row">
                <label class="small">Year</label>
                <input type="number" name="year" min="1" value="<?php echo old('year','1'); ?>">
            </div>
            <button type="submit">Create</button>
        </form>
        <?php clear_old(); ?>

    <?php elseif ($action === 'edit'): ?>
        <?php
            $id = $_GET['id'] ?? '';
            if (!isset($_SESSION['students'][$id])) {
                flash('Student not found.');
                header('Location: ?');
                exit;
            }
            $student = $_SESSION['students'][$id];
        ?>
        <p><a href="?" class="button">⟵ Back to list</a></p>
        <h2>Edit student: <?php echo $student['name']; ?></h2>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="form_action" value="update">
            <input type="hidden" name="id" value="<?php echo $student['id']; ?>">

            <div class="row">
                <label class="small">Name</label>
                <input type="text" name="name" value="<?php echo old('name',$student['name']); ?>">
            </div>
            <div class="row">
                <label class="small">Email</label>
                <input type="email" name="email" value="<?php echo old('email',$student['email']); ?>">
            </div>
            <div class="row">
                <label class="small">Course</label>
                <input type="text" name="course" value="<?php echo old('course',$student['course']); ?>">
            </div>
            <div class="row">
                <label class="small">Year</label>
                <input type="number" name="year" min="1" value="<?php echo old('year',$student['year']); ?>">
            </div>
            <button type="submit">Update</button>
        </form>
        <?php clear_old(); ?>

    <?php else: ?>
        <p>Unknown action.</p>
    <?php endif; ?>

    <hr>
    <p class="muted">Session id: <?php echo session_id(); ?> — Students in session: <?php echo count($_SESSION['students']); ?></p>
</div>
</body>
</html>
