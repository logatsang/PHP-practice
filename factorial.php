<html>
    <body>

    <?php

    function x ($a) {
        return $a == 0 ? 1 : $a * x($a-1);
    }

    ?>

    <?php echo $_GET["query"]; ?>! = <?php echo x($_GET["query"]); ?>

    </body>
</html> 