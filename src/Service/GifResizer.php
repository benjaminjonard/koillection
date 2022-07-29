<?php

declare(strict_types=1);

namespace App\Service;

class GifResizer
{
    public string $tempDir;
    private int $pointer = 0;
    private int $index = 0;
    private array $globalData = [];
    private array $imageData = [];
    private array $imageInfo = [];
    private $handle = null;
    private array $orgVars = [];
    private array $encData = [];
    private array $parsedFiles = [];
    private int $originalWidth = 0;
    private int $originalHeight = 0;
    private float $wr;
    private float $hr;
    private bool $decoding = false;

    public function __construct(string $publicPath)
    {
        $this->tempDir = $publicPath.'/tmp';
    }

    public function resize(string $path, string $thumbnailPath, int $width, int $height): void
    {
        $this->decode($path);
        $this->wr = $width / $this->originalWidth;
        $this->hr = $height / $this->originalHeight;
        $this->resizeFrames();
        $this->encode($thumbnailPath, $width, $height);
        $this->clearFrames();
    }

    /**
     * Parses the GIF animation into single frames.
     */
    private function decode(string $filename): void
    {
        $this->decoding = true;
        $this->clearVariables();
        $this->loadFile($filename);
        $this->getGifHeader();
        $this->getGraphicsExtension(0);
        $this->getApplicationData();
        $this->getApplicationData();
        $this->getimageBlock(0);
        $this->getGraphicsExtension(1);
        $this->getCommentData();
        $this->getApplicationData();
        $this->getimageBlock(1);
        while (!$this->checkByte(0x3B) && !$this->checkEOF()) {
            $this->getCommentData();
            $this->getGraphicsExtension(2);
            $this->getimageBlock(2);
        }
        $this->writeFrames(time());
        $this->closeFile();
        $this->decoding = false;
    }

