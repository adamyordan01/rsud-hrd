<?php

namespace App\Http\Controllers;


use Endroid\QrCode\QrCode;
use Illuminate\Http\Request;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeEnlarge;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;

class QRController extends Controller
{
    public function index()
    {
        return view('qr-index');
    }

    public function create(Request $request)
    {
        $write = new PngWriter();
        $QrCOde = QrCode::create($request->url)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->setSize(300)
            ->setMargin(10)
            ->setRoundBlockSizeMode(new RoundBlockSizeModeEnlarge())
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        $label = null;

        if ($request->label) {
            $label = Label::create($request->label)
                ->setTextColor(new Color(0, 0, 0));
        }

        if ($request->hasFile('logo')) {
            if (!$request->logoWidth) {
                $logo = Logo::create($request->logo)
                    ->setResizeToHeight($request->logoHeight);
            } elseif (!$request->logoHeight) {
                $logo = Logo::create($request->logo)
                    ->setResizeToWidth($request->logoWidth);
            } elseif (!$request->logoHeight && !$request->logoWidth) {
                $logo = Logo::create($request->logo);
            } else {
                $logo = Logo::create($request->logo)
                    ->setResizeToWidth($request->logoWidth)
                    ->setResizeToHeight($request->logoHeight);
            }
            $result = $write->write($QrCOde, $logo, $label);
        } else {
            $result = $write->write($QrCOde, null, $label);
        }

        return $result->getDataUri();
    }
}
