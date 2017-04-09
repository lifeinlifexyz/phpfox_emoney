<?php
$js = <<<EOF
    <script type="text/javascript">
    \$Behavior.init_elmoney_pay = function () {
        $('form[action$="/elmoney/pay/"]')
            .attr('onsubmit', "$(this).ajaxCall('elmoney.pay'); return false;");
    }
</script>
EOF;
echo $js;
