<?php
echo 'Welcome User, ' . $_SESSION['user']['name'];
?>
<a href="<?php echo $base; ?>/logout">Logout</a>