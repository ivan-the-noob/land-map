<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Sign Out Modal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/typicons/2.1.2/typicons.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

</head>
<body>

<!-- Sign Out Button -->
<button id="signOutButton" class="dropdown-item" style="width: 100%; text-align: left; border: none; background: none; cursor: pointer; display: flex; align-items: center; padding: 8px 15px;" data-toggle="modal" data-target="#signOutModal">
    <i class="typcn typcn-power-outline" style="margin-right: 10px; color: #dc3545;"></i>
    <span style="color: #dc3545;">Sign Out</span>
</button>

<!-- Signout Modal (Bootstrap 4) -->
<div class="modal fade" id="signOutModal" tabindex="-1" role="dialog" aria-labelledby="signOutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="signOutModalLabel">Sign Out</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body text-center">
                <div class="signout-icon-wrapper mb-3">
                    <i class="fas fa-sign-out-alt signout-icon fa-3x text-danger"></i>
                </div>
                <p class="signout-modal-message">Are you sure you want to sign out?</p>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form action="logout.php" method="POST">
                    <button type="submit" class="btn btn-danger">Confirm</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap and jQuery -->

</body>
</html>