    /**
     * Combines the parsed GIF frames into one single animation.
     */
    private function encode(string $newFilename, int $newWidth, int $newHeight): void
    {
        $string = '';
        $this->pointer = 0;
        $this->imageData = [];
        $this->imageInfo = [];
        $this->handle = null;
        $this->index = 0;

        $k = 0;
        foreach ($this->parsedFiles as $imagePart) {
            $this->loadFile($imagePart);
            $this->getGifHeader();
            $this->getApplicationData();
            $this->getCommentData();
            $this->getGraphicsExtension(0);
            $this->getimageBlock(0);

            // get transparent color index and color
            if (isset($this->encData[$this->index - 1])) {
                $gxData = $this->encData[$this->index - 1]['graphicsextension'];
            } else {
                $gxData = null;
            }

            $ghData = $this->imageInfo['gifheader'];
            $trColor = '';

            $hasTransparency = $gxData ? (bool) $gxData[3] : false;

            if ($hasTransparency) {
                $trcx = \ord($gxData[6]);
                $trColor = substr($ghData, 13 + $trcx * 3, 3);
            }

            // global color table to image data;
            $this->transferColorTable($this->imageInfo['gifheader'], $this->imageData[$this->index - 1]['imageData']);

            $imageBlock = &$this->imageData[$this->index - 1]['imageData'];

            // if transparency exists transfer transparency index
            if ($hasTransparency) {
                $hasLocalColorTable = ((\ord($imageBlock[9]) & 128) == 128);
                if ($hasLocalColorTable) {
                    // local table exists. determine boundaries and look for it.
                    $tableSize = (pow(2, (\ord($imageBlock[9]) & 7) + 1) * 3) + 10;
                    $this->orgVars[$this->index - 1]['transparent_color_index'] =
                        ((strrpos(substr($this->imageData[$this->index - 1]['imageData'], 0, $tableSize), $trColor) - 10) / 3);
                } else {
                    // local table doesnt exist, look at the global one.
                    $tableSize = (pow(2, (\ord($gxData[10]) & 7) + 1) * 3) + 10;
                    $this->orgVars[$this->index - 1]['transparent_color_index'] =
                        ((strrpos(substr($ghData, 0, $tableSize), $trColor) - 10) / 3);
                }
            }

            // apply original delay time,transparent index and disposal values to graphics extension

            if (!$this->imageData[$this->index - 1]['graphicsextension']) {
                $this->imageData[$this->index - 1]['graphicsextension'] = \chr(0x21).\chr(0xF9).\chr(0x04).\chr(0x00).\chr(0x00).\chr(0x00).\chr(0x00).\chr(0x00);
            }

            $imageData = &$this->imageData[$this->index - 1]['graphicsextension'];

            $imageData[3] = \chr((\ord($imageData[3]) & 0xE3) | ($this->orgVars[$this->index - 1]['disposal_method'] << 2));
            $imageData[4] = \chr($this->orgVars[$this->index - 1]['delay_time'] % 256);
            $imageData[5] = \chr((int) floor($this->orgVars[$this->index - 1]['delay_time'] / 256));
            if ($hasTransparency) {
                $imageData[6] = \chr($this->orgVars[$this->index - 1]['transparent_color_index']);
            }
            $imageData[3] = \chr(\ord($imageData[3]) | $hasTransparency);

            // apply calculated left and top offset
            $imageBlock[1] = \chr((int) round(($this->orgVars[$this->index - 1]['offset_left'] * $this->wr) % 256));
            $imageBlock[2] = \chr((int) floor(($this->orgVars[$this->index - 1]['offset_left'] * $this->wr) / 256));
            $imageBlock[3] = \chr((int) round(($this->orgVars[$this->index - 1]['offset_top'] * $this->hr) % 256));
            $imageBlock[4] = \chr((int) floor(($this->orgVars[$this->index - 1]['offset_top'] * $this->hr) / 256));

            if (1 == $this->index) {
                if (!isset($this->imageInfo['applicationdata']) || !$this->imageInfo['applicationdata']) {
                    $this->imageInfo['applicationdata'] = \chr(0x21).\chr(0xFF).\chr(0x0B).'NETSCAPE2.0'.\chr(0x03).\chr(0x01).\chr(0x00).\chr(0x00).\chr(0x00);
                }
                if (!isset($this->imageInfo['commentdata']) || !$this->imageInfo['commentdata']) {
                    $this->imageInfo['commentdata'] = \chr(0x21).\chr(0xFE).\chr(0x10).'PHPGIFRESIZER1.0'.\chr(0);
                }
                $string .= $this->orgVars['gifheader'].$this->imageInfo['applicationdata'].$this->imageInfo['commentdata'];
                if (isset($this->orgVars['hasgx_type_0']) && $this->orgVars['hasgx_type_0']) {
                    $string .= $this->globalData['graphicsextension_0'];
                }
                if (isset($this->orgVars['hasgx_type_1']) && $this->orgVars['hasgx_type_1']) {
                    $string .= $this->globalData['graphicsextension'];
                }
            }

            $string .= $imageData.$imageBlock;
            ++$k;
            $this->closeFile();
        }

        $string .= \chr(0x3B);

        // applying new width & height to gif header
        $string[6] = \chr($newWidth % 256);
        $string[7] = \chr((int) floor($newWidth / 256));
        $string[8] = \chr($newHeight % 256);
        $string[9] = \chr((int) floor($newHeight / 256));
        $string[11] = $this->orgVars['background_color'];
        // if(file_exists($newFilename)){unlink($newFilename);}
        file_put_contents($newFilename, $string);
    }

    /**
     * Variable Reset function
     * If a instance is used multiple times, it's needed. Trust me.
     */
    private function clearVariables(): void
    {
        $this->pointer = 0;
        $this->index = 0;
        $this->imageData = [];
        $this->imageInfo = [];
        $this->handle = 0;
        $this->parsedFiles = [];
    }

    /**
     * Clear Frames function
     * For deleting the frames after encoding.
     */
    private function clearFrames(): void
    {
        foreach ($this->parsedFiles as $temp_frame) {
            unlink($temp_frame);
        }
    }

    /**
     * Frame Writer
     * Writes the GIF frames into files.
     */
    private function writeFrames(int $prepend): void
    {
        $size = \sizeof($this->imageData);

        for ($i = 0; $i < $size; ++$i) {
            file_put_contents($this->tempDir.'/frame_'.$prepend.'_'.str_pad((string) $i, 2, '0', STR_PAD_LEFT).'.gif', $this->imageInfo['gifheader'].$this->imageData[$i]['graphicsextension'].$this->imageData[$i]['imageData'].\chr(0x3B));
            $this->parsedFiles[] = $this->tempDir.'/frame_'.$prepend.'_'.str_pad((string) $i, 2, '0', STR_PAD_LEFT).'.gif';
        }
    }

