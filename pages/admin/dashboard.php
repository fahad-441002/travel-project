<?php
echo 'Welcome Admin, ' . $_SESSION['user']['name'];
?>
<a href="<?php echo $base; ?>/logout">Logout</a>