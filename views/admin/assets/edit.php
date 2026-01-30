<?php
/**
 * Edit Asset View
 * ITAM System - P-line Company
 * 
 * Form for editing existing assets
 */

$page_title = "Edit Asset";
require_once __DIR__ . '/../../layouts/header.php';

// Get form errors from session
$form_errors = $_SESSION['form_errors'] ?? [];
unset($_SESSION['form_errors']);
?>

<!-- Main Content -->
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-1 font-bold text-gray-800">Edit Asset</h1>
            <p class="text-gray-600 mb-0">Update asset information</p>
        </div>
        <a href="/views/admin/assets/index.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to List
        </a>
    </div>

    <?php if ($flash = get_flash_message()): ?>
        <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Asset Form -->
    <div class="glass-card p-4">
        <form method="POST" action="/controllers/AssetController.php?action=update&id=<?= $asset['asset_id'] ?>" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

            <div class="row g-4">
                <!-- Asset Code (Read-only) -->
                <div class="col-md-6">
                    <div class="input-group">
                        <label class="input-label">Asset Code</label>
                        <input type="text" 
                               class="input-field bg-light" 
                               value="<?= htmlspecialchars($asset['asset_code']) ?>" 
                               disabled>
                        <small class="text-gray-600 mt-1">
                            <i class="bi bi-info-circle me-1"></i>Asset code cannot be changed
                        </small>
                    </div>
                </div>

                <!-- Asset Name -->
                <div class="col-md-6">
                    <div class="input-group">
                        <label class="input-label required">Asset Name</label>
                        <input type="text" 
                               name="asset_name" 
                               class="input-field <?= isset($form_errors['asset_name']) ? 'error' : '' ?>"
                               placeholder="e.g., Dell Laptop XPS 15"
                               value="<?= htmlspecialchars($asset['asset_name']) ?>"
                               required>
                        <?php if (isset($form_errors['asset_name'])): ?>
                            <div class="error-message"><?= htmlspecialchars($form_errors['asset_name']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Category -->
                <div class="col-md-6">
                    <div class="input-group">
                        <label class="input-label required">Category</label>
                        <select name="category" 
                                class="input-field <?= isset($form_errors['category']) ? 'error' : '' ?>"
                                required>
                            <option value="">Select Category</option>
                            <option value="Computer" <?= $asset['category'] === 'Computer' ? 'selected' : '' ?>>Computer</option>
                            <option value="Laptop" <?= $asset['category'] === 'Laptop' ? 'selected' : '' ?>>Laptop</option>
                            <option value="Phone" <?= $asset['category'] === 'Phone' ? 'selected' : '' ?>>Phone</option>
                            <option value="Tablet" <?= $asset['category'] === 'Tablet' ? 'selected' : '' ?>>Tablet</option>
                            <option value="Printer" <?= $asset['category'] === 'Printer' ? 'selected' : '' ?>>Printer</option>
                            <option value="Monitor" <?= $asset['category'] === 'Monitor' ? 'selected' : '' ?>>Monitor</option>
                            <option value="Server" <?= $asset['category'] === 'Server' ? 'selected' : '' ?>>Server</option>
                            <option value="Network Device" <?= $asset['category'] === 'Network Device' ? 'selected' : '' ?>>Network Device</option>
                            <option value="Accessories" <?= $asset['category'] === 'Accessories' ? 'selected' : '' ?>>Accessories</option>
                            <option value="Other" <?= $asset['category'] === 'Other' ? 'selected' : '' ?>>Other</option>
                        </select>
                        <?php if (isset($form_errors['category'])): ?>
                            <div class="error-message"><?= htmlspecialchars($form_errors['category']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Serial Number -->
                <div class="col-md-6">
                    <div class="input-group">
                        <label class="input-label">Serial Number</label>
                        <input type="text" 
                               name="serial_number" 
                               class="input-field <?= isset($form_errors['serial_number']) ? 'error' : '' ?>"
                               placeholder="e.g., DL123456789"
                               value="<?= htmlspecialchars($asset['serial_number'] ?? '') ?>">
                        <?php if (isset($form_errors['serial_number'])): ?>
                            <div class="error-message"><?= htmlspecialchars($form_errors['serial_number']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Brand -->
                <div class="col-md-6">
                    <div class="input-group">
                        <label class="input-label">Brand</label>
                        <input type="text" 
                               name="brand" 
                               class="input-field <?= isset($form_errors['brand']) ? 'error' : '' ?>"
                               placeholder="e.g., Dell, HP, Apple"
                               value="<?= htmlspecialchars($asset['brand'] ?? '') ?>">
                        <?php if (isset($form_errors['brand'])): ?>
                            <div class="error-message"><?= htmlspecialchars($form_errors['brand']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Model -->
                <div class="col-md-6">
                    <div class="input-group">
                        <label class="input-label">Model</label>
                        <input type="text" 
                               name="model" 
                               class="input-field <?= isset($form_errors['model']) ? 'error' : '' ?>"
                               placeholder="e.g., XPS 15"
                               value="<?= htmlspecialchars($asset['model'] ?? '') ?>">
                        <?php if (isset($form_errors['model'])): ?>
                            <div class="error-message"><?= htmlspecialchars($form_errors['model']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Purchase Date -->
                <div class="col-md-6">
                    <div class="input-group">
                        <label class="input-label">Purchase Date</label>
                        <input type="date" 
                               name="purchase_date" 
                               class="input-field <?= isset($form_errors['purchase_date']) ? 'error' : '' ?>"
                               value="<?= htmlspecialchars($asset['purchase_date'] ?? '') ?>">
                        <?php if (isset($form_errors['purchase_date'])): ?>
                            <div class="error-message"><?= htmlspecialchars($form_errors['purchase_date']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Purchase Price -->
                <div class="col-md-6">
                    <div class="input-group">
                        <label class="input-label">Purchase Price ($)</label>
                        <input type="number" 
                               name="purchase_price" 
                               class="input-field <?= isset($form_errors['purchase_price']) ? 'error' : '' ?>"
                               placeholder="e.g., 1200"
                               step="0.01"
                               min="0"
                               value="<?= htmlspecialchars($asset['purchase_price'] ?? '') ?>">
                        <?php if (isset($form_errors['purchase_price'])): ?>
                            <div class="error-message"><?= htmlspecialchars($form_errors['purchase_price']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Status -->
                <div class="col-md-6">
                    <div class="input-group">
                        <label class="input-label required">Status</label>
                        <select name="status" 
                                class="input-field <?= isset($form_errors['status']) ? 'error' : '' ?>"
                                required>
                            <option value="Available" <?= $asset['status'] === 'Available' ? 'selected' : '' ?>>Available</option>
                            <option value="In Use" <?= $asset['status'] === 'In Use' ? 'selected' : '' ?>>In Use</option>
                        </select>
                        <?php if (isset($form_errors['status'])): ?>
                            <div class="error-message"><?= htmlspecialchars($form_errors['status']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Current Photo -->
                <?php if (!empty($asset['photo_url'])): ?>
                    <div class="col-12">
                        <div class="input-group">
                            <label class="input-label">Current Photo</label>
                            <div class="p-3 bg-light rounded">
                                <img src="/public/<?= htmlspecialchars($asset['photo_url']) ?>" 
                                     alt="Asset Photo" 
                                     class="rounded"
                                     style="max-width: 200px;">
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Upload New Photo -->
                <div class="col-12">
                    <div class="input-group">
                        <label class="input-label">
                            <?= !empty($asset['photo_url']) ? 'Replace Photo' : 'Upload Photo' ?>
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded p-4 text-center hover-border-primary transition" 
                             style="cursor: pointer;"
                             onclick="document.getElementById('photoInput').click()">
                            <i class="bi bi-cloud-upload display-5 text-gray-400 mb-2"></i>
                            <p class="text-gray-600 mb-1">Click to upload or drag and drop</p>
                            <p class="text-gray-500 mb-0">
                                <small>PNG, JPG, WEBP up to 5MB</small>
                            </p>
                            <input type="file" 
                                   id="photoInput"
                                   name="photo" 
                                   class="d-none" 
                                   accept="image/jpeg,image/png,image/jpg,image/webp"
                                   onchange="previewPhoto(this)">
                        </div>
                        <div id="photoPreview" class="mt-3" style="display: none;">
                            <img id="photoPreviewImage" src="" alt="Photo Preview" class="rounded" style="max-width: 200px;">
                            <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removePhoto()">
                                <i class="bi bi-trash me-1"></i>Remove
                            </button>
                        </div>
                        <?php if (isset($form_errors['photo'])): ?>
                            <div class="error-message"><?= htmlspecialchars($form_errors['photo']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex gap-3 mt-4 pt-4 border-top">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-2"></i>Update Asset
                </button>
                <a href="/views/admin/assets/index.php" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-2"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function previewPhoto(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            document.getElementById('photoPreviewImage').src = e.target.result;
            document.getElementById('photoPreview').style.display = 'block';
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

function removePhoto() {
    document.getElementById('photoInput').value = '';
    document.getElementById('photoPreview').style.display = 'none';
    document.getElementById('photoPreviewImage').src = '';
}
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>