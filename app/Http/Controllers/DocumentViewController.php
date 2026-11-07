<?php

namespace App\Http\Controllers;

use App\Models\DownloadRequest;
use App\Models\Research;
use App\Models\ResearchFile;
use App\Services\AI\AIService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DocumentViewController extends Controller
{
    /**
     * Ensure FPDI and FPDF classes are available.
     */
    private function ensureFpdiLoaded(): void
    {
        if (!class_exists(\FPDF::class) && file_exists(base_path('vendor/setasign/fpdf/fpdf.php'))) {
            require_once base_path('vendor/setasign/fpdf/fpdf.php');
        }
        if (!class_exists(\setasign\Fpdi\Fpdi::class) && file_exists(base_path('vendor/setasign/fpdi/src/autoload.php'))) {
            require_once base_path('vendor/setasign/fpdi/src/autoload.php');
        }
    }

    /**
     * Convert a Word document (.docx) to PDF locally in pure PHP.
     */
    public function convertDocxToPdfLocally(string $docxPath, string $outputPath): bool
    {
        $this->ensureFpdiLoaded();

        if (!file_exists($docxPath) || !class_exists(\FPDF::class)) {
            return false;
        }

        try {
            $realPath = realpath($docxPath) ?: $docxPath;
            $xml = $this->extractDocxXml($docxPath, $realPath);

            if (!$xml) {
                return $this->generateFallbackPdfForFile(null, $docxPath, $outputPath);
            }

            $dom = new \DOMDocument();
            if (!@$dom->loadXML($xml)) {
                return $this->generateFallbackPdfForFile(null, $docxPath, $outputPath);
            }

            $xpath = new \DOMXPath($dom);
            $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
            $xpath->registerNamespace('wp', 'http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing');
            $xpath->registerNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
            $xpath->registerNamespace('pic', 'http://schemas.openxmlformats.org/drawingml/2006/picture');
            $xpath->registerNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
            $xpath->registerNamespace('v', 'urn:schemas-microsoft-com:vml');

            // Extract all embedded images and logos from docx
            $imageMap = $this->extractDocxImages($docxPath, $realPath);
            $floatingBottom = 0;

            // Render PDF via FPDF with academic layout margins
            $fpdf = new \FPDF('P', 'mm', 'A4');
            $fpdf->SetMargins(25, 25, 20);
            $fpdf->SetAutoPageBreak(true, 20);
            $fpdf->AddPage();
            $fpdf->SetFont('Helvetica', '', 10);

            $pageWidth = 210 - 45; // 165mm printable width
            $consecutiveBlank = 0;

            // Process top-level body elements in exact document order
            $bodyNodes = $xpath->query('//w:body/*');
            if (!$bodyNodes || $bodyNodes->length === 0) {
                return $this->generateFallbackPdfForFile(null, $docxPath, $outputPath);
            }

            foreach ($bodyNodes as $node) {
                $tag = $node->nodeName;

                if ($tag === 'w:p') {
                    // Check for explicit page break in paragraph
                    $hasPageBreak = $xpath->query('.//w:br[@w:type="page"] | .//w:pageBreakBefore', $node)->length > 0;
                    if ($hasPageBreak) {
                        $fpdf->AddPage();
                        $consecutiveBlank = 0;
                        $floatingBottom = 0;
                    }

                    // Render embedded drawings / logos / pictures in this paragraph
                    $drawings = $xpath->query('.//w:drawing | .//w:pict', $node);
                    if ($drawings && $drawings->length > 0 && !empty($imageMap)) {
                        foreach ($drawings as $d) {
                            $blip = $xpath->query('.//a:blip/@r:embed | .//v:imagedata/@r:id', $d)->item(0)?->nodeValue;
                            if ($blip && isset($imageMap[$blip])) {
                                $imgPath = $imageMap[$blip]['path'];
                                
                                $cx = intval($xpath->query('.//wp:extent/@cx', $d)->item(0)?->nodeValue ?? 0);
                                $cy = intval($xpath->query('.//wp:extent/@cy', $d)->item(0)?->nodeValue ?? 0);
                                $wMm = ($cx > 0) ? round($cx / 36000, 1) : 25;
                                $hMm = ($cy > 0) ? round($cy / 36000, 1) : 25;
                                if ($wMm > 165) {
                                    $ratio = 165 / $wMm;
                                    $wMm = 165;
                                    $hMm = round($hMm * $ratio, 1);
                                }

                                $posH = $xpath->query('.//wp:positionH/wp:posOffset', $d)->item(0)?->nodeValue;
                                $posV = $xpath->query('.//wp:positionV/wp:posOffset', $d)->item(0)?->nodeValue;

                                try {
                                    if ($posH !== null && $posV !== null) {
                                        // Floating anchor image (e.g. logos flanking cover header)
                                        $imgX = 25 + round(intval($posH) / 36000, 1);
                                        $imgY = max(15, $fpdf->GetY() + round(intval($posV) / 36000, 1));
                                        $fpdf->Image($imgPath, $imgX, $imgY, $wMm, $hMm);
                                        $floatingBottom = max($floatingBottom, $imgY + $hMm);
                                    } else {
                                        // Inline image
                                        $jc = $xpath->query('.//w:pPr/w:jc/@w:val', $node)->item(0)?->nodeValue;
                                        if ($jc === 'right') {
                                            $imgX = 210 - 20 - $wMm;
                                        } elseif ($jc === 'left') {
                                            $imgX = 25;
                                        } else {
                                            $imgX = (210 - $wMm) / 2;
                                        }

                                        $imgY = $fpdf->GetY();
                                        if ($imgY + $hMm > 270) {
                                            $fpdf->AddPage();
                                            $imgY = $fpdf->GetY();
                                        }
                                        $fpdf->Image($imgPath, $imgX, $imgY, $wMm, $hMm);
                                        $fpdf->SetY($imgY + $hMm + 2);
                                    }
                                } catch (\Throwable $imgEx) {
                                    Log::warning("FPDF Image insertion warning: " . $imgEx->getMessage());
                                }
                            }
                        }
                    }

                    // Extract text from paragraph runs
                    $text = '';
                    foreach ($xpath->query('.//w:t', $node) as $t) {
                        $text .= $t->nodeValue;
                    }
                    $cleanText = trim($text);

                    // Preserve intentional vertical spacing between sections (up to 3 consecutive empty lines)
                    if ($cleanText === '') {
                        $consecutiveBlank++;
                        if ($consecutiveBlank <= 3) {
                            if ($floatingBottom > 0 && $fpdf->GetY() < $floatingBottom) {
                                $fpdf->SetY($floatingBottom + 2);
                                $floatingBottom = 0;
                            } else {
                                $fpdf->Ln(4.5);
                            }
                        }
                        continue;
                    }
                    $consecutiveBlank = 0;

                    // Spacing before
                    $spacingBefore = intval($xpath->query('.//w:pPr/w:spacing/@w:before', $node)->item(0)?->nodeValue ?? 0);
                    if ($spacingBefore > 0) {
                        $fpdf->Ln(min(6, round($spacingBefore / 40)));
                    }

                    // Alignment: center, justify (both), right, left
                    $jc = $xpath->query('.//w:pPr/w:jc/@w:val', $node)->item(0)?->nodeValue;
                    $align = 'L';
                    if ($jc === 'center') {
                        $align = 'C';
                    } elseif ($jc === 'both' || $jc === 'justify') {
                        $align = 'J';
                    } elseif ($jc === 'right') {
                        $align = 'R';
                    }

                    // Heading / Title check
                    $pStyle = strtolower($xpath->query('.//w:pPr/w:pStyle/@w:val', $node)->item(0)?->nodeValue ?? '');
                    $isHeading = str_contains($pStyle, 'heading') || str_contains($pStyle, 'title');

                    // Font styling (bold, italic)
                    $isBold = $isHeading || ($xpath->query('.//w:b', $node)->length > 0);
                    $isItalic = $xpath->query('.//w:i', $node)->length > 0;
                    $fontStyle = ($isBold ? 'B' : '') . ($isItalic ? 'I' : '');

                    // Font size hierarchy
                    $szVal = intval($xpath->query('.//w:sz/@w:val', $node)->item(0)?->nodeValue ?? 0);
                    $fontSize = 10;
                    if ($szVal > 0) {
                        $pt = round($szVal / 2);
                        if ($pt >= 16) $fontSize = 14;
                        elseif ($pt >= 13) $fontSize = 12;
                        elseif ($pt >= 11) $fontSize = 10.5;
                        elseif ($pt <= 8) $fontSize = 8.5;
                        else $fontSize = 10;
                    } elseif ($isHeading) {
                        $fontSize = 13;
                    } elseif ($isBold && ($align === 'C')) {
                        $fontSize = 11.5;
                    }

                    // Academic first-line indent for justified/left body text
                    $firstLine = intval($xpath->query('.//w:pPr/w:ind/@w:firstLine', $node)->item(0)?->nodeValue ?? 0);
                    if ($firstLine > 0 && ($align === 'J' || $align === 'L') && !$isBold && !$isHeading) {
                        $cleanText = "     " . $cleanText;
                    }

                    // List item / bullet formatting
                    $hasNum = $xpath->query('.//w:pPr/w:numPr', $node)->length > 0;
                    if ($hasNum) {
                        $cleanText = chr(149) . '  ' . $cleanText;
                    }

                    // Character encoding conversion with accents and special characters
                    $encodedText = @iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $cleanText);
                    if ($encodedText === false || $encodedText === '') {
                        $encodedText = @mb_convert_encoding($cleanText, 'windows-1252', 'UTF-8');
                    }
                    if ($encodedText === false || $encodedText === '') {
                        $encodedText = utf8_decode($cleanText);
                    }
                    $encodedText = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', ' ', $encodedText);

                    $fpdf->SetFont('Helvetica', $fontStyle, $fontSize);
                    if ($isBold) {
                        $fpdf->SetTextColor(15, 23, 42);
                    } else {
                        $fpdf->SetTextColor(30, 41, 59);
                    }

                    $lineHeight = ($fontSize >= 13) ? 6.5 : (($fontSize >= 11) ? 5.5 : 4.8);
                    $fpdf->MultiCell(0, $lineHeight, $encodedText, 0, $align);

                    // Spacing after
                    $spacingAfter = intval($xpath->query('.//w:pPr/w:spacing/@w:after', $node)->item(0)?->nodeValue ?? 0);
                    if ($spacingAfter > 0) {
                        $fpdf->Ln(min(5, round($spacingAfter / 40)));
                    } else {
                        $fpdf->Ln(1.2);
                    }
                } elseif ($tag === 'w:tbl') {
                    // Render Table row by row with structured columns and borders
                    $rows = $xpath->query('.//w:tr', $node);
                    if ($rows && $rows->length > 0) {
                        $fpdf->Ln(2);
                        $firstRowCells = $xpath->query('.//w:tc', $rows->item(0));
                        $colCount = max(1, $firstRowCells ? $firstRowCells->length : 1);
                        $colWidth = round($pageWidth / $colCount, 1);

                        foreach ($rows as $rIdx => $row) {
                            $cells = $xpath->query('.//w:tc', $row);
                            $cellData = [];

                            foreach ($cells as $cell) {
                                $cText = '';
                                foreach ($xpath->query('.//w:t', $cell) as $ct) {
                                    $cText .= $ct->nodeValue;
                                }
                                $cText = trim($cText);
                                $cEnc = @iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $cText) ?: utf8_decode($cText);
                                $cEnc = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', ' ', $cEnc);
                                $cellData[] = $cEnc;
                            }

                            if ($fpdf->GetY() > 265) {
                                $fpdf->AddPage();
                            }

                            $rowHeight = 6.5;
                            $isHeader = ($rIdx === 0);
                            $fpdf->SetFont('Helvetica', $isHeader ? 'B' : '', 9.5);
                            if ($isHeader) {
                                $fpdf->SetFillColor(241, 245, 249);
                                $fpdf->SetTextColor(15, 23, 42);
                            } else {
                                $fpdf->SetFillColor(255, 255, 255);
                                $fpdf->SetTextColor(51, 65, 85);
                            }

                            foreach ($cellData as $cIdx => $cVal) {
                                $fpdf->Cell($colWidth, $rowHeight, substr($cVal, 0, 45), 1, 0, 'L', true);
                            }
                            $fpdf->Ln($rowHeight);
                        }
                        $fpdf->Ln(3);
                        $consecutiveBlank = 0;
                    }
                }
            }

            if (!is_dir(dirname($outputPath))) {
                mkdir(dirname($outputPath), 0755, true);
            }

            $fpdf->Output($outputPath, 'F');

            // Cleanup temporary image files
            if (!empty($imageMap)) {
                foreach ($imageMap as $img) {
                    if (isset($img['path']) && file_exists($img['path'])) {
                        @unlink($img['path']);
                        @rmdir(dirname($img['path']));
                    }
                }
            }

            return file_exists($outputPath) && filesize($outputPath) > 100;
        } catch (\Throwable $e) {
            Log::warning("Local docx to pdf conversion failed: " . $e->getMessage());
            return $this->generateFallbackPdfForFile(null, $docxPath, $outputPath);
        }
    }

    /**
     * Extract images and media relationship mappings from docx file.
     * Returns an array mapping rId => ['path' => tempFilePath, 'ext' => extension].
     */
    private function extractDocxImages(string $docxPath, string $realPath): array
    {
        $imageMap = [];
        $mediaBytes = [];
        $relsXml = null;

        // 1. Try ZipArchive
        if (class_exists(\ZipArchive::class)) {
            $zip = new \ZipArchive();
            $targets = array_unique([$realPath, $docxPath]);
            foreach ($targets as $target) {
                if ($zip->open($target, \ZipArchive::RDONLY) === true || $zip->open($target) === true) {
                    $relsXml = $zip->getFromName('word/_rels/document.xml.rels');
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $name = $zip->getNameIndex($i);
                        if (str_starts_with(strtolower($name), 'word/media/')) {
                            $mediaBytes[strtolower($name)] = $zip->getFromIndex($i);
                        }
                    }
                    $zip->close();
                    if (!empty($mediaBytes)) {
                        break;
                    }
                }
            }
        }

        // 2. Pure-PHP direct scanner fallback if ZipArchive was unavailable or missed any files
        if (empty($mediaBytes) || !$relsXml) {
            $rawBytes = @file_get_contents($docxPath);
            if ($rawBytes) {
                $offset = 0;
                while (($pos = strpos($rawBytes, "PK\x03\x04", $offset)) !== false) {
                    $header = substr($rawBytes, $pos, 30);
                    if (strlen($header) < 30) {
                        break;
                    }
                    $unpacked = unpack('vversion/vflags/vmethod/vmtime/vmdate/Vcrc/VcompSize/VuncompSize/vnameLen/vextraLen', substr($header, 4, 26));
                    $name = substr($rawBytes, $pos + 30, $unpacked['nameLen']);
                    $dataOffset = $pos + 30 + $unpacked['nameLen'] + $unpacked['extraLen'];
                    $nameLower = strtolower($name);

                    if ($nameLower === 'word/_rels/document.xml.rels' && !$relsXml) {
                        $compData = substr($rawBytes, $dataOffset, $unpacked['compSize']);
                        $relsXml = ($unpacked['method'] === 0) ? $compData : @gzinflate($compData);
                    } elseif (str_starts_with($nameLower, 'word/media/') && !isset($mediaBytes[$nameLower])) {
                        $compData = substr($rawBytes, $dataOffset, $unpacked['compSize']);
                        $mediaBytes[$nameLower] = ($unpacked['method'] === 0) ? $compData : @gzinflate($compData);
                    }
                    $offset = $dataOffset;
                }
            }
        }

        // Parse relationships to map rId => temp file for FPDF insertion
        if ($relsXml && !empty($mediaBytes)) {
            $relsDom = new \DOMDocument();
            if (@$relsDom->loadXML($relsXml)) {
                $tempSubdir = sys_get_temp_dir() . '/capstone_img_' . uniqid();
                @mkdir($tempSubdir, 0755, true);

                foreach ($relsDom->getElementsByTagName('Relationship') as $rel) {
                    $type = $rel->getAttribute('Type');
                    if (str_contains($type, '/image')) {
                        $rId = $rel->getAttribute('Id');
                        $target = $rel->getAttribute('Target');
                        $targetLower = strtolower($target);
                        $zipKey = str_starts_with($targetLower, 'media/') ? 'word/' . $targetLower : ltrim($targetLower, '/');

                        if (isset($mediaBytes[$zipKey])) {
                            $ext = strtolower(pathinfo($target, PATHINFO_EXTENSION) ?: 'png');
                            if (!in_array($ext, ['png', 'jpg', 'jpeg'])) {
                                $ext = 'png';
                            }
                            $tmpFile = $tempSubdir . '/' . $rId . '.' . $ext;
                            @file_put_contents($tmpFile, $mediaBytes[$zipKey]);
                            if (file_exists($tmpFile) && filesize($tmpFile) > 0) {
                                $imageMap[$rId] = [
                                    'path' => $tmpFile,
                                    'ext' => $ext,
                                ];
                            }
                        }
                    }
                }
            }
        }

        return $imageMap;
    }

    /**
     * Robust multi-layer extraction of word/document.xml from any docx file.
     */
    private function extractDocxXml(string $docxPath, string $realPath): ?string
    {
        // 1. Try ZipArchive with RDONLY and default modes
        if (class_exists(\ZipArchive::class)) {
            $zip = new \ZipArchive();
            $targets = array_unique([$realPath, $docxPath]);
            foreach ($targets as $target) {
                if ($zip->open($target, \ZipArchive::RDONLY) === true || $zip->open($target) === true) {
                    $xml = $zip->getFromName('word/document.xml');
                    if (!$xml) {
                        for ($i = 0; $i < $zip->numFiles; $i++) {
                            $entry = $zip->getNameIndex($i);
                            if (strcasecmp($entry, 'word/document.xml') === 0 || preg_match('/word\/document.*\.xml$/i', $entry)) {
                                $xml = $zip->getFromIndex($i);
                                break;
                            }
                        }
                    }
                    $zip->close();
                    if (!empty($xml)) {
                        return $xml;
                    }
                }
            }
        }

        // 2. Pure-PHP direct ZIP local header scanner
        $rawBytes = @file_get_contents($docxPath);
        if ($rawBytes) {
            $offset = 0;
            while (($pos = strpos($rawBytes, "PK\x03\x04", $offset)) !== false) {
                $header = substr($rawBytes, $pos, 30);
                if (strlen($header) < 30) {
                    break;
                }
                $unpacked = unpack('vversion/vflags/vmethod/vmtime/vmdate/Vcrc/VcompSize/VuncompSize/vnameLen/vextraLen', substr($header, 4, 26));
                $nameLen = $unpacked['nameLen'];
                $extraLen = $unpacked['extraLen'];
                $name = substr($rawBytes, $pos + 30, $nameLen);
                $dataOffset = $pos + 30 + $nameLen + $extraLen;

                if (strcasecmp($name, 'word/document.xml') === 0 || preg_match('/word\/document.*\.xml$/i', $name)) {
                    $method = $unpacked['method'];
                    $compSize = $unpacked['compSize'];
                    $data = substr($rawBytes, $dataOffset, $compSize);
                    if ($method === 0) {
                        return $data;
                    }
                    if ($method === 8) {
                        $inflated = @gzinflate($data);
                        if (!empty($inflated)) {
                            return $inflated;
                        }
                    }
                }
                $offset = $dataOffset;
            }
        }

        // 3. zip:// stream wrapper fallback
        $normalized = str_replace('\\', '/', $realPath);
        $streamXml = @file_get_contents('zip://' . $normalized . '#word/document.xml');
        if (!empty($streamXml)) {
            return $streamXml;
        }

        return null;
    }

    /**
     * Generate a clean fallback PDF cover page for an unreadable/unconvertible attached file.
     * NEVER dumps raw zip binary data.
     */
    private function generateFallbackPdfForFile($fileModel, string $srcPath, string $outputPath): bool
    {
        $this->ensureFpdiLoaded();
        if (!class_exists(\FPDF::class)) {
            return false;
        }

        try {
            $fpdf = new \FPDF('P', 'mm', 'A4');
            $fpdf->SetMargins(20, 20, 20);
            $fpdf->SetAutoPageBreak(true, 20);
            $fpdf->AddPage();
            $fpdf->SetFont('Helvetica', 'B', 14);
            $fpdf->SetTextColor(15, 23, 42);
            $fileName = is_object($fileModel) ? ($fileModel->original_name ?? basename($srcPath)) : basename($srcPath);
            $fpdf->Cell(0, 10, 'Attachment: ' . $fileName, 0, 1, 'C');
            $fpdf->Ln(8);

            $fpdf->SetFont('Helvetica', '', 11);
            $fpdf->SetTextColor(71, 85, 105);
            $sizeText = file_exists($srcPath) ? (round(filesize($srcPath) / 1024, 1) . ' KB') : 'Available in repository';
            $fpdf->MultiCell(0, 6, "This section represents attached manuscript document: {$fileName}.\nFile size: {$sizeText}\nThis document is part of the complete research manuscript.");

            if (!is_dir(dirname($outputPath))) {
                mkdir(dirname($outputPath), 0755, true);
            }
            $fpdf->Output($outputPath, 'F');
            return file_exists($outputPath) && filesize($outputPath) > 100;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Public Preview: Serve ONLY the first page of the first uploaded file as a PDF for guest users.
     */
    public function preview(Research $research, AIService $ai)
    {
        $this->ensureFpdiLoaded();

        // 1. Get the first uploaded file that actually exists on disk in sequential order
        $files = $research->files()->orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get();
        $primaryPath = null;
        $displayName = null;

        foreach ($files as $f) {
            if ($f->file_path && Storage::disk('public')->exists($f->file_path)) {
                $primaryPath = $f->file_path;
                $displayName = $f->original_name;
                break;
            }
        }

        // Fallback to research->pdf_path if no valid file in files relation
        if (!$primaryPath && $research->pdf_path && Storage::disk('public')->exists($research->pdf_path)) {
            $primaryPath = $research->pdf_path;
        }

        if (!$primaryPath) {
            return $this->serveMissingFileNotice($research, 'Guest Preview');
        }

        $extension = strtolower(pathinfo($primaryPath, PATHINFO_EXTENSION));
        $absoluteSourcePath = Storage::disk('public')->path($primaryPath);

        // 2. If it's a DOCX file, check cache first or convert locally
        if ($extension !== 'pdf') {
            $pdfRelativePath = 'researches/pdf_cache/' . pathinfo($primaryPath, PATHINFO_FILENAME) . '.pdf';
            $absolutePdfPath = Storage::disk('public')->path($pdfRelativePath);

            if (Storage::disk('public')->exists($pdfRelativePath)) {
                $cachedContent = @file_get_contents($absolutePdfPath);
                if ($cachedContent && (strpos($cachedContent, 'This section represents attached manuscript document') !== false || strpos($cachedContent, 'PK !') !== false)) {
                    @unlink($absolutePdfPath);
                }
            }

            if (!Storage::disk('public')->exists($pdfRelativePath)) {
                $converted = $this->convertDocxToPdfLocally($absoluteSourcePath, $absolutePdfPath);
                if (!$converted) {
                    try {
                        $ai->convertDocxToPdf($absoluteSourcePath, $absolutePdfPath);
                    } catch (\Throwable $e) {
                        // ignore
                    }
                }
            }

            if (!Storage::disk('public')->exists($pdfRelativePath)) {
                $this->generateFallbackPdfForFile(null, $absoluteSourcePath, $absolutePdfPath);
            }

            if (Storage::disk('public')->exists($pdfRelativePath)) {
                $absoluteSourcePath = $absolutePdfPath;
            } else {
                return $this->serveMissingFileNotice($research, 'Guest Preview');
            }
        }

        // 3. Generate or retrieve the cached 1-page PDF
        $mtime = file_exists($absoluteSourcePath) ? filemtime($absoluteSourcePath) : time();
        $cacheHash = substr(md5($primaryPath . '_' . $mtime), 0, 12);
        $previewRelativePath = 'researches/pdf_cache/page1_preview_' . $research->id . '_' . $cacheHash . '.pdf';
        $absolutePreviewPath = Storage::disk('public')->path($previewRelativePath);
        $safeTitle = preg_replace('/[^\w\s\-\.\(\)]/u', '_', $research->title);

        if (!Storage::disk('public')->exists($previewRelativePath) && strtolower(pathinfo($absoluteSourcePath, PATHINFO_EXTENSION)) === 'pdf') {
            try {
                if (class_exists(\setasign\Fpdi\Fpdi::class)) {
                    $fpdi = new \setasign\Fpdi\Fpdi();
                    $pageCount = $fpdi->setSourceFile($absoluteSourcePath);
                    if ($pageCount >= 1) {
                        $templateId = $fpdi->importPage(1);
                        $size = $fpdi->getTemplateSize($templateId);
                        $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
                        $fpdi->useTemplate($templateId);

                        if (!is_dir(dirname($absolutePreviewPath))) {
                            mkdir(dirname($absolutePreviewPath), 0755, true);
                        }
                        $fpdi->Output($absolutePreviewPath, 'F');
                    }
                }
            } catch (\Throwable $e) {
                Log::warning("FPDI Page 1 extraction exception for research {$research->id}: " . $e->getMessage());
            }
        }

        // If 1-page sliced PDF exists, serve it
        if (Storage::disk('public')->exists($previewRelativePath)) {
            $content = Storage::disk('public')->get($previewRelativePath);
            return response($content, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $safeTitle . ' (Page 1 Preview).pdf"',
                'Content-Length' => strlen($content),
                'Cache-Control' => 'no-cache, private, must-revalidate',
            ]);
        }

        // Fallback: If FPDI slicing failed on non-standard PDF compression, serve the source PDF directly
        // The embedded iframe URL uses #page=1&toolbar=0&navpanes=0&scrollbar=0 which restricts view to Page 1
        if (file_exists($absoluteSourcePath) && strtolower(pathinfo($absoluteSourcePath, PATHINFO_EXTENSION)) === 'pdf') {
            $content = file_get_contents($absoluteSourcePath);
            return response($content, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $safeTitle . ' (Page 1 Preview).pdf"',
                'Content-Length' => strlen($content),
                'Cache-Control' => 'no-cache, private, must-revalidate',
            ]);
        }

        return $this->serveMissingFileNotice($research, 'Guest Preview');
    }

    /**
     * Ensure physical file exists on local storage disk.
     * If Render's ephemeral container restarted and wiped the file, restore it seamlessly from TiDB!
     */
    private function ensureFileRestoredFromDatabase(?ResearchFile $file): void
    {
        if (!$file || empty($file->file_path) || empty($file->file_data)) {
            return;
        }

        if (!Storage::disk('public')->exists($file->file_path)) {
            try {
                $content = base64_decode($file->file_data);
                if ($content !== false && strlen($content) > 0) {
                    $absPath = Storage::disk('public')->path($file->file_path);
                    $dir = dirname($absPath);
                    if (!is_dir($dir)) {
                        @mkdir($dir, 0755, true);
                    }
                    Storage::disk('public')->put($file->file_path, $content);
                    Log::info("Restored missing manuscript file from TiDB database: {$file->file_path}");
                }
            } catch (\Throwable $e) {
                Log::warning("Failed to restore file from TiDB: " . $e->getMessage());
            }
        }
    }

    /**
     * View research document as PDF inline in the browser.
     */
    public function show(Research $research, AIService $ai)
    {
        if (request()->has('download')) {
            return $this->download($research, $ai);
        }

        $isStudent = auth()->check() && auth()->user()->role === 'student';
        $files = $research->files()->orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get();

        foreach ($files as $f) {
            $this->ensureFileRestoredFromDatabase($f);
        }

        // For student user: they can ONLY view the FIRST file (#1) and only Page 1 of that file
        if ($isStudent) {
            $firstFile = $files->first();
            $primaryPath = $firstFile?->file_path ?? $research->pdf_path;
            if (!$primaryPath || !Storage::disk('public')->exists($primaryPath)) {
                return $this->serveMissingFileNotice($research, 'Manuscript Reader');
            }

            return $this->serveSingleFile($research, $primaryPath, $ai, $firstFile?->original_name);
        }

        // --- Admin view logic below ---
        // If specific file requested via ?file_id=X
        $fileId = request()->query('file_id');
        if ($fileId && $specificFile = $files->firstWhere('id', $fileId)) {
            return $this->serveSingleFile($research, $specificFile->file_path, $ai, $specificFile->original_name);
        }

        // If continuous combined view is explicitly requested via ?combined=1 (Admin only)
        if (request()->has('combined') && $files->count() > 1) {
            $pdfData = $this->getMergedOrConvertedPdf($research, $ai);
            if ($pdfData) {
                return response($pdfData['content'], 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $pdfData['filename'] . '"',
                    'Content-Length' => strlen($pdfData['content']),
                ]);
            }
        }

        // Default: ALWAYS serve the first uploaded file (#1 primary view)
        $firstFile = $files->first();
        $primaryPath = $firstFile?->file_path ?? $research->pdf_path;
        if (!$primaryPath || !Storage::disk('public')->exists($primaryPath)) {
            return $this->serveMissingFileNotice($research, 'Manuscript Reader');
        }

        return $this->serveSingleFile($research, $primaryPath, $ai, $firstFile?->original_name);
    }

    /**
     * Download the research manuscript as a single PDF containing all uploaded files.
     */
    public function download(Research $research, AIService $ai)
    {
        if (!$this->canDownload($research)) {
            return redirect()->route('repository.show', $research)
                ->with('error', 'Download permission required. Please submit a download request and wait for faculty approval.');
        }

        $pdfData = $this->getMergedOrConvertedPdf($research, $ai);

        if (!$pdfData) {
            return redirect()->route('repository.show', $research)
                ->with('error', 'Unable to prepare download file. Please contact the administrator.');
        }

        $safeTitle = preg_replace('/[^\w\s\-\.\(\)]/u', '_', $research->title);
        $filename = (auth()->check() && auth()->user()->role === 'student')
            ? (trim($safeTitle) . '.pdf')
            : $pdfData['filename'];

        return response($pdfData['content'], 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => strlen($pdfData['content']),
        ]);
    }

    /**
     * Check if the authenticated user has permission to download this research paper.
     */
    public function canDownload(Research $research): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }

        // Admin can always download
        if ($user->role === 'admin') {
            return true;
        }

        // All student users must have an approved download request for this specific research project
        return DownloadRequest::where('user_id', $user->id)
            ->where('research_id', $research->id)
            ->where('status', 'approved')
            ->exists();
    }

    /**
     * Merge multiple PDF files into one output file locally using FPDI.
     */
    private function mergePdfsLocally(array $inputPdfPaths, string $outputPath): bool
    {
        $this->ensureFpdiLoaded();

        if (!class_exists(\setasign\Fpdi\Fpdi::class)) {
            return false;
        }

        try {
            $fpdi = new \setasign\Fpdi\Fpdi();
            $hasPages = false;

            foreach ($inputPdfPaths as $path) {
                if (!file_exists($path)) {
                    continue;
                }
                try {
                    $pageCount = $fpdi->setSourceFile($path);
                    for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                        $templateId = $fpdi->importPage($pageNo);
                        $size = $fpdi->getTemplateSize($templateId);
                        $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
                        $fpdi->useTemplate($templateId);
                        $hasPages = true;
                    }
                } catch (\Throwable $pageEx) {
                    Log::warning("Skipping incompatible page or file {$path}: " . $pageEx->getMessage());
                    try {
                        $fpdi->AddPage('P', [210, 297]);
                        $fpdi->SetFont('Helvetica', 'B', 14);
                        $fpdi->SetTextColor(15, 23, 42);
                        $fpdi->Cell(0, 10, 'Attachment: ' . basename($path), 0, 1, 'C');
                        $fpdi->Ln(8);
                        $fpdi->SetFont('Helvetica', '', 11);
                        $fpdi->SetTextColor(71, 85, 105);
                        $fpdi->MultiCell(0, 6, "Attached Manuscript Section: " . basename($path));
                        $hasPages = true;
                    } catch (\Throwable $fEx) {
                        // ignore
                    }
                }
            }

            if (!$hasPages) {
                if (file_exists($outputPath)) {
                    @unlink($outputPath);
                }
                return false;
            }

            if (!is_dir(dirname($outputPath))) {
                mkdir(dirname($outputPath), 0755, true);
            }

            $fpdi->Output($outputPath, 'F');
            return file_exists($outputPath) && filesize($outputPath) > 100;
        } catch (\Throwable $e) {
            Log::warning("Local FPDI merge failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get or generate the single merged PDF file content (and safe filename) containing all files.
     */
    public function getMergedOrConvertedPdf(Research $research, AIService $ai): ?array
    {
        $this->ensureFpdiLoaded();
        $files = $research->files()->orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get();
        foreach ($files as $f) {
            $this->ensureFileRestoredFromDatabase($f);
        }
        $safeTitle = preg_replace('/[^\w\s\-\.\(\)]/u', '_', $research->title);

        // 1. Multiple files: merge into single PDF in sequential order
        if ($files->count() > 1) {
            $inputPdfPaths = [];

            foreach ($files as $f) {
                if (!Storage::disk('public')->exists($f->file_path)) {
                    continue;
                }

                $ext = strtolower(pathinfo($f->file_path, PATHINFO_EXTENSION));
                $absoluteSrcPath = Storage::disk('public')->path($f->file_path);

                if ($ext === 'pdf') {
                    $inputPdfPaths[] = $absoluteSrcPath;
                } else {
                    // Check if cached PDF exists
                    $cachedPdfRel = 'researches/pdf_cache/' . pathinfo($f->file_path, PATHINFO_FILENAME) . '.pdf';
                    $cachedPdfAbs = Storage::disk('public')->path($cachedPdfRel);

                    if (Storage::disk('public')->exists($cachedPdfRel) && file_exists($cachedPdfAbs)) {
                        $cachedContent = @file_get_contents($cachedPdfAbs);
                        if ($cachedContent && (strpos($cachedContent, 'This section represents attached manuscript document') !== false || strpos($cachedContent, 'PK !') !== false)) {
                            @unlink($cachedPdfAbs);
                        }
                    }

                    if (Storage::disk('public')->exists($cachedPdfRel) && file_exists($cachedPdfAbs) && filesize($cachedPdfAbs) > 100) {
                        $inputPdfPaths[] = $cachedPdfAbs;
                    } else {
                        // 1. Attempt local pure-PHP conversion
                        $converted = $this->convertDocxToPdfLocally($absoluteSrcPath, $cachedPdfAbs);

                        // 2. Fallback to AI service
                        if (!$converted || !file_exists($cachedPdfAbs) || filesize($cachedPdfAbs) < 100) {
                            try {
                                if ($ai->convertDocxToPdf($absoluteSrcPath, $cachedPdfAbs) && file_exists($cachedPdfAbs) && filesize($cachedPdfAbs) > 100) {
                                    $converted = true;
                                }
                            } catch (\Throwable $e) {
                                // ignore
                            }
                        }

                        // 3. Fallback: generate a fallback PDF for this file if conversion could not parse it
                        if (!$converted || !file_exists($cachedPdfAbs) || filesize($cachedPdfAbs) < 100) {
                            $converted = $this->generateFallbackPdfForFile($f, $absoluteSrcPath, $cachedPdfAbs);
                        }

                        if ($converted && file_exists($cachedPdfAbs) && filesize($cachedPdfAbs) > 100) {
                            $inputPdfPaths[] = $cachedPdfAbs;
                        }
                    }
                }
            }

            if (!empty($inputPdfPaths)) {
                $hash = substr(md5(implode('|', $inputPdfPaths)), 0, 10);
                $mergedRelativePath = 'researches/pdf_cache/merged_' . $research->id . '_' . $hash . '.pdf';
                $absoluteOutputPath = Storage::disk('public')->path($mergedRelativePath);

                if (Storage::disk('public')->exists($mergedRelativePath) && file_exists($absoluteOutputPath)) {
                    $mergedContent = @file_get_contents($absoluteOutputPath);
                    if ($mergedContent && (strpos($mergedContent, 'This section represents attached manuscript document') !== false || strpos($mergedContent, 'PK !') !== false)) {
                        @unlink($absoluteOutputPath);
                    }
                }

                if (!Storage::disk('public')->exists($mergedRelativePath) || !file_exists($absoluteOutputPath) || filesize($absoluteOutputPath) < 100) {
                    // 1. Try local pure-PHP FPDI merge
                    $merged = $this->mergePdfsLocally($inputPdfPaths, $absoluteOutputPath);

                    // 2. Fallback to AI service if local FPDI did not complete
                    if (!$merged || !file_exists($absoluteOutputPath) || filesize($absoluteOutputPath) < 100) {
                        try {
                            $ai->mergeDocuments($inputPdfPaths, $absoluteOutputPath);
                        } catch (\Throwable $e) {
                            Log::warning("AI service merge fallback failed: " . $e->getMessage());
                        }
                    }
                }

                if (Storage::disk('public')->exists($mergedRelativePath) && file_exists($absoluteOutputPath) && filesize($absoluteOutputPath) > 100) {
                    $fileContent = Storage::disk('public')->get($mergedRelativePath);
                    return [
                        'content' => $fileContent,
                        'filename' => $safeTitle . '.pdf',
                    ];
                }

                // If merging failed but at least one PDF file exists, serve the first PDF as fallback
                if (!empty($inputPdfPaths) && file_exists($inputPdfPaths[0])) {
                    return [
                        'content' => file_get_contents($inputPdfPaths[0]),
                        'filename' => $safeTitle . '.pdf',
                    ];
                }
            }
        }

        // 2. Single file: PDF or converted DOCX
        $primaryPath = $files->first()?->file_path ?? $research->pdf_path;
        if (!$primaryPath || !Storage::disk('public')->exists($primaryPath)) {
            return null;
        }

        $extension = strtolower(pathinfo($primaryPath, PATHINFO_EXTENSION));

        if ($extension === 'pdf') {
            return [
                'content' => Storage::disk('public')->get($primaryPath),
                'filename' => $safeTitle . '.pdf',
            ];
        }

        // DOCX conversion to PDF
        $pdfRelativePath = 'researches/pdf_cache/' . pathinfo($primaryPath, PATHINFO_FILENAME) . '.pdf';
        $absolutePdfPath = Storage::disk('public')->path($pdfRelativePath);
        $absoluteDocxPath = Storage::disk('public')->path($primaryPath);

        if (Storage::disk('public')->exists($pdfRelativePath)) {
            return [
                'content' => Storage::disk('public')->get($pdfRelativePath),
                'filename' => $safeTitle . '.pdf',
            ];
        }

        $converted = $this->convertDocxToPdfLocally($absoluteDocxPath, $absolutePdfPath);
        if (!$converted) {
            try {
                $converted = $ai->convertDocxToPdf($absoluteDocxPath, $absolutePdfPath);
            } catch (\Throwable $e) {
                Log::warning("Single DOCX conversion failed: {$primaryPath}");
            }
        }

        if (Storage::disk('public')->exists($pdfRelativePath)) {
            return [
                'content' => Storage::disk('public')->get($pdfRelativePath),
                'filename' => $safeTitle . '.pdf',
            ];
        }

        return null;
    }

    /**
     * Serve a single file inline (PDF as inline PDF, DOCX via converted PDF or embedded docx-preview).
     */
    /**
     * Slice and return Page 1 only of a PDF for student user view.
     */
    private function servePageOneOnly(Research $research, string $absolutePdfPath, string $displayName)
    {
        $this->ensureFpdiLoaded();

        $mtime = file_exists($absolutePdfPath) ? filemtime($absolutePdfPath) : time();
        $cacheHash = substr(md5($absolutePdfPath . '_' . $mtime), 0, 12);
        $page1RelPath = 'researches/pdf_cache/student_page1_' . $research->id . '_' . $cacheHash . '.pdf';
        $page1AbsPath = Storage::disk('public')->path($page1RelPath);

        if (!Storage::disk('public')->exists($page1RelPath) && class_exists(\setasign\Fpdi\Fpdi::class)) {
            try {
                $fpdi = new \setasign\Fpdi\Fpdi();
                $pageCount = $fpdi->setSourceFile($absolutePdfPath);
                if ($pageCount >= 1) {
                    $templateId = $fpdi->importPage(1);
                    $size = $fpdi->getTemplateSize($templateId);
                    $fpdi->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $fpdi->useTemplate($templateId);

                    if (!is_dir(dirname($page1AbsPath))) {
                        mkdir(dirname($page1AbsPath), 0755, true);
                    }
                    $fpdi->Output($page1AbsPath, 'F');
                }
            } catch (\Throwable $e) {
                Log::warning("Student Page 1 slicing exception for research {$research->id}: " . $e->getMessage());
            }
        }

        if (Storage::disk('public')->exists($page1RelPath)) {
            $existingP1 = @file_get_contents($page1AbsPath);
            if ($existingP1 && (strpos($existingP1, 'This section represents attached manuscript document') !== false || strpos($existingP1, 'PK !') !== false)) {
                @unlink($page1AbsPath);
            } else {
                $content = Storage::disk('public')->get($page1RelPath);
                return response($content, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $displayName . ' (Page 1).pdf"',
                    'Content-Length' => strlen($content),
                    'Cache-Control' => 'no-cache, private, must-revalidate',
                ]);
            }
        }

        // Fallback if slicing failed: serve source directly
        $content = file_get_contents($absolutePdfPath);
        return response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $displayName . ' (Page 1).pdf"',
            'Content-Length' => strlen($content),
            'Cache-Control' => 'no-cache, private, must-revalidate',
        ]);
    }

    private function serveSingleFile(Research $research, string $path, AIService $ai, ?string $originalName = null)
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $absoluteDocxPath = Storage::disk('public')->path($path);
        $displayName = $originalName ?: ($research->title . '.' . $extension);
        $isStudent = auth()->check() && auth()->user()->role === 'student';

        if ($extension === 'pdf') {
            $absolutePdfPath = Storage::disk('public')->path($path);
            if ($isStudent) {
                return $this->servePageOneOnly($research, $absolutePdfPath, pathinfo($displayName, PATHINFO_FILENAME));
            }

            $file = Storage::disk('public')->get($path);
            return response($file, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $displayName . '"',
                'Content-Length' => strlen($file),
            ]);
        }

        // For DOCX files: check if cached PDF already exists first
        $pdfRelativePath = 'researches/pdf_cache/' . pathinfo($path, PATHINFO_FILENAME) . '.pdf';
        $absolutePdfPath = Storage::disk('public')->path($pdfRelativePath);

        if (Storage::disk('public')->exists($pdfRelativePath)) {
            $existingContent = @file_get_contents($absolutePdfPath);
            if ($existingContent && (strpos($existingContent, 'This section represents attached manuscript document') !== false || strpos($existingContent, 'PK !') !== false)) {
                @unlink($absolutePdfPath);
            } else {
                if ($isStudent) {
                    return $this->servePageOneOnly($research, $absolutePdfPath, pathinfo($displayName, PATHINFO_FILENAME));
                }

                $file = Storage::disk('public')->get($pdfRelativePath);
                return response($file, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . pathinfo($displayName, PATHINFO_FILENAME) . '.pdf"',
                    'Content-Length' => strlen($file),
                ]);
            }
        }

        // If not cached, attempt local conversion
        $converted = $this->convertDocxToPdfLocally($absoluteDocxPath, $absolutePdfPath);
        if (!$converted) {
            try {
                $converted = $ai->convertDocxToPdf($absoluteDocxPath, $absolutePdfPath);
            } catch (\Throwable $e) {
                Log::warning("DOCX conversion exception: {$path}");
            }
        }

        if (!Storage::disk('public')->exists($pdfRelativePath)) {
            $this->generateFallbackPdfForFile(null, $absoluteDocxPath, $absolutePdfPath);
        }

        if (Storage::disk('public')->exists($pdfRelativePath)) {
            if ($isStudent) {
                return $this->servePageOneOnly($research, $absolutePdfPath, pathinfo($displayName, PATHINFO_FILENAME));
            }

            $file = Storage::disk('public')->get($pdfRelativePath);
            return response($file, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . pathinfo($displayName, PATHINFO_FILENAME) . '.pdf"',
                'Content-Length' => strlen($file),
            ]);
        }

        return $this->serveMissingFileNotice($research, 'Manuscript Reader');
    }

    /**
     * Clean PDF notice when a physical file is missing from storage.
     */
    private function serveMissingFileNotice(Research $research, string $context = 'Manuscript Viewer')
    {
        $this->ensureFpdiLoaded();
        $safeTitle = preg_replace('/[^\w\s\-\.\(\)]/u', '_', $research->title);

        if (class_exists(\FPDF::class)) {
            try {
                $fpdf = new \FPDF('P', 'mm', 'A4');
                $fpdf->AddPage();
                $fpdf->SetMargins(20, 20, 20);

                // Top decorative bar
                $fpdf->SetFillColor(12, 66, 51); // Capstone dark green
                $fpdf->Rect(0, 0, 210, 8, 'F');

                // Header
                $fpdf->Ln(14);
                $fpdf->SetFont('Helvetica', 'B', 16);
                $fpdf->SetTextColor(15, 23, 42);
                $fpdf->Cell(0, 10, 'JHCSC CAPSTONE REPOSITORY', 0, 1, 'C');

                $fpdf->SetFont('Helvetica', '', 10);
                $fpdf->SetTextColor(100, 116, 139);
                $fpdf->Cell(0, 6, 'Digital Manuscript Archival Services', 0, 1, 'C');

                $fpdf->Ln(10);
                $fpdf->SetDrawColor(226, 232, 240);
                $fpdf->Line(20, $fpdf->GetY(), 190, $fpdf->GetY());
                $fpdf->Ln(12);

                // Notice Box
                $boxY = $fpdf->GetY();
                $fpdf->SetFillColor(248, 250, 252);
                $fpdf->SetDrawColor(203, 213, 225);
                $fpdf->Rect(20, $boxY, 170, 80, 'DF');

                $fpdf->SetXY(25, $boxY + 8);
                $fpdf->SetFont('Helvetica', 'B', 11);
                $fpdf->SetTextColor(12, 66, 51);
                $fpdf->Cell(160, 7, strtoupper($context) . ' - ARCHIVAL NOTICE', 0, 1, 'L');

                $fpdf->Ln(2);
                $fpdf->SetX(25);
                $fpdf->SetFont('Helvetica', 'B', 10);
                $fpdf->SetTextColor(30, 41, 59);
                $cleanTitle = iconv('UTF-8', 'windows-1252//TRANSLIT', $research->title);
                $fpdf->MultiCell(160, 5, 'Title: ' . $cleanTitle);

                $fpdf->Ln(4);
                $fpdf->SetX(25);
                $fpdf->SetFont('Helvetica', '', 9.5);
                $fpdf->SetTextColor(71, 85, 105);
                $fpdf->MultiCell(160, 5, 'The uploaded manuscript file for this record is currently stored in institutional cloud storage and is being synchronized to this viewer node.');

                $fpdf->Ln(3);
                $fpdf->SetX(25);
                $fpdf->SetFont('Helvetica', 'I', 8.5);
                $fpdf->SetTextColor(100, 116, 139);
                $fpdf->MultiCell(160, 4.5, 'Institutional administrators can verify storage synchronization in storage/app/public/researches or re-upload the manuscript file.');

                $pdfString = $fpdf->Output('S');

                return response($pdfString, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $safeTitle . ' (Notice).pdf"',
                    'Content-Length' => strlen($pdfString),
                ]);
            } catch (\Throwable $e) {
                Log::warning("Error creating missing file notice PDF: " . $e->getMessage());
            }
        }

        return response('Manuscript document is undergoing archival synchronization.', 200, [
            'Content-Type' => 'text/plain',
        ]);
    }
}
