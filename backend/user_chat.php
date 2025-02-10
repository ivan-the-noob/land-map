// Add this before the closing </body> tag
<script>
    const USER_ID = <?php echo json_encode($_SESSION['user_id']); ?>;
    const USERNAME = <?php echo json_encode($_SESSION['user_name']); ?>;
    const ROLE_TYPE = <?php echo json_encode($_SESSION['role_type']); ?>;
    
    const chat = new ChatSystem();
</script>