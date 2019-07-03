## PDFMerger for PHP (PHP 5 & 7 Compatible)
Uses Setasign/FPDI-FPDF
https://github.com/Setasign/FPDI-FPDF

To install add this line to your composer.json

```"fcarbah/pdf-merger": "^1.0"```

or

```composer require fcarbah/pdf-merger: "^1.0"```

### Example Usage
```php

use Classes\PdfMerger;
use Classes\Orientation;
use Classes\OutputFormat;

$pdf = new PdfMerger();

$pdf->addPdf('samplepdfs/one.pdf', '1, 3, 4');
$pdf->addPdf('samplepdfs/two.pdf', '1-2');
$pdf->addPdf('samplepdfs/three.pdf', 'all');

//You can optionally specify a different orientation for each PDF
$pdf->addPdf('samplepdfs/one.pdf', '1, 3, 4', Orientation::POTRAIT);
$pdf->addPdf('samplepdfs/two.pdf', '1-2', Orientation::LANDSCAPE);

$pdf->merge(OutPutFormat::STRING, 'samplepdfs/TEST2.pdf', Orientation::POTRAIT);
 
// This will be used for every PDF that doesn't have an orientation specified