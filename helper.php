<?php

/**
 * Return application base path
 * @return string
 */
function basePath() {
    $path = pathinfo( __FILE__, PATHINFO_DIRNAME );
    $path = realpath( $path.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR );
    $path = rtrim( $path, DIRECTORY_SEPARATOR ).DIRECTORY_SEPARATOR;

    return $path;
}

function removeUtf8Bom($text)
{
    $bom = pack('H*', 'EFBBBF');
    $text = preg_replace("/^$bom/", '', $text);

    return $text;
}
function normalizeNewLines($file_content)
{
    $file_content = str_replace("\r\n", "\n", $file_content);
    $file_content = str_replace("\r", "\n", $file_content);

    return $file_content;
}

function fileExtension($filename) {
    $parts = explode('.', $filename);
    $extension = end($parts);
    $extension = strtolower($extension);

    return $extension;
}
//srt
function fileContentToInternalFormat($file_content)
{
    $internal_format = []; // array - where file content will be stored

    $blocks = explode("\n\n", trim($file_content)); // each block contains: start and end times + text
    foreach ($blocks as $block) {
        preg_match('/(?<start>.*) --> (?<end>.*)\n(?<text>(\n*.*)*)/m', $block, $matches);

        // if block doesn't contain text (invalid srt file given)
        if (empty($matches)) {
            continue;
        }

        $internal_format[] = [
            'start' => srtTimeToInternal($matches['start']),
            'end' => srtTimeToInternal($matches['end']),
            'lines' => explode("\n", $matches['text']),
        ];
    }

    return $internal_format;
}
//to Dfxp
function internalFormatToFileContent($internal_format)
{

    $file_content = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<tt xmlns:tt="http://www.w3.org/ns/ttml" xmlns:ttm="http://www.w3.org/ns/ttml#metadata" xmlns:ttp="http://www.w3.org/ns/ttml#parameter" xmlns:tts="http://www.w3.org/ns/ttml#styling" ttp:cellResolution="40 19" ttp:pixelAspectRatio="1 1" ttp:tickRate="10000000" ttp:timeBase="media" tts:extent="640px 480px" xmlns="http://www.w3.org/ns/ttml">
	<head>
		<ttp:profile use="http://netflix.com/ttml/profile/dfxp-ls-sdh"/>
		<styling>
			<style tts:color="white" tts:fontFamily="monospaceSansSerif" tts:fontSize="100%" xml:id="bodyStyle"/>
			<style tts:color="white" tts:fontFamily="monospaceSansSerif" tts:fontSize="100%" tts:fontStyle="italic" xml:id="style_0"/>
		</styling>
		<layout>
			<region xml:id="region_00">
				<style tts:textAlign="left"/>
				<style tts:displayAlign="center"/>
			</region>
			<region xml:id="region_01">
				<style tts:textAlign="left"/>
				<style tts:displayAlign="center"/>
			</region>
			<region xml:id="region_02">
				<style tts:textAlign="left"/>
				<style tts:displayAlign="center"/>
			</region>
		</layout>
	</head>
	<body style="bodyStyle">
		<div xml:space="preserve">
			
';

    foreach ($internal_format as $k => $block) {


        $start = internalTimeToDfxp($block['start']);
        $end = internalTimeToDfxp($block['end']);
        $lines = implode("<br/>", $block['lines']);
        $lines = iconv('cp1251', 'UTF-8', $lines);
        $file_content .= "<p begin=\"{$start}\" end=\"{$end}\">{$lines}</p>\n";
    }

    $file_content .= '  </div>
  </body>
</tt>';


    $file_content = str_replace("\r", "", $file_content);
    $file_content = str_replace("<i>", "", $file_content);
    $file_content = str_replace("</i>", "", $file_content);
    $file_content = str_replace("\n", "\r\n", $file_content);

    return $file_content;
}


function internalTimeToDfxp($internal_time)
{

    $internal_time = $internal_time * 10 **7;

    return $internal_time . 't';

}


function srtTimeToInternal($srt_time)
{
    $parts = explode(',', $srt_time);

    $only_seconds = strtotime("1970-01-01 {$parts[0]} UTC");
    $milliseconds = (float)('0.' . $parts[1]);

    $time = $only_seconds + $milliseconds;

    return $time;
}


function array_dfxp_download($filename = "ShadowAndBoneS01E01.dfxp" , $internalFormatToFileContent) {

      $fp = fopen($filename, 'w');
      fwrite($fp, $internalFormatToFileContent);
      fclose($fp);

      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename='.basename($filename));
      header('Content-Transfer-Encoding: binary');
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize($filename));




      ob_clean();
      flush();
      readfile($filename);
      exit;
}