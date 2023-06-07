<html>
    <head>
        <style>
            p.inline{
                display: inline-block;                
            }
            span {font-size: 8px;}
            span.rate { font-size: 12px;}
        </style>
        <style type="text/css" media="print">
            @page 
            {
                size: auto;   /* auto is the initial value */
                margin: 0mm;  /* this affects the margin in the printer settings */
            }
            div.b128{
                border-left: 1px black solid;
                height: 30px;
            } 
        </style>
    </head>
    <body onload="window.print();">
        <div style="margin-left: 5%">
            <?php foreach($barcode AS $info): ?>            
                <?php echo $info; ?>
            <?php endforeach; ?>
        </div>
    </body>
</html>