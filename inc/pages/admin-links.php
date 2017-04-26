<?php
if (!defined('ABSPATH')) {
    return; 
}

$image_dir = plugins_url('img', dirname(__DIR__));

?>
<div class="cc-social">
    <a href="https://github.com/ClassCube/wordpress-lti-consumer">
        <img src="<?php echo $image_dir; ?>/github-32px.png">
    </a>
    <a href="https://www.facebook.com/ClassCube/">
        <img src="<?php echo $image_dir; ?>/facebook-32px.png">
    </a>
    <a href="https://twitter.com/classcube/">
        <img src="<?php echo $image_dir; ?>/twitter-32px.png">
    </a>
    <a href="https://www.linkedin.com/company/16245650/">
        <img src="<?php echo $image_dir; ?>/linkedin-32px.png">
    </a>
    <a href="https://www.youtube.com/channel/UCni3wW88-dLjVrMjWf8eG4A">
        <img src="<?php echo $image_dir; ?>/youtube-32px.png">
    </a>
</div>