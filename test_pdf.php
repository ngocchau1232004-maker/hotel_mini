Trang file modules/room_types/create.php:

<?php

require 'vendor/autoload.php';

use Dompdf\Dompdf;

$dompdf = new Dompdf();

$html = '
<h1 style="text-align:center">
Xin chào Dompdf
</h1>

<p>Đây là file PDF đầu tiên.</p>
';

$dompdf->loadHtml($html);

$dompdf->setPaper('A4','portrait');

$dompdf->render();

$dompdf->stream("Test.pdf");