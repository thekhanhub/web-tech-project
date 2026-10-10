<?php
session_start();
require_once 'db_connect.php';

// Fetch all records
$stmt = $pdo->query("SELECT * FROM staynest_properties ORDER BY created_at DESC");
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check for edit mode
$editRecord = null;
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $stmt2  = $pdo->prepare("SELECT * FROM staynest_properties WHERE id = :id");
    $stmt2->execute([':id' => $editId]);
    $editRecord = $stmt2->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <title>StayNest Admin Panel</title>
  <style>
    /* paste in your StayNest CSS variables here, or link to a shared stylesheet */
    body { font-family: 'DM Sans', sans-serif; background: #fafafa; padding: 30px; }
    .alert-success { background:#e8f5e9; color:#2e7d32; padding:12px 18px; border-radius:8px; margin-bottom:16px; border-left:4px solid #43a047; }
    .alert-error   { background:#ffebee; color:#c62828; padding:12px 18px; border-radius:8px; margin-bottom:16px; border-left:4px solid #e53935; }
    table { width:100%; border-collapse:collapse; background:white; border-radius:12px; overflow:hidden; box-shadow:0 2px 12px rgba(0,0,0,0.08); }
    thead tr { background:#ff385c; color:white; }
    th, td { padding:12px 16px; text-align:left; border-bottom:1px solid #eee; font-size:14px; }
    tbody tr:hover { background:#fff5f6; }
    .btn { padding:8px 16px; border:none; border-radius:8px; font-weight:700; cursor:pointer; font-size:13px; text-decoration:none; display:inline-block; }
    .btn-edit   { background:#e3f2fd; color:#1565c0; }
    .btn-delete { background:#ffebee; color:#c62828; }
    .btn-submit { background:#ff385c; color:white; padding:11px 28px; }
    form { background:white; padding:28px; border-radius:14px; box-shadow:0 2px 12px rgba(0,0,0,0.08); margin-bottom:30px; }
    form h2 { margin-bottom:20px; font-size:22px; }
    .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
    label { display:block; font-size:13px; font-weight:700; margin-bottom:5px; }
    input, select { width:100%; padding:10px 12px; border:1.8px solid #e0e0e0; border-radius:8px; font-size:14px; font-family:inherit; }
    input:focus, select:focus { border-color:#ff385c; outline:none; }
    h1 { font-size:28px; margin-bottom:24px; }
    h3 { font-size:20px; margin-bottom:16px; }
  </style>
</head>
<body>

<h1>🏠 StayNest — Admin Panel</h1>

<!-- Success / Error Messages -->
<?php if (isset($_SESSION['success'])): ?>
  <div class="alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
  <div class="alert-error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<!-- INSERT / EDIT FORM -->
<form method="POST" action="<?= $editRecord ? 'update.php' : 'insert.php' ?>">
  <?php if ($editRecord): ?>
    <input type="hidden" name="id" value="<?= $editRecord['id'] ?>"/>
    <h2>✏️ Edit Record #<?= $editRecord['id'] ?></h2>
  <?php else: ?>
    <h2>➕ Insert New Property</h2>
  <?php endif; ?>

  <div class="form-grid">
    <div>
      <label>Property Name</label>
      <input type="text" name="name" value="<?= htmlspecialchars($editRecord['name'] ?? '') ?>" required/>
    </div>
    <div>
      <label>Location</label>
      <input type="text" name="location" value="<?= htmlspecialchars($editRecord['location'] ?? '') ?>" required/>
    </div>
    <div>
      <label>Type</label>
      <select name="type" required>
        <option value="">Select</option>
        <?php foreach(['villa','apartment','cabin','resort','cottage'] as $t): ?>
          <option value="<?= $t ?>" <?= ($editRecord['type'] ?? '') === $t ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label>Region</label>
      <select name="region">
        <?php foreach(['usa','europe','asia','middle-east','africa','oceania'] as $r): ?>
          <option value="<?= $r ?>" <?= ($editRecord['region'] ?? '') === $r ? 'selected' : '' ?>><?= ucfirst($r) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label>Price per Night ($)</label>
      <input type="number" name="price" min="1" step="0.01" value="<?= $editRecord['price'] ?? '' ?>" required/>
    </div>
    <div>
      <label>Rating (1.0 – 5.0)</label>
      <input type="number" name="rating" min="1" max="5" step="0.1" value="<?= $editRecord['rating'] ?? '' ?>" required/>
    </div>
  </div>
  <br/>
  <button type="submit" class="btn btn-submit">
    <?= $editRecord ? '💾 Update Record' : '💾 Insert Record' ?>
  </button>
  <?php if ($editRecord): ?>
    <a href="admin.php" class="btn" style="margin-left:10px;background:#eee;color:#333">✕ Cancel</a>
  <?php endif; ?>
</form>

<!-- RECORDS TABLE -->
<h3>📋 All Records (<?= count($records) ?> total)</h3>
<table>
  <thead>
    <tr><th>#ID</th><th>Name</th><th>Location</th><th>Type</th><th>Price/Night</th><th>Rating</th><th>Actions</th></tr>
  </thead>
  <tbody>
    <?php if (empty($records)): ?>
      <tr><td colspan="7" style="text-align:center;padding:30px;color:#999">No records found.</td></tr>
    <?php else: ?>
      <?php foreach ($records as $row): ?>
        <tr>
          <td><strong>#<?= $row['id'] ?></strong></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['location']) ?></td>
          <td><?= ucfirst($row['type']) ?></td>
          <td><strong>$<?= $row['price'] ?></strong></td>
          <td>★ <?= $row['rating'] ?></td>
          <td>
            <a href="admin.php?edit=<?= $row['id'] ?>" class="btn btn-edit">✏️ Edit</a>
            <a href="delete.php?id=<?= $row['id'] ?>"
               class="btn btn-delete"
               onclick="return confirm('Delete <?= htmlspecialchars($row['name']) ?>?')">🗑️ Delete</a>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>

</body>
</html>