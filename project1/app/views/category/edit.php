<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- You might want to link your project's main CSS file here -->
    <!-- <link rel="stylesheet" href="/project1/public/css/styles.css"> -->
    <script>
        function validateCategoryForm() {
            let name = document.getElementById('name').value;
            let description = document.getElementById('description').value;
            let errors = [];

            if (name.trim().length < 3 || name.trim().length > 100) {
                errors.push('Category name must be between 3 and 100 characters.');
            }
            if (description.trim().length > 255) {
                errors.push('Description cannot exceed 255 characters.');
            }

            if (errors.length > 0) {
                alert(errors.join('\n'));
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="container mt-5">
        <h1>Edit Category</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if (isset($category) && $category): ?>
            <form method="POST" action="/project1/Category/update" onsubmit="return validateCategoryForm();">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($category->id, ENT_QUOTES, 'UTF-8'); ?>">

                <div class="form-group">
                    <label for="name">Category Name:</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        class="form-control" 
                        value="<?php echo htmlspecialchars($category->name ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea 
                        id="description" 
                        name="description" 
                        class="form-control"
                    ><?php echo htmlspecialchars($category->description ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="/project1/Category/list" class="btn btn-secondary">Cancel</a>
            </form>
        <?php else: ?>
            <div class="alert alert-warning">Category data not found.</div>
            <a href="/project1/Category/list" class="btn btn-secondary">Back to List</a>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>