<?php 
if (isset($_SESSION['donor_message'])) {
    echo "<p class='success-message'>{$_SESSION['donor_message']}</p>";
    unset($_SESSION['donor_message']); // Clear the message after displaying
}
?>