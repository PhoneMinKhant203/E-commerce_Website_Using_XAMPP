function confirmLogout() {
    var confirmAction = confirm("Are you sure you want to sign out?");
    if (confirmAction) {
        window.location.href = 'logout.php';  
    } else {
        return false;
    }
}