    /**
     * Color Palette Transfer Device
     * Transferring Global Color Table (GCT) from frames into Local Color Tables in animation.
     */
    private function transferColorTable(string $src, string &$dst): void
    {
        // src is gif header,dst is image data block
        // if global color table exists,transfer it
        if ((\ord($src[10]) & 128) == 128) {
            // Gif Header Global Color Table Length
            $ghctl = pow(2, $this->readBits(\ord($src[10]), 5, 3) + 1) * 3;
            // cut global color table from gif header
            $ghgct = substr($src, 13, $ghctl);
            // check image block color table length
            if ((\ord($dst[9]) & 128) == 128) {
                // Image data contains color table. skip.
            } else {
                // Image data needs a color table.
                // get last color table length so we can truncate the dummy color table
                $idctl = pow(2, $this->readBits(\ord($dst[9]), 5, 3) + 1) * 3;
                // set color table flag and length
                $dst[9] = \chr(\ord($dst[9]) | (0x80 | (log($ghctl / 3, 2) - 1)));
                // inject color table
                $dst = substr($dst, 0, 10).$ghgct.substr($dst, -1 * \strlen($dst) + 10);
            }
        }
    }

    /**
     * Below functions are the main structure parser components.
     */
    private function getGifHeader(): void
    {
        $this->pForward(10);
        if (1 == $this->readBits($mybyte = $this->readByteInt(), 0, 1)) {
            $this->pForward(2);
            $this->pForward(pow(2, $this->readBits($mybyte, 5, 3) + 1) * 3);
        } else {
            $this->pForward(2);
        }

        $this->imageInfo['gifheader'] = $this->dataPart(0, $this->pointer);
        if ($this->decoding) {
            $this->orgVars['gifheader'] = $this->imageInfo['gifheader'];
            $this->originalWidth = \ord($this->orgVars['gifheader'][7]) * 256 + \ord($this->orgVars['gifheader'][6]);
            $this->originalHeight = \ord($this->orgVars['gifheader'][9]) * 256 + \ord($this->orgVars['gifheader'][8]);
            $this->orgVars['background_color'] = $this->orgVars['gifheader'][11];
        }
    }

    private function getApplicationData(): void
    {
        $startData = $this->readByte(2);
        if ($startData == \chr(0x21).\chr(0xFF)) {
            $start = $this->pointer - 2;
            $this->pForward($this->readByteInt());
            $this->readDataStream($this->readByteInt());
            $this->imageInfo['applicationdata'] = $this->dataPart($start, $this->pointer - $start);
        } else {
            $this->pRewind(2);
        }
    }

    private function getCommentData(): void
    {
        $startData = $this->readByte(2);
        if ($startData == \chr(0x21).\chr(0xFE)) {
            $start = $this->pointer - 2;
            $this->readDataStream($this->readByteInt());
            $this->imageInfo['commentdata'] = $this->dataPart($start, $this->pointer - $start);
        } else {
            $this->pRewind(2);
        }
    }

    private function getGraphicsExtension(int $type): void
    {
        $startData = $this->readByte(2);
        if ($startData == \chr(0x21).\chr(0xF9)) {
            $start = $this->pointer - 2;
            $this->pForward($this->readByteInt());
            $this->pForward(1);
            if (2 == $type) {
                $this->imageData[$this->index]['graphicsextension'] = $this->dataPart($start, $this->pointer - $start);
            } elseif (1 == $type) {
                $this->orgVars['hasgx_type_1'] = 1;
                $this->globalData['graphicsextension'] = $this->dataPart($start, $this->pointer - $start);
            } elseif (0 == $type && false === $this->decoding) {
                $this->encData[$this->index]['graphicsextension'] = $this->dataPart($start, $this->pointer - $start);
            } elseif (0 == $type && true === $this->decoding) {
                $this->orgVars['hasgx_type_0'] = 1;
                $this->globalData['graphicsextension_0'] = $this->dataPart($start, $this->pointer - $start);
            }
        } else {
            $this->pRewind(2);
        }
    }

