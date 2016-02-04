<?php
if($_REQUEST['quarter'] == 1)
{
    $quarterText = '1st Quarter';
}
elseif($_REQUEST['quarter'] == 2)
{
    $quarterText = '2nd Quarter';
}
elseif($_REQUEST['quarter'] == 3)
{
    $quarterText = '3rd Quarter';
}
elseif($_REQUEST['quarter'] == 4)
{
    $quarterText = '4th Quarter';
}
?>
<html><head><script>
        function subst() {
            var vars={};
            var x=window.location.search.substring(1).split('&');
            for (var i in x) {var z=x[i].split('=',2);vars[z[0]] = unescape(z[1]);}
            var x=['frompage','topage','page','webpage','section','subsection','subsubsection'];
            for (var i in x) {
                var y = document.getElementsByClassName(x[i]);
                for (var j=0; j<y.length; ++j) y[j].textContent = vars[x[i]];
            }
        }
    </script></head><body style="border:0; margin: 0;" onload="subst()">
<pre>
<table style="border-bottom: 1px solid black; width: 100%">
    <tr>
        <td colspan="4">I certify that each person whose name appears in this roll are<br>
            entitled to cash assistacne. For the period of <strong><?php echo $quarterText ?></strong><br>
            Payroll <strong><?php echo $_REQUEST['year'] ?></strong></td>
    </tr>
    <tr>
        <td height="20px" colspan="4"></td>
    </tr>
    <tr>
        <td height="20px" colspan="4"></td>
    </tr>
    <tr>
        <td><strong><span style="font-size: large"><?php echo strtoupper($_REQUEST['siga']) ?></span></strong><br><?php echo ucwords(strtolower($_REQUEST['sigposa'])) ?></td>
        <td><strong><span style="font-size: large"><?php echo strtoupper($_REQUEST['sigb']) ?></span></strong><br><?php echo ucwords(strtolower($_REQUEST['sigposb'])) ?></td>
        <td><strong><span style="font-size: large"><?php echo strtoupper($_REQUEST['sigc']) ?></span></strong><br><?php echo ucwords(strtolower($_REQUEST['sigposc'])) ?></td>
        <td></td></tr>
    <tr>
        <td height="20px" colspan="4"></td>
    </tr>
    <tr>
        <td class="section"></td>
        <td></td>
        <td></td>
        <td style="text-align:right">
            Page <span class="page"></span> of <span class="topage"></span>
        </td>
    </tr>
</table>
</pre>
</body></html>