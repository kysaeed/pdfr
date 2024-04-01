<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;
use Smalot\PdfParser\PDFObject;


class MainController extends Controller
{
    public function index(Request $request)
    {
        $pdfFilename = 'catalog.pdf';

        /*
         * or
            $content = Storage::disk('local')->get($pdfFilename);
            $pdf = $parser->parseContent($content);
         */

        $parser = new Parser();

        $pdf = $parser->parseFile(storage_path('app/' . $pdfFilename));
        $objectList = $pdf->getObjectsByType('XObject');

        $order = 1;
        /** @var PDFObject $xobject */
        foreach ($objectList as $xobject) {
            //dump($xobject);
            $type = $xobject?->getHeader()?->getDetails();
            if ($type) {
                dump([$type['Type'] ?? null, $type['SubType'] ?? null]);
            }

            if (strtolower($type['Subtype'] ?? '') === 'image') {
                $fileContent = $xobject->getContent();
                Storage::disk('local')->put("img{$order}.jpg", $fileContent);

                $order++;
            }

        }

        dd('OK!!');

    }
}
