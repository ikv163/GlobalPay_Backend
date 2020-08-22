<script type="text/javascript">
    //自制消息提示
    <?php
    if (isset($msg) && $msg != null){
    ?>
    layer.alert(<?php echo "'$msg'";?>);
    <?php
    }
    ?>
</script>