    private function getimageBlock(int $type): void
    {
        if ($this->checkByte(0x2C)) {
            $start = $this->pointer;
            $this->pForward(9);
            if (1 == $this->readBits($mybyte = $this->readByteInt(), 0, 1)) {
                $this->pForward(pow(2, $this->readBits($mybyte, 5, 3) + 1) * 3);
            }
            $this->pForward(1);
            $this->readDataStream($this->readByteInt());
            $this->imageData[$this->index]['imageData'] = $this->dataPart($start, $this->pointer - $start);

            if (0 == $type) {
                $this->orgVars['hasgx_type_0'] = 0;
                if (isset($this->globalData['graphicsextension_0'])) {
                    $this->imageData[$this->index]['graphicsextension'] = $this->globalData['graphicsextension_0'];
                } else {
                    $this->imageData[$this->index]['graphicsextension'] = null;
                }
                unset($this->globalData['graphicsextension_0']);
            } elseif (1 == $type) {
                if (isset($this->orgVars['hasgx_type_1']) && 1 == $this->orgVars['hasgx_type_1']) {
                    $this->orgVars['hasgx_type_1'] = 0;
                    $this->imageData[$this->index]['graphicsextension'] = $this->globalData['graphicsextension'];
                    unset($this->globalData['graphicsextension']);
                } else {
                    $this->orgVars['hasgx_type_0'] = 0;
                    $this->imageData[$this->index]['graphicsextension'] = $this->globalData['graphicsextension_0'];
                    unset($this->globalData['graphicsextension_0']);
                }
            }

            $this->parseImageData();
            ++$this->index;
        }
    }

    private function parseImageData(): void
    {
        $this->imageData[$this->index]['disposal_method'] = $this->getImageDataBit('ext', 3, 3, 3);
        $this->imageData[$this->index]['user_input_flag'] = $this->getImageDataBit('ext', 3, 6, 1);
        $this->imageData[$this->index]['transparent_color_flag'] = $this->getImageDataBit('ext', 3, 7, 1);
        $this->imageData[$this->index]['delay_time'] = $this->dualByteVal($this->getImageDataByte('ext', 4, 2));
        $this->imageData[$this->index]['transparent_color_index'] = \ord($this->getImageDataByte('ext', 6, 1));
        $this->imageData[$this->index]['offset_left'] = $this->dualByteVal($this->getImageDataByte('dat', 1, 2));
        $this->imageData[$this->index]['offset_top'] = $this->dualByteVal($this->getImageDataByte('dat', 3, 2));
        $this->imageData[$this->index]['width'] = $this->dualByteVal($this->getImageDataByte('dat', 5, 2));
        $this->imageData[$this->index]['height'] = $this->dualByteVal($this->getImageDataByte('dat', 7, 2));
        $this->imageData[$this->index]['local_color_table_flag'] = $this->getImageDataBit('dat', 9, 0, 1);
        $this->imageData[$this->index]['interlace_flag'] = $this->getImageDataBit('dat', 9, 1, 1);
        $this->imageData[$this->index]['sort_flag'] = $this->getImageDataBit('dat', 9, 2, 1);
        $this->imageData[$this->index]['color_table_size'] = pow(2, $this->getImageDataBit('dat', 9, 5, 3) + 1) * 3;
        $this->imageData[$this->index]['color_table'] = substr($this->imageData[$this->index]['imageData'], 10, $this->imageData[$this->index]['color_table_size']);
        $this->imageData[$this->index]['lzw_code_size'] = \ord($this->getImageDataByte('dat', 10, 1));
        if ($this->decoding) {
            $this->orgVars[$this->index]['transparent_color_flag'] = $this->imageData[$this->index]['transparent_color_flag'];
            $this->orgVars[$this->index]['transparent_color_index'] = $this->imageData[$this->index]['transparent_color_index'];
            $this->orgVars[$this->index]['delay_time'] = $this->imageData[$this->index]['delay_time'];
            $this->orgVars[$this->index]['disposal_method'] = $this->imageData[$this->index]['disposal_method'];
            $this->orgVars[$this->index]['offset_left'] = $this->imageData[$this->index]['offset_left'];
            $this->orgVars[$this->index]['offset_top'] = $this->imageData[$this->index]['offset_top'];
        }
    }

    private function getImageDataByte(string $type, int $start, int $length): string
    {
        if ('ext' == $type && null !== $this->imageData[$this->index]['graphicsextension']) {
            return substr($this->imageData[$this->index]['graphicsextension'], $start, $length);
        }

        if ('dat' == $type && null !== $this->imageData[$this->index]['imageData']) {
            return substr($this->imageData[$this->index]['imageData'], $start, $length);
        }

        return '';
    }

    /**
     * @return float|int|null
     */
    private function getImageDataBit(string $type, int $byteIndex, int $bitStart, int $bitLength)
    {
        if ('ext' == $type && null !== $this->imageData[$this->index]['graphicsextension']) {
            return $this->readBits(\ord(substr($this->imageData[$this->index]['graphicsextension'], $byteIndex, 1)), $bitStart, $bitLength);
        }

        if ('dat' == $type && null !== $this->imageData[$this->index]['imageData']) {
            return $this->readBits(\ord(substr($this->imageData[$this->index]['imageData'], $byteIndex, 1)), $bitStart, $bitLength);
        }
    }

    private function dualByteVal(string $s): int
    {
        if (null === $s || !isset($s[1]) || !isset($s[0])) {
            return 0;
        }

        return \ord($s[1]) * 256 + \ord($s[0]);
    }

    private function readDataStream(int $firstLength): bool
    {
        $this->pForward($firstLength);
        $length = $this->readByteInt();
        if (0 != $length) {
            while (0 != $length) {
                $this->pForward($length);
                $length = $this->readByteInt();
            }
        }

        return true;
    }

    private function loadFile(string $filename): void
    {
        $this->handle = fopen($filename, 'rb');
        $this->pointer = 0;
    }

    private function closeFile(): void
    {
        fclose($this->handle);
        $this->handle = 0;
    }

    private function readByte(int $byteCount): string|false
    {
        $data = fread($this->handle, $byteCount);
        $this->pointer += $byteCount;

        return $data;
    }

    private function readByteInt(): int
    {
        $data = fread($this->handle, 1);
        ++$this->pointer;

        return \ord($data);
    }

    private function readBits(int $byte, int $start, int $length): float|int
    {
        $bin = str_pad(decbin($byte), 8, '0', STR_PAD_LEFT);
        $data = substr($bin, $start, $length);

        return bindec($data);
    }

    private function pRewind(int $length): void
    {
        $this->pointer -= $length;
        fseek($this->handle, $this->pointer);
    }

    private function pForward(int $length): void
    {
        $this->pointer += $length;
        fseek($this->handle, $this->pointer);
    }

    private function dataPart(int $start, int $length): string|false
    {
        fseek($this->handle, $start);
        $data = fread($this->handle, $length);
        fseek($this->handle, $this->pointer);

        return $data;
    }

    private function checkByte(int $byte): bool
    {
        if (fgetc($this->handle) == \chr($byte)) {
            fseek($this->handle, $this->pointer);

            return true;
        } else {
            fseek($this->handle, $this->pointer);

            return false;
        }
    }

    private function checkEOF(): bool
    {
        if (false === fgetc($this->handle)) {
            return true;
        } else {
            fseek($this->handle, $this->pointer);

            return false;
        }
    }

    /**
     * Resizes the animation frames.
     */
    private function resizeFrames(): void
    {
        $k = 0;

        $sw = $this->imageData[0]['width'];
        $sh = $this->imageData[0]['height'];
        $nw = (int) round($sw * $this->wr);
        $nh = (int) round($sh * $this->hr);

        foreach ($this->parsedFiles as $img) {
            $src = imagecreatefromgif($img);
            $sprite = imagecreatetruecolor($nw, $nh);
            $trans = imagecolortransparent($sprite);
            imagealphablending($sprite, false);
            imagesavealpha($sprite, true);
            imagepalettecopy($sprite, $src);
            imagefill($sprite, 0, 0, imagecolortransparent($src));
            imagecolortransparent($sprite, imagecolortransparent($src));
            imagecopyresized($sprite, $src, 0, 0, 0, 0, $nw, $nh, $sw, $sh);
            imagegif($sprite, $img);
            imagedestroy($sprite);
            imagedestroy($src);
            ++$k;
        }
    }
}
